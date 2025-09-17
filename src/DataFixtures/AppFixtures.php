<?php

namespace App\DataFixtures;

use App\Entity\Employee;
use App\Factory\EmployeeFactory;
use App\Factory\ProjectFactory;
use App\Factory\TaskFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        EmployeeFactory::createMany(20);
        ProjectFactory::createMany(10);
        TaskFactory::createMany(50);

        $manager->flush();
    }
}
