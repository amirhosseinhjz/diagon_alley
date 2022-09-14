<?php

namespace App\Message;

final class SendVirtualMessage
{
     private $shipmentItemId;

     private $purchaseId;

     public function __construct($shipmentItemId, $purchaseId)
     {
         $this->shipmentItemId = $shipmentItemId;

         $this->purchaseId = $purchaseId;
     }

    /**
     * @return mixed
     */
    public function getShipmentItemId()
    {
        return $this->shipmentItemId;
    }

    /**
     * @return mixed
     */
    public function getPurchaseId()
    {
        return $this->purchaseId;
    }
}
