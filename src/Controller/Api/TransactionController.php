<?php

namespace App\Controller\Api;

use App\Entity\Transaction;
use App\Enum\Status;
use App\Manager\TransactionManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/transaction')]
class TransactionController extends AbstractController
{
    public function __construct(
        private readonly TransactionManager $transactionManager,
    ) {
    }

    #[Route(path: '/update-status/{id}', methods: ['PATCH'])]
    #[ParamConverter('transaction')]
    public function updateStatus(Transaction $transaction, Request $request): Response
    {
        $status = Status::tryFrom($request->getPayload()->get('status'));

        if (!$status) {
            return $this->json(['success' => false, 'error' => 'Unknow status: ' . $request->getPayload()->get('status')], Response::HTTP_BAD_REQUEST);
        }

        if (!$this->transactionManager->updateStatus($transaction, $status)) {
            return $this->json(['success' => false, 'error' => 'Status can not be changed'], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(['success' => true]);
    }
}