<?php

namespace App\Form;

use App\Entity\Products;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('price', MoneyType::class, [
                'currency' => 'USD',
                'divisor' => 1,
                'scale' => 2,
            ])
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'Casual Shoes' => 'casual shoes',
                    'Formal Shoes' => 'formal shoes',
                    'Boots' => 'boots',
                    "Women's Heels" => 'womens heels',
                ],
                'placeholder' => 'Select category',
            ])
            ->add('isActive', CheckboxType::class, [
                'required' => false,
                'label' => 'Active',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
