<?php

namespace App\Form\Type;

use App\Entity\Method;
use App\Enum\Status;
use App\Validator\PaymentAccount;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class UpdateTransactionType extends CreateTransactionType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('method', EntityType::class, [
                'class'        => Method::class,
                'choice_label' => 'name',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('status', EnumType::class, [
                'class'        => Status::class,
                'choice_label' => fn($choice) => match ($choice) {
                    Status::NEW => 'New',
                    Status::PENDING => 'Pending',
                    Status::FAIL => 'Fail',
                    Status::SUCCESS => 'Success',
                },
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('paymentDetails', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new PaymentAccount(),
                ],
            ])
            ->add('submit', SubmitType::class)
            ->setMethod('PATCH');
    }

    public function getBlockPrefix(): string
    {
        return 'update_transaction';
    }
}