<?php

namespace App\Tests\Repository;

use App\Entity\Race;
use App\Entity\Team;
use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testfindAnsweredByTeamAndRace()
    {
        $races = $this->entityManager
            ->getRepository(Race::class)
            ->findAll();

        $teams = $this->entityManager
            ->getRepository(Team::class)
            ->findAll();

        $task = $this->entityManager->getRepository(Task::class)->findAnsweredByTeamAndRace($teams[0]->getId(), $races[0]->getId());
        $this->assertEmpty($task); // HBR 2020, Team 1
        //dd($task);

        $task = $this->entityManager->getRepository(Task::class)->findAnsweredByTeamAndRace($teams[2]->getId(), $races[2]->getId());
        $this->assertNotEmpty($task); // Palavska Streka, Team A
        $this->assertCount(2, $task);
        

        $task = $this->entityManager->getRepository(Task::class)->findAnsweredByTeamAndRace($teams[3]->getId(), $races[2]->getId());
        $this->assertNotEmpty($task); // Palavska Streka, Team B
        $this->assertCount(2, $task);

    }

    //findNotAnsweredByTeam($teamid, $raceid)
    public function testfindNotAnsweredByTeam()
    {
        $races = $this->entityManager
            ->getRepository(Race::class)
            ->findAll();

        $teams = $this->entityManager
            ->getRepository(Team::class)
            ->findAll();

        $task = $this->entityManager->getRepository(Task::class)->findNotAnsweredByTeam($teams[0]->getId(), $races[0]->getId());
        $this->assertEmpty($task);  // HBR 2020, Team 1

        $task = $this->entityManager->getRepository(Task::class)->findNotAnsweredByTeam($teams[2]->getId(), $races[2]->getId());
        $this->assertNotEmpty($task); // Palavska Streka, Team A
        $this->assertCount(2, $task);

        $task = $this->entityManager->getRepository(Task::class)->findNotAnsweredByTeam($teams[3]->getId(), $races[2]->getId());
        $this->assertNotEmpty($task); // Palavska Streka, Team B
        $this->assertCount(2, $task);
        
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}