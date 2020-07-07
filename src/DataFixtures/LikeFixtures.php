<?php

namespace App\DataFixtures;

use App\DataFixtures\BaseFixture;
use App\DataFixtures\CompanyUserFixtures;
use App\DataFixtures\PostFixtures;
use App\Entity\Like;
use App\Entity\Post;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LikeFixtures extends BaseFixture implements DependentFixtureInterface
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(150, function ($num) {
            $like = new Like();
            $like->setAccount($this->faker->boolean() ? $this->getReference('account-' . rand(1, 49)) : $this->getReference('account-company-' . rand(1, 49)));
            $like->setPost($this->getReference('post-' . rand(1, 29)));
            $like->setLiked(true);

            return $like;
        });

        $this->createMany(20, function ($num) {
            $like = new Like();
            $like->setAccount($this->faker->boolean() ? $this->getReference('account00') : $this->getReference('account-company00'));
            $like->setPost($this->getReference('post-' . rand(1, 29)));
            $like->setLiked(true);

            return $like;
        });

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            CompanyUserFixtures::class,
            PostFixtures::class
        );
    }
}
