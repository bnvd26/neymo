<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\DataFixtures\BaseFixture;
use App\Entity\Category;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends BaseFixture
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(7, function ($num) {
            $categories = ['Restauration', 'Culture', 'Parc', 'Commerces de proximités', 'Service', 'Logement / Hôtel', 'Association'];
            $category = (new Category())
            ->setName($categories[$num]);
            $this->addReference('category-' . $num, $category);
            return $category;
        });

        $manager->flush();
    }
}
