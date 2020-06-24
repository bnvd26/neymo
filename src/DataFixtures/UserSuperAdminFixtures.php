<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserSuperAdminFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('superadmin@neymo.com');
        $user->setPassword('$2y$10$34ChwRD3d7zBRP2BlMV2tuPfYuOu3wngBBjtIE.BWk4HZY0yq9Niq');
        $user->setRoles(['ROLE_SUPER_ADMIN']);
        $manager->persist($user);

        $manager->flush();
    }
}
