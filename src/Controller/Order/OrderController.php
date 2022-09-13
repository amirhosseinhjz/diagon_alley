<?php

namespace App\Controller\Order;

use App\Trait\ControllerTrait;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\OrderService\OrderService;
use OpenApi\Attributes as OA;
use App\Utils\Swagger\Order;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\Order\Purchase;

#[Route('/api')]
class OrderController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        OrderService $orderService,
        SerializerInterface $serializer
    )
    {
        $this->orderService = $orderService;
        $this->serializer = $serializer;
    }

    #[Route('/order', name: 'app_order_new', methods: ['POST'])]
    #[OA\RequestBody(
        description: 'Submit an order from a cart',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: Order\SubmitOrder::class)
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns the Order-Id',
    )]
    #[OA\Response(
        response: 401,
        description: 'Invalid credentials',
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\Tag(name: 'Order')]
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
    #[OA\Response(
        response: 200,
        description: 'Returns the Order details',
        content: new OA\JsonContent(
            ref: new Model(type: Purchase::class, groups: ['Order.read'])
        ),
    )]
    #[OA\Response(
        response: 401,
        description: 'Invalid credentials',
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\Tag(name: 'Order')]
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
            $data = $this->serializer->normalize($order, 'json', ['groups' => 'Order.read']);
            $response = [
                'order' => $data,
                'message' => 'Order fetched successfully',
                'status' => Response::HTTP_OK,
            ];} catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/order', name: 'app_order_get_all', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns all the Orders',
        content: new OA\JsonContent(
            ref: new Model(type: Purchase::class, groups: ['Order.read'])
        ),
    )]
    #[OA\Response(
        response: 401,
        description: 'Invalid credentials',
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\Tag(name: 'Order')]
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
            $data = $this->serializer->normalize($orders, 'json', ['groups' => 'Order.read']);
            $response = [
                'orders' => $data,
                'message' => 'Orders fetched successfully',
                'status' => Response::HTTP_OK,
            ];
            return $this->json($response, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/order/{orderId}', name: 'app_order_cancel', methods: ['DELETE'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the Order details',
    )]
    #[OA\Response(
        response: 401,
        description: 'Invalid credentials',
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\Tag(name: 'Order')]
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
            $response = [
                'message' => 'Order cancelled successfully',
                'status' => Response::HTTP_OK,
            ];
            return $this->json($response, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/order/{orderId}/item/{orderItemId}', name: 'app_order_item_cancel', methods: ['DELETE'])]
    #[OA\Response(
        response: 200,
        description: 'Order Item cancelled successfully',
    )]
    #[OA\Response(
        response: 401,
        description: 'Invalid credentials',
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\Tag(name: 'Order')]
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
            $response = [
                'message' => 'Order item cancelled successfully',
                'status' => Response::HTTP_OK,
            ];
            return $this->json($response, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

}