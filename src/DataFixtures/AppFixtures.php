<?php

namespace App\DataFixtures;

use App\Entity\Peak;
use App\Entity\User;
use App\Entity\Race;
use App\Entity\Team;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Common\Persistence\ObjectManager;
use function Symfony\Component\String\u;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    private function getPhrases(): array
    {
        return [
            'Lorem ipsum dolor sit amet consectetur adipiscing elit',
            'Pellentesque vitae velit ex',
            'Mauris dapibus risus quis suscipit vulputate',
            'Eros diam egestas libero eu vulputate risus',
            'In hac habitasse platea dictumst',
            'Morbi tempus commodo mattis',
            'Ut suscipit posuere justo at vulputate',
            'Ut eleifend mauris et risus ultrices egestas',
            'Aliquam sodales odio id eleifend tristique',
            'Urna nisl sollicitudin id varius orci quam id turpis',
            'Nulla porta lobortis ligula vel egestas',
            'Curabitur aliquam euismod dolor non ornare',
            'Sed varius a risus eget aliquam',
            'Nunc viverra elit ac laoreet suscipit',
            'Pellentesque et sapien pulvinar consectetur',
            'Ubi est barbatus nix',
            'Abnobas sunt hilotaes de placidus vita',
            'Ubi est audax amicitia',
            'Eposs sunt solems de superbus fortis',
            'Vae humani generis',
            'Diatrias tolerare tanquam noster caesium',
            'Teres talis saepe tractare de camerarius flavum sensorem',
            'Silva de secundus galatae demitto quadra',
            'Sunt accentores vitare salvus flavum parses',
            'Potus sensim ad ferox abnoba',
            'Sunt seculaes transferre talis camerarius fluctuies',
            'Era brevis ratione est',
            'Sunt torquises imitari velox mirabilis medicinaes',
            'Mineralis persuadere omnes finises desiderium',
            'Bassus fatalis classiss virtualiter transferre de flavum',
        ];
    }

    private function getRandomText(int $maxLength = 255): string
    {
        $phrases = $this->getPhrases();
        shuffle($phrases);

        do {
            $text = u('. ')->join($phrases)->append('.');
            array_pop($phrases);
        } while ($text->length() > $maxLength);

        return $text;
    }

    private function getPeakData(): array
    {
        return [
            // $peakData = [$shorrtid, $title, $description, $lat, $lon];
            ['vrchol_01', 'První vrchol',  $this->getRandomText(), mt_rand(-90, 90), mt_rand(-180, 180) ],
            ['vrchol_02', 'Druhý vrchol',  $this->getRandomText(), mt_rand(-90, 90), mt_rand(-180, 180) ],
            ['vrchol_03', 'Třetí vrchol',  $this->getRandomText(), mt_rand(-90, 90), mt_rand(-180, 180) ],
            ['vrchol_04', 'Čtvrtý vrchol', $this->getRandomText(), mt_rand(-90, 90), mt_rand(-180, 180) ],
            ['vrchol_05', 'Pátý vrchol',   $this->getRandomText(), mt_rand(-90, 90), mt_rand(-180, 180) ],
            ['vrchol_06', 'Šestý vrchol',  $this->getRandomText(), mt_rand(-90, 90), mt_rand(-180, 180) ]
        ];
    }

    private function loadPeaks(ObjectManager $manager)
    {
        foreach ($this->getPeakData() as [$shortid, $title, $description, $lat, $lon]) {
            $peak = new Peak();
            $peak->setShortId($shortid);
            $peak->setTitle($title);
            $peak->setDescription($description);
            $peak->setLatitude($lat);
            $peak->setLongitude($lon);

            $manager->persist($peak);
            $this->addReference($shortid, $peak);
        }
    }

    private function getUserData(): array
    {
        return [
            // $userData = [$name, $email, $password, $roles];
            ['Jane Doe', 'jane_admin@symfony.com', 'kitten', ['ROLE_ADMIN']],
            ['Tom Doe', 'tom_user@symfony.com', 'kitten', ['ROLE_USER']],
            ['John Doe', 'john_user@symfony.com', 'kitten', ['ROLE_USER']],
            ['Jack Doe', 'jack_user@symfony.com', 'kitten', ['ROLE_USER']],
            ['Joe Doe', 'joe_user@symfony.com', 'kitten', ['ROLE_USER']],
            ['Annie Doe', 'annie_user@symfony.com', 'kitten', ['ROLE_USER']],
            ['Fred Doe', 'fred_user@symfony.com', 'kitten', ['ROLE_USER']]
        ];
    }

    private function loadUsers(ObjectManager $manager): void
    {
        foreach ($this->getUserData() as [$name, $email, $password, $roles]) {
            $user = new User();
            $user->setName($name);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
            $user->setEmail($email);
            $user->setRoles($roles);

            $manager->persist($user);
            $this->addReference($email, $user);
        }

        $manager->flush();
    }

    private function getTeamData(): array
    {
        return [
            // $teamData = [$title, $leader, $members]
            ['Team 1', 'tom_user@symfony.com', ['john_user@symfony.com', 'jack_user@symfony.com']],
            ['Team 2', 'joe_user@symfony.com', ['annie_user@symfony.com', 'fred_user@symfony.com']]
        ];
    }

    private function loadTeams(ObjectManager $manager): void
    {
        foreach ($this->getTeamData() as [$title, $leader, $members]) {
            $team = new Team();
            $team->setTitle($title);
            $team->setLeader($this->getReference($leader));
            foreach($members as $member){
                $team->addMember($this->getReference($member));
            }
            $manager->persist($team);
            $this->addReference($title, $team);
        }

        $manager->flush();
    }

    private function getRaceData(): array
    {
        return [
            // $raceData = [$title, $description, $teams, $peaks]
            [   'Hill Bill Rally 2020', 
                'Objevitelský závod, který nejde prohrát.', 
                ['Team 1', 'Team 2'],
                ['vrchol_01','vrchol_03','vrchol_05']
            ],
            [   'Pálavská štreka', 
                'Pálavská štreka je nevšední amatérský cyklopiknikovací závod.', 
                [],
                ['vrchol_02','vrchol_04']
            ]
        ];
    }

    private function loadRaces(ObjectManager $manager): void
    {
        foreach ($this->getRaceData() as [$title, $description, $teams, $peaks]) {
            $race = new Race();
            $race->setTitle($title);
            $race->setDescription($description);

            foreach($teams as $teamTitle)
            {
                $race->addSigned($this->getReference($teamTitle));
            }

            foreach($peaks as $peakId)
            {
                $race->addPeak($this->getReference($peakId));
            }

            $manager->persist($race);
            
        }

        $manager->flush();
    }

    /* load fictures by command:
         php bin/console doctrine:fixtures:load 
    */
    public function load(ObjectManager $manager)
    {
        $this->loadPeaks($manager);
        $this->loadUsers($manager);
        $this->loadTeams($manager);
        $this->loadRaces($manager);
        $manager->flush();
    }
}
