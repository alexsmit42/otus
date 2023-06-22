<?php

namespace App\Repository;

use App\Entity\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }

    public function getCountMethodsByCountry(): array {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('c.id, c.name, COUNT(m.id) AS total')
            ->from(Country::class, 'c')
            ->leftJoin('c.methods', 'm')
            ->groupBy('c.id')
            ->orderBy('total', 'DESC')
        ;

        return $qb->getQuery()->getResult();
    }
}