<?php

namespace App\Manager;

use App\DTO\Request\ManageCurrencyDTO;
use App\Entity\Currency;
use App\Repository\CurrencyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class CurrencyManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    private function save(Currency $currency): bool
    {
        $this->entityManager->persist($currency);
        $this->entityManager->flush();

        return true;
    }

    public function createFromDTO(ManageCurrencyDTO $dto): Currency
    {
        if (!$currency = $this->findByIso($dto->iso)) {
            $currency = new Currency();
            $currency->setIso($dto->iso);
            $currency->setRate($dto->rate);
            $this->save($currency);
        }

        return $currency;
    }

    public function updateFromDto(int $id, ManageCurrencyDTO $dto): bool
    {
        $currency = $this->entityManager->getRepository(Currency::class)->find($id);

        if ($currency === null) {
            throw new UnprocessableEntityHttpException('Currency does not exists');
        }

        $currency->setRate($dto->rate);

        $this->save($currency);

        return true;
    }

    public function delete(Currency $currency): bool
    {
        $this->entityManager->remove($currency);
        $this->entityManager->flush();

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