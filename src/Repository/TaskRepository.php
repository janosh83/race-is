<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findAnsweredByTeamAndRace($teamid, $raceid)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager
            ->createQuery('SELECT t.id, t.title FROM App\Entity\Task t LEFT JOIN t.answers ta WHERE ta.team = :teamid AND ta.race = :raceid');
        $query->setParameter('teamid', $teamid);
        $query->setParameter('raceid', $raceid);

        return $query->getResult();
    }

    public function findNotAnsweredByTeam($teamid, $raceid)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager
            ->createQuery('SELECT t.id, t.title FROM App\Entity\Task t WHERE 
                            t.id NOT IN (SELECT tt.id FROM App\Entity\Task tt LEFT JOIN tt.answers tta WHERE 
                                            tta.team = :teamid AND tta.race = :raceid ) AND
                            t.race = :raceid');
        $query->setParameter('teamid', $teamid);
        $query->setParameter('raceid', $raceid);      

        return $query->getResult();

    }

    // /**
    //  * @return Task[] Returns an array of Task objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Task
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
