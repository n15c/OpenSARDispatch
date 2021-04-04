<?php

namespace App\Repository;

use App\Entity\RescueOP;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RescueOP|null find($id, $lockMode = null, $lockVersion = null)
 * @method RescueOP|null findOneBy(array $criteria, array $orderBy = null)
 * @method RescueOP[]    findAll()
 * @method RescueOP[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RescueOPRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RescueOP::class);
    }

    // /**
    //  * @return RescueOP[] Returns an array of RescueOP objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RescueOP
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
