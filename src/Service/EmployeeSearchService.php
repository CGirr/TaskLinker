<?php

namespace App\Service;

use App\DTO\EmployeeSearchDTO;
use App\Repository\EmployeeRepository;
use Symfony\Component\HttpFoundation\Request;

class EmployeeSearchService
{
    public function __construct(
        private readonly EmployeeRepository $repository
    ) {
    }

    public function searchAndPaginateEmployees(Request $request): EmployeeSearchDTO
    {
        $search = $request->query->get('search') ?? '';
        $currentPage = max(1, (int) $request->query->get('currentPage', 1));
        $perPage = 10;

        $totalResults = $search
            ? $this->repository->getNumberOfSearchResults($search)
            : $this->repository->getNumberOfEmployees();


        $totalPages = max(1, ceil($totalResults / $perPage));
        $offset = ($currentPage - 1) * $perPage;

        $dto = (new EmployeeSearchDTO())
            ->setPerPage($perPage)
            ->setSearch($search)
            ->setTotalPages($totalPages)
            ->setOffset($offset)
            ->setCurrentPage($currentPage)
            ->setTotalResults($totalResults);

        $employees = $this->repository->getPaginateEmployees($dto);
        $dto->setEmployees($employees);

        return $dto;
    }
}
