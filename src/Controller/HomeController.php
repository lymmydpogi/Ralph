<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(OrderRepository $orders): Response
    {
        // Fetch the 5 most recent orders by orderDate (fallback to id if needed)
        $recentOrders = $orders->createQueryBuilder('o')
            ->leftJoin('o.customer', 'c')
            ->addSelect('c')
            ->orderBy('o.orderDate', 'DESC')
            ->addOrderBy('o.id', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        // Build recent activity entries from orders
        $recentActivity = [];
        foreach ($recentOrders as $o) {
            $recentActivity[] = [
                'text' => sprintf(
                    'Order #%d %s by %s • $%s',
                    $o->getId(),
                    strtolower((string)$o->getStatus() ?: 'pending'),
                    $o->getCustomer()?->getName() ?? 'Unknown',
                    $o->getTotalPrice() ?? '0.00'
                ),
                'date' => $o->getOrderDate(),
                'status' => strtolower((string)$o->getStatus() ?: 'pending'),
            ];
        }

        return $this->render('home/index.html.twig', [
            'recent_orders' => $recentOrders,
            'recent_activity' => $recentActivity,
        ]);
    }
}
