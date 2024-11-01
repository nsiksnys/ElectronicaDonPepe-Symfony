<?php

namespace App\Repository;

use App\Entity\Bonus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bonus>
 */
class BonusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bonus::class);
    }

    /**
     * @param string $from the beginning of the search window
     * @param string $to the end of the search window
     * @param array $salespeople an array of Salesman ids
     * @return Bonus[] an array of Bonus objects
     */
    public function findByDatesAndSalespeople(string $from, string $to, array $salespeople) : array
    {
        return $this->createQueryBuilder('b')
                    ->where(['b.dateFrom = :from', 'b.dateTo = :to'])
                    ->andWhere('IDENTITY(b.salesman) IN (:salespeople)')
                    ->setParameter('from', $from)
                    ->setParameter('to', $to)
                    ->setParameter('salespeople', $salespeople)
                    ->getQuery()
                    ->getResult();
    }

    //    /**
    //     * @return Bonus[] Returns an array of Bonus objects
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

    //    public function findOneBySomeField($value): ?Bonus
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
