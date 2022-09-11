<?php

namespace App\Entity\Shipment;

use App\Entity\Order\PurchaseItem;
use App\Repository\ShipmentRepository\ShipmentItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ShipmentItemRepository::class)]
class ShipmentItem
{
    const TYPES =  [
        'DIGITAL'=>'digital',
        'PHYSICAL'=>'physical',
    ];

    const STATUS = ['CANCEL','PENDING','DELIVERED','FINALIZED'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Serializer\Groups(['shipment.shipmentItem.read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'shipmentItems')]
    #[ORM\JoinColumn(nullable: false)]
    #[Serializer\Groups(['shipmentItem.shipment.read'])]
    private ?Shipment $shipment = null;

    #[ORM\OneToOne(inversedBy: 'shipmentItem')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PurchaseItem $purchaseItem = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Serializer\Groups(['shipment.shipmentItem.read'])]
    private ?string $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Serializer\Groups(['shipment.shipmentItem.read'])]
    private ?string $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShipment(): ?Shipment
    {
        return $this->shipment;
    }

    public function setShipment(?Shipment $shipment): self
    {
        $this->shipment = $shipment;

        return $this;
    }

    public function getPurchaseItem(): ?PurchaseItem
    {
        return $this->purchaseItem;
    }

    public function setPurchaseItem(PurchaseItem $purchaseItem): self
    {
        $this->purchaseItem = $purchaseItem;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }
}
