<?php

namespace App\DataFixtures;

use App\Entity\Contacts;
use App\Entity\Governance;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\DataFixtures\BaseFixture;
use App\DataFixtures\ParticularUserFixtures;
use App\Entity\Transaction;

class TransactionFixtures extends BaseFixture implements DependentFixtureInterface
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(10, function ($num)  {
            $transaction = new Transaction();
            $transaction->setBeneficiary($this->faker->boolean() ? $this->getReference('account-company00') : $this->getReference('account00'));
            $transaction->setEmiter($this->faker->boolean() ? $this->getReference('account-company00') : $this->getReference('account00'));
            $transaction->setDate($this->faker->dateTimeBetween($startDate = '-5 months', $endDate = 'now', $timezone = null));
            $transaction->setTransferedMoney($this->faker->numberBetween($min = 1, $max = 200));
           
            return $transaction;
        });

        $this->createMany(50, function ($num)  {
            $transaction = new Transaction();
            $transaction->setBeneficiary($this->faker->boolean() ? $this->getReference('account-company-' . $num) : $this->getReference('account-' . $num));
            $transaction->setEmiter($this->faker->boolean() ? $this->getReference('account-company-' . $num) : $this->getReference('account-' . $num));
            $transaction->setDate($this->faker->dateTimeBetween($startDate = '-5 months', $endDate = 'now', $timezone = null));
            $transaction->setTransferedMoney($this->faker->numberBetween($min = 1, $max = 200));
           
            return $transaction;
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
