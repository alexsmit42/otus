<?php

namespace App\Manager;

use App\DTO\ManageCurrencyDTO;
use App\Entity\Currency;
use App\Repository\CurrencyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class CurrencyManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function createFromDTO(ManageCurrencyDTO $dto): Currency
    {
        if (!$currency = $this->findByIso($dto->getIso())) {
            $currency = new Currency();
            $currency->setIso($dto->getIso());
        }

        $currency->setRate($dto->getRate());
        $this->entityManager->persist($currency);
        $this->entityManager->flush();

        return $currency;
    }

    public function update(int $id, float $rate): bool
    {
        $currency = $this->entityManager->getRepository(Currency::class)->find($id);

        if (!$currency) {
            return false;
        }

        $currency->setRate($rate);
        $this->entityManager->flush();

        return true;
    }

    public function delete(Currency $currency): bool
    {
        try {
            $this->entityManager->remove($currency);
            $this->entityManager->flush();
        } catch (Throwable) {
            // TODO: log/message error
            return false;
        }

        return true;
    }

    public function findByIso(string $iso): ?Currency
    {
        return $this->entityManager->getRepository(Currency::class)->findOneBy(['iso' => strtoupper($iso)]);
    }

    public function getAll(): array
    {
        return $this->entityManager->getRepository(Currency::class)->findAll();
    }

    public function getCountUsersByCurrency(): array
    {
        /** @var CurrencyRepository $currencyRepository */
        $currencyRepository = $this->entityManager->getRepository(Currency::class);

        return $currencyRepository->getCountUsersByCurrency();
    }
}