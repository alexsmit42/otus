<?php

namespace App\Repository;

use App\Entity\Method;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Method>
 *
 * @method Method|null find($id, $lockMode = null, $lockVersion = null)
 * @method Method|null findOneBy(array $criteria, array $orderBy = null)
 * @method Method[]    findAll()
 * @method Method[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MethodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Method::class);
    }

    public function save(Method $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Method $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
