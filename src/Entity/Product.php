<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product_basic'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product_basic'])]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['product_category'])]
    private ?Category $category = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product_basic'])]
    private ?string $type = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Variant::class)]
    #[Groups(['product_variants'])]
    private Collection $variants;

    #[ORM\Column(length: 511, nullable: true)]
    #[Groups(['product_basic'])]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['product_brand'])]
    private ?Brand $brand = null;

    #[ORM\Column]
    #[Groups(['product_basic'])]
    private ?bool $active = null;

    #[ORM\ManyToMany(targetEntity: ItemValue::class, mappedBy: 'products')]
    #[Groups(['product_itemValues'])]
    private Collection $itemValues;

    public function __construct()
    {
        $this->variants = new ArrayCollection();
        $this->itemValues = new ArrayCollection();
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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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
            $variant->setProduct($this);
        }

        return $this;
    }

    public function removeVariant(Variant $variant): self
    {
        if ($this->variants->removeElement($variant)) {
            // set the owning side to null (unless already changed)
            if ($variant->getProduct() === $this) {
                $variant->setProduct(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

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
            $itemValue->addProduct($this);
        }

        return $this;
    }

    public function removeItemValue(ItemValue $itemValue): self
    {
        if ($this->itemValues->removeElement($itemValue)) {
            $itemValue->removeProduct($this);
        }

        return $this;
    }
}
