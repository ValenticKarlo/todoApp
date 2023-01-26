<?php

namespace App\DataFixtures;

use App\Factory\TaskFactory;
use App\Factory\TodoListFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        UserFactory::new()->createMany(10);
        TodoListFactory::new()->createMany(40, function (){
            return [
             'user' => UserFactory::random(),
            ];
        });


        TaskFactory::new()->createMany(80, function (){
              return [
                  'todoList'=>TodoListFactory::random(),
              ];
        });
        // $product = new Product();
        // $manager->persist($product);

        //$manager->flush();
    }
}
