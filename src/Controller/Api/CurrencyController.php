<?php

namespace App\Controller\Api;

use App\DTO\ManageCurrencyDTO;
use App\Entity\Currency;
use App\Manager\CurrencyManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(path: '/api/currency')]
class CurrencyController extends AbstractController
{

    public function __construct(private readonly CurrencyManager $currencyManager)
    {
    }

    #[Route(path: '', methods: ['POST'])]
    public function createCurrency(Request $request, ValidatorInterface $validator): Response
    {
        $dto = (new ManageCurrencyDTO())->fromRequest($request);

        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(['success' => false, 'errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $currency = $this->currencyManager->createFromDTO($dto);

        return $this->json(['id' => $currency->getId()], Response::HTTP_OK);
    }

    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['PATCH'])]
    public function updateCurrency(Request $request, int $id): Response
    {
        $rate = $request->query->get('rate');

        $result = $this->currencyManager->update($id, $rate);

        return $this->json(['success' => $result], $result ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }

    #[Route(path: '', methods: ['GET'])]
    public function getCurrencies(): Response
    {
        $currencies = array_map(fn(Currency $currency) => $currency->toArray(), $this->currencyManager->getAll());

        return $this->json($currencies, $currencies ? Response::HTTP_OK : Response::HTTP_NO_CONTENT);
    }

    #[Route(path: '/by-iso/{iso}', methods: ['GET'])]
    public function getCurrencyByIso(string $iso): Response
    {
        $currency = $this->currencyManager->findByIso($iso);

        return $this->json([$currency->toArray()], Response::HTTP_OK);
    }

    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[Entity('currency', expr: 'repository.find(id)')]
    public function getCurrency(Currency $currency): Response
    {
        return $this->json([$currency->toArray()], Response::HTTP_OK);
    }

    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[Entity('currency', expr: 'repository.find(id)')]
    public function deleteCurrency(Currency $currency): Response
    {
        $result = $this->currencyManager->delete($currency);

        return $this->json(['success' => $result], $result ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }

}