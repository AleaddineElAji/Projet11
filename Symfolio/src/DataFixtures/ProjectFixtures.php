<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Project;

class ProjectFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 10; $i++){
            $projet = new Project();
            $projet->setTitle("Titre du projet n°$i")
                   ->setDescription("<p>Contenu de l'article n°$i</p>")
                   ->setImage("<img src='placehold.it/350x150'/>")
                   ->setGithub("https://github.com/DylanCharton")
                   ->setWeblink("https://dylanc903.promo-93.codeur.online/");
                   
            $manager->persist($projet);
        }

        $manager->flush();
    }
}
