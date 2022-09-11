<?php

namespace App\Service\Shipment;

use App\Entity\Shipment\Shipment;
use App\Entity\Shipment\ShipmentItem;
use App\Interface\Order\OrderManagementInterface;
use App\Interface\Shipment\ShipmentManagementInterface;
use App\Service\UserService\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

class ShipmentManagement implements ShipmentManagementInterface
{
    protected $messageBus;

    protected $orderService;

    protected $entityManager;

    protected $userService;

    public function __construct(
        MessageBusInterface $messageBus,
        OrderManagementInterface $orderService,
        EntityManagerInterface $entityManager,
        UserService $userService,
    )
    {
        $this->dispatcher = $messageBus;
        $this->entityManager = $entityManager;
        $this->orderService = $orderService;
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
            $orderItems = $this->orderService->getPurchaseItemsBySellerIdAndPurchaseId($criteria);

            foreach ($orderItems as $orderItem)
            {
                $shipmentItem = $this->createShipmentItem($orderItem,$shipment);
                $this->digitalProductDelivery($shipmentItem,$orderItem);
            }
        }
    }

    public function changeStatus($object,$status)
    {
        if (!in_array($status,Shipment::STATUS))
        {
            throw new Exception
            (
                json_encode('not a valid shipment status'),
                code: Response::HTTP_NOT_FOUND
            );
        }
        $object->setStatus($status);
        $this->entityManager->flush();
        return $object;
    }

    public function getShipmentBySellerId($id)
    {
        if (!$this->userService->getUserById($id))
        {
            throw new Exception
            (
                json_encode('There is no seller for given id'),
                code: Response::HTTP_NOT_FOUND
            );
        }
        return $this->entityManager->getRepository(Shipment::class)->findWithSeller($id);
    }

    public function getShipmentItemById($id)
    {
        if (!$shipmentItem = $this->entityManager->getRepository(ShipmentItem::class)->find($id))
        {
            throw new Exception
            (
                json_encode('There is no shipment-item for giving id'),
                code: Response::HTTP_NOT_FOUND
            );
        }
        return $shipmentItem;
    }

    public function getShipmentById($id)
    {
        if (!$this->entityManager->getRepository(Shipment::class)->find($id))
        {
            throw new Exception
            (
                json_encode('There is no shipment for giving id'),
                code: Response::HTTP_NOT_FOUND
            );
        }
        return $this->entityManager->getRepository(Shipment::class)->find($id);
    }

    public function getShipmentItems($id)
    {
        if (!$this->entityManager->getRepository(Shipment::class)->find($id))
        {
            throw new \Exception(json_encode('There is no shipment for given id'),Response::HTTP_NOT_FOUND);
        }
        return $this->entityManager->getRepository(Shipment::class)->findWithItems($id);
    }

    private function createShipmentItem($fields,$shipment)
    {
        $shipmentItem = new ShipmentItem();
        $shipmentItem->setPurchaseItem($this->orderService->getPurchaseItemById($fields['purchase_item_id']));
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

    private function digitalProductDelivery(ShipmentItem $shipmentItem, $orderItem)
    {
        if ($shipmentItem->getType() === ShipmentItem::TYPES['DIGITAL'])
        {
//            Todo take files from $orderItem id and set status to shipmentItem delivered
//            $this->fileShipment($file);
            return;
        }
    }

    private function fileShipment($file)
    {
//        TODO send email
    }
}