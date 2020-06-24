<?php

namespace App\DataFixtures;

use App\DataFixtures\BaseFixture;
use App\Entity\Account;
use App\Entity\Company;
use App\Repository\AccountRepository;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CompanyFixtures extends BaseFixture implements DependentFixtureInterface
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(50, function ($num) use (&$account) {
            $company = (new Company())
            ->setName($this->faker->company())
            ->setAddress($this->faker->streetAddress())
            ->setCity($this->faker->city())
            ->setZipCode($this->faker->postcode())
            ->setPhoneNumber($this->faker->phoneNumber())
            ->setSiret($this->faker->siret())
            ->setFirstName($this->faker->firstName())
            ->setLastName($this->faker->lastName())
            ->setAccount($this->getReference('account-' . $num))
            ->setGovernance($this->getReference('governance-1'));
            return $company;
        });

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            GovernanceFixtures::class,
        );
    }
}
