<?php

namespace App\Entity\User;

use App\Entity\Shipment\Shipment;
use App\Entity\Variant\Variant;
use App\Repository\UserRepository\SellerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Entity(repositoryClass: SellerRepository::class)]
#[UniqueEntity(fields: ["shopSlug"], message: "This shopSlug is already in use")]
class Seller extends User
{
    const SELLER = 'ROLE_SELLER';

    #[ORM\Column(length: 255, unique: true)]
    #[Serializer\Groups(['shipment.seller.read'])]
    private ?string $shopSlug = null;

    #[ORM\OneToMany(mappedBy: 'seller', targetEntity: Shipment::class)]
    private Collection $shipments;

    #[ORM\OneToMany(mappedBy: 'seller', targetEntity: Variant::class, orphanRemoval: true)]
    private Collection $variants;

    public function __construct()
    {
        $this->shipments = new ArrayCollection();
        $this->variants = new ArrayCollection();
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

    /**
     * @return Collection<int, Variant>
     */
    public function getVariants(): Collection
    {
        return $this->variants;
    }

    public function addVariant(Variant $variant): self
    {
        if (!$this->variants->contains($variant)) {
            $this->variants->add($variant);
            $variant->setSeller($this);
        }

        return $this;
    }

    public function removeVariant(Variant $variant): self
    {
        if ($this->variants->removeElement($variant)) {
            // set the owning side to null (unless already changed)
            if ($variant->getSeller() === $this) {
                $variant->setSeller(null);
            }
        }

        return $this;
    }
}
