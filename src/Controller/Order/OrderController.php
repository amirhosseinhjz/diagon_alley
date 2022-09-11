<?php

namespace App\Controller\Order;

use \Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\OrderService\OrderService;

class OrderController extends AbstractController
{
    #[Route('/api/order', name: 'app_order_new', methods: ['POST'])]
    public function finalizeOrder(Request $request, OrderService $orderService)
    {
        try {
            $orderId = $orderService->submitOrder($request->request->all());

            $response = [
                'orderId' => $orderId,
                'message' => 'Order created successfully',
                'status' => Response::HTTP_CREATED,
            ];
            return $this->json($response, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/order/{orderId}', name: 'app_order_get', methods: ['GET'])]
    public function getOrder(int $orderId, OrderService $orderService)
    {
        try {
            $order = $orderService->getOrderById($orderId);

            return $this->json($order, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

}