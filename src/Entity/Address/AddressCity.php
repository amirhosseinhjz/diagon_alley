<?php

namespace App\Entity\Address;

use App\Repository\Address\AddressCityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AddressCityRepository::class)]
class AddressCity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['city'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 3, max: 255)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Groups(['city','province','address'])]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'addressCities')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['city'])]
    private ?AddressProvince $province = null;

    #[ORM\OneToMany(mappedBy: 'city', targetEntity: Address::class, orphanRemoval: true)]
    private Collection $addresses;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Groups(['city'])]
    private ?bool $isActive = true;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getProvince(): ?AddressProvince
    {
        return $this->province;
    }

    public function setProvince(?AddressProvince $province): self
    {
        if ($this->province != null) {
            $this->province->getAddressCities()->removeElement($this);
        }
        $this->province = $province;

        return $this;
    }

    /**
     * @return Collection<int, Address>
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): self
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses->add($address);
            $address->setCity($this);
        }

        return $this;
    }

    public function removeAdderess($element): self
    {
        $this->addresses->removeElement($element);
        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}
