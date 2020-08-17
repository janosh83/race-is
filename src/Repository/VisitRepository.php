<?php

namespace App\Repository;

use App\Entity\Visit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Visit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Visit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Visit[]    findAll()
 * @method Visit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Visit::class);
    }

    public function findByPeakAndTeam($peakid, $teamid)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.peak = :peakid')
            ->andWhere('v.team = :teamid')
            ->setParameter('peakid', $peakid)
            ->setParameter('teamid', $teamid)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByRaceAndTeam($raceid, $teamid)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager
            ->createQuery('SELECT p.id, p.title,p.pointsPerVisit, v.time, v.note FROM App\Entity\Visit v LEFT JOIN v.peak p WHERE v.race = :raceid AND v.team = :teamid');
        $query->setParameter('raceid', $raceid);
        $query->setParameter('teamid', $teamid);

        return $query->getResult();
    }

    public function findByRace($raceid)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.race = :raceid')
            ->setParameter('raceid', $raceid)
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Visit[] Returns an array of Visit objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Visit
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
