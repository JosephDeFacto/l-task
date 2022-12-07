<?php

namespace App\Factory;

use App\Entity\Race;
use Doctrine\ORM\EntityManagerInterface;

class RaceFactory
{

    public function build($race): Race
    {
        switch ($race) {
            case 'Race':
                $race = new Race();
                break;
        }
        return $race;
    }

    public function persist($race, EntityManagerInterface $entityManager): void
    {
        $race->setRaceName($race->getRaceName());
        $race->setDate($race->getDate());
        $entityManager->persist($race);
        $entityManager->flush();
    }
}