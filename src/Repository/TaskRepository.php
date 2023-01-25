<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function save(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function completeTask(Task $entity, bool $flush = false): void
    {
        $entity->setStatus(true);
        $this->getEntityManager()->persist($entity);
        if($flush)
        {
            $this->getEntityManager()->flush();
        }
    }

    public function orderAndSearchByParameters( int $listId, string $orderBy, string $direction, $search): array
    {
        $qb = $this->createQueryBuilder('tasks');
        $qb ->andWhere('tasks.todoList = :listId')
            ->setParameter('listId', $listId);
        if( $direction == 'ASC')
        {
            $qb->orderBy('tasks.'.$orderBy, 'ASC');
        }
        else
        {
            $qb->orderBy('tasks.'.$orderBy, 'DESC');
        }
        if (!($search === null))
        {
            $qb->andWhere('lower(tasks.task) LIKE :searchTerm')
                ->setParameter('searchTerm', '%'.strtolower($search).'%');
        }
        return $qb->getQuery()->getResult();
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
