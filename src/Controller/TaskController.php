<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
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
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ProjectRepository $projectRepository
     * @return Response
     */
    #[Route('/new/{project}', name: 'app_task_new', methods: ['GET', 'POST'])]
    #[Route('/edit/{task}', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function new(
        #[MapsEntity(mapping: ['task' => 'id'])] ?Task $task,
        #[MapEntity(mapping: ['project' => 'id'])] ?Project $project,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        if (!$task) {
            if (!$project) {
                throw $this->createNotFoundException('Projet introuvable');
            }
            $task = new Task();
            $task->setProject($project);
        }

        $form = $this->createForm(TaskType::class, $task, [
            'members' => $task->getProject()->getMembers()->toArray()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('app_projects_show', ['project' => $task->getProject()->getId()]);
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
    #[Route('/delete/{task}', name: 'app_task_delete', methods: ['POST'])]
    public function delete(
        Task $task,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        if (!$task) {
            throw $this->createNotFoundException('Tâche introuvable');
        }

        if (!$this->isCsrfTokenValid('delete_task' . $task->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }

        $entityManager->remove($task);
        $entityManager->flush();

        return $this->redirectToRoute('app_projects_show', ['project' => $task->getProject()->getId()]);
    }
}
