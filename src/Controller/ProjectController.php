<?php

namespace App\Controller;

use App\Entity\Project;
use App\Enum\TaskStatus;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 *
 */
#[Route('/projects')]
final class ProjectController extends AbstractController
{
    /**
     * @param ProjectRepository $repository
     * @return Response
     */
    #[Route('', name: 'app_projects')]
    public function index(ProjectRepository $repository): Response
    {
        $employee = $this->getUser();

        if (in_array('ROLE_ADMIN', $employee->getRoles())) {
            $projects = $repository->findActiveProjects();
        } else {
            $projects = $repository->findByMember($employee);
        }

        return $this->render('projects/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    /**
     * @param Project|null $project
     * @param TaskRepository $taskRepository
     * @return Response
     */
    #[Route('/show/{project}', name: 'app_projects_show', methods: ['GET'])]
    public function show(?Project $project, TaskRepository $taskRepository): Response
    {
        if (!$project) {
            throw $this->createNotFoundException('Projet introuvable');
        }

        $this->denyAccessUnlessGranted('PROJECT_VIEW', $project);
        $members = $project->getMembers();
        $tasksByStatus = $taskRepository->findByProjectGroupedByStatusOrderedByDeadline($project);
        return $this->render('projects/show.html.twig', [
            'project' => $project,
            'members' => $members,
            'tasksByStatus' => $tasksByStatus,
            'allStatuses' => TaskStatus::cases(),
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/new', name: 'app_project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $project = new Project();
        return $this->handleForm($project, $request, $entityManager);
    }

    #[Route('/edit/{project}', name: 'app_projects_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->handleForm($project, $request, $entityManager);
    }

    /**
     * @param Project $project
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/archive/{project}', name: 'app_projects_archive', methods: ['GET'])]
    public function archive(Project $project, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $project->setArchived(true);
        $entityManager->flush();
        return $this->redirectToRoute('app_projects');
    }

    private function handleForm(Project $project, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project = $form->getData();
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('app_projects_show', ['project' => $project->getId()]);
        }

        return $this->render('projects/form.html.twig', [
            'form' => $form,
        ]);
    }
}
