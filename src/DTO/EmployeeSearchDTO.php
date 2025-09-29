<?php

namespace App\DTO;

/**
 *
 */
class EmployeeSearchDTO
{
    /**
     * @var string|null
     */
    private ?string $search = null;
    /**
     * @var int
     */
    private int $currentPage;
    /**
     * @var int
     */
    private int $perPage;

    /**
     * @var array
     */
    private array $employees = [];

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @return array
     */
    public function getEmployees(): array
    {
        return $this->employees;
    }

    /**
     * @param array $employees
     * @return EmployeeSearchDTO
     */
    public function setEmployees(array $employees): self
    {
        $this->employees = $employees;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSearch(): ?string
    {
        return $this->search;
    }

    /**
     * @param string|null $search
     * @return EmployeeSearchDTO
     */
    public function setSearch(?string $search): self
    {
        $this->search = $search;
        return $this;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @param int $currentPage
     * @return EmployeeSearchDTO
     */
    public function setCurrentPage(int $currentPage): self
    {
        $this->currentPage = $currentPage;
        return $this;
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @param int $perPage
     * @return EmployeeSearchDTO
     */
    public function setPerPage(int $perPage): self
    {
        $this->perPage = $perPage;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalResults(): int
    {
        return $this->totalResults;
    }

    /**
     * @param int $totalResults
     * @return EmployeeSearchDTO
     */
    public function setTotalResults(int $totalResults): self
    {
        $this->totalResults = $totalResults;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    /**
     * @param int $totalPages
     * @return EmployeeSearchDTO
     */
    public function setTotalPages(int $totalPages): self
    {
        $this->totalPages = $totalPages;
        return $this;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     * @return EmployeeSearchDTO
     */
    public function setOffset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @var int
     */
    private int $totalResults;
    /**
     * @var int
     */
    private int $totalPages;
    /**
     * @var int
     */
    private int $offset;
}
