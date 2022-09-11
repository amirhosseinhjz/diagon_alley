<?php

namespace App\Controller\Shipment;

use App\DTO\ShipmentDTO\ShipmentAndShipmentItemUpdateDTO;
use App\Interface\Shipment\ShipmentManagementInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

#[Route('/api',name: '_api_shipment_')]
class ShipmentController extends AbstractController
{
    public $managementShipment;

    public $serializer;

    private ValidatorInterface $validator;

    public function __construct(
        ShipmentManagementInterface $managementShipment,
        ValidatorInterface $validator,
        SerializerInterface $serializer
    )
    {
        $this->managementShipment = $managementShipment;

        $this->validator = $validator;

        $this->serializer = $serializer;
    }

    #[Route('/shipment/{id}/shipment-items', name: 'app_shipment_items_show',methods: ['GET'])]
    public function shipmentItemIndex($id): Response
    {
        $this->denyAccessUnlessGranted
        (
            'SHIPMENT_ACCESS',
            subject: $this->managementShipment->getShipmentById($id)
            ,message: 'Access Denied, not the owner of the shipment'
        );
        try {
            $shipmentItems = $this->managementShipment->getShipmentItems($id);
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

    #[Route('/shipment-seller/{id}', name: 'app_shipment_seller',methods: ['GET'])]
    public function shipmentSellerIndex($id): Response
    {
        $this->denyAccessUnlessGranted
        (
            'SHIPMENT_ACCESS',
            subject: $this->managementShipment->getShipmentBySellerId($id),
            message:  'Access Denied, not the owner of the shipment'
        );
        try {
            $shipment = $this->managementShipment->getShipmentBySellerId($id);
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

    #[Route('/shipment/{id}', name: 'app_shipment_status_update',methods: ['PUT','PATCH'])]
    public function shipmentStatusUpdate(Request $request,$id): Response
    {
        $this->denyAccessUnlessGranted
        (
            'SHIPMENT_ACCESS',
            subject: $this->managementShipment->getShipmentById($id),
            message:  'Access Denied, not the owner of the shipment'
        );
        try {
            $request = $request->toArray();
            (new ShipmentAndShipmentItemUpdateDTO($request,$this->validator))
                ->doValidate();
            $shipment = $this->managementShipment->changeStatus
            (
                $this->managementShipment->getShipmentById($id),
                $request['status']
            );
            $data = $this->serializer->normalize($shipment, null, ['groups' => ['shipment.read']]);
            return $this->json
            (
                ['shipment' => $data],
                status: 200
            );
        } catch (\Throwable $exception){
            return $this->json(json_decode($exception->getMessage()));
        }
    }

    #[Route('/shipment-item/{id}', name: 'app_shipment_item_status_update',methods: ['PUT','PATCH'])]
    public function shipmentItemStatusUpdate(Request $request,$id): Response
    {
        $this->denyAccessUnlessGranted
        (
            'SHIPMENT_ITEM_ACCESS',
            subject: $this->managementShipment->getShipmentItemById($id),
            message:  'Access Denied, not the owner of the shipment-item'
        );
        try {
            $request = $request->toArray();
            (new ShipmentAndShipmentItemUpdateDTO($request,$this->validator))
                ->doValidate();
            $shipment = $this->managementShipment->changeStatus
            (
                $this->managementShipment->getShipmentItemById($id),
                $request['status']
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
}
