<?php

namespace App\Controller;

use App\Entity\Employee;
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
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
     * @param Project $project
     * @param TaskRepository $taskRepository
     * @return Response
     */
    #[Route('/show/{project}', name: 'app_projects_show', methods: ['GET'])]
    #[IsGranted('PROJECT_VIEW', 'project')]
    public function show(Project $project, TaskRepository $taskRepository): Response
    {
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
     * @param Project|null $project
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/new', name: 'app_project_new', methods: ['GET', 'POST'])]
    #[Route('/edit/{project}', name: 'app_projects_edit', methods: ['GET', 'POST'])]
    public function new(?Project $project, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $project ??= new Project();

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project = $form->getData();
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('app_projects_show', ['project' => $project->getId()]);
        }

        return $this->render('projects/new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @param Project $project
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/archive/{project}', name: 'app_projects_archive', methods: ['GET'])]
    public function delete(Project $project, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $project->setArchived(true);
        $entityManager->persist($project);
        $entityManager->flush();
        return $this->redirectToRoute('app_projects');
    }
}
