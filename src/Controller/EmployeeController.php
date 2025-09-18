<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/employee')]
final class EmployeeController extends AbstractController
{
    #[Route('', name: 'app_employee')]
    public function index(EmployeeRepository $repository): Response
    {
        $employees = $repository->getEmployeesOrderedByStatus();

        return $this->render('employee/index.html.twig', [
            'employees' => $employees,
        ]);
    }

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

    #[Route('/delete/{id}', name: 'app_employee_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(EmployeeRepository $repository, EntityManagerInterface $entityManager, int $id): Response
    {
        $employee = $repository->find($id);

        if (!$employee) {
            throw $this->createNotFoundException('Cet employé n\'existe pas');
        }

        $entityManager->remove($employee);
        $entityManager->flush();

        return $this->redirectToRoute('app_employee');
    }
}
