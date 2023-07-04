<?php

namespace App\Form\Type;

use App\Entity\Currency;
use App\Entity\Method;
use App\Entity\Transaction;
use App\Entity\User;
use App\Enum\Direction;
use App\Validator\PaymentAccount;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class CreateTransactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', NumberType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Positive(),
                ],
            ])
            ->add('currency', EntityType::class, [
                'class'        => Currency::class,
                'choice_label' => 'iso',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('direction', EnumType::class, [
                'class'        => Direction::class,
                'choice_label' => fn($choice) => match ($choice) {
                    Direction::DEPOSIT => 'deposit',
                    Direction::WITHDRAW => 'withdraw',
                },
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('payer', EntityType::class, [
                'class'        => User::class,
                'choice_label' => 'login',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('method', EntityType::class, [
                'class'        => Method::class,
                'choice_label' => 'name',
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
            ->setMethod('POST');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
            'empty_data' => new TRansaction(),
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'save_transaction';
    }
}