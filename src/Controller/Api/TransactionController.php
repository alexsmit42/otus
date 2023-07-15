<?php

namespace App\Controller\Api;

use App\DTO\Request\GetTransactionsFilterDTO;
use App\DTO\Response\TransactionResponseDTO;
use App\Entity\Transaction;
use App\Enum\Status;
use App\Service\TransactionService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/transaction')]
class TransactionController extends AbstractController
{
    public function __construct(
        private readonly TransactionService $transactionService,
    ) {
    }

    #[Route(path: '',  methods: ['GET'])]
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
        return $this->json(TransactionResponseDTO::fromEntity($transaction), Response::HTTP_OK);
    }

    #[Route(path: '/update-status/{id}', methods: ['PATCH'])]
    #[ParamConverter('transaction')]
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