<?php

namespace App\Repository;

use App\Entity\Peak;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Peak|null find($id, $lockMode = null, $lockVersion = null)
 * @method Peak|null findOneBy(array $criteria, array $orderBy = null)
 * @method Peak[]    findAll()
 * @method Peak[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PeakRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Peak::class);
    }

    // /**
    //  * @return Peak[] Returns an array of Peak objects
    //  */
    
    public function findByRace($race)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.race = :race')
            ->setParameter('race', $race)
            ->getQuery()
            ->getResult()
        ;
    }
    

    /*
    public function findOneBySomeField($value): ?Peak
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
