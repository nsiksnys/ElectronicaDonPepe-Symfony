<?php

namespace App\Repository;

use App\Entity\Award;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Award>
 */
class AwardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Award::class);
    }

    /**
     * @param string $from the beginning of the search limit
     * @param string $to the end of the search limit
     * @param array $salespeople an array of Salesman objects
     * @return Award[] Returns an array of Award objects
    */
    public function findBestSalesmanByDates(string $from, string $to, array $salespeople) : array
    {
        $salespeople = array_map(function(\App\Entity\Salesman $salesman) { return $salesman->getId(); }, $salespeople);
        return $this->createQueryBuilder('a')
                    ->where(['a.dateFrom = :from', 'a.dateTo = :to', $this->getEntityManager()->createQueryBuilder()->expr()->in('a.awardedTo', $salespeople), 'a.isCampaign = :campaign'])
                    ->setParameter('from', $from)
                    ->setParameter('to', $to)
                    ->setParameter('campaign', false)
                    ->getQuery()
                    ->getResult();
    }

    /**
     * @param string $from the beginning of the search limit
     * @param string $to the end of the search limit
     * @param array $salespeople an array of Salesman objects
     * @param \App\Entity\Product $product a specific product
     * @return Award[] Returns an array of Award objects
    */
    public function findCampaignAwardByDatesAndProduct(string $from, string $to, array $salespeople, \App\Entity\Product $product) : array
    {
        $salespeople = array_map(function(\App\Entity\Salesman $salesman) { return $salesman->getId(); }, $salespeople);
        return $this->createQueryBuilder('a')
                    ->where(['a.dateFrom = :from', 'a.dateTo = :to', $this->getEntityManager()->createQueryBuilder()->expr()->in('a.awardedTo', $salespeople), 'a.product = :product', 'a.isCampaign = :campaign'])
                    ->setParameter('from', $from)
                    ->setParameter('to', $to)
                    ->setParameter('product', $product)
                    ->setParameter('campaign', true)
                    ->getQuery()
                    ->getResult();
    }

    //    /**
    //     * @return Award[] Returns an array of Award objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Award
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
