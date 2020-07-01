<?php

namespace App\DataFixtures;

use App\DataFixtures\BaseFixture;
use App\Entity\Account;
use App\Entity\Company;
use App\Entity\User;
use App\Repository\AccountRepository;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CompanyUserFixtures extends BaseFixture implements DependentFixtureInterface
{
    protected function loadData(ObjectManager $manager)
    {
        $user = (new User())
            ->setRoles(["ROLE_USER"])
            ->setPassword('$2y$10$34ChwRD3d7zBRP2BlMV2tuPfYuOu3wngBBjtIE.BWk4HZY0yq9Niq')
            ->setEmail('company@neymo.com');
        $manager->persist($user);
        $account = (new Account())
            ->setAccountNumber($this->faker->numberBetween($min = 1000, $max = 20000))
            ->setAvailableCash($this->faker->numberBetween($min = 10, $max = 2000));
        $manager->persist($account);
        $company = (new Company())
            ->setName($this->faker->company())
            ->setAddress($this->faker->streetAddress())
            ->setCity($this->faker->city())
            ->setZipCode($this->faker->postcode())
            ->setPhoneNumber($this->faker->phoneNumber())
            ->setSiret($this->faker->siret())
            ->setFirstName($this->faker->firstName())
            ->setLastName($this->faker->lastName())
            ->setAccount($account)
            ->setCategory($this->getReference('category-' . rand(0,7)))
            ->setGovernance($this->getReference('governance-' . rand(1, 2)))
            ->setValidated($this->faker->boolean())
            ->addUser($user);
        $manager->persist($company);

        $this->createMany(50, function ($num) use (&$account) {
            $company = (new Company())
            ->setName($this->faker->company())
            ->setAddress($this->faker->streetAddress())
            ->setCity($this->faker->city())
            ->setZipCode($this->faker->postcode())
            ->setPhoneNumber($this->faker->phoneNumber())
            ->setSiret($this->faker->siret())
            ->setFirstName($this->faker->firstName())
            ->setCategory($this->getReference('category-' . rand(0,6)))
            ->setLastName($this->faker->lastName())
            ->setAccount($this->getReference('account-' . $num))
            ->setGovernance($this->getReference('governance-' . rand(1, 2)))
            ->setValidated($this->faker->boolean())
            ->addUser($this->getReference('user-' . $num));
            return $company;
        });

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            GovernanceFixtures::class,
            UserFixtures::class,
            CategoryFixtures::class,
        );
    }
}
