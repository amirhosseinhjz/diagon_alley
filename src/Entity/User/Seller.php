<?php

namespace App\Entity\User;

use App\Entity\Variant\Variant;
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

    #[ORM\OneToMany(mappedBy: 'seller', targetEntity: Variant::class, orphanRemoval: true)]
    private Collection $variants;

    public function __construct()
    {
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
