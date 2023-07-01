<?php

namespace App\Repository;

use App\Entity\Currency;
use App\Entity\Transaction;
use App\Entity\User;
use App\Enum\Status;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CurrencyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Currency::class);
    }

    public function getCountUsersByCurrency(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('c.id, c.iso, COUNT(u.id) AS total')
            ->from(User::class, 'u')
            ->leftJoin('u.currency', 'c')
            ->groupBy('c.id')
            ->orderBy('total', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function getCountTransactionsByCurrency(?Status $status = null): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('c.id, c.iso, COUNT(t.id) AS total')
            ->from(Transaction::class, 't')
            ->leftJoin('t.currency', 'c')
            ->groupBy('c.id')
            ->orderBy('total', 'DESC');

        if ($status) {
            $qb
                ->where($qb->expr()->eq('t.status', ':status'))
                ->setParameter('status', $status->value);
        }

        return $qb->getQuery()->getResult();
    }

    public function getSumTransactionsByCurrency(?Status $status = null): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('c.id, c.iso, SUM(t.amount) AS sum')
            ->from(Transaction::class, 't')
            ->leftJoin('t.currency', 'c')
            ->groupBy('c.id')
            ->orderBy('total', 'DESC');

        if ($status) {
            $qb
                ->where($qb->expr()->eq('t.status', ':status'))
                ->setParameter('status', $status->value);
        }

        return $qb->getQuery()->getResult();
    }
}
