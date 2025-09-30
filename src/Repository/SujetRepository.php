<?php

namespace App\Repository;

use App\Entity\Sujet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sujet>
 */
class SujetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sujet::class);
    }

    public function findTopDiscussed(int $limit = 5): array
{
    return $this->createQueryBuilder('s')
        ->leftJoin('s.comments', 'c')        // relation Sujet -> Comment (adapter le nom si besoin)
        ->addSelect('COUNT(c.id) AS nbComments')
        ->groupBy('s.id')
        ->orderBy('nbComments', 'DESC')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult(); // renvoie des tableaux [0 => Sujet, 'nbComments' => string]
}

    //    /**
    //     * @return Sujet[] Returns an array of Sujet objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Sujet
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
