<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/task')]
final class TaskController extends AbstractController
{
    #[Route('/new/{projectId}', name: 'app_task_new', requirements: ['projectId' => '\d+'], methods: ['GET', 'POST'])]
    #[Route('/edit/{id}', name: 'app_task_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function new(
        ?Task $task, ?int $projectId, Request $request, EntityManagerInterface $entityManager, ProjectRepository $projectRepository): Response
    {
        $task ??= new Task();

        if ($task->getId() === null && $projectId) {
            $project = $projectRepository->find($projectId);
            if (!$project) {
                throw $this->createNotFoundException('Projet introuvable');
            }
            $task->setProject($project);
        }

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('app_projects_show', ['id' => $task->getProject()->getId()]);
        }

        return $this->render('task/new.html.twig', [
            'form' => $form,
            'task' => $task,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_task_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(TaskRepository $repository, EntityManagerInterface $entityManager, int $id): Response
    {
        $task = $repository->find($id);

        if (!$task) {
            throw $this->createNotFoundException('Tâche introuvable');
        };

        $entityManager->remove($task);
        $entityManager->flush();

        return $this->redirectToRoute('app_projects_show', ['id' => $task->getProject()->getId()]);
    }
}
