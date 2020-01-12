<?php

namespace App\DataFixtures;

use App\Entity\News;
use App\Entity\Comments;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;

class CommentFixture extends BaseFixture implements DependentFixtureInterface
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(Comments::class, 500, function(Comments $comment) {
            $comment->setComment(
                $this->faker->boolean ? $this->faker->paragraph : $this->faker->sentences(2, true)
            );
            $comment->setName($this->faker->name);
            $comment->setEmail($this->faker->email);
            $comment->setPublishedAt($this->faker->dateTimeBetween('-1 months', '-1 seconds'));
            $comment->setNews($this->getRandomReference(News::class));
        });
        $manager->flush();
    }

    public function getDependencies()
    {
        return [NewsFixture::class];
    }
}
