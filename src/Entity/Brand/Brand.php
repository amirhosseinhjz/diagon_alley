<?php

namespace App\Entity\Brand;

use App\Entity\Product\Product;
use App\Repository\Brand\BrandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BrandRepository::class)]
class Brand
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['brand_basic'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: false, nullable: false)]
    #[Groups(['brand_basic', 'elastica'])]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(length: 511, nullable: true)]
    #[Groups(['brand_basic', 'elastica'])]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'brand', targetEntity: Product::class)]
    #[Groups(['brand_products'])]
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function setWithKeyValue(string $key, $value)
    {
        $method = 'set'.ucfirst($key);
        $this->$method($value);
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
        $name = trim($name);
        $this->name = $name;

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
            $product->setBrand($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getBrand() === $this) {
                $product->setBrand(null);
            }
        }

        return $this;
    }
}
