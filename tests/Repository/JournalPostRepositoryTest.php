<?php

namespace App\Tests\Repository;

use App\Entity\JournalPost;
use App\Entity\Race;
use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class JournalPostRepositoryTest extends KernelTestCase
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

    public function testfindByRaceAndTeam()
    {
        $races = $this->entityManager
            ->getRepository(Race::class)
            ->findAll();

        $teams = $this->entityManager
            ->getRepository(Team::class)
            ->findAll();

        $post = $this->entityManager->getRepository(JournalPost::class)->findByRaceAndTeam($races[0]->getId(), $teams[0]->getId());
        $this->assertNotEmpty($post); // HBR 2020, Team 1
        $this->assertCount(2, $post);

        $post = $this->entityManager->getRepository(JournalPost::class)->findByRaceAndTeam($races[0]->getId(), $teams[1]->getId());
        $this->assertNotEmpty($post); // HBR 2020, Team 2
        $this->assertCount(1, $post);

        $post = $this->entityManager->getRepository(JournalPost::class)->findByRaceAndTeam($races[1]->getId(), $teams[0]->getId());
        $this->assertEmpty($post); // Jizda, Team 1

        $post = $this->entityManager->getRepository(JournalPost::class)->findByRaceAndTeam($races[2]->getId(), $teams[2]->getId());
        $this->assertEmpty($post); // Palavska Streka, Team A
        
    }

    
    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}