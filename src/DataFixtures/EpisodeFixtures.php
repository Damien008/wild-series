<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Episode;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker =Faker\Factory::create('fr_FR');

        for ($i = 0; $i <= 50; $i++){
            $episode = new Episode();
            $episode->setNumber($faker->numberBetween($min = 1, $max = 10));
            $episode->setTitle($faker->sentence());
            $episode->setSynopsis($faker->text());
            $episode->setSeasonId($this->getReference('season_' . rand(0, 50)));
            $manager->persist($episode);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [SeasonFixtures::class];
    }
}
