<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Task;
use App\Enum\TaskStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findByProjectGroupedByStatusOrderedByDeadline(Project $project): array
    {
        $tasks = $this->createQueryBuilder('t')
            ->andWhere('t.project = :project')
            ->setParameter('project', $project)
            ->leftJoin('t.member', 'm')
            ->addSelect('m')
            ->orderBy('t.date', 'ASC')
            ->addOrderBy('t.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        $taskByStatus = [];

        foreach (TaskStatus::cases() as $status) {
            $taskByStatus[$status->value] = [];
        }

        foreach ($tasks as $task) {
            if ($task->getStatus() !== null) {
                $taskByStatus[$task->getStatus()->value][] = $task;
            }
        }

        return $taskByStatus;
    }

//    /**
//     * @return Task[] Returns an array of Task objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Task
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
