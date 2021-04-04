<?php

namespace App\Repository;

use App\Entity\Standby;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Standby|null find($id, $lockMode = null, $lockVersion = null)
 * @method Standby|null findOneBy(array $criteria, array $orderBy = null)
 * @method Standby[]    findAll()
 * @method Standby[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StandbyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Standby::class);
    }

    // /**
    //  * @return Standby[] Returns an array of Standby objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Standby
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

   /**
    * @return Standby[] Returns an array of Standby objects
    */
   public function findAllPlannedStandbies(\DateTimeInterface $opdate)
   {
       return $this->createQueryBuilder('s')
           // ->andWhere('s.dateFrom <= :opdate')
           // ->andWhere('s.dateTo >= :opdate')
           ->andWhere(':opdate BETWEEN s.dateFrom AND s.dateTo')
           ->andWhere('s.status = 1')
           ->setParameter('opdate', $opdate->format('Y-m-d'))
           ->orderBy('s.id', 'ASC')
           ->setMaxResults(10)
           ->getQuery()
           ->getResult()
       ;
   }
}
