<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoriesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();
        for ($i=0; $i <= 5 ; $i++) { 
        	$category = new Category;
        	$category->setTitle($faker->catchPhrase)
        			->setDescription($faker->bs);
        	$manager->persist($category);
        }

        $manager->flush();
    }
}
