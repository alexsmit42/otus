<?php

namespace App\Form\Type;

use App\Entity\Method;
use App\Enum\Status;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UpdateTransactionType extends CreateTransactionType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('method', EntityType::class, [
                'class'        => Method::class,
                'choice_label' => 'name',
            ])
            ->add('status', EnumType::class, [
                'class'        => Status::class,
                'choice_label' => fn(Status $choice) => match ($choice) {
                    Status::NEW => 'New',
                    Status::PENDING => 'Pending',
                    Status::FAIL => 'Fail',
                    Status::SUCCESS => 'Success',
                },
            ])
            ->add('paymentDetails', TextType::class)
            ->add('submit', SubmitType::class)
            ->setMethod('PATCH');
    }

    public function getBlockPrefix(): string
    {
        return 'update_transaction';
    }
}