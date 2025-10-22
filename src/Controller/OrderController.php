<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\OrderItem;
use App\Form\OrdersType;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormError;

#[Route('/order')]
final class OrderController extends AbstractController
{
    #[Route('/', name: 'app_order_index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository): Response
    {
        return $this->render('order/index.html.twig', [
            'orders' => $orderRepository->findAll(),
        ]);
    }

    private function normalizeOrderItems(Orders $order): void
    {
        // Remove empty rows and fill name/price from chosen entity
        // Use a copy since we'll possibly add new items during iteration
        $existing = iterator_to_array($order->getOrderItems());
        foreach ($existing as $item) {
            $product = $item->getProduct();
            $service = $item->getService();

            if (!$product && !$service) {
                $order->removeOrderItem($item);
                continue;
            }

            // If both are selected in one row, split into two entries
            if ($product && $service) {
                // Current item becomes the product line
                $item->setName($product->getName());
                $item->setPrice($product->getPrice());
                $item->setService(null);
                $item->setType('product');

                // Create a new item for the service line
                $serviceItem = new OrderItem();
                $serviceItem->setOrder($order);
                $serviceItem->setService($service);
                $serviceItem->setProduct(null);
                $serviceItem->setName($service->getName());
                $serviceItem->setPrice($service->getPrice());
                $serviceItem->setQuantity($item->getQuantity());
                $order->addOrderItem($serviceItem);
            } elseif ($product) {
                $item->setName($product->getName());
                $item->setPrice($product->getPrice());
                $item->setType('product');
            } elseif ($service) {
                $item->setName($service->getName());
                $item->setPrice($service->getPrice());
                $item->setType('service');
            }

            if ($item->getQuantity() <= 0) {
                $item->setQuantity(1);
            }
        }

        $order->recalculateTotal();
    }

    private function saveOrder(Orders $order, EntityManagerInterface $em): void
    {
        // Ensure order items are valid and totals computed
        $this->normalizeOrderItems($order);

        // Persist if new, otherwise just flush
        if ($order->getId() === null) {
            $em->persist($order);
        }
        $em->flush();
    }

    private function getFormErrors($form): string
    {
        $messages = [];
        foreach ($form->getErrors(true, true) as $error) {
            $messages[] = $error->getMessage();
        }
        return implode('; ', array_unique($messages));
    }

    #[Route('/new', name: 'app_order_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $order = new Orders(); // match entity
        $form = $this->createForm(OrdersType::class, $order); // match form type
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // Normalize early so collection reflects actual intent
            $this->normalizeOrderItems($order);

            // Basic validations
            if ($order->getCustomer() === null) {
                $form->get('customer')->addError(new FormError('Please select a customer.'));
            }
            if ($order->getOrderItems()->count() === 0) {
                $form->get('orderItems')->addError(new FormError('Add at least one item (product or service).'));
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveOrder($order, $em);

            $this->addFlash('success', 'Order created successfully!');
            return $this->redirectToRoute('app_order_index');
        }

        return $this->render('order/new.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_order_show', methods: ['GET'])]
    public function show(Orders $order): Response
    {
        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_order_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Orders $order, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(OrdersType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->normalizeOrderItems($order);
            if ($order->getCustomer() === null) {
                $form->get('customer')->addError(new FormError('Please select a customer.'));
            }
            if ($order->getOrderItems()->count() === 0) {
                $form->get('orderItems')->addError(new FormError('Add at least one item (product or service).'));
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveOrder($order, $em);
            $this->addFlash('success', 'Order updated successfully!');
            return $this->redirectToRoute('app_order_index');
        } elseif ($form->isSubmitted()) {
            $this->addFlash('danger', 'Cannot update order: ' . $this->getFormErrors($form));
        }

        return $this->render('order/edit.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_order_delete', methods: ['POST'])]
    public function delete(Request $request, Orders $order, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->request->get('_token'))) {
            $em->remove($order);
            $em->flush();
            $this->addFlash('success', 'Order deleted successfully!');
        }

        return $this->redirectToRoute('app_order_index');
    }
}
