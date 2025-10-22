<?php

namespace App\Form;

use App\Entity\Orders;
use App\Entity\Users;
use App\Form\OrderItemType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrdersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Select an existing customer (from Users entity)
            ->add('customer', EntityType::class, [
                'class' => Users::class,
                'choice_label' => 'name',
                'placeholder' => 'Select customer',
                'label' => 'Customer',
            ])

            // Order Date (defaults to now)
            ->add('orderDate', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => 'Order Date',
                'html5' => true,
            ])

            // Total Price (calculated dynamically)
            ->add('totalPrice', MoneyType::class, [
                'currency' => 'PHP', // You can change this as needed
                'required' => false,
                'disabled' => true,
                'scale' => 2,
                'label' => 'Total Price',
                'empty_data' => '0.00',
            ])

            // Order Status
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Pending' => 'pending',
                    'Completed' => 'completed',
                    'Canceled' => 'canceled',
                ],
                'placeholder' => 'Select status',
                'label' => 'Order Status',
            ])

            // Collection of Order Items (relation to OrderItemType)
            ->add('orderItems', CollectionType::class, [
                'entry_type' => OrderItemType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'label' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Orders::class,
        ]);
    }
}
