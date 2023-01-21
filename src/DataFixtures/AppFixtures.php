<?php

namespace App\DataFixtures;

use App\Factory\TaskFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        TaskFactory::new()->createMany(10);
        // $product = new Product();
        // $manager->persist($product);

        //$manager->flush();
    }
}
