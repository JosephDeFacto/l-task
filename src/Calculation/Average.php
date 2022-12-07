<?php

namespace App\Calculation;

use App\Repository\ResultsRepository;
use Doctrine\DBAL\Exception;

class Average
{

    /**
     * @var ResultsRepository
     */
    private ResultsRepository $resultsRepository;

    public function __construct(ResultsRepository $resultsRepository)
    {
        $this->resultsRepository = $resultsRepository;
    }

    /**
     * @param int $race_id
     * @return array
     * @throws Exception
     */
    public function average(int $race_id): array
    {
        $result = $this->resultsRepository->findResultsByRaceId($race_id);

        $medium = "medium";
        $long = "long";
        foreach ($result as $row) {

            if (empty($row)) {
                return [];
            }

            if (in_array($medium, $row))
                $medium = $row['distance'];

            if (in_array($long, $row)) {
                $long = $row['distance'];
            }
        }

        $medAvg["medium"] = $this->resultsRepository->findAverage($medium);
        $longAvg["long"] = $this->resultsRepository->findAverage($long);
        return array_merge($medAvg, $longAvg);
    }
}