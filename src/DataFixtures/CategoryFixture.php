<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;


class CategoryFixture extends BaseFixture
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(Category::class, 5, function(Category $category) {
            $category->setTitle($this->faker->sentence(1, true));
        });
        $manager->flush();
    }


}
