<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Episode;
use App\Service\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{

    private $slugify;

    public function __construct( Slugify $slugify)
    {
        $this->slugify = $slugify;
    }
    
    public function load(ObjectManager $manager)
    {
        $faker =Faker\Factory::create('fr_FR');
        $slugify = new Slugify;

        for ($i = 0; $i <= 50; $i++){
            $episode = new Episode();
            $episode->setNumber($faker->numberBetween($min = 1, $max = 10));
            $episode->setTitle($title = $faker->sentence());
            $episode->setSlug($slugify->generate($title));
            $episode->setSynopsis($faker->text());
            $episode->setSeason($this->getReference('season_' . rand(0, 50)));
            $manager->persist($episode);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [SeasonFixtures::class];
    }
}
