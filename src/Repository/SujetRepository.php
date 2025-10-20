<?php

namespace App\Repository;

use App\Entity\Sujet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SujetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sujet::class);
    }

    public function findTopDiscussed(int $limit = 5): array
{
    return $this->createQueryBuilder('s')
        ->leftJoin('s.comments', 'c')        
        ->addSelect('COUNT(c.id) AS nbComments')
        ->groupBy('s.id')
        ->orderBy('nbComments', 'DESC')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult(); 
}
}
