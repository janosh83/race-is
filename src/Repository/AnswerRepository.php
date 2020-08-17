<?php

namespace App\Repository;

use App\Entity\Answer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Answer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Answer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Answer[]    findAll()
 * @method Answer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    public function findByTaskAndTeam($taskid, $teamid)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.task = :taskid')
            ->andWhere('a.team = :teamid')
            ->setParameter('taskid', $taskid)
            ->setParameter('teamid', $teamid)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByRaceAndTeam($raceid, $teamid)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager
            ->createQuery('SELECT t.id, t.title, t.pointsPerAnswer, a.time, a.note FROM App\Entity\Answer a LEFT JOIN a.task t WHERE t.race = :raceid AND a.team = :teamid');
        $query->setParameter('raceid', $raceid);
        $query->setParameter('teamid', $teamid);

        return $query->getResult();
    }

    // /**
    //  * @return Answer[] Returns an array of Answer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Answer
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
