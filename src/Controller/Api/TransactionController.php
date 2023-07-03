<?php

namespace App\Controller\Api;

use App\DTO\ManageTransactionDTO;
use App\Entity\Transaction;
use App\Enum\Status;
use App\Form\Type\CreateTransactionType;
use App\Form\Type\UpdateTransactionType;
use App\Manager\TransactionManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/transaction')]
class TransactionController extends AbstractController
{
    public function __construct(
        private readonly TransactionManager $transactionManager,
        private readonly FormFactoryInterface $formFactory,
    ) {
    }

    #[Route(path: '/create-transaction', name: 'create_transaction', methods: ['GET', 'POST'])]
    #[Route(path: '/update-transaction/{id}', name: 'update_transaction', methods: ['GET', 'PATCH'])]
    public function createTransactionForm(Request $request, string $_route, ?int $id = null): Response
    {
        if ($id) {
            $transaction = $this->transactionManager->getById($id);
            $dto         = ManageTransactionDTO::fromEntity($transaction);
        }

        $form = $this->formFactory->create(
            $_route === 'create_transaction' ? CreateTransactionType::class : UpdateTransactionType::class,
            $dto ?? null
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $transactionDTO = $form->getData();
            $_route === 'create_transaction'
                ? $this->transactionManager->createFromDTO($transactionDTO)
                : $this->transactionManager->updateFromDTO($transaction, $transactionDTO);
        }

        return $this->render('transaction_form.twig', [
            'form' => $form,
        ]);
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