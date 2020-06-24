<?php

namespace App\DataFixtures;

use App\Entity\Governance;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class GovernanceFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $governance = new Governance();
        $governance->setName('Paris');
        $governance->setMoneyName('monnaie locale de paris');
        $manager->persist($governance);
        $manager->flush();
        $this->addReference('governance-1', $governance);

        $governance = new Governance();
        $governance->setName('Lyon');
        $governance->setMoneyName('monnaie locale de lyon');
        $manager->persist($governance);
        $manager->flush();
        $this->addReference('governance-2', $governance);
    }
}
