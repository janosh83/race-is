<?php

namespace App\Tests\Repository;

use App\Entity\Peak;
use App\Entity\Race;
use App\Entity\Team;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RaceRepositoryTest extends KernelTestCase
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

    public function testFindByIdAndTeam()
    {
        $races = $this->entityManager
            ->getRepository(Race::class)
            ->findAll();

        $teams = $this->entityManager
            ->getRepository(Team::class)
            ->findAll();

        $race = $this->entityManager->getRepository(Race::class)->findByIdAndTeam($races[0]->getId(), $teams[0]->getId());
        $this->assertNotEmpty($race); // HBR 2020, Team 1
        $race = $this->entityManager->getRepository(Race::class)->findByIdAndTeam($races[0]->getId(), $teams[1]->getId());
        $this->assertNotEmpty($race); // HBR 2020, Team 2
        $race = $this->entityManager->getRepository(Race::class)->findByIdAndTeam($races[0]->getId(), $teams[2]->getId());
        $this->assertEmpty($race); // HBR 2020, Team A

        $race = $this->entityManager->getRepository(Race::class)->findByIdAndTeam($races[1]->getId(), $teams[0]->getId());
        $this->assertNotEmpty($race); // Jizda, Team 1

        $race = $this->entityManager->getRepository(Race::class)->findByIdAndTeam($races[2]->getId(), $teams[0]->getId());
        $this->assertEmpty($race); // Streka, Team 1
    }

    public function testFindAllWhereMember()
    {
        $users = $this->entityManager
            ->getRepository(User::class)
            ->findAll();

        $races = $this->entityManager->getRepository(Race::class)->findAllWhereMember($users[0]->getId());
        $this->assertEmpty($races); // jane_admin

        $races = $this->entityManager->getRepository(Race::class)->findAllWhereMember($users[1]->getId());
        $this->assertEmpty($races); // tom_user

        $races = $this->entityManager->getRepository(Race::class)->findAllWhereMember($users[2]->getId());
        $this->assertNotEmpty($races); // john_user
        $this->assertCount(2, $races);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }


}