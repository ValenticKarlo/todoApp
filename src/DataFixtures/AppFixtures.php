<?php

namespace App\DataFixtures;

use App\Factory\TaskFactory;
use App\Factory\TodoListFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        TodoListFactory::new()->createMany(10);


          TaskFactory::new()->createMany(20, function (){
              return [
                  'todoList'=>TodoListFactory::random(),
              ];
          });
        // $product = new Product();
        // $manager->persist($product);

        //$manager->flush();
    }
}
