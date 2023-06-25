<?php

namespace App\Controller\Api;

use App\Entity\Currency;
use App\Manager\CurrencyManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/currency')]
class CurrencyController extends AbstractController
{

    public function __construct(private readonly CurrencyManager $currencyManager)
    {
    }

    #[Route(path: '', methods: ['POST'])]
    public function createCurrency(Request $request): Response
    {
        $iso  = $request->request->get('iso');
        $rate = $request->request->get('rate');

        $currency = $this->currencyManager->createOrUpdate($iso, $rate);

        return new JsonResponse(['id' => $currency->getId()], Response::HTTP_OK);
    }

    #[Route(path: '', methods: ['GET'])]
    public function getCurrencies(): Response {
        $currencies = array_map(fn(Currency $currency) => $currency->toArray(), $this->currencyManager->getAll());

        return new JsonResponse($currencies, $currencies ? Response::HTTP_OK : Response::HTTP_NO_CONTENT);
    }

    #[Route(path: '/by-iso/{iso}', methods: ['GET'])]
    #[ParamConverter('currency')]
    public function getCurrencyByIso(Currency $currency): Response {
        return new JsonResponse([$currency->toArray()], Response::HTTP_OK);
    }

    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[Entity('currency', expr: 'repository.find(id)')]
    public function getCurrency(Currency $currency): Response {
        return new JsonResponse([$currency->toArray()], Response::HTTP_OK);
    }

    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[Entity('currency', expr: 'repository.find(id)')]
    public function deleteCurrency(Currency $currency): Response {
        $result = $this->currencyManager->delete($currency);

        return new JsonResponse(['success' => $result], $result ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }

}