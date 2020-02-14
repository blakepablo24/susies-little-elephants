<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Family;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class FamilyFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        foreach ($this->getFamilyData() as [$name, $mum, $dad, $guardian, $user_id])
        {
            $user_id = $manager->getRepository(User::class)->find($user_id);
            $family = new Family();
            $family->setUser($user_id);
            $family->setName($name);
            $family->setMum($mum);
            $family->setDad($dad);
            $family->setGuardian($guardian);
            $manager->persist($family);
        }

        $manager->flush();
    }

    private function getFamilyData(): array
    {
        return [
            ['Robson', 'Susan', 'Henry', '', 2],
            ['Sarkozi', 'Joanne', 'Joe', '', 3],
            ['Eade', 'Rachel', 'Billy', '', 4],
            ['Schwarzenegger', 'Hillary', 'Arnold', '', 5],
            ['Willis', '', '', 'Bruce', 6]
        ];
    }

    public function getDependencies()
        {
            return array(
                UserFixtures::class,
            );
        }

}
