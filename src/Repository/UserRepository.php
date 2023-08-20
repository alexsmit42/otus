<?php

namespace App\Repository;

use App\Entity\Method;
use App\Entity\Transaction;
use App\Entity\User;
use App\Enum\Direction;
use App\Enum\Status;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    private function getMethodCriteria(Method $method): Criteria {
        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->eq('u.method', $method));
    }

    private function createQueryBuilderForAvailableMethods(User $user): QueryBuilder {
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $qb
            ->select('m')
            ->from(Method::class, 'm')
            ->leftJoin('m.countries', 'c')
            ->where($qb->expr()->eq('c.id', ':country_id'))
            ->setParameter('country_id', $user->getCountry()->getId());
    }

    /**
     * find available withdraw methods for user
     *
     * @param User $user
     * @return array
     */
    public function findAvailableMethodsForDeposit(User $user): array
    {
        return $this->createQueryBuilderForAvailableMethods($user)->getQuery()->getResult();
    }

    /**
     * find available withdraw methods for user (min_limit should be less than user's balance)
     *
     * @param User $user
     * @return array
     */
    public function findAvailableMethodsForWithdraw(User $user, float $balance): array {
        $qb = $this->createQueryBuilderForAvailableMethods($user);

        $qb
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->isNull('m.min_limit'),
                    $qb->expr()->andX(
                        $qb->expr()->isNotNull('m.min_limit'),
                        $qb->expr()->lte('m.min_limit', ':balance')
                    )
                )
            )
            ->setParameter('balance', $balance);

        return $qb->getQuery()->getResult();
    }
}
