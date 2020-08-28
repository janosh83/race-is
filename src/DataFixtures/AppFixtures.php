<?php

namespace App\DataFixtures;

use App\Entity\Peak;
use App\Entity\User;
use App\Entity\Race;
use App\Entity\Team;
use App\Entity\Task;
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

    private function getTaskData(): array
    {
        return [
            // $taskData = [$title, $points, $description]
            ['Schody na rozhlednu', 1, 'Vystup na rozhlednu a spočítej kolik schodů tam bylo.'],
            ['Večeře v restauraci', 1, 'Dej si nějakou dobrou večeři někde v kolibě.'],
            ['Koupačka pod vodopádem', 2, 'Vykoupej se pod horským vodopádem']
        ];
    }

    private function loadTasks(ObjectManager $manager)
    {
        foreach ($this->getTaskData() as [$title, $points, $description]) {
            $task = new Task();
            $task->setTitle($title);
            $task->setDescription($description);
            $task->setPointsPerAnswer($points);
            
            $manager->persist($task);
            $this->addReference($title, $task);
        }
    }

    private function getPeakData(): array
    {
        return [
            // $peakData = [$shorrtid, $title, $description, $lat, $lon, $points];
            ['vrchol_01', 'První vrchol',  $this->getRandomText(), mt_rand(-90, 90), mt_rand(-180, 180), 1 ],
            ['vrchol_02', 'Druhý vrchol',  $this->getRandomText(), mt_rand(-90, 90), mt_rand(-180, 180), 2 ],
            ['vrchol_03', 'Třetí vrchol',  $this->getRandomText(), mt_rand(-90, 90), mt_rand(-180, 180), 1 ],
            ['vrchol_04', 'Čtvrtý vrchol', $this->getRandomText(), mt_rand(-90, 90), mt_rand(-180, 180), 2 ],
            ['vrchol_05', 'Pátý vrchol',   $this->getRandomText(), mt_rand(-90, 90), mt_rand(-180, 180), 1 ],
            ['vrchol_06', 'Šestý vrchol',  $this->getRandomText(), mt_rand(-90, 90), mt_rand(-180, 180), 3 ],

            ["short_01", "1_Zámek Vranov nad Dyjí", $this->getRandomText(), 	48.89254, 15.81085, 1],
            ["short_02", "2_Hardeggská vyhlídka", $this->getRandomText(), 	48.85741, 15.861, 1],
            ["short_03", "3_Zámek Jaroměřice nad Rokytnou", $this->getRandomText(), 	49.09362,15.8922, 1],
            ["short_04", "4_Muzeum kol Boskovštejn", $this->getRandomText(), 	48.98371, 15.92602, 1],
            ["short_05", "5_Starý zámek Jevišovice", $this->getRandomText(), 	48.99094, 15.98843, 1],
            ["short_06", "6_Pivovarský Hostinec Heřman", $this->getRandomText(), 49.20995, 15.98931, 1],
            ["short_07", "7_Muzeum motorismu", $this->getRandomText(), 	48.85418, 16.04284, 1],
            ["short_08", "8_Rotunda Nanebevzetí Panny Marie", $this->getRandomText(), 	48.92967, 16.07977, 1],
            ["short_09", "9_Pivovar Dalešice", $this->getRandomText(), 	49.131, 16.08055, 1],
            ["short_10", "10_Wilsonova skála", $this->getRandomText(), 	49.16154, 16.08981, 1],
            ["short_11", "11_Rozhledna Ocmanice", $this->getRandomText(), 	49.22773, 16.11759, 1],
            ["short_12", "12_Jaderná elektrárna Dukovany", $this->getRandomText(), 	49.08508, 16.15009, 1],
            ["short_13", "13_Zámek Náměšť na Oslavou", $this->getRandomText(), 	49.20873, 16.16241, 1],
            ["short_14", "14_Mohelenská hadcová step", $this->getRandomText(), 	49.10775, 16.1876, 1],
            ["short_15", "15_Pivovar Krum", $this->getRandomText(), 	49.03902, 16.31241, 1],
            ["short_16", "16_Zastavení Zvěrokruh", $this->getRandomText(), 	49.07137, 16.34912, 1],
            ["short_17", "17_Kostel sv. Linharta", $this->getRandomText(), 	48.89595, 16.59998, 1],
            ["short_18", "18_Moravské zemské muzeum", $this->getRandomText(), 49.19185, 16.60845, 1],
            ["short_19", "19_Zámek Mikulov", $this->getRandomText(), 48.80667, 16.6365, 1],
            ["short_20", "20_Rozhledna Akátová věž", $this->getRandomText(),	49.04181, 16.63916, 1]
        ];
    }

    private function loadPeaks(ObjectManager $manager)
    {
        foreach ($this->getPeakData() as [$shortid, $title, $description, $lat, $lon, $points]) {
            $peak = new Peak();
            $peak->setShortId($shortid);
            $peak->setTitle($title);
            $peak->setDescription($description);
            $peak->setLatitude($lat);
            $peak->setLongitude($lon);
            $peak->setPointsPerVisit($points);

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
            ['Fred Doe', 'fred_user@symfony.com', 'kitten', ['ROLE_USER']],
            ['Pepa Doe', 'pepa_user@symfony.com', 'kitten', ['ROLE_USER']],
            ['Lada Doe', 'lada_user@symfony.com', 'kitten', ['ROLE_USER']],
            ['Karel Doe', 'karel_user@symfony.com', 'kitten', ['ROLE_USER']],
            ['Petr Doe', 'petr_user@symfony.com', 'kitten', ['ROLE_USER']],
            ['Marek Doe', 'marek_user@symfony.com', 'kitten', ['ROLE_USER']],
            ['Fero Doe', 'fero_user@symfony.com', 'kitten', ['ROLE_USER']],
            ['Robo Doe', 'robo_user@symfony.com', 'kitten', ['ROLE_USER']],
            ['Janosik', 'zdenek.jancik@gmail.com', 'kitten', ['ROLE_USER','ROLE_ADMIN']],
            ['Radim', 'radim.vecera@gmail.com', 'kitten', ['ROLE_USER','ROLE_ADMIN']],
            ['Vilda', 'ondrak.vilem@gmail.com', 'kitten', ['ROLE_USER','ROLE_ADMIN']],
            ['Cuzl', 'cuzl@centrum.cz', 'kitten', ['ROLE_USER','ROLE_ADMIN']]
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
            ['Team 2', 'joe_user@symfony.com', ['annie_user@symfony.com', 'fred_user@symfony.com']],
            ['Team A', 'zdenek.jancik@gmail.com', ['zdenek.jancik@gmail.com']],
            ['Team B', 'radim.vecera@gmail.com ', ['radim.vecera@gmail.com ']],
            ['Team C', 'ondrak.vilem@gmail.com ', ['ondrak.vilem@gmail.com ']],
            ['Team D', 'cuzl@centrum.cz ', ['cuzl@centrum.cz ']],
            ['Team E', 'marek_user@symfony.com', ['marek_user@symfony.com']],
            ['Team F', 'fero_user@symfony.com', ['fero_user@symfony.com']],
            ['Team G', 'robo_user@symfony.com', ['robo_user@symfony.com']],
        ];
    }

    private function loadTeams(ObjectManager $manager): void
    {
        foreach ($this->getTeamData() as [$title, $leader, $members]) {
            $team = new Team();
            $team->setTitle($title);
            $team->setLeader($this->getReference($leader));
            $team->addMember($this->getReference($leader));
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
            // $raceData = [$title, $description, $teams, $peaks, $tasks]
            [   'Hill Bill Rally 2020', 
                'Objevitelský závod, který nejde prohrát.', 
                ['Team 1', 'Team 2'],
                ['vrchol_01', 'vrchol_02', 'vrchol_03', 'vrchol_04', 'vrchol_05'],
                []
            ],
            [   'Pálavská štreka', 
                '<p>Pálavská štreka je nevšední amatérský cyklopiknikovací závod. 
                Během 25 hodin objevíš skryté krásy Vysočiny a Jižní Moravy. Cílem 
                je navštívit cestou na Pálavu co nejvíce zajímavých míst – bodů 
                vyznačených v připravené mapě. Závodníci, kteří posbírají nejvíce 
                těchto bodů stanou v cíli na stupních vítězů.</p>', 
                ['Team A', 'Team B', 'Team C', 'Team D', 'Team E', 'Team F', 'Team G'],
                ['short_01', 'short_02', 'short_03', 'short_04', 'short_05',
                 'short_06', 'short_07', 'short_08', 'short_09', 'short_10',
                 'short_11', 'short_12', 'short_13', 'short_14', 'short_15',
                 'short_16', 'short_17', 'short_18', 'short_19', 'short_20'],
                 ['Schody na rozhlednu', 'Večeře v restauraci', 'Koupačka pod vodopádem']
            ]
        ];
    }

    private function loadRaces(ObjectManager $manager): void
    {
        foreach ($this->getRaceData() as [$title, $description, $teams, $peaks, $tasks]) {
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

            foreach($tasks as $taskId)
            {
                $race->addTask($this->getReference($taskId));
            }

            $manager->persist($race);
            $this->addReference($title, $race);
            
        }

        $manager->flush();
    }

    /* load fictures by command:
         php bin/console doctrine:fixtures:load 
    */
    public function load(ObjectManager $manager)
    {
        $this->loadPeaks($manager);
        $this->loadTasks($manager);
        $this->loadUsers($manager);
        $this->loadTeams($manager);
        $this->loadRaces($manager);
        $manager->flush();
    }
}
