<?php

namespace App\DataFixtures;

use App\Entity\GovernanceUserInformation;
use App\Entity\User;
use App\DataFixtures\BaseFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class GovernanceUserInformationFixtures extends BaseFixture implements DependentFixtureInterface
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(1, function ($num) {
            $govUserInfo = (new GovernanceUserInformation())
            ->setFirstName($this->faker->firstName())
            ->setLastName($this->faker->lastName())
            ->setRole('Manager')
            ->setUser($this->getReference('admin-11'))
            ->setGovernance($this->getReference('governance-' . rand(1, 2)));
            return $govUserInfo;
        });
        $this->createMany(10, function ($num) {
            $govUserInfo = (new GovernanceUserInformation())
            ->setFirstName($this->faker->firstName())
            ->setLastName($this->faker->lastName())
            ->setRole('Manager')
            ->setUser($this->getReference('admin-' . $num))
            ->setGovernance($this->getReference('governance-' . rand(1, 2)));
            return $govUserInfo;
        });

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserAdminFixtures::class,
        );
    }
}
