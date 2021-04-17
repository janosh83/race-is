<?php

namespace App\Repository;

use App\Entity\Race;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Race|null find($id, $lockMode = null, $lockVersion = null)
 * @method Race|null findOneBy(array $criteria, array $orderBy = null)
 * @method Race[]    findAll()
 * @method Race[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Race::class);
    }

    public function findByIdAndTeam($raceid, $teamid)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager
            ->createQuery('SELECT r.id, r.title, r.description, r.logoPath FROM App\Entity\Race r LEFT JOIN r.signed rs WHERE r.id = :raceid AND rs.id = :teamid');
        $query->setParameter('teamid', $teamid);
        $query->setParameter('raceid', $raceid);

        return $query->getOneOrNullResult();
    }

    public function findAllWhereLeader($userid)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager
            ->createQuery('SELECT r.id, r.title, r.description, r.logoPath FROM App\Entity\Race r LEFT JOIN r.signed rs LEFT JOIN rs.leader sl WHERE sl.id = :userid');
        $query->setParameter('userid', $userid);
        
        return $query->getResult();
    }

    public function findAllWhereMember($userid)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager
            ->createQuery('SELECT r.id, r.title, r.description, r.logoPath FROM App\Entity\Race r LEFT JOIN r.signed rs LEFT JOIN rs.member sl WHERE sl.id = :userid');
        $query->setParameter('userid', $userid);
        
        return $query->getResult();
    }

    // /**
    //  * @return Race[] Returns an array of Race objects
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
    public function findOneBySomeField($value): ?Race
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
