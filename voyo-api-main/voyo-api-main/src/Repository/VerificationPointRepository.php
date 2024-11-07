<?php

namespace App\Repository;

use App\Entity\VerificationPoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VerificationPoint>
 *
 * @method VerificationPoint|null find($id, $lockMode = null, $lockVersion = null)
 * @method VerificationPoint|null findOneBy(array $criteria, array $orderBy = null)
 * @method VerificationPoint[]    findAll()
 * @method VerificationPoint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VerificationPointRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VerificationPoint::class);
    }

//    /**
//     * @return VerificationPoint[] Returns an array of VerificationPoint objects
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

//    public function findOneBySomeField($value): ?VerificationPoint
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
