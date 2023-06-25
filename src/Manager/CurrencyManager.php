<?php

namespace App\Manager;

use App\Entity\Currency;
use App\Repository\CurrencyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class CurrencyManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function createOrUpdate(string $iso, float $rate): Currency
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

    public function delete(Currency $currency): bool {
        try {
            $this->entityManager->remove($currency);
            $this->entityManager->flush();
        } catch (Exception) {
            // TODO: log/message error
            return false;
        }

        return true;
    }

    public function deleteById(int $id): bool {
        /** @var CurrencyRepository $currencyRepository */
        $currencyRepository = $this->entityManager->getRepository(Currency::class);

        /** @var Currency $currency */
        if (!$currency = $currencyRepository->find($id)) {
            return false;
        }

        try {
            $this->entityManager->remove($currency);
            $this->entityManager->flush();
        } catch (Exception) {
            return false;
        }

        return true;
    }

    public function findByIso(string $iso): ?Currency {
        return $this->entityManager->getRepository(Currency::class)->findOneBy(['iso' => $iso]);
    }

    public function getAll(): array {
        /** @var CurrencyRepository $currencyRepository */
        $currencyRepository = $this->entityManager->getRepository(Currency::class);

        return $currencyRepository->findAll();
    }

    public function getCountUsersByCurrency(): array {
        /** @var CurrencyRepository $currencyRepository */
        $currencyRepository = $this->entityManager->getRepository(Currency::class);

        return $currencyRepository->getCountUsersByCurrency();
    }
}