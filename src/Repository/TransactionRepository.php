<?php

namespace App\Repository;

use App\Entity\Method;
use App\Entity\Transaction;
use App\Entity\User;
use App\Enum\Direction;
use App\Enum\Status;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function getTransactions(?User $user, ?Method $method, ?Direction $direction = null, ?Status $status = null): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('t')
            ->from(Transaction::class, 't')
            ->orderBy('t.id', 'DESC')
        ;

        if ($user) {
            $qb
                ->leftJoin('t.payer', 'u')
                ->andWhere('u.id = :user_id')
                ->setParameter('user_id', $user->getId());
        }

        if ($method) {
            $qb
                ->leftJoin('t.method', 'm')
                ->andWhere('m.id = :method_id')
                ->setParameter('method_id', $method->getId());
        }

        if ($status) {
            $qb
                ->andWhere('t.status = :status')
                ->setParameter('status', $status->value);
        }

        if ($direction) {
            $qb
                ->andWhere('t.direction = :direction')
                ->setParameter('direction', $direction->value);
        }

        return $qb->getQuery()->getResult();
    }
}
