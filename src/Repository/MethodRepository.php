<?php

namespace App\Repository;

use App\Entity\Country;
use App\Entity\Method;
use App\Entity\Transaction;
use App\Enum\Status;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
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

    public function countMethodsByCountry(Country $country): int {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('COUNT(m.id)')
            ->from(Method::class, 'm')
            ->leftJoin('m.countries', 'c')
            ->where($qb->expr()->eq('c.id', ':country_id'))
            ->setParameter('country_id', $country->getId())
            ->groupBy('m.id')
        ;

        try {
            return $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException) {
            return 0;
        }
    }
}
