<?php

namespace App\Repository;

use App\Entity\ConversationsUsers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ConversationsUsers|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConversationsUsers|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConversationsUsers[]    findAll()
 * @method ConversationsUsers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversationsUsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConversationsUsers::class);
    }

    // /**
    //  * @return ConversationsUsers[] Returns an array of ConversationsUsers objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ConversationsUsers
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @param string $conversationId
     * @param string $userId
     * @return ConversationsUsers|null
     * @throws NonUniqueResultException
     */
    public function findByConversationIdAndUserId(string $conversationId, string $userId)
    {
        $qb = $this->createQueryBuilder('cu');
        $qb
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->eq('cu.conversation', ':conversationId'),
                    $qb->expr()->neq('cu.user', ':userId')
                )
            )
            ->setParameters([
                'conversationId' => $conversationId,
                'userId' => $userId
            ])

        ;
        return $qb->getQuery()->getOneOrNullResult();
    }
}
