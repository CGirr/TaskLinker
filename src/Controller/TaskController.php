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

/**
 *
 */
#[Route('/task')]
final class TaskController extends AbstractController
{
    /**
     * @param Task|null $task
     * @param int|null $projectId
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ProjectRepository $projectRepository
     * @return Response
     */
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

        $form = $this->createForm(TaskType::class, $task, [
            'members' => $task->getProject()->getMembers()->toArray()
        ]);
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

    /**
     * @param TaskRepository $repository
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param int $id
     * @return Response
     */
    #[Route('/delete/{id}', name: 'app_task_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(TaskRepository $repository, EntityManagerInterface $entityManager, Request $request, int $id): Response
    {
        $task = $repository->find($id);

        if (!$task) {
            throw $this->createNotFoundException('Tâche introuvable');
        }

        if (!$this->isCsrfTokenValid('delete_task' . $task->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }

        $entityManager->remove($task);
        $entityManager->flush();

        return $this->redirectToRoute('app_projects_show', ['id' => $task->getProject()->getId()]);
    }
}
