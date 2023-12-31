<?php

namespace App\Controller\Api;

use App\DTO\Request\ManageCountryDTO;
use App\Entity\Country;
use App\Manager\CountryManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/api/country')]
#[IsGranted('ROLE_VIEW')]
class CountryController extends AbstractController
{

    public function __construct(private readonly CountryManager $countryManager)
    {
    }

    #[Route(path: '', methods: ['POST'])]
    #[IsGranted('ROLE_MODERATOR')]
    public function createCountry(
        #[MapRequestPayload] ManageCountryDTO $countryRequestDTO,
    ): JsonResponse {
        $country = $this->countryManager->createfromDTO($countryRequestDTO);

        return $this->json(['id' => $country->getId()], Response::HTTP_OK);
    }

    #[Route(path: '', methods: ['GET'])]
    public function getCountries(): Response
    {
        $countries = array_map(fn(Country $country) => $country->toArray(), $this->countryManager->getAll());

        return new JsonResponse($countries, $countries ? Response::HTTP_OK : Response::HTTP_NO_CONTENT);
    }

    #[Route(path: '/by-name/{name}', methods: ['GET'])]
    public function getCountryByName(string $name): Response
    {
        $country = $this->countryManager->findByName($name);

        return new JsonResponse([$country->toArray()], Response::HTTP_OK);
    }

    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[ParamConverter('country')]
    public function getCountry(Country $country): Response
    {
        return new JsonResponse([$country->toArray()], Response::HTTP_OK);
    }

    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[Entity('country')]
    #[IsGranted('ROLE_MODERATOR')]
    public function deleteCountry(Country $country): Response
    {
        $result = $this->countryManager->delete($country);

        return new JsonResponse(['success' => $result], $result ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }
}