<?php

namespace App\Repository;

use App\DTO\EmployeeSearchDTO;
use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Employee>
 */
class EmployeeRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
    }

    /**
     * @return array
     */
    public function getEmployeesOrderedByStatus(): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.status', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param string $search
     * @return int
     */
    public function getNumberOfSearchResults(string $search): int
    {
        return (int) $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('UPPER(e.firstname) LIKE :search OR UPPER(e.lastname) LIKE :search')
            ->setParameter('search', '%' . strtoupper($search) . '%')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return int
     */
    public function getNumberOfEmployees(): int
    {
        return (int) $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param EmployeeSearchDTO $dto
     * @return Employee[]
     */
    public function getPaginateEmployees(EmployeeSearchDTO $dto): array
    {
        $qb = $this->createQueryBuilder('e');

        if ($dto->getSearch()) {
             $qb->andWhere('UPPER(e.firstname) LIKE :search OR UPPER(e.lastname) LIKE :search')
                ->setParameter('search', '%' . strtoupper($dto->getSearch()) . '%');
        }
        $qb->orderBy('e.status', 'ASC')
            ->setFirstResult($dto->getOffset())
            ->setMaxResults($dto->getPerPage());

        return $qb->getQuery()->getResult();
    }

    //    /**
//     * @return Employee[] Returns an array of Employee objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Employee
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
