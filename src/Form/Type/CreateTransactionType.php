<?php

namespace App\Form\Type;

use App\DTO\ManageTransactionDTO;
use App\Entity\Currency;
use App\Entity\Method;
use App\Entity\User;
use App\Enum\Direction;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateTransactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', NumberType::class)
            ->add('currency', EntityType::class, [
                'class'        => Currency::class,
                'choice_label' => 'iso',
            ])
            ->add('direction', EnumType::class, [
                'class'        => Direction::class,
                'choice_label' => fn(Direction $choice) => match ($choice) {
                    Direction::DEPOSIT => 'deposit',
                    Direction::WITHDRAW => 'withdraw',
                },
            ])
            ->add('payer', EntityType::class, [
                'class'        => User::class,
                'choice_label' => 'login',
            ])
            ->add('method', EntityType::class, [
                'class'        => Method::class,
                'choice_label' => 'name',
            ])
            ->add('paymentDetails', TextType::class)
            ->add('submit', SubmitType::class)
            ->setMethod('POST')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ManageTransactionDTO::class,
            'empty_data' => new ManageTransactionDTO(),
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'save_transaction';
    }
}