<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Point;

class PointFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $tab = file("D:/projets/notes/villes.txt");
        $packet = 100;
        $i = 0;
        foreach($tab as $line) {
            $each = explode("	",$line);            
            if (count($each) == 6 && in_array($each[0],["37","41","18","28","45"]) && is_numeric(trim($each[4])) && is_numeric(trim($each[5]))) {
                $i++;
                echo $i.PHP_EOL;
                $point = new Point();
                $point->setLatitude(trim($each[4])*1);
                $point->setLongitude(trim($each[5])*1);
                $point->setName($each[1]);
                $point->setDescription("Code postal : ".$each[2].", Code INSEE : ".$each[3]);
                $manager->persist($point);
                if ($i%$packet==0) {                    
                    $manager->flush();
                    $manager->clear();
                    //gc_collect_cycles();
                }
            }
        }
        $manager->flush();
    }
    


}
