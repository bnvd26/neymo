<?php

namespace App\DataFixtures;

use App\DataFixtures\BaseFixture;
use App\Entity\Account;
use App\Entity\Company;
use App\Entity\Particular;
use App\Entity\User;
use App\Repository\AccountRepository;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CompanyParticularUserFixtures extends BaseFixture implements DependentFixtureInterface
{
    protected function loadData(ObjectManager $manager)
    {
        $user = (new User())
            ->setRoles(["ROLE_USER"])
            ->setPassword('$2y$10$34ChwRD3d7zBRP2BlMV2tuPfYuOu3wngBBjtIE.BWk4HZY0yq9Niq')
            ->setEmail('companyparticular@neymo.com');
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
            ->setGovernance($this->getReference('governance-1'))
            ->setValidated(false)
            ->addUser($user);
        $manager->persist($company);

        $account = (new Account())
            ->setAccountNumber($this->faker->numberBetween($min = 1000, $max = 20000))
            ->setAvailableCash($this->faker->numberBetween($min = 10, $max = 2000));
        $manager->persist($account);

        $particular = (new Particular())
            ->setAddress($this->faker->streetAddress())
            ->setCity($this->faker->city())
            ->setZipCode($this->faker->postcode())
            ->setPhoneNumber($this->faker->phoneNumber())
            ->setFirstName($this->faker->firstName())
            ->setLastName($this->faker->lastName())
            ->setAccount($account)
            ->setGovernance($this->getReference('governance-1'))
            ->setValidated(false)
            ->setUser($user);
        $manager->persist($particular);


        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            GovernanceFixtures::class,
            UserFixtures::class,
        );
    }
}
