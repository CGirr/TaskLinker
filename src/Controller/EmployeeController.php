<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;
use App\Service\EmployeeSearchService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 *
 */
#[Route('/employee')]
final class EmployeeController extends AbstractController
{
    /**
     * @param Request $request
     * @param EmployeeSearchService $service
     * @return Response
     */
    #[Route('', name: 'app_employee')]
    public function index(Request $request, EmployeeSearchService $service): Response
    {
        $search = (string) $request->query->get('search') ?? '';
        $currentPage = max(1, (int) $request->query->get('currentPage', 1));
        $perPage = 10;

        $dto = $service->searchAndPaginateEmployees($search, $currentPage, $perPage);

        return $this->render('employee/index.html.twig', [
            'employees' => $dto->getEmployees(),
            'data' => $dto,
        ]);
    }

    /**
     * @param Employee $employee
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/edit/{id}', name: 'app_employee_edit', methods: ['GET', 'POST'])]
    public function edit(Employee $employee, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $employee = $form->getData();
            $entityManager->persist($employee);
            $entityManager->flush();

            return $this->redirectToRoute('app_employee');
        }

        return $this->render('employee/edit.html.twig', [
           'employee' => $employee,
           'form' => $form,
        ]);
    }

    /**
     * @param Request $request
     * @param EmployeeRepository $repository
     * @param EntityManagerInterface $entityManager
     * @param int $id
     * @return Response
     */
    #[Route('/delete/{id}', name: 'app_employee_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, EmployeeRepository $repository, EntityManagerInterface $entityManager, int $id): Response
    {
        $employee = $repository->find($id);

        if (!$employee) {
            throw $this->createNotFoundException('Cet employé n\'existe pas');
        }

        if (!$this->isCsrfTokenValid('delete_employee' . $employee->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }

        $entityManager->remove($employee);
        $entityManager->flush();

        return $this->redirectToRoute('app_employee');
    }
}
