<?php

namespace App\Form;

use App\Entity\OrderItem;
use App\Entity\Products;
use App\Entity\Services;
use App\Repository\ProductsRepository;
use App\Repository\ServiceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Whether this item is a Product or Service
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Product' => 'product',
                    'Service' => 'service',
                ],
                'placeholder' => 'Select type',
                'required' => true,
                'label' => 'Item Type',
                'attr' => ['class' => 'item-type form-select'],
            ])

            // Product dropdown (auto-populated from repository)
            ->add('product', EntityType::class, [
                'class' => Products::class,
                'choice_label' => 'name',
                'placeholder' => 'Select product',
                'query_builder' => function (ProductsRepository $r) {
                    return $r->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC');
                },
                'choice_attr' => function (?Products $product) {
                    if (!$product) return [];
                    return [
                        'data-price' => (string)$product->getPrice(),
                        'data-name' => (string)$product->getName(),
                    ];
                },
                'required' => false,
                'label' => 'Product',
                'attr' => ['class' => 'item-product form-select'],
            ])

            // Service dropdown (auto-populated from repository)
            ->add('service', EntityType::class, [
                'class' => Services::class,
                'choice_label' => 'name',
                'placeholder' => 'Select service',
                'query_builder' => function (ServiceRepository $r) {
                    return $r->createQueryBuilder('s')
                        ->orderBy('s.name', 'ASC');
                },
                'choice_attr' => function (?Services $service) {
                    if (!$service) return [];
                    return [
                        'data-price' => (string)$service->getPrice(),
                        'data-name' => (string)$service->getName(),
                    ];
                },
                'required' => false,
                'label' => 'Service',
                'attr' => ['class' => 'item-service form-select'],
            ])

            // Hidden or readonly field that stores name (can be filled dynamically)
            ->add('name', TextType::class, [
                'required' => false,
                'label' => 'Item Name',
                'attr' => [
                    'readonly' => true,
                    'class' => 'item-name form-control',
                ],
            ])

            // Price field — can be auto-filled from selected product/service
            ->add('price', MoneyType::class, [
                'currency' => 'PHP',
                'scale' => 2,
                'required' => false,
                'label' => 'Price',
                'attr' => [
                    'class' => 'item-price form-control',
                    'readonly' => true,
                ],
            ])

            // Quantity input
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantity',
                'attr' => [
                    'min' => 1,
                    'class' => 'item-quantity form-control',
                ],
                'empty_data' => '1',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrderItem::class,
        ]);
    }
}
