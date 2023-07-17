<?php

namespace App\Controller\Api;

use App\DTO\Request\GetTransactionsFilterDTO;
use App\DTO\Request\ManageTransactionDTO;
use App\DTO\Response\TransactionResponseDTO;
use App\Entity\Transaction;
use App\Enum\Status;
use App\Service\TransactionService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/api/transaction')]
class TransactionController extends AbstractController
{
    public function __construct(
        private readonly TransactionService $transactionService,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    #[Route(path: '', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function createTransaction(
        #[MapRequestPayload] ManageTransactionDTO $dto,
    ): Response {
        $result = $this->transactionService->createFromDTO($dto);

        return $this->json(['success' => $result], $result ? Response::HTTP_OK : Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    #[Route(path: '', methods: ['GET'])]
    public function getTransactions(
        #[MapQueryString] ?GetTransactionsFilterDTO $dto,
    ): Response {
        $transactions = $this->transactionService->getTransactions($dto ?? new GetTransactionsFilterDTO());

        return $this->json(array_map(fn(Transaction $transaction) => TransactionResponseDTO::fromEntity($transaction), $transactions));
    }

    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[ParamConverter('transaction')]
    public function getTransaction(Transaction $transaction): Response
    {
        // access only for ROLE_SUPPORT or for transactions where user is owner
        if (!$this->authorizationChecker->isGranted('get_transaction', $transaction->getPayer())) {
            throw new AuthenticationException('Access denied');
        }

        return $this->json(TransactionResponseDTO::fromEntity($transaction), Response::HTTP_OK);
    }

    #[Route(path: '/{id}/update-status', methods: ['PATCH'])]
    #[ParamConverter('transaction')]
    #[IsGranted('ROLE_SUPPORT')]
    public function updateStatus(Transaction $transaction, Request $request): Response
    {
        $status = Status::tryFrom($request->getPayload()->get('status'));

        if (!$status) {
            return $this->json(['success' => false, 'error' => 'Unknow status: ' . $request->getPayload()->get('status')], Response::HTTP_BAD_REQUEST);
        }

        if (!$this->transactionService->updateStatus($transaction, $status)) {
            return $this->json(['success' => false, 'error' => 'Status can not be changed'], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(['success' => true]);
    }
}