<?php

namespace App\Repository;

use App\Entity\Peak;
use App\Entity\Visit;
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

    public function findVisitedByTeamAndRace($teamid, $raceid)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager
            ->createQuery('SELECT p.id, p.title FROM App\Entity\Peak p LEFT JOIN p.visits pv WHERE pv.team = :teamid AND pv.race = :raceid');
        $query->setParameter('teamid', $teamid);
        $query->setParameter('raceid', $raceid);

        return $query->getResult();
    }

    public function findNotVisitedByTeam($raceid)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager
            ->createQuery('SELECT p.id, p.title FROM App\Entity\Peak p LEFT JOIN p.visits pv WHERE pv IS NULL AND p.race = :raceid');
        $query->setParameter('raceid', $raceid);      

        return $query->getResult();

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
