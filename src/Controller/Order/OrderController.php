<?php

namespace App\Controller\Order;

use App\Trait\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\OrderService\OrderService;


#[Route('/api')]
class OrderController extends AbstractController
{
    use ControllerTrait;

    #[Route('/order', name: 'app_order_new', methods: ['POST'])]
    public function finalizeOrder(Request $request, OrderService $orderService)
    {
        try {
            $this->checkAccess
            (
                'ORDER_FINALIZE',
                $request->request->all()
                ,message: 'Access Denied, not the owner of the cart'
            );
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

    #[Route('/order/{orderId}', name: 'app_order_get', methods: ['GET'])]
    public function getOrder(int $orderId, OrderService $orderService)
    {
        try {
            $order = $orderService->getOrderById($orderId);
            $this->checkAccess
            (
                'ORDER_VIEW',
                $order
                ,message: 'Access Denied, not the owner of the order'
            );
            return $this->json($order, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/order', name: 'app_order_get_all', methods: ['GET'])]
    public function getAllOrders(OrderService $orderService)
    {
        try {
            $this->checkAccess
            (
                'ORDER_VIEW_ALL',
                null,
            );
            $customer = $this->getUser();
            $orders = $orderService->getCustomerOrders($customer);
            return $this->json($orders, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/order/{orderId}', name: 'app_order_cancel', methods: ['DELETE'])]
    public function cancelOrder(int $orderId, OrderService $orderService)
    {
        try {
            $order = $orderService->getOrderById($orderId);
            $this->checkAccess
            (
                'ORDER_CANCEL',
                $order
                ,message: 'Access Denied, not the owner of the order'
            );
            $orderService->cancelOrder($order);
            return $this->json('Order cancelled successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

//    cancel orderItem
    #[Route('/order/{orderId}/item/{orderItemId}', name: 'app_order_item_cancel', methods: ['DELETE'])]
    public function cancelOrderItem(int $orderId, int $orderItemId, OrderService $orderService)
    {
        try {
            $order = $orderService->getOrderById($orderId);
            $this->checkAccess
            (
                'ORDER_CANCEL',
                $order
                ,message: 'Access Denied, not the owner of the order'
            );
            $orderService->cancelOrderItemById($order, $orderItemId);
            return $this->json('Order item cancelled successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

}