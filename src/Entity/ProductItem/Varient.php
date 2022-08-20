<?php

namespace App\EntityProductItem;

use App\Repository\VarientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VarientRepository::class)]
class Varient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $serial = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\OneToMany(mappedBy: 'varient', targetEntity: ItemValue::class, orphanRemoval: true)]
    private Collection $itemValues;

    public function __construct()
    {
        $this->itemValues = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSerial(): ?string
    {
        return $this->serial;
    }

    public function setSerial(string $serial): self
    {
        $this->serial = $serial;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, ItemValue>
     */
    public function getItemValues(): Collection
    {
        return $this->itemValues;
    }

    public function addItemValue(ItemValue $itemValue): self
    {
        if (!$this->itemValues->contains($itemValue)) {
            $this->itemValues->add($itemValue);
            $itemValue->setVarient($this);
        }

        return $this;
    }

    public function removeItemValue(ItemValue $itemValue): self
    {
        if ($this->itemValues->removeElement($itemValue)) {
            // set the owning side to null (unless already changed)
            if ($itemValue->getVarient() === $this) {
                $itemValue->setVarient(null);
            }
        }

        return $this;
    }
}
