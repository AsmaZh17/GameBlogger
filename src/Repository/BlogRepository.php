<?php

namespace App\Repository;

use App\Entity\Blog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Blog>
 */
class BlogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Blog::class);
    }

    /**
     * @return Blog[] Returns an array of Blog objects
     */
    public function lastTree(): array
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.id', 'DESC')
            ->setMaxResults(3)
            ->getQuery()    
            ->getResult()
        ;
    }
    public function lastFoor(): array
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.id', 'DESC')
            ->setMaxResults(4)
            ->getQuery()    
            ->getResult()
        ;
    }

    public function lastTreeDiffId($id): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.id <> :val')
            ->setParameter('val', $id)
            ->orderBy('b.id', 'DESC')
            ->setMaxResults(3)
            ->getQuery()    
            ->getResult()
        ;
    }

    /**
     * @return Blog[] Returns an array of Blog objects
     */
    public function findByCategorieId($id): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.categorie = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getResult()
        ;
    }

    public function countFavoritesForBlogIdOne($blogId): int
    {
    return $this->createQueryBuilder('b')
        ->select('COUNT(u)')
        ->leftJoin('b.usersFavorite', 'u')
        ->andWhere('b.id = :blogId')
        ->setParameter('blogId', $blogId)
        ->getQuery()
        ->getSingleScalarResult();
    }

    public function findByValue($value): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.titre LIKE :value')
            ->setParameter('value', '%' . $value . '%')
            ->orderBy('b.titre', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
