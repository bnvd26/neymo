<?php

namespace App\DataFixtures;

use App\Entity\Contacts;
use App\Entity\Governance;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\DataFixtures\BaseFixture;
use App\DataFixtures\ParticularUserFixtures;

class ContactFixtures extends BaseFixture implements DependentFixtureInterface
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(10, function ($num)  {
            $contacts = new Contacts();
            $contacts->setDirectory($this->getReference('directory00'));
            $contacts->setAccount($this->faker->boolean() ? $this->getReference('account-company-' . $num) : $this->getReference('account-' . $num));
           
            return $contacts;
        });

        $this->createMany(5, function ($num)  {
            $contacts = new Contacts();
            $contacts->setDirectory($this->getReference('directory-' . $num));
            $contacts->setAccount($this->faker->boolean() ? $this->getReference('account-company-' . $num) : $this->getReference('account-' . $num));
           
            return $contacts;
        });

        $this->createMany(10, function ($num)  {
            $contacts = new Contacts();
            $contacts->setDirectory($this->getReference('directory-company00'));
            $contacts->setAccount($this->faker->boolean() ? $this->getReference('account-company-' . $num) : $this->getReference('account-' . $num));
           
            return $contacts;
        });

        $this->createMany(5, function ($num)  {
            $contacts = new Contacts();
            $contacts->setDirectory($this->getReference('directory-company-' . $num));
            $contacts->setAccount($this->faker->boolean() ? $this->getReference('account-company-' . $num) : $this->getReference('account-' . $num));
           
            return $contacts;
        });
        
        $manager->flush();
        
    }

    public function getDependencies()
    {
        return array(
            ParticularUserFixtures::class
        );
    }
}
