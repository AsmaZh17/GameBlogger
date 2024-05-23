<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function countReviewsByGameId($gameId): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->andWhere('c.game = :gameId')
            ->setParameter('gameId', $gameId)
            ->getQuery()
            ->getSingleScalarResult(); 
    }
    public function getReview($gameId , $authorId): ?Review
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.game = :game')
            ->andWhere('b.author = :author')
            ->setParameter('game', $gameId)
            ->setParameter('author', $authorId)
            ->getQuery()    
            ->getOneOrNullResult()
        ;
    }
}
