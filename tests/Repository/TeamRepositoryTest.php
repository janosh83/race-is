<?php

namespace App\Tests\Repository;

use App\Entity\Race;
use App\Entity\User;
use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TeamRepositoryTest extends KernelTestCase
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

    public function testfindMemberByUserAndRace()
    {
        $races = $this->entityManager
            ->getRepository(Race::class)
            ->findAll();

        $users = $this->entityManager
            ->getRepository(User::class)
            ->findAll();

        $team = $this->entityManager->getRepository(Team::class)->findMemberByUserAndRace($users[2]->getId(), $races[0]->getId());
        $this->assertNotEmpty($team); // HBR 2020, john_user
        $this->assertEquals("Team 1", $team["title"]);

        //dd($users[0]->getId(), $races[2]->getId(),$team);

        $team = $this->entityManager->getRepository(Team::class)->findMemberByUserAndRace($users[0]->getId(), $races[0]->getId());
        $this->assertEmpty($team); // HBR 2020, jane_admin

        // TODO add more tests where user is in more teams
    }

    public function testcountByVisistedPeaks()
    {
        $races = $this->entityManager
            ->getRepository(Race::class)
            ->findAll();

        $team = $this->entityManager->getRepository(Team::class)->countByVisistedPeaks($races[0]->getId());
        $this->assertNotEmpty($team); // HBR 2020
        $this->assertCount(2, $team);

        $team = $this->entityManager->getRepository(Team::class)->countByVisistedPeaks($races[1]->getId());
        $this->assertEmpty($team); // Jizda
        
    }

    public function testcountByAnsweredTasks()
    {
        $races = $this->entityManager
            ->getRepository(Race::class)
            ->findAll();

        $team = $this->entityManager->getRepository(Team::class)->countByAnsweredTasks($races[0]->getId());
        $this->assertEmpty($team); // HBR 2020

        $team = $this->entityManager->getRepository(Team::class)->countByAnsweredTasks($races[1]->getId());
        $this->assertEmpty($team); // Jizda
        
        // TODO: add some answers
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}