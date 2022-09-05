<?php

namespace App\Entity\User;

use App\Entity\Shipment\Shipment;
use App\Repository\UserRepository\SellerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: SellerRepository::class)]
#[UniqueEntity(fields: ["shopSlug"], message: "This shopSlug is already in use")]
class Seller extends User
{
    const SELLER = 'ROLE_SELLER';

    #[ORM\Column(length: 255, unique: true)]
    private ?string $shopSlug = null;

    #[ORM\OneToMany(mappedBy: 'seller', targetEntity: Shipment::class)]
    private Collection $shipments;

    public function __construct()
    {
        $this->shipments = new ArrayCollection();
    }

    public function getRoles(): array
    {
        return [self::SELLER];
    }
    public function getShopSlug(): ?string
    {
        return $this->shopSlug;
    }

    public function setShopSlug(string $shopSlug): self
    {
        $this->shopSlug = $shopSlug;

        return $this;
    }


    public function eraseCredentials()
    {

    }

    public function getUserIdentifier() : string
    {
        return $this->getPhoneNumber();
    }

    /**
     * @return Collection<int, \App\Entity\Shipment\Shipment>
     */
    public function getShipments(): Collection
    {
        return $this->shipments;
    }

    public function addShipment(Shipment $shipment): self
    {
        if (!$this->shipments->contains($shipment)) {
            $this->shipments->add($shipment);
            $shipment->setSeller($this);
        }

        return $this;
    }

    public function removeShipment(Shipment $shipment): self
    {
        if ($this->shipments->removeElement($shipment)) {
            // set the owning side to null (unless already changed)
            if ($shipment->getSeller() === $this) {
                $shipment->setSeller(null);
            }
        }

        return $this;
    }
}
