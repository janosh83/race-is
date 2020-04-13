<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Team;
use App\Entity\Peak;

class TeamFixtures extends Fixture
{
    private $passwordEncoder;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $team = new Team();
        $team->setTitle("První tým naší skvělé hry");
        $team->setUsername("prvni");
        $team->setPassword($this->passwordEncoder->encodePassword($team, "123"));
        $manager->persist($team);

        $team = new Team();
        $team->setTitle("Druhý tým naší skvělé hry");
        $team->setUsername("dvojka");
        $team->setPassword($this->passwordEncoder->encodePassword($team, "123"));
        $manager->persist($team);

        $team = new Team();
        $team->setTitle("Třetí tým naší skvělé hry");
        $team->setUsername("trojka");
        $team->setPassword($this->passwordEncoder->encodePassword($team, "123"));
        $manager->persist($team);

        $team = new Team();
        $team->setTitle("Čtvrtý tým naší skvělé hry");
        $team->setUsername("ctverka");
        $team->setPassword($this->passwordEncoder->encodePassword($team, "123"));
        $manager->persist($team);

        $manager->flush();
    }
}
