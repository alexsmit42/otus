<?php

namespace App\Controller\Api;

use App\Entity\Method;
use App\Entity\User;
use App\Enum\Direction;
use App\Manager\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/user')]
class UserController extends AbstractController
{

    public function __construct(private readonly UserManager $userManager)
    {
    }

    #[Route(path: '', methods: ['POST'])]
    public function createUser(Request $request): Response
    {
        $currency = $this->userManager->createFromRequest($request);

        return $this->json(['id' => $currency->getId()], Response::HTTP_OK);
    }

    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['PATCH'])]
    public function updateUser(Request $request, int $id): Response
    {
        $countryId = $request->query->get('country_id');

        $result = $this->userManager->update($id, $countryId);

        return $this->json(['success' => $result], $result ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }

    #[Route(path: '', methods: ['GET'])]
    public function getUsers(): Response
    {
        $users = array_map(fn(User $user) => $user->toArray(), $this->userManager->getAll());

        return $this->json($users, $users ? Response::HTTP_OK : Response::HTTP_NO_CONTENT);
    }

    #[Route(path: '/by-login/{login}', methods: ['GET'])]
    #[ParamConverter('user')]
    public function getUserByLogin(User $user): Response
    {
        return $this->json([$user->toArray()], Response::HTTP_OK);
    }

    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[ParamConverter('user')]
    public function getUserById(User $user): Response
    {
        return $this->json([$user->toArray()], Response::HTTP_OK);
    }

    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[ParamConverter('user')]
    public function deleteUser(User $user): Response
    {
        $result = $this->userManager->delete($user);

        return $this->json(['success' => $result], $result ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }

    #[Route(
        path: '/{id}/available-methods/{direction}',
        requirements: [
            'id' => '\d+',
            'direction' => 'deposit|withdraw'
        ],
        methods: ['GET'])
    ]
    #[ParamConverter('user', options: ['mapping' => ['id' => 'id']])]
    public function getAvailableMethods(User $user, string $direction): Response {
        $methods = $this->userManager->findAvailableMethods($user, Direction::fromString($direction));
        $methods = array_map(fn(Method $method) => $method->toArray(), $methods);

        return $this->json($methods);
    }
}