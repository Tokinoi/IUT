<?php

namespace App\Repository;

use App\Entity\VisitRequestSolicitation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VisitRequestSolicitation>
 *
 * @method VisitRequestSolicitation|null find($id, $lockMode = null, $lockVersion = null)
 * @method VisitRequestSolicitation|null findOneBy(array $criteria, array $orderBy = null)
 * @method VisitRequestSolicitation[]    findAll()
 * @method VisitRequestSolicitation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisitRequestSolicitationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VisitRequestSolicitation::class);
    }

//    /**
//     * @return VisitRequestSolicitation[] Returns an array of VisitRequestSolicitation objects
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

//    public function findOneBySomeField($value): ?VisitRequestSolicitation
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
