<?php

namespace App\Repository;

use App\Entity\Results;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Results>
 *
 * @method Results|null find($id, $lockMode = null, $lockVersion = null)
 * @method Results|null findOneBy(array $criteria, array $orderBy = null)
 * @method Results[]    findAll()
 * @method Results[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResultsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Results::class);
    }

    public function add(Results $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Results $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findResultsByRaceId(int $race_id): array
    {
        $connection = $this->getEntityManager()->getConnection();
        $sql = "SELECT * FROM results WHERE race_id = '$race_id' ORDER BY placement ASC";
        $stmt = $connection->prepare($sql);
        $result = $stmt->executeQuery(['id' => $race_id]);

        $data = $result->fetchAllAssociative();

        return $data;
        /*return $this->createQueryBuilder('r')
            ->select('r')
            ->andWhere('r.race = :id')
            ->setParameter('id', $id)
            ->orderBy('r.placement', 'ASC')
            ->getQuery()
            ->getResult()
            ;*/
    }

    /**
     * @throws Exception
     */
    public function findAverage($distance): array
    {
        $connection = $this->getEntityManager()->getConnection();
        $sql = "SELECT TIME_FORMAT(SEC_TO_TIME(avg(hour(race_time) * 3600 + (minute(race_time) * 60) + second(race_time))),'%H:%i:%s') as AvgTime 
                FROM results WHERE distance = '$distance'";
        $stmt = $connection->prepare($sql);
        $result = $stmt->executeQuery(['distance' => $distance]);

        return $result->fetchAssociative();
    }

//    /**
//     * @return Results[] Returns an array of Results objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Results
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
