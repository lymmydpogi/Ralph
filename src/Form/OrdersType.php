<?php

namespace App\Form;

use App\Entity\Orders;
use App\Entity\Services;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrdersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customerName')
            ->add('customerEmail')
            ->add('customerPhone')
            ->add('orderDate')
            ->add('totalPrice')
            ->add('service', EntityType::class, [
                'class' => Services::class,
                'choice_label' => 'name', // use 'name' for readability instead of id
        ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Orders::class,
        ]);
    }
}
