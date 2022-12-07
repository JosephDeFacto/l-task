<?php

namespace App\Factory;

use App\Entity\Results;
use App\Repository\ResultsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class ResultsFactory
{

    /**
     * @var ResultsRepository
     */
    private ResultsRepository $resultsRepository;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param ResultsRepository $resultsRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ResultsRepository $resultsRepository, EntityManagerInterface $entityManager)
    {
        $this->resultsRepository = $resultsRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param $results
     * @param $fileUploader
     * @param EntityManagerInterface $entityManager
     * @param $race
     * @return Results
     * @throws Exception
     */
    public function build($results, $fileUploader, EntityManagerInterface $entityManager, $race): Results
    {
        switch ($results) {
            case 'Results':
                foreach ($fileUploader->parse() as $row) {
                    $results = new Results();
                    $results->setFullName($row[0]);
                    $results->setDistance($row[1]);
                    $results->setRaceTime($row[2]);
                    $results->setRace($race);
                    $entityManager->persist($results);
                }
                $entityManager->flush();
        }
        if (!is_object($results)) {
            throw new Exception('Invalid type data, the type should be object');
        }
        return $results;
    }

    /**
     * @param $id
     * @param $distance
     * @return void
     */
    public function persistPlacement($id, $distance)
    {
        $results = $this->resultsRepository->findBy(['race' => $id, 'distance' => $distance], ['raceTime' => 'ASC']);

        $placement = 1;

        foreach ($results as $row) {
            $result = $row->setPlacement($placement);
            $this->entityManager->persist($result);

            $placement++;
            $this->entityManager->flush();
        }
    }
}