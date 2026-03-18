<?php

namespace App\Repository;


use App\Entity\Orders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Orders>
 */
class OrdersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Orders::class);
    }

//    /**
//     * @return Orders[] Returns an array of Orders objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Orders
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function save(Orders $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Orders $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getOrders(int $userId): array
    {
        return $this->createQueryBuilder('o')
            ->select('o.id, o.user_id, o.shipping_address, o.status, o.total_amount, o.created_at')
            ->where('o.user_id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getArrayResult();
    }

    public function findOrderFullByUser(int $orderId, int $userId): array
    {
        return $this->createQueryBuilder('o')
        ->select('PARTIAL o.{id, user_id, total_amount, status, shipping_address, created_at},
         PARTIAL oi.{id, quantity, price, status, seller_id},
         PARTIAL p.{id, name, type}')
        ->leftJoin('o.orderItems', 'oi')
        ->leftJoin('oi.product', 'p')
        ->where('o.id = :orderId')
        ->setParameter('orderId', $orderId)
        ->andWhere('o.user_id = :userId')
        ->setParameter('userId', $userId)
        ->getQuery()
        ->getArrayResult();
    }

}
