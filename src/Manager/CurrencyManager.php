<?php

namespace App\Manager;

use App\Entity\Currency;
use App\Repository\CurrencyRepository;
use Doctrine\ORM\EntityManagerInterface;

class CurrencyManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function create(string $iso, int $rate): Currency
    {
        if (!$currency = $this->findByIso($iso)) {
            $currency = new Currency();
            $currency->setIso($iso);
        }

        $currency->setRate($rate);
        $this->entityManager->persist($currency);
        $this->entityManager->flush();

        return $currency;
    }

    public function findByIso(string $iso): ?Currency {
        return $this->entityManager->getRepository(Currency::class)->findOneBy(['iso' => $iso]);
    }

    public function getCountUsersByCurrency(): array {
        /** @var CurrencyRepository $currencyRepository */
        $currencyRepository = $this->entityManager->getRepository(Currency::class);

        return $currencyRepository->getCountUsersByCurrency();
    }
}