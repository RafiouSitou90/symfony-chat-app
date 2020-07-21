<?php

namespace App\Repository;

use App\Entity\Conversations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Conversations|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conversations|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conversations[]    findAll()
 * @method Conversations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversations::class);
    }

    // /**
    //  * @return Conversations[] Returns an array of Conversations objects
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
    public function findOneBySomeField($value): ?Conversations
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findConversationByUsers (string $current_user_id, string $second_user_id)
    {
        $qb = $this->createQueryBuilder('c');

        $qb
            ->select($qb->expr()->count('cu.conversation'))
            ->innerJoin('c.conversationsUsers', 'cu')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->eq('cu.user', ':currentUserId'),
                    $qb->expr()->eq('cu.user', ':secondUserId')
                )
            )
            ->groupBy('cu.conversation')
            ->having(
                $qb->expr()->eq(
                    $qb->expr()->count('cu.conversation'), 2
                )
            )
            ->setParameters([
                'currentUserId' => $current_user_id,
                'secondUserId' => $second_user_id
            ])
        ;

        return $qb->getQuery()->getResult();
    }

    public function findConversationsByUserId (string $userId)
    {
        $qb = $this->createQueryBuilder('c');
        $qb
            ->select('secondUser.username', 'c.id as conversationId', 'lastMessage.content', 'lastMessage.createdAt')
            ->innerJoin('c.conversationsUsers', 'cuSecond', Join::WITH,
                $qb->expr()->neq('cuSecond.user', ':user')
            )
            ->innerJoin('c.conversationsUsers', 'cuFirst', Join::WITH,
                $qb->expr()->eq('cuFirst.user', ':user')
            )
            ->leftJoin('c.lastMessage', 'lastMessage')
            ->innerJoin('cuFirst.user', 'firstUser')
            ->innerJoin('cuSecond.user', 'secondUser')
            ->where('firstUser.id = :user')
            ->setParameter('user', $userId)
            ->orderBy('lastMessage.createdAt', 'DESC')
        ;

        return $qb->getQuery()->getResult();
    }

    public function checkIfUserIsParticipant(string $conversationId, string $userId)
    {
        $qb = $this->createQueryBuilder('c');
        $qb
            ->innerJoin('c.conversationsUsers', 'cu')
            ->where('c.id = :conversationId')
            ->andWhere(
                $qb->expr()->eq('cu.user', ':userId')
            )
            ->setParameters([
                'conversationId' => $conversationId,
                'userId' => $userId
            ])

        ;
        return $qb->getQuery()->getResult();
    }
}
