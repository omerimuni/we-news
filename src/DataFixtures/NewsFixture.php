<?php

namespace App\DataFixtures;

use App\Entity\News;
use App\Entity\Comments;
use App\Entity\Category;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class NewsFixture extends BaseFixture implements DependentFixtureInterface
{
    private static $articleTitles = [
        'Prince Harry and Meghan: Royal Family \'hurt\' as couple begin \'next chapter\'',
        'Why Brexit Stage Two may turn into a rocky ride',
        'Prince Harry and Meghan: Where do they get their money?',
        'The young Koreans pushing back on a culture of endurance',
        'Iran plane crash: Airliner \'was trying to return to airport\'',
        'Iran crisis: Commander says more air strikes were planned against US',
        'Nasa Moon rocket core leaves for testing',
        'Zlatan Ibrahimovic: Statue \'must be moved\' from Malmo say supporters',
        'Sergino Dest: Ajax\'s US full-back leaves Qatar training camp amid Iran tensions',
        'What it’s like to survive a shipwreck',
        'My Money: \'I made it through the day without spending a dollar\'',


    ];

    private static $articleImages = [
        '_110421217_chelsea2.jpg',
        '_110433501_irancrash_index_afp_2.jpg',
        '_110441027_zlatan_reuters.jpg',
        '_110444268_bartytwo_epa.jpg',
        '_110444643_serena_auckland.jpg',
        '_110446816_dest_getty2.jpg',
        '_110447467_mediaitem110447466.jpg',
        '_110449778_mediaitem110449777.jpg',
        '_110450017_gettyimages-970347806.jpg',
        '_110450021_a683be2b-7d81-40ac-8e72-84cbc7f47873.jpg',
        '_110451032_mediaitem110451029.jpg',
        '_110452277_4384200.jpg',
        '20180323-143428-5e162f7c68601.jpeg',
        'p07zs9r0.jpg',
        'p07zs9sm.jpg',
        'p07ztzzs.jpg',
        'p07zv0jv.jpg',
        'p07ykbkd.jpg',
        'p07ykbvg.jpg',
    ];

    public function loadData(ObjectManager $manager)
    {
        $this->createMany(News::class, 150, function(News $article, $count) use ($manager) {
            $article->setTitle($this->faker->randomElement(self::$articleTitles))
                ->setShort($this->faker->realText(20, 1))
                ->setContent($this->faker->realText(5000, 2));
            // publish most articles
            if ($this->faker->boolean(70)) {
                $article->setPublishedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            }
            $article->setPicture($this->faker->randomElement(self::$articleImages));

            $categories = $this->getRandomReferences(Category::class, $this->faker->numberBetween(0, 5));
            foreach ($categories as $cat) {
                $article->addCategory($cat);
            }
        });
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixture::class,
        ];
    }
}
