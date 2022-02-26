<?php

namespace App\Repository;

use App\Entity\Peak;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    public function findByRace($race)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.race = :race')
            ->setParameter('race', $race)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findVisitedByTeamAndRace($teamid, $raceid)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager
            ->createQuery('SELECT p.id, p.short_id, p.title, p.latitude, p.longitude FROM App\Entity\Visit v LEFT JOIN v.peak p WHERE v.team = :teamid AND v.race = :raceid');
        $query->setParameter('teamid', $teamid);
        $query->setParameter('raceid', $raceid);

        return $query->getResult();
    }

    public function findNotVisitedByTeam($teamid, $raceid)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager
            ->createQuery('SELECT p.id, p.short_id, p.title, p.latitude, p.longitude FROM App\Entity\Peak p WHERE 
                            p.id NOT IN (SELECT pp.id FROM App\Entity\Peak pp LEFT JOIN pp.visits ppv WHERE 
                                            ppv.team = :teamid AND ppv.race = :raceid ) AND
                            p.race = :raceid');
        $query->setParameter('teamid', $teamid);
        $query->setParameter('raceid', $raceid);      

        return $query->getResult();

    }

    public function countPeaks($raceid)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager
            ->createQuery('SELECT COUNT(p.id) FROM App\Entity\Peak p WHERE p.race = :raceid');
        $query->setParameter('raceid', $raceid);

        return $query->getSingleScalarResult();
    }

    // FIXME there is a bug when peak is visited by two teams, in taht case is counted twice
    public function countPeaksWithVisit($raceid)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager
            ->createQuery('SELECT v.id FROM App\Entity\Visit v JOIN v.peak p WHERE v.race = :raceid GROUP BY p.id');
        $query->setParameter('raceid', $raceid);

        //return $query->getSingleScalarResult();
        return $query->getResult();
    }

    // /**
    //  * @return Peak[] Returns an array of Peak objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

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
