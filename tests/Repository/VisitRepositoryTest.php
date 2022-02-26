<?php

namespace App\Tests\Repository;

use App\Entity\Peak;
use App\Entity\Race;
use App\Entity\Team;
use App\Entity\Visit;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class VisitRepositoryTest extends KernelTestCase
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

    public function testfindByPeakAndRace()
    {
        $races = $this->entityManager
            ->getRepository(Race::class)
            ->findAll();

        $peaks = $this->entityManager
            ->getRepository(Peak::class)
            ->findAll();

        $visit = $this->entityManager->getRepository(Visit::class)->findByPeakAndRace($peaks[0]->getId(), $races[0]->getId());
        $this->assertNotEmpty($visit); // vrchol_01, HBR 2020
        $this->assertCount(1, $visit);

        $visit = $this->entityManager->getRepository(Visit::class)->findByPeakAndRace($peaks[0]->getId(), $races[1]->getId());
        $this->assertEmpty($visit); // vrchol_01, Jizda
    }

    public function testfindByRaceAndTeam()
    {
        $races = $this->entityManager
            ->getRepository(Race::class)
            ->findAll();

        $teams = $this->entityManager
            ->getRepository(Team::class)
            ->findAll();

        $visit = $this->entityManager->getRepository(Visit::class)->findByRaceAndTeam($races[0]->getId(), $teams[0]->getId());
        $this->assertNotEmpty($visit); // HBR 2020, Team 1
        $this->assertCount(3, $visit);

        $visit = $this->entityManager->getRepository(Visit::class)->findByRaceAndTeam($races[0]->getId(), $teams[1]->getId());
        $this->assertNotEmpty($visit); // HBR 2020, Team 2
        $this->assertCount(2, $visit);

        $visit = $this->entityManager->getRepository(Visit::class)->findByRaceAndTeam($races[1]->getId(), $teams[0]->getId());
        $this->assertEmpty($visit); // Jizda, Team 1

        $visit = $this->entityManager->getRepository(Visit::class)->findByRaceAndTeam($races[1]->getId(), $teams[1]->getId());
        $this->assertEmpty($visit); // Jizda, Team 2

        $visit = $this->entityManager->getRepository(Visit::class)->findByRaceAndTeam($races[1]->getId(), $teams[2]->getId());
        $this->assertEmpty($visit); // Jizda, Team 1
    }

    public function testFindTeamsWithoutVisit()
    {
        $races = $this->entityManager
            ->getRepository(Race::class)
            ->findAll();

        $visit = $this->entityManager->getRepository(Visit::class)->findTeamsWithoutVisit($races[0]->getId());
        $this->assertEmpty($visit); // HBR 2020

        $visit = $this->entityManager->getRepository(Visit::class)->findTeamsWithoutVisit($races[1]->getId());
        $this->assertNotEmpty($visit); // Jizda
        $this->assertCount(2, $visit);

        // TODO add more tests

    }

    public function testCountVisits()
    {
        $races = $this->entityManager
            ->getRepository(Race::class)
            ->findAll();

        $visit = $this->entityManager->getRepository(Visit::class)->countVisits($races[0]->getId());
        $this->assertEquals(5, $visit); // HBR 2020

        $visit = $this->entityManager->getRepository(Visit::class)->countVisits($races[1]->getId());
        $this->assertEquals(0, $visit); // Jizda

        // TODO add more tests

    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }

}