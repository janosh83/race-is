<?php

namespace App\Repository;

use App\Entity\Visit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    /*public function findByPeak($peakid, $raceid)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager
            ->createQuery('SELECT t.title as team_title, v.time, v.note FROM App\Entity\Visit v LEFT JOIN v.team t WHERE v.race = :raceid AND v.peak = :peakid');
         
        $query->setParameter('peakid', $peakid);
        $query->setParameter('raceid', $raceid);

        return $query->getResult();
            
    }*/

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

    public function findByPeakAndRace($peakid, $raceid)
    {
        
        $entityManager = $this->getEntityManager();
        $query = $entityManager
            ->createQuery('SELECT t.title as team_title, v.time, v.note FROM App\Entity\Visit v LEFT JOIN v.team t WHERE v.race = :raceid AND v.peak = :peakid');
         
        $query->setParameter('peakid', $peakid);
        $query->setParameter('raceid', $raceid);

        return $query->getResult();
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

    public function findTeamsWithoutVisit($raceid)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager
            ->createQuery('SELECT t.id, t.title FROM App\Entity\Team t JOIN t.registration reg WHERE 
                            reg.race = :raceid AND
                            t.id NOT IN (SELECT tt.id FROM App\Entity\Visit v JOIN v.team tt WHERE v.race = :raceid)');
        $query->setParameter('raceid', $raceid);

        return $query->getResult();
    }

    public function countVisits($raceid)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager
            ->createQuery('SELECT COUNT(v.id) FROM App\Entity\Visit v WHERE v.race = :raceid');
        $query->setParameter('raceid', $raceid);

        return $query->getSingleScalarResult();
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
