<?php

namespace App\Entity\Order;

use App\Entity\Shipment\ShipmentItem;
use App\Entity\Variant\Variant;
use App\Repository\OrderRepository\PurchaseItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PurchaseItemRepository::class)]
class PurchaseItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'purchaseItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Purchase $purchase = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Variant $variant = null;

    #[ORM\Column]
    private ?int $paidPrice = null;

    #[ORM\OneToOne(mappedBy: 'purchaseItem', cascade: ['persist', 'remove'])]
    private ?ShipmentItem $shipmentItem = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?int $totalPrice = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPurchase(): ?Purchase
    {
        return $this->purchase;
    }

    public function setPurchase(?Purchase $purchase): self
    {
        $this->purchase = $purchase;

        return $this;
    }

    public function getVariant(): ?Variant
    {
        return $this->variant;
    }

    public function setVariant(?Variant $variant): self
    {
        $this->variant = $variant;

        return $this;
    }

    public function getPaidPrice(): ?int
    {
        return $this->paidPrice;
    }

    public function setPaidPrice(int $paidPrice): self
    {
        $this->paidPrice = $paidPrice;

        return $this;
    }

    public function getShipmentItem(): ?ShipmentItem
    {
        return $this->shipmentItem;
    }

    public function setShipmentItem(ShipmentItem $shipmentItem): self
    {
        // set the owning side of the relation if necessary
        if ($shipmentItem->getPurchaseItem() !== $this) {
            $shipmentItem->setPurchaseItem($this);
        }

        $this->shipmentItem = $shipmentItem;
        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getTotalPrice(): ?int
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(int $totalPrice): self
    {
        $this->totalPrice = $totalPrice;
        return $this;
    }
}
