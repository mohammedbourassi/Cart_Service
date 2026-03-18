<?php

namespace App\Repository;

use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Products>
 */
class ProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Products::class);
    }

    //    /**
    //     * @return Products[] Returns an array of Products objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Products
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function save(Products $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getOneProduct(int $productId, int $sellerID)
    {
        return $this->createQueryBuilder('p')
        ->select('p.id, p.name, p.description, p.price, p.stock, p.type')
        ->andWhere('p.id = :productId')
        ->andWhere('p.seller_Id = :sellerId')
        ->setParameter('productId', $productId)
        ->setParameter('sellerId', $sellerID)
        ->getQuery()
        ->getOneOrNullResult();
        
    }

    public function getProduct(int $productId, int $sellerID) : ?Products
    {
        return $this->createQueryBuilder('p')
        ->select('p')
        ->andWhere('p.id = :productId')
        ->andWhere('p.seller_Id = :sellerId')
        ->setParameter('productId', $productId)
        ->setParameter('sellerId', $sellerID)
        ->getQuery()
        ->getOneOrNullResult();
        
    }

    public function getProductsOfSeller(int $sellerId) 
    {
        return $this->createQueryBuilder('p')
        ->select('p')
        ->andWhere('p.seller_Id = :sellerId')
        ->setParameter('sellerId', $sellerId)
        ->getQuery()
        ->getArrayResult();
    }

    public function getProductOfSellerById(int $productId, int $sellerId) 
    {
        return $this->createQueryBuilder('p')
        ->select('p')
        ->andWhere('p.id = :productId')
        ->andWhere('p.seller_Id = :sellerId')
        ->setParameter('productId', $productId)
        ->setParameter('sellerId', $sellerId)
        ->getQuery()
        ->getArrayResult();
    }

    public function remove(Products $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
