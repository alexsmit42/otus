<?php

namespace App\Controller\Api;

use App\DTO\ManageTransactionDTO;
use App\Form\Type\CreateTransactionType;
use App\Form\Type\UpdateTransactionType;
use App\Manager\TransactionManager;
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
}