<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user); 
        $this->getEntityManager()->flush();
    }

    
    public function getAdmin(): User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :roles')
            ->setParameter('roles', '%"ROLE_ADMIN"%')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function countFavorites($id): int
    {
    return $this->createQueryBuilder('b')
        ->select('COUNT(blog)')
        ->leftJoin('b.favoriteBlog', 'blog')
        ->andWhere('b.id = :id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getSingleScalarResult();
    }
    public function countOrderedGames($userId): int
    {
    return $this->createQueryBuilder('u')
        ->select('COUNT(DISTINCT g.id)')
        ->join('u.orders', 'o')
        ->join('o.orderItems', 'oi')
        ->join('oi.product', 'g')
        ->andWhere('u.id = :userId')
        ->setParameter('userId', $userId)
        ->getQuery()
        ->getSingleScalarResult();
    }
}
