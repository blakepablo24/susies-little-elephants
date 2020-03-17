<?php

namespace App\Repository;

use App\Entity\GlobalPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method GlobalPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method GlobalPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method GlobalPost[]    findAll()
 * @method GlobalPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GlobalPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GlobalPost::class);
    }

    /**
     * @return GlobalPost[]
     */
    public function findAllGlobalPosts(): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT p
            FROM App\Entity\GlobalPost p
            ORDER BY p.date DESC, p.Time DESC'
        );

        // returns an array of Product objects
        return $query->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
