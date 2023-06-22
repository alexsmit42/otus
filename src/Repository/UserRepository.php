<?php

namespace App\Repository;

use App\Entity\Method;
use App\Entity\Product;
use App\Entity\User;
use App\Enum\Direction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * get available methods for user
     * @param User      $user
     * @param Direction $direction
     * @return array
     */
    public function findAvailableMethods(User $user, Direction $direction = Direction::DEPOSIT): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('m')
            ->from(Method::class, 'm')
            ->leftJoin('m.countries', 'c')
            ->where($qb->expr()->eq('c.id', ':country_id'))
            ->setParameter('country_id', $user->getCountry()->getId());

        // for withdrawals ara not available methods with minimal limit less than user's balance
        if ($direction === Direction::WITHDRAW) {
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
                ->setParameter('balance', $user->getBalance());
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * get available products for user
     * @param User $user
     * @return array
     */
    public function findAvailableProducts(User $user): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('p')
            ->from(Product::class, 'p')
            ->leftJoin('p.country', 'c')
            ->where('c.id = :country_id')
            ->setParameter('country_id', $user->getCountry()->getId());

        return $qb->getQuery()->getResult();
    }
}
