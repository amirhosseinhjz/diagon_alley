<?php

namespace App\Service\Shipment;

use App\Entity\Shipment\Shipment;
use App\Entity\Shipment\ShipmentItem;
use App\Entity\User;
use App\Interface\Shipment\ShipmentManagementInterface;
use App\Message\SendBookMessage;
use App\Service\UserService\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ShipmentManagement implements ShipmentManagementInterface
{
    protected $messageBus;

    protected $userService;

    protected $entityManager;

    public function __construct(
        MessageBusInterface $messageBus,
        UserService $userService,
        EntityManagerInterface $entityManager
    )
    {
        $this->dispatcher = $messageBus;
        $this->entityManager = $entityManager;
        $this->userService = $userService;
    }

    public function add($purchaseId)
    {
        $sellerIds = $this->userService->getSellerIdsByPurchaseId($purchaseId);
        $ids = $this->arrayFlatten($sellerIds);
        foreach ($ids as $id)
        {
            $seller = $this->userService->getUserById($id);
            $shipment = $this->createShipment($seller);
            $criteria = ['purchaseId'=>$purchaseId,'sellerId'=>$id];
            $orderItems = $this->userService->getSellerIdsByPurchaseId($criteria);

            foreach ($orderItems as $orderItem)
            {
                $shipmentItem = $this->createShipmentItem($orderItem,$shipment);
                $this->pushDigitalOrdersToQueue($shipmentItem,$purchaseId);
            }
        }
    }

    public function changeStatus(Shipment|ShipmentItem $object,$status)
    {
        if(in_array($status,Shipment::STATUS))
        {
            $object->setStatus($status);
            $this->entityManager->flush();
        }
    }

    public function digitalProductDelivery()
    {

    }

    private function getByType()
    {

    }

    private function createShipmentItem($fields,$shipment)
    {
        $shipmentItem = new ShipmentItem();
        $shipmentItem->setPurchaseItem($this->userService->getPurchaseItemById($fields['purchase_item_id']));
        $shipmentItem->setShipment($shipment);
        $shipmentItem->setType($fields['type']);
        $shipmentItem->setStatus('PENDING');
        $this->entityManager->persist($shipmentItem);
        $this->entityManager->flush();
        return $shipmentItem;
    }

    private function createShipment($seller)
    {
        $shipment = new Shipment();
        $shipment->setSeller($seller);
        $shipment->setStatus('PENDING');
        $this->entityManager->persist($shipment);
        $this->entityManager->flush();
        return $shipment;
    }

    private function arrayFlatten($sellerIds)
    {
        $callback = function ($sellers)
        {
            foreach ($sellers as $seller)
            {
                return $seller;
            }
        };
        return array_map($callback,$sellerIds);
    }

    private function pushDigitalOrdersToQueue(ShipmentItem $shipmentItem, $purchaseId)
    {
        if ($shipmentItem->getType() === ShipmentItem::TYPES['DIGITAL'])
        {
            $this->messageBus->dispatch(new SendBookMessage($shipmentItem->getId() , $purchaseId));
        }
    }
}