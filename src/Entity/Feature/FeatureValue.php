<?php

namespace App\Entity\Feature;

use App\Entity\Product\Product;
use App\Entity\Variant\Variant;
use App\Repository\FeatureRepository\FeatureValueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FeatureValueRepository::class)]
class FeatureValue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['showFeatureValue', 'showFeature'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['showFeatureValue' , 'showFeature' , 'showVariant' ,'FeatureValueOA'])]
    private ?string $value = null;

    #[ORM\Column]
    #[Groups(['showFeature'])]
    private ?bool $active = null;

    #[ORM\ManyToOne(inversedBy: 'featureValues')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['showFeatureValue', 'showVariant'])]
    private ?Feature $feature = null;

    #[ORM\ManyToMany(targetEntity: Variant::class, inversedBy: 'featureValues')]
    private Collection $variants;

    #[ORM\ManyToMany(targetEntity: Product::class, inversedBy: 'featureValues')]
    private Collection $products;

    public function __construct()
    {
        $this->variants = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getFeature(): ?Feature
    {
        return $this->feature;
    }

    public function setFeature(?Feature $feature): self
    {
        $this->feature = $feature;

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
        }

        return $this;
    }

    public function removeVariant(Variant $variant): self
    {
        $this->variants->removeElement($variant);

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        $this->products->removeElement($product);

        return $this;
    }
}
