<?php
namespace App\DataFixtures;

use App\Entity\Peak;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class PeakFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // create 20 peaks! Bam!
        for ($i = 1; $i <= 20; $i++) {
            $peak = new Peak();
            $peak->setShortId('peak_'.$i);
            $peak->setTitle('Vrchol '.$i);
            $peak->setDescription("Lorem ipsum. Lorem ipsum. Lorem ipsum. Lorem ipsum. Lorem ipsum. Lorem ipsum. Lorem ipsum. Lorem ipsum. ");
            $peak->setLatitude(mt_rand(-90, 90));
            $peak->setLongitude(mt_rand(-180, 180));

            $manager->persist($peak);
        }

        $manager->flush();
    }
}