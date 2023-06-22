<?php

namespace App\Repository;

use App\Entity\Method;
use App\Entity\Transaction;
use App\Enum\Status;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MethodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Method::class);
    }

    public function findTransactionsByStatus(
        Method $method,
        ?Status $status = null
    ): array {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('t')
            ->from(Transaction::class, 't')
            ->where($qb->expr()->eq('t.method_id', ':method_id'))
            ->setParameter('method_id', $method->getId())
            ->orderBy('t.id', 'DESC')
        ;

        if ($status) {
            $qb
                ->andWhere($qb->expr()->eq('t.status', ':status'))
                ->setParameter('status', $status->value);
        }

        return $qb->getQuery()->getResult();
    }
}
