<?php

namespace App\Repository;

use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Team|null find($id, $lockMode = null, $lockVersion = null)
 * @method Team|null findOneBy(array $criteria, array $orderBy = null)
 * @method Team[]    findAll()
 * @method Team[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    public function findMemberByUserAndRace($userid, $raceid)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager
            ->createQuery('SELECT t.id, t.title FROM App\Entity\Team t LEFT JOIN t.members ul LEFT JOIN t.registrations reg WHERE ul.id = :userid AND reg.race = :raceid');
        $query->setParameter('userid', $userid);
        $query->setParameter('raceid', $raceid);

        return $query->getOneOrNullResult();
    }

    public function countByVisistedPeaks($raceid): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT t.id, t.title, cat.title AS race_category,  SUM(p.pointsPerVisit) AS peak_points FROM App\Entity\Registration reg 
                JOIN reg.team t JOIN reg.race rr JOIN t.visits v JOIN v.peak p JOIN reg.category cat
                WHERE reg.race = :raceid AND v.race = :raceid GROUP BY reg.team ORDER BY peak_points DESC' 
        );
        $query->setParameter('raceid', $raceid);

        return $query->getResult();
    }

    public function countByAnsweredTasks($raceid): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT t.id, t.title, cat.title AS race_category, SUM(tsk.pointsPerAnswer) AS task_points FROM App\Entity\Registration reg 
                JOIN reg.team t JOIN reg.race rr JOIN t.answers a JOIN a.task tsk JOIN reg.category cat 
                WHERE reg.race = :raceid AND a.race = :raceid GROUP BY a.team ORDER BY task_points DESC' 
        );
        $query->setParameter('raceid', $raceid);

        return $query->getResult();
    }

    // /**
    //  * @return Team[] Returns an array of Team objects
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
    public function findOneBySomeField($value): ?Team
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
