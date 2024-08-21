<?php

namespace App\Repository;

use App\Entity\FlightPrices;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FlightPrices>
 *
 * @method FlightPrices|null find($id, $lockMode = null, $lockVersion = null)
 * @method FlightPrices|null findOneBy(array $criteria, array $orderBy = null)
 * @method FlightPrices[]    findAll()
 * @method FlightPrices[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FlightPricesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FlightPrices::class);
    }

//    /**
//     * @return FlightPrices[] Returns an array of FlightPrices objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FlightPrices
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
