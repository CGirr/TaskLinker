<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Form\TaskType;
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
     * @param Project $project
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/new/{project}', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(
        Project $project,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $task = new Task();
        $task->setProject($project);

        return $this->handleForm($task, $request, $entityManager);
    }

    /**
     * @param Request $request
     * @param Task $task
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/edit/{task}', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Task $task,
        EntityManagerInterface $entityManager,
    ): Response {
        return $this->handleForm($task, $request, $entityManager);
    }

    /**
     * @param Task $task
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    #[Route('/delete/{task}', name: 'app_task_delete', methods: ['POST'])]
    public function delete(
        Task $task,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        if (!$this->isCsrfTokenValid('delete_task' . $task->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }

        $entityManager->remove($task);
        $entityManager->flush();

        return $this->redirectToRoute('app_projects_show', ['project' => $task->getProject()->getId()]);
    }

    /**
     * @param Task $task
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    private function handleForm(Task $task, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TaskType::class, $task, [
            'members' => $task->getProject()->getMembers()->toArray()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('app_projects_show', ['project' => $task->getProject()->getId()]);
        }

        return $this->render('task/form.html.twig', [
            'form' => $form,
            'task' => $task,
        ]);
    }
}
