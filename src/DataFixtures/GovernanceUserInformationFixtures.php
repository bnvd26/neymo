<?php

namespace App\DataFixtures;

use App\Entity\GovernanceUserInformation;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class GovernanceUserInformationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user = new GovernanceUserInformation();
        $user->setFirstName('John');
        $user->setLastName('Doe');
        $user->setRole('Manager');
        $user->setUser($this->getReference(UserAdminFixtures::USER_ADMIN));
        $manager->persist($user);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserAdminFixtures::class,
        );
    }
}
