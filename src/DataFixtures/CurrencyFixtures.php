<?php

namespace App\DataFixtures;

use App\Entity\Currency;
use App\Entity\Governance;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CurrencyFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $currency = new Currency();
        $currency->setGovernance($this->getReference('governance-1'));
        $currency->setShortName('PRS');
        $currency->setExchangeRate(91);
        $manager->persist($currency);
        $manager->flush();

        $currency = new Currency();
        $currency->setGovernance($this->getReference('governance-2'));
        $currency->setShortName('LYO');
        $currency->setExchangeRate(93);
        $manager->persist($currency);
        $manager->flush();
    }
}
