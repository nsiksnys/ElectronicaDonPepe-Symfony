<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findProductsInNoCommission(string $subquery): array
    {
        $qb = $this->createQueryBuilder('p');

        $productIds = $this->getSubqueryResults($subquery);
        
        return $this->createQueryBuilder('p')
                ->where($qb->expr()->notIn('p.id', ':subquery'))
                ->setParameter('subquery', $productIds)
                ->getQuery()
                ->getResult();
    }

    public function findProductsInNoCampaign(string $subquery): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $productIds = $this->getSubqueryResults($subquery);
        
        return $this->createQueryBuilder('p')
                ->where($qb->expr()->notIn('p.id', ':subquery'))
                ->setParameter('subquery', $productIds)
                ->getQuery()
                ->getResult();
    }

    private function getSubqueryResults(string $subquery)
    {
        return $this->getEntityManager()->getConnection()->executeQuery($subquery)->fetchAllAssociative();
    }

    //    /**
    //     * @return Product[] Returns an array of Product objects
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

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
