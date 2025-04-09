<?php

namespace App\DataFixtures;

use App\Factory\AuthorFactory;
use App\Factory\BookFactory;
use App\Factory\EditorFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Factory\UserFactory;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        AuthorFactory::createMany(50);
        EditorFactory::createMany(20);
        UserFactory::createMany(5);
        BookFactory::createMany(100);
    }
} 
