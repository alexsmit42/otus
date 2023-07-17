<?php

namespace App\Controller\Api;

use App\DTO\Request\ManageUserDTO;
use App\Entity\Method;
use App\Entity\User;
use App\Enum\Direction;
use App\Manager\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/api/user')]
class UserController extends AbstractController
{

    public function __construct(
        private readonly UserManager $userManager,
    ) {
    }

    #[Route(path: '', methods: ['POST'])]
    public function createUser(
        #[MapRequestPayload] ManageUserDTO $dto,
    ): Response {
        $user = $this->userManager->createFromDTO($dto);

        return $this->json(['id' => $user->getId()], Response::HTTP_OK);
    }

    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['PATCH'])]
    #[ParamConverter('user')]
    #[IsGranted('ROLE_MODERATOR')]
    public function updateUser(
        User $user,
        #[MapRequestPayload] ManageUserDTO $dto,
    ): Response {
        $result = $this->userManager->updateFromDTO($user, $dto);

        return $this->json(['success' => $result], $result ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }

    #[Route(path: '', methods: ['GET'])]
    #[IsGranted('ROLE_MODERATOR')]
    public function getUsers(): Response
    {
        $users = array_map(fn(User $user) => $user->toArray(), $this->userManager->getAll());

        return $this->json($users, $users ? Response::HTTP_OK : Response::HTTP_NO_CONTENT);
    }

    #[Route(path: '/by-login/{login}', methods: ['GET'])]
    #[ParamConverter('user')]
    #[IsGranted('get_user', 'user')]
    public function getUserByLogin(User $user): Response
    {
        return $this->json($user->toArray(), Response::HTTP_OK);
    }

    #[Route(path: '/{id}/password', methods: ['PATCH'])]
    #[ParamConverter('user')]
    #[IsGranted('update_password', 'user')]
    public function updatePassword(
        User $user,
        #[MapRequestPayload] ManageUserDTO $dto,
    ): Response {
        return $this->json(['success' => $this->userManager->updatePassword($user, $dto->password)], Response::HTTP_OK);
    }

    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[ParamConverter('user')]
    #[IsGranted('get_user', 'user')]
    public function getUserById(User $user): Response
    {
        return $this->json($user->toArray(), Response::HTTP_OK);
    }

    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[ParamConverter('user')]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteUser(User $user): Response
    {
        $result = $this->userManager->delete($user);

        return $this->json(['success' => $result], $result ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }

    #[Route(
        path: '/{id}/available-methods/{direction}',
        requirements: [
            'id'        => '\d+',
            'direction' => 'deposit|withdraw',
        ],
        methods: ['GET'])
    ]
    #[ParamConverter('user', options: ['mapping' => ['id' => 'id']])]
    #[IsGranted('view_available_methods', 'user')]
    public function getAvailableMethods(User $user, string $direction): Response
    {
        $methods = $this->userManager->findAvailableMethods($user, Direction::fromString($direction));
        $methods = array_map(fn(Method $method) => $method->toArray(), $methods);

        return $this->json($methods);
    }
}