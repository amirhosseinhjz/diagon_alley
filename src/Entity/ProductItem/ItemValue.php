<?php

namespace App\EntityProductItem;

use App\Repository\ItemValueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemValueRepository::class)]
class ItemValue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'itemValues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Varient $varient = null;

    #[ORM\Column(length: 255)]
    private ?string $value = null;

    #[ORM\ManyToMany(targetEntity: ItemFeature::class, mappedBy: 'itemValues')]
    private Collection $itemFeatures;

    public function __construct()
    {
        $this->itemFeatures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVarient(): ?Varient
    {
        return $this->varient;
    }

    public function setVarient(?Varient $varient): self
    {
        $this->varient = $varient;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return Collection<int, ItemFeature>
     */
    public function getItemFeatures(): Collection
    {
        return $this->itemFeatures;
    }

    public function addItemFeature(ItemFeature $itemFeature): self
    {
        if (!$this->itemFeatures->contains($itemFeature)) {
            $this->itemFeatures->add($itemFeature);
            $itemFeature->addItemValue($this);
        }

        return $this;
    }

    public function removeItemFeature(ItemFeature $itemFeature): self
    {
        if ($this->itemFeatures->removeElement($itemFeature)) {
            $itemFeature->removeItemValue($this);
        }

        return $this;
    }
}
