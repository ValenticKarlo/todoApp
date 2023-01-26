<?php

namespace App\Repository;

use App\Entity\TodoList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Internal\Hydration\HydrationException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TodoList>
 *
 * @method TodoList|null find($id, $lockMode = null, $lockVersion = null)
 * @method TodoList|null findOneBy(array $criteria, array $orderBy = null)
 * @method TodoList[]    findAll()
 * @method TodoList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TodoListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TodoList::class);
    }

    public function save(TodoList $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TodoList $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function updateCount(int $listId): void
    {
        $em = $this->getEntityManager();
        $sqb1 = $em->createQueryBuilder()
            ->from('App:Task', 'tasks')
            ->select('count(tasks)')
            ->andWhere('tasks.todoList = :list')
            ->setParameter('list', $listId)
            ->andWhere('tasks.status = :val')
            ->setParameter('val', true)
            ->getQuery()->getSingleScalarResult();

        $sqb2= $em->createQueryBuilder()
            ->from('App:Task', 'tasks')
            ->select('count(tasks)')
            ->andWhere('tasks.todoList = :list')
            ->setParameter('list', $listId)
            ->getQuery()->getSingleScalarResult();

        $qb = $this->createQueryBuilder('list');
        $qb->update('App:TodoList', 'list')
            ->set('list.completedTasks', ':val')
            ->set('list.totalTasks', ':val2')
            ->andWhere('list.id = :listId')
            ->setParameter('val', $sqb1)
            ->setParameter('val2', $sqb2)
            ->setParameter('listId', $listId)
            ->getQuery()
            ->execute();
    }

    public function orderAndSearchByParameters(int $userId, string $orderBy, string $direction, ?string $search): array
    {
        $qb = $this->createQueryBuilder('lists');
        $qb ->andWhere('lists.user = :userId')
            ->setParameter('userId', $userId);
        if($direction == 'ASC')
        {
            $qb->orderBy('lists.'.$orderBy, 'ASC');
        }
        else
        {
            $qb->orderBy('lists.'.$orderBy, 'DESC');
        }
        if ($search === null) {
            $qb->andWhere('lower(lists.name) LIKE :searchTerm')
                ->setParameter('searchTerm', '%'.strtolower($search).'%');
        }
        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return TodoList[] Returns an array of TodoList objects
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

//    public function findOneBySomeField($value): ?TodoList
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
