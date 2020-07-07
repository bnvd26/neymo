<?php

namespace App\DataFixtures;

use App\DataFixtures\BaseFixture;
use App\DataFixtures\CompanyUserFixtures;
use App\Entity\Post;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class PostFixtures extends BaseFixture implements DependentFixtureInterface
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(30, function ($num) {
            $post = new Post();
            $post->setCompany($this->getReference('company-' . $num));
            $post->setTitle($this->faker->text($maxNbChars = 50));
            $post->setContent($this->faker->text($maxNbChars = 170));
            $post->setDate($this->faker->dateTimeBetween($startDate = '-5 months', $endDate = 'now', $timezone = null));
            $this->addReference('post-' . $num, $post);
            return $post;
        });

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            CompanyUserFixtures::class
        );
    }
}
