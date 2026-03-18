<?php

namespace App\Repository;

use App\Entity\OrderItems;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderItems>
 */
class OrderItemsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderItems::class);
    }

//    /**
//     * @return OrderItems[] Returns an array of OrderItems objects
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

//    public function findOneBySomeField($value): ?OrderItems
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function add(OrderItems $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getOrderItemsOfUser(int $orderId, int $userId ) : array
    {
        return $this->createQueryBuilder('oi')
        ->select('oi.id, oi.quantity, oi.price, oi.status, oi.seller_id, oi.created_at')
        ->addSelect('p.name AS product_name')
        ->addSelect('o.user_id')
        ->leftJoin('oi.product', 'p')
        ->leftJoin('oi.order_', 'o')
        ->andWhere('o.id = :orderId')
        ->andWhere('o.user_id = :userId')
        ->setParameter('orderId', $orderId)
        ->setParameter('userId', $userId)
        ->getQuery()
        ->getArrayResult();
    }

    public function getOneItemOfUser(int $orderItemId, int $userId, int $sellerId) : ?OrderItems
    {
        return $this->createQueryBuilder('oi')
        ->select('oi')
        ->leftJoin('oi.order_', 'o')
        ->andWhere('oi.id = :orderItemId')
        ->andWhere('oi.seller_id = :sellerId')
        ->andWhere('o.user_id = :userId')
        ->setParameter('orderItemId', $orderItemId)
        ->setParameter('userId', $userId)
        ->setParameter('sellerId', $sellerId)
        ->getQuery()
        ->getOneOrNullResult();
    }

    public function getSellerOrderItemsBySellerId(int $sellerId) : array
    {
        return $this->createQueryBuilder('oi')
        ->select('oi.id, oi.quantity, oi.price, oi.status, oi.seller_id, oi.created_at')
        ->addSelect('p.name AS product_name')
        ->addSelect('o.user_id')
        ->leftJoin('oi.product', 'p')
        ->leftJoin('oi.order_', 'o')
        ->andWhere('oi.seller_id = :sellerId')
        ->setParameter('sellerId', $sellerId)
        ->getQuery()
        ->getArrayResult();
    }

    public function getOrdersOfMyItemsByUser(int $userId, int $sellerId) : array
    {
        return $this->createQueryBuilder('oi')
        ->select('oi.id, oi.quantity, oi.price, oi.status, oi.seller_id, oi.created_at, o.user_id, p.name')
        ->leftJoin('oi.product', 'p')
        ->leftJoin('oi.order_', 'o')
        ->andWhere('o.user_id = :userId')
        ->andWhere('oi.seller_id = :sellerId')
        ->setParameter('userId', $userId)
        ->setParameter('sellerId', $sellerId)
        ->getQuery()
        ->getArrayResult();
    }

    public function countTheNumberOfItemsInOrder(int $orderId) : int
    {
        return (int) $this->createQueryBuilder('oi')
        ->select('COUNT(oi.id)')
        ->andWhere('oi.order_ = :orderId')
        ->setParameter('orderId', $orderId)
        ->getQuery()
        ->getSingleScalarResult();
    }

    public function countTheNumberOfItemsInOrderByStatus(int $orderId, string $status) : int
    {
        return (int) $this->createQueryBuilder('oi')
        ->select('COUNT(oi.id)')
        ->andWhere('oi.order_ = :orderId')
        ->andWhere('oi.status = :status')
        ->setParameter('orderId', $orderId)
        ->setParameter('status', $status)
        ->getQuery()
        ->getSingleScalarResult();
    }

    
    
}
