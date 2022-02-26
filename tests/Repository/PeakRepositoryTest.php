<?php

namespace App\Tests\Repository;

use App\Entity\Peak;
use App\Entity\Race;
use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PeakRepositoryTest extends KernelTestCase
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

    public function testFindByRace()
    {
        $races = $this->entityManager
            ->getRepository(Race::class)
            ->findAll();

        $this->assertNotEmpty($races);

        $peaks = $this->entityManager->getRepository(Peak::class)->findByRace($races[0]->getId()); // HBR
        $this->assertSame(5, count($peaks));

        $peaks = $this->entityManager->getRepository(Peak::class)->findByRace($races[1]->getId()); // Jizda
        $this->assertSame(10, count($peaks));
    }

    public function testFindVisitedByTeamAndRace()
    {
        $races = $this->entityManager
            ->getRepository(Race::class)
            ->findAll();

        $teams = $this->entityManager
            ->getRepository(Team::class)
            ->findAll();

        $this->assertNotEmpty($races);
        $this->assertNotEmpty($teams);

        $peaks = $this->entityManager->getRepository(Peak::class)->findVisitedByTeamAndRace($teams[0]->getId(), $races[0]->getId()); // Team 1, HBR
        $this->assertSame(3, count($peaks));

        $peaks = $this->entityManager->getRepository(Peak::class)->findVisitedByTeamAndRace($teams[1]->getId(), $races[0]->getId()); // Team 2, HBR
        $this->assertSame(2, count($peaks));
        
        $peaks = $this->entityManager->getRepository(Peak::class)->findVisitedByTeamAndRace($teams[2]->getId(), $races[0]->getId()); // Team A, HBR
        $this->assertSame(0, count($peaks));

        $peaks = $this->entityManager->getRepository(Peak::class)->findVisitedByTeamAndRace($teams[0]->getId(), $races[1]->getId()); // Team 2, Jizda
        $this->assertSame(0, count($peaks));
    }

    public function testFindNotVisitedByTeam()
    {
        $races = $this->entityManager
            ->getRepository(Race::class)
            ->findAll();

        $teams = $this->entityManager
            ->getRepository(Team::class)
            ->findAll();

        $this->assertNotEmpty($races);
        $this->assertNotEmpty($teams);

        $peaks = $this->entityManager->getRepository(Peak::class)->findNotVisitedByTeam($teams[0]->getId(), $races[0]->getId()); // Team 1, HBR
        $this->assertSame(2, count($peaks));

        $peaks = $this->entityManager->getRepository(Peak::class)->findNotVisitedByTeam($teams[1]->getId(), $races[0]->getId()); // Team 2, HBR
        $this->assertSame(3, count($peaks));

        $peaks = $this->entityManager->getRepository(Peak::class)->findNotVisitedByTeam($teams[2]->getId(), $races[1]->getId()); // Team A, Jizda
        $this->assertSame(10, count($peaks));
    }

    public function testCountPeaks()
    {
        $races = $this->entityManager
            ->getRepository(Race::class)
            ->findAll();

        $this->assertNotEmpty($races);
        $peaks = $this->entityManager->getRepository(Peak::class)->countPeaks($races[0]->getId()); // HBR
        $this->assertSame(5, intval($peaks));

        $peaks = $this->entityManager->getRepository(Peak::class)->countPeaks($races[1]->getId()); // Jizda
        $this->assertSame(10, intval($peaks));
        
        $peaks = $this->entityManager->getRepository(Peak::class)->countPeaks($races[3]->getId()); // HBR 2022
        $this->assertSame(0, intval($peaks));
    }

    public function testCountPeaksWithVisit()
    {
        $races = $this->entityManager
            ->getRepository(Race::class)
            ->findAll();

        $this->assertNotEmpty($races);
        $peaks = $this->entityManager->getRepository(Peak::class)->countPeaksWithVisit($races[0]->getId()); // HBR

        $this->assertSame(4, count($peaks));
        // commented out for now

        $peaks = $this->entityManager->getRepository(Peak::class)->countPeaksWithVisit($races[1]->getId()); // Jizda
        $this->assertSame(0, count($peaks));
        
        // TODO some race with zero peaks
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}