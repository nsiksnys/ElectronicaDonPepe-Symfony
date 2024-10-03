<?php

namespace App\Repository;

use App\Entity\Sale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sale>
 */
class SaleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sale::class);
    }

    /**
     * @param \App\Entity\Salesman $salesman a Salesman object
     * @param string $from the beginning of the search window
     * @param string $to the end of the search window
     * @return Sale[] Returns an array of Sale objects
    */
    public function findBySalesmanAndDates(\App\Entity\Salesman $salesman, string $from, string $to) : array
    {
        return $this->createQueryBuilder('s')
                    ->where($this->getEntityManager()->createQueryBuilder()->expr()->between('s.salesDate', ':from', ':to'))
                    ->andWhere('s.salesman = :salesman')
                    ->setParameter('salesman', $salesman)
                    ->setParameter('from', $from)
                    ->setParameter('to', $to)
                    ->getQuery()
                    ->getResult();
    }

    public function countByDates(string $from, string $to) : int
    {
        return $this->createQueryBuilder('s')
                    ->select('COUNT(s.id)')
                    ->where($this->getEntityManager()->createQueryBuilder()->expr()->between('s.salesDate', ':from', ':to'))
                    ->setParameter('from', $from)
                    ->setParameter('to', $to)
                    ->getQuery()
                    ->getSingleScalarResult();
    }

    public function findByDates(string $from = "", string $to = "") : array
    {
        $expr = $this->getEntityManager()->createQueryBuilder()->expr();
        $queryBuilder = $this->createQueryBuilder('s');

        if ($from != "" && $to == ""){
            $queryBuilder->where($expr->gte('s.salesDate', ':from'))
                         ->setParameter('from', $from);
        }
        elseif ($from == "" && $to != "") {
            $queryBuilder->where($expr->lte('s.salesDate', ':to'))
                         ->setParameter('to', $to);
        }
        else {
            $queryBuilder->where($this->getEntityManager()->createQueryBuilder()->expr()->between('s.salesDate', ':from', ':to'))
                         ->setParameter('from', $from)
                         ->setParameter('to', $to);
        }
        
        return $queryBuilder->getQuery()
                            ->getResult();
    }

    //    /**
    //     * @return Sale[] Returns an array of Sale objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Sale
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
