<?php

namespace App\DataFixtures;

use App\DataFixtures\FamilyFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Child;
use App\Entity\Family;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ChildFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        foreach ($this->getChildData() as [$name, $start_date, $family_id])
        {
            $family_id = $manager->getRepository(Family::class)->find($family_id);
            $child = new Child();
            $child->setFamily($family_id);
            $child->setName($name);
            $child->setStartDate($start_date);
            $manager->persist($child);
        }

        $manager->flush();
    }

        private function getChildData(): array
        {
            return [
                ['Paul', '18-01-2019', 1],
                ['Sienna', '23-01-2020', 2],
                ['Laura', '07-06-2019', 3],
                ['John', '19-12-2019', 4],
                ['Sydney', '02-03-2019', 5]
            ];
        }
    
        public function getDependencies()
        {
            return array(
                FamilyFixtures::class,
            );
        }
}
