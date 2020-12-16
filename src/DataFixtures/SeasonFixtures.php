<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Season;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker =Faker\Factory::create('fr_FR');

        for ($i = 0; $i <= 50; $i++){
            $season = new Season();
            $season->setNumber($faker->numberBetween($min = 1, $max = 10));
            $season->setYear($faker->year());
            $season->setDescription($faker->text());
            $season->setProgramId($this->getReference('program_' . rand(0, 5)));
            $this->addReference('season_' . $i, $season);
            $manager->persist($season);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }
}
