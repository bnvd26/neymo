<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\DataFixtures\BaseFixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends BaseFixture
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(50, function ($num) {
            $user = (new User())
            ->setEmail($this->faker->email())
            ->setRoles(['ROLE_USER'])
            ->setPassword('$2y$10$34ChwRD3d7zBRP2BlMV2tuPfYuOu3wngBBjtIE.BWk4HZY0yq9Niq');
            $this->addReference('user-' . $num, $user);
            return $user;
        });

        $manager->flush();
    }
}
