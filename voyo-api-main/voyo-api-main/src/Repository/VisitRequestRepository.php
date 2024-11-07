<?php

namespace App\Repository;

use App\Entity\VisitRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VisitRequest>
 *
 * @method VisitRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method VisitRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method VisitRequest[]    findAll()
 * @method VisitRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisitRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VisitRequest::class);
    }

//    /**
//     * @return VisitRequest[] Returns an array of VisitRequest objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?VisitRequest
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
