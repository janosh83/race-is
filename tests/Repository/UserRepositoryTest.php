<?php

namespace App\Tests\Repository;

use App\Entity\Race;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
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

    public function testFindUserByRace()
    {
        $races = $this->entityManager
            ->getRepository(Race::class)
            ->findAll();

        $users = $this->entityManager->getRepository(User::class)->findUserByRace($races[0]->getId());
        $this->assertNotEmpty($users); // HBR 2020
        $this->assertCount(4, $users);

        $users = $this->entityManager->getRepository(User::class)->findUserByRace($races[1]->getId());
        $this->assertNotEmpty($users); // Jizda
        $this->assertCount(4, $users);

        $users = $this->entityManager->getRepository(User::class)->findUserByRace($races[2]->getId());
        $this->assertNotEmpty($users); // Streka
        $this->assertCount(7, $users);

        $users = $this->entityManager->getRepository(User::class)->findUserByRace($races[3]->getId());
        $this->assertEmpty($users); // HBR 2022

    }


    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }


}