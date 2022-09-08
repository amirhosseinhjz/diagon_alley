<?php

namespace App\Entity\Shipment;

use App\Entity\User\Seller;
use App\Repository\ShipmentRepository\ShipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Entity(repositoryClass: ShipmentRepository::class)]
class Shipment
{
    const STATUS = ['CANCEL','PENDING','DELIVERED','FINALIZED'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Serializer\Groups(['shipment.read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'shipments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Serializer\Groups(['shipment.seller.read'])]
    private ?Seller $seller = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Serializer\Groups(['shipment.read','shipment.shipmentItem.read','shipment.seller.read'])]
    private ?string $status = null;

    #[ORM\OneToMany(mappedBy: 'shipment', targetEntity: ShipmentItem::class)]
    #[Serializer\Groups(['shipment.shipmentItem.read'])]
    private Collection $shipmentItems;

    public function __construct()
    {
        $this->shipmentItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeller(): ?Seller
    {
        return $this->seller;
    }

    public function setSeller(?Seller $seller): self
    {
        $this->seller = $seller;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, ShipmentItem>
     */
    public function getShipmentItems(): Collection
    {
        return $this->shipmentItems;
    }

    public function addShipmentItem(ShipmentItem $shipmentItem): self
    {
        if (!$this->shipmentItems->contains($shipmentItem)) {
            $this->shipmentItems->add($shipmentItem);
            $shipmentItem->setShipment($this);
        }

        return $this;
    }

    public function removeShipmentItem(ShipmentItem $shipmentItem): self
    {
        if ($this->shipmentItems->removeElement($shipmentItem)) {
            if ($shipmentItem->getShipment() === $this) {
                $shipmentItem->setShipment(null);
            }
        }

        return $this;
    }
}
