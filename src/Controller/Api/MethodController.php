<?php

namespace App\Controller\Api;

use App\Entity\Country;
use App\Entity\Method;
use App\Manager\MethodManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/method')]
class MethodController extends AbstractController
{

    public function __construct(private readonly MethodManager $methodManager)
    {
    }

    #[Route(path: '', methods: ['POST'])]
    public function createMethod(Request $request): Response
    {
        $name     = $request->request->get('name');
        $minLimit = $request->request->get('min_limit');
        $maxLimit = $request->request->get('max_limit');

        $country = $this->methodManager->createOrUpdate($name, $minLimit, $maxLimit);

        return $this->json(['id' => $country->getId()], Response::HTTP_OK);
    }

    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['PATCH'])]
    public function updateMethod(Request $request, int $id): Response
    {
        $minLimit = $request->query->get('min_limit');
        $maxLimit = $request->query->get('max_limit');

        $result = $this->methodManager->update($id, $minLimit, $maxLimit);

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
    public function removeCountryFromMethod(Method $method, Country $country): Response
    {
        $result = $this->methodManager->removeCountry($method, $country);

        return new JsonResponse(['success' => $result], $result ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }
}