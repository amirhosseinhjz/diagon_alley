<?php

namespace App\Controller\Shipment;

use App\Entity\Shipment\Shipment;
use App\Entity\Shipment\ShipmentItem;
use App\Interface\Shipment\ShipmentManagementInterface;
use App\Trait\ControllerTrait;
use Nelmio\ApiDocBundle\Annotation\Model;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;
use App\Service\OrderService\OrderService;
use App\Service\Wallet\WalletService;

#[Route('/api',name: '_api_shipment_')]
class ShipmentController extends AbstractController
{
    use ControllerTrait;

    public $managementShipment;

    public $serializer;

    private ValidatorInterface $validator;

    private OrderService $orderService;

    public function __construct(
        ShipmentManagementInterface $managementShipment,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        OrderService $orderService,
        WalletService $walletService
    )
    {
        $this->managementShipment = $managementShipment;

        $this->validator = $validator;

        $this->serializer = $serializer;

        $this->orderService = $orderService;

        $this->walletService = $walletService;
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns the feature information',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: ShipmentItem::class, groups: ['shipment.shipmentItem.read']))
        ),
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Invalid Request',
    )]
    #[OA\Response(
        response: Response::HTTP_FORBIDDEN,
        description: 'forbidden',
    )]
    #[OA\Tag(name: 'Shipment')]
    #[Route('/shipment/{id}/shipment-items', name: 'app_shipment_items_show',methods: ['GET'])]
    public function shipmentItemIndex($id): Response
    {
        try {
            $shipmentItems = $this->managementShipment->getShipmentItems($id);
            $this->checkAccess
            (
                'SHIPMENT_ACCESS',
                $shipmentItems
                ,message: 'Access Denied, not the owner of the shipment'
            );
            $data = $this->serializer->normalize($shipmentItems, null, ['groups' => ['shipment.shipmentItem.read']]);
            return $this->json
            (
                ['shipment' => $data],
                status: 200
            );
        } catch (\Throwable $exception){
            return $this->json(json_decode($exception->getMessage()));
        }
    }

    #[OA\Response(
        response: 200,
        description: 'Returns the feature information',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Shipment::class, groups: ['shipment.seller.read','shipment.read']))
        ),
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Invalid Request',
    )]
    #[OA\Response(
        response: Response::HTTP_FORBIDDEN,
        description: 'forbidden',
    )]
    #[OA\Tag(name: 'Shipment')]
    #[Route('/shipment-seller/{id}', name: 'app_shipment_seller',methods: ['GET'])]
    public function shipmentSellerIndex($id): Response
    {
        try {
            $shipment = $this->managementShipment->getShipmentBySellerId($id);
            $this->checkAccess
            (
                'SHIPMENT_ACCESS',
                $shipment,
                message:  'Access Denied, not the owner of the shipment'
            );
            $data = $this->serializer->normalize($shipment, null, ['groups' => ['shipment.seller.read','shipment.read']]);
            return $this->json
            (
                ['shipment' => $data],
                status: 200
            );
        } catch (\Throwable $exception){
            return $this->json(json_decode($exception->getMessage()));
        }
    }

    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'There is no shipment for given id',
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns the feature information',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Shipment::class, groups: ['shipment.read']))
        ),
    )]
    #[OA\Tag(name: 'Shipment')]
    #[Route('/shipment/{id}/cancel', name: 'app_shipment_status_update_cancel',methods: ['PUT','PATCH'])]
    public function shipmentStatusUpdateCancel($id): Response
    {
        try {
            $shipment = $this->managementShipment->getShipmentById($id);
            $this->checkAccess
            (
                'SHIPMENT_ACCESS',
                $shipment,
                message:  'Access Denied, not the owner of the shipment'
            );
            $data = $this->managementShipment->changeStatusShipmentToCancel
            (
                $shipment
            );
            $orderIds = $data['orderItemIds'];
            $this->orderService->cancelMultipleOrderItems($orderIds, $this->walletService);
            $shipmentRefresh = $data['shipment'];
            $data = $this->serializer->normalize($shipmentRefresh, null, ['groups' => ['shipment.read']]);
            return $this->json
            (
                ['shipment' => $data],
                status: 200
            );
        } catch (\Throwable $exception){
            return $this->json(json_decode($exception->getMessage()));
        }
    }

    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'There is no shipment-item for given id',
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns the feature information',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: ShipmentItem::class, groups: ['shipment.shipmentItem.read']))
        ),
    )]
    #[OA\Tag(name: 'Shipment')]
    #[Route('/shipment-item/{id}/cancel', name: 'app_shipment_item_status_update_cancel',methods: ['PUT','PATCH'])]
    public function shipmentItemStatusUpdate($id): Response
    {
        try {
            $shipmentItem = $this->managementShipment->getShipmentItemById($id);
            $this->checkAccess
            (
                'SHIPMENT_ITEM_ACCESS',
                $shipmentItem,
                message:  'Access Denied, not the owner of the shipment-item'
            );
            $shipment = $this->managementShipment->changeStatusShipmentItemCancel
            (
                $shipmentItem
            );
            $this->orderService->cancelItemById($shipmentItem->getOrderItem()->getId(), $this->walletService);
            $data = $this->serializer->normalize($shipment, null, ['groups' => ['shipment.shipmentItem.read']]);
            return $this->json
            (
                ['shipmentItem' => $data],
                status: 200
            );
        } catch (\Throwable $exception){
            return $this->json(json_decode($exception->getMessage()));
        }
    }

    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'There is no shipment-item for given id',
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns the feature information',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: ShipmentItem::class, groups: ['shipment.shipmentItem.read']))
        ),
    )]
    #[OA\Tag(name: 'Shipment')]
    #[Route('/shipment-item/{id}/finalized', name: 'app_shipment_item_status_update_finalized',methods: ['PUT','PATCH'])]
    public function changeStatusShipmentItemFinalized($id): Response
    {
        try {
            $shipmentItem = $this->managementShipment->getShipmentItemById($id);
            $this->checkAccess
            (
                'SHIPMENT_ITEM_ACCESS',
                $shipmentItem,
                message:  'Access Denied, not the owner of the shipment-item'
            );
            $shipment = $this->managementShipment->changeStatusShipmentItemFinalized
            (
                $shipmentItem
            );
            $data = $this->serializer->normalize($shipment, null, ['groups' => ['shipment.shipmentItem.read']]);
            return $this->json
            (
                ['shipmentItem' => $data],
                status: 200
            );
        } catch (\Throwable $exception){
            return $this->json(json_decode($exception->getMessage()));
        }
    }

    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'There is no shipment for given id',
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'One of the items is Canceled,there is no way to update shipment status to finalized for all items',
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns the feature information',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: ShipmentItem::class|Shipment::class, groups: ['shipment.shipmentItem.read'|'shipment.read']))
        ),
    )]
    #[OA\Tag(name: 'Shipment')]
    #[Route('/shipment/{id}/finalize', name: 'app_shipment_status_update_finalize',methods: ['GET'])]
    public function shipmentFinalize($id): Response
    {
        try {
            $shipment = $this->managementShipment->getShipmentById($id);
            $this->checkAccess
            (
                'SHIPMENT_ACCESS',
                $shipment,
                message:  'Access Denied, not the owner of the shipment'
            );
            $data = $this->managementShipment->changeStatusFinalizedForShipment
            (
                $shipment
            );
            $orderItemIds = $data['orderItemIds'];
            $this->orderService->deliverMultipleOrderItems($orderItemIds, $this->walletService);
            $shipmentRefresh = $data['shipment'];
            $data = $this->serializer->normalize($shipmentRefresh, null, ['groups' => ['shipment.read']]);
            return $this->json
            (
                ['shipment' => $data],
                status: 200
            );
        } catch (\Throwable $exception){
            return $this->json(json_decode($exception->getMessage()));
        }
    }
}
