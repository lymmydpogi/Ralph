<?php

namespace App\Form;

use App\Entity\Orders;
use App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use App\Form\OrderItemType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrdersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Select an existing user as the customer
            ->add('customer', EntityType::class, [
                'class' => Users::class,
                'choice_label' => 'name',
                'placeholder' => 'Select customer',
            ])
            ->add('orderDate', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('totalPrice', null, [
                'required' => false,
                'disabled' => true,
                'empty_data' => '0.00',
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Pending' => 'pending',
                    'Completed' => 'completed',
                    'Canceled' => 'canceled',
                ],
                'placeholder' => 'Select status',
            ])
            ->add('orderItems', CollectionType::class, [
                'entry_type' => OrderItemType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'label' => false,
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Orders::class,
        ]);
    }
}
