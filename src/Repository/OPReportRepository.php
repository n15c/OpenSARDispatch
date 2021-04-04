<?php

namespace App\Repository;

use App\Entity\OPReport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OPReport|null find($id, $lockMode = null, $lockVersion = null)
 * @method OPReport|null findOneBy(array $criteria, array $orderBy = null)
 * @method OPReport[]    findAll()
 * @method OPReport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OPReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OPReport::class);
    }

    // /**
    //  * @return OPReport[] Returns an array of OPReport objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OPReport
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
