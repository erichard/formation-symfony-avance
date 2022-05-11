<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Carrier;
use App\Entity\LogisticProvider;
use App\Entity\Warehouse;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $clog = new LogisticProvider('CLOG');
        $clog->setPriority(100);
        $manager->persist($clog);
        $orliweb = new LogisticProvider('Orliweb');
        $manager->persist($orliweb);
        $retail = new LogisticProvider('Retail');
        $manager->persist($retail);

        $w1 = new Warehouse('CLOG');
        $w1->setReference('CLOG');
        $w1->setLogisticProvider($clog);
        $manager->persist($w1);

        $w4 = new Warehouse('Boutique Nantes');
        $w5 = new Warehouse('Boutique Angers');
        $w4->setLogisticProvider($retail);
        $w5->setLogisticProvider($retail);
        $manager->persist($w4);
        $manager->persist($w5);

        $colissimo = new Carrier('Colissimo');
        $dpd = new Carrier('DPD');
        $manager->persist($colissimo);
        $manager->persist($dpd);

        $manager->flush();
    }
}
