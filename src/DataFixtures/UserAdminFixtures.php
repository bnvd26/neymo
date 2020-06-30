<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\DataFixtures\BaseFixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserAdminFixtures extends BaseFixture
{
    public function loadData(ObjectManager $manager)
    {

        $user = (new User())
        ->setEmail('admin@neymo.com')
        ->setPassword('$2y$10$34ChwRD3d7zBRP2BlMV2tuPfYuOu3wngBBjtIE.BWk4HZY0yq9Niq')
        ->setRoles(['ROLE_ADMIN']);
        $this->addReference('admin-11', $user);

        $manager->persist($user);


        $this->createMany(10, function ($num) {
            $user = (new User())
            ->setEmail($this->faker->email())
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword('$2y$10$34ChwRD3d7zBRP2BlMV2tuPfYuOu3wngBBjtIE.BWk4HZY0yq9Niq');
            $this->addReference('admin-' . $num, $user);
            return $user;
        });

        $manager->flush();
    }
}
