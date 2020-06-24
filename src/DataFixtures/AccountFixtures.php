<?php

namespace App\DataFixtures;

use App\DataFixtures\BaseFixture;
use App\Entity\Account;
use App\Entity\Company;
use Doctrine\Common\Persistence\ObjectManager;

class AccountFixtures extends BaseFixture
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(50, function ($num) use (&$account) {
            $account = (new Account())
            ->setAccountNumber($this->faker->numberBetween($min = 1000, $max = 20000))
            ->setAvailableCash($this->faker->numberBetween($min = 10, $max = 2000));
            $this->addReference('account-' . $num, $account);
            return $account;
        });

        $manager->flush();
    }
}
