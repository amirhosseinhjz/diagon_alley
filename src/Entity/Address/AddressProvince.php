<?php

namespace App\Entity\Address;

use App\Repository\Address\AddressProvinceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AddressProvinceRepository::class)]
#[UniqueEntity(fields: ["name"], message: "This name is already in use")]
class AddressProvince
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['province'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\Length(min: 3, max: 255)]
    #[Groups(['city','province'])]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'province', targetEntity: AddressCity::class)]
    #[Groups(['province'])]
    private Collection $addressCities;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Groups(['province'])]
    private ?bool $isActive = true;

    public function __construct()
    {
        $this->addressCities = new ArrayCollection();
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

    /**
     * @return Collection<int, AddressCity>
     */
    public function getAddressCities(): Collection
    {
        return $this->addressCities;
    }

    public function addAddressCity(AddressCity $addressCity): self
    {
        if (!$this->addressCities->contains($addressCity)) {
            $this->addressCities->add($addressCity);
            $addressCity->setProvince($this);
        }

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
