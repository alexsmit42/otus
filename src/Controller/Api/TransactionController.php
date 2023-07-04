<?php

namespace App\Controller\Api;

use App\Entity\Transaction;
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
    #[Route(path: '/update-transaction/{id}', name: 'update_transaction', requirements: ['id' => '\d+'], methods: ['GET', 'PATCH'])]
    #[ParamConverter('transaction')]
    public function createTransactionForm(Request $request, string $_route, ?Transaction $transaction = null): Response
    {
        $form = $this->formFactory->create(
            $_route === 'create_transaction' ? CreateTransactionType::class : UpdateTransactionType::class,
            $transaction
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $transaction = $form->getData();

            $this->transactionManager->save($transaction);
        }

        return $this->render('transaction_form.twig', [
            'form' => $form,
        ]);
    }
}