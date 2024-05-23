<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Game>
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }
    public function lastGame(): ?Game
    {
    return $this->createQueryBuilder('g')
        ->where('g.end IS NULL')
        ->orderBy('g.id', 'DESC')
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
    }
    public function lastTree(): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.end IS NULL')
            ->orderBy('b.id', 'DESC')
            ->setMaxResults(3)
            ->getQuery()    
            ->getResult()
        ;
    }
    public function lastFoor(): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.end IS NULL')
            ->orderBy('b.id', 'DESC')
            ->setMaxResults(4)
            ->getQuery()    
            ->getResult()
        ;
    }
    public function lastTreeDiffId($id): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.end IS NULL')
            ->andWhere('b.id <> :val')
            ->setParameter('val', $id)
            ->orderBy('b.id', 'DESC')
            ->setMaxResults(3)
            ->getQuery()    
            ->getResult()
        ;
    }
    public function getFree(): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.end IS NULL')
            ->andWhere('b.solde = :solde')
            ->setParameter('solde', 100)
            ->getQuery()    
            ->getResult()
        ;
    }
    public function getSolde(): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.end IS NULL')
            ->andWhere('b.solde IS NOT NULL')
            ->getQuery()    
            ->getResult()
        ;
    }
    
    public function findByValue($value): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.nom LIKE :value')
            ->setParameter('value', '%' . $value . '%')
            ->orderBy('b.nom', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
