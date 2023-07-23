<?php

namespace App\Controller\Api;

use App\DTO\Request\ManageMethodDTO;
use App\Entity\Country;
use App\Entity\Method;
use App\Manager\MethodManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/api/method')]
//#[IsGranted('ROLE_VIEW')]
class MethodController extends AbstractController
{

    public function __construct(private readonly MethodManager $methodManager)
    {
    }

    #[Route(path: '', methods: ['POST'])]
    #[IsGranted('ROLE_MODERATOR')]
    public function createMethod(
        #[MapRequestPayload] ManageMethodDTO $dto,
    ): Response {
        $country = $this->methodManager->createFromDTO($dto);

        return $this->json(['id' => $country->getId()], Response::HTTP_OK);
    }

    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['PATCH'])]
    #[IsGranted('ROLE_MODERATOR')]
    public function updateMethod(
        int $id,
        #[MapRequestPayload] ManageMethodDTO $dto,
    ): Response {
        $result = $this->methodManager->updateFromDTO($id, $dto);

        return $this->json(['success' => $result], $result ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }

    #[Route(path: '', methods: ['GET'])]
    public function getMethods(): Response
    {
        $methods = array_map(fn(Method $method) => $method->toArray(), $this->methodManager->getAll());

        return $this->json($methods, $methods ? Response::HTTP_OK : Response::HTTP_NO_CONTENT);
    }

    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[ParamConverter('method')]
    public function getMethod(Method $method): Response
    {
        return $this->json([$method->toArray()], Response::HTTP_OK);
    }

    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[Entity('method')]
    #[IsGranted('ROLE_MODERATOR')]
    public function deleteMethod(Method $method): Response
    {
        $result = $this->methodManager->delete($method);

        return new JsonResponse(['success' => $result], $result ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }

    #[Route(
        path: '/{method_id}/country/{country_id}',
        requirements: ['method_id' => '\d+', 'country_id' => '\d+'],
        methods: ['POST'])
    ]
    #[ParamConverter('method', options: ['mapping' => ['method_id' => 'id']])]
    #[ParamConverter('country', options: ['mapping' => ['country_id' => 'id']])]
    #[IsGranted('ROLE_MODERATOR')]
    public function addCountryToMethod(Method $method, Country $country): Response
    {
        $result = $this->methodManager->addCountry($method, $country);

        return new JsonResponse(['success' => $result], $result ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }

    #[Route(
        path: '/{method_id}/country/{country_id}',
        requirements: ['method_id' => '\d+', 'country_id' => '\d+'],
        methods: ['DELETE'])
    ]
    #[ParamConverter('method', options: ['mapping' => ['method_id' => 'id']])]
    #[ParamConverter('country', options: ['mapping' => ['country_id' => 'id']])]
    #[IsGranted('ROLE_MODERATOR')]
    public function removeCountryFromMethod(Method $method, Country $country): Response
    {
        $result = $this->methodManager->removeCountry($method, $country);

        return new JsonResponse(['success' => $result], $result ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }
}