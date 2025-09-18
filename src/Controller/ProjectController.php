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

#[Route('/projects')]
final class ProjectController extends AbstractController
{
    #[Route('', name: 'app_projects')]
    public function index(ProjectRepository $repository): Response
    {
        $projects = $repository->findActiveProjects();

        return $this->render('projects/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/show/{id}', name: 'app_projects_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(ProjectRepository $projectRepository, TaskRepository $taskRepository, int $id): Response
    {
        $project = $projectRepository->find($id);
        $members = $project->getMembers();
        $tasksByStatus = $taskRepository->findByProjectGroupedByStatusOrderedByDeadline($project);
        return $this->render('projects/show.html.twig', [
            'project' => $project,
            'members' => $members,
            'tasksByStatus' => $tasksByStatus,
            'allStatuses' => TaskStatus::cases(),
        ]);
    }

    #[Route('/edit/{id}', name: 'app_projects_edit', methods: ['GET', 'POST'])]
    public function new(?Project $project, Request $request, EntityManagerInterface $entityManager): Response
    {
        $project ??= new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $project = $form->getData();
            $entityManager->persist($project);
            $entityManager->flush();
        }
        return $this->render('projects/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/archive/{id}', name: 'app_projects_archive', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function delete(ProjectRepository $repository, EntityManagerInterface $entityManager, int $id): Response
    {
        $project = $repository->find($id);
        if (!$project) {
            throw $this->createNotFoundException(
                'Ce projet n\'existe pas'
            );
        }
        $project->setArchived(true);
        $entityManager->persist($project);
        $entityManager->flush();
        return $this->redirectToRoute('app_projects');
    }
}
