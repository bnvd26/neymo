<?php

namespace App\DataFixtures;

use App\Entity\GovernanceUserInformation;
use App\Entity\User;
use App\Repository\GovernanceRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserAdminFixtures extends Fixture
{
    public const USER_ADMIN = 'admin';

    public function load(ObjectManager $manager)
    {
        // User of governance
        $user = new User();
        $user->setEmail('admin@neymo.com');
        $user->setPassword('$2y$10$34ChwRD3d7zBRP2BlMV2tuPfYuOu3wngBBjtIE.BWk4HZY0yq9Niq');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setGovernance($this->getReference(GovernanceFixtures::GOVERNANCE));
        $manager->persist($user);

        $this->addReference(self::USER_ADMIN, $user);
    }
}
