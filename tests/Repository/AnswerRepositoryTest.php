<?php

namespace App\Tests\Repository;

use App\Entity\Answer;
use App\Entity\Race;
use App\Entity\Team;
use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AnswerRepositoryTest extends KernelTestCase
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

    // findByTaskAndTeam($taskid, $teamid)
    public function testfindByTaskAndTeam()
    {
        $tasks = $this->entityManager
            ->getRepository(Task::class)
            ->findAll();

        $teams = $this->entityManager
            ->getRepository(Team::class)
            ->findAll();

        $answer = $this->entityManager->getRepository(Answer::class)->findByTaskAndTeam($tasks[0]->getId(), $teams[2]->getId());
        $this->assertNotEmpty($answer); // Schody na rozhlednu, Team A

        $answer = $this->entityManager->getRepository(Answer::class)->findByTaskAndTeam($tasks[0]->getId(), $teams[3]->getId());
        $this->assertNotEmpty($answer); // Schody na rozhlednu, Team B

        $answer = $this->entityManager->getRepository(Answer::class)->findByTaskAndTeam($tasks[0]->getId(), $teams[0]->getId());
        $this->assertEmpty($answer); // Schody na rozhlednu, Team 1

        $answer = $this->entityManager->getRepository(Answer::class)->findByTaskAndTeam($tasks[1]->getId(), $teams[2]->getId());
        $this->assertNotEmpty($answer); // Večeře v restauraci, Team A

        $answer = $this->entityManager->getRepository(Answer::class)->findByTaskAndTeam($tasks[1]->getId(), $teams[3]->getId());
        $this->assertEmpty($answer); // Večeře v restauraci, Team B
    }


    // findByTaskAndRace($taskid, $raceid)
    public function testfindByTaskAndRace()
    {
        $tasks = $this->entityManager
            ->getRepository(Task::class)
            ->findAll();

        $races = $this->entityManager
            ->getRepository(Race::class)
            ->findAll();

        $answer = $this->entityManager->getRepository(Answer::class)->findByTaskAndRace($tasks[0]->getId(), $races[2]->getId());
        $this->assertNotEmpty($answer); // Schody na rozhlednu, Palavska streka
        $this->assertCount(2, $answer);

        $answer = $this->entityManager->getRepository(Answer::class)->findByTaskAndRace($tasks[1]->getId(), $races[2]->getId());
        $this->assertNotEmpty($answer); // Večeře v restauraci, Palavska streka
        $this->assertCount(1, $answer);

        $answer = $this->entityManager->getRepository(Answer::class)->findByTaskAndRace($tasks[2]->getId(), $races[2]->getId());
        $this->assertNotEmpty($answer); // Koupacka pod vodopadem, Palavska streka
        $this->assertCount(1, $answer);

        $answer = $this->entityManager->getRepository(Answer::class)->findByTaskAndRace($tasks[3]->getId(), $races[2]->getId());
        $this->assertEmpty($answer); // Koupačka v jezirku, Palavska streka

        $answer = $this->entityManager->getRepository(Answer::class)->findByTaskAndRace($tasks[1]->getId(), $races[1]->getId());
        $this->assertEmpty($answer); // Večeře v restauraci, Jarni Jizda

        $answer = $this->entityManager->getRepository(Answer::class)->findByTaskAndRace($tasks[1]->getId(), $races[3]->getId());
        $this->assertEmpty($answer); // Večeře v restauraci, HBR 2022
    }

    public function testfindByRaceAndTeam()
    {
        $races = $this->entityManager
            ->getRepository(Race::class)
            ->findAll();

        $teams = $this->entityManager
            ->getRepository(Team::class)
            ->findAll();

        $answer = $this->entityManager->getRepository(Answer::class)->findByRaceAndTeam($races[2]->getId(), $teams[2]->getId());
        $this->assertNotEmpty($answer); // Palavska streka, Team A
        $this->assertCount(2, $answer);

        $answer = $this->entityManager->getRepository(Answer::class)->findByRaceAndTeam($races[2]->getId(), $teams[3]->getId());
        $this->assertNotEmpty($answer); // Palavska streka, Team B
        $this->assertCount(2, $answer);

        $answer = $this->entityManager->getRepository(Answer::class)->findByRaceAndTeam($races[2]->getId(), $teams[4]->getId());
        $this->assertEmpty($answer); // Palavska streka, Team C

        $post = $this->entityManager->getRepository(Answer::class)->findByRaceAndTeam($races[1]->getId(), $teams[1]->getId());
        $this->assertEmpty($post); // Jarni jizda, Team 2
        
    }

    
    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}