<?php

namespace App\Repository;

use App\Entity\GlobalPostComment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method GlobalPostComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method GlobalPostComment|null findOneBy(array $criteria, array $orderBy = null)
 * @method GlobalPostComment[]    findAll()
 * @method GlobalPostComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GlobalPostCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GlobalPostComment::class);
    }

    /**
     * @return GlobalPostComment[]
     */
    public function findCommentsByPostId($global_post_id): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT g
            FROM App\Entity\GlobalPostComment g
            WHERE g.GlobalPost = :global_post_id
            ORDER BY g.date DESC, g.time DESC'
        )->setParameter('global_post_id', $global_post_id);

        // returns an array of Product objects
        return $query->getResult();
    }

    /*
    public function findOneBySomeField($value): ?GlobalPostComment
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
