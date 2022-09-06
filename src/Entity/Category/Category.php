<?php

namespace App\Entity\Category;

use App\Entity\Feature\Feature;
use App\Entity\Product\Product;
use App\Repository\Category\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    const validTypes = ['digital', 'physical'];
    const defaultType = 'physical';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['category_basic'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true, nullable: false)]
    #[Groups(['category_basic'])]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    #[Groups(['category_parent'])]
    private ?self $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    #[Groups(['category_children'])]
    #[MaxDepth(1)]
    private Collection $children;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Product::class)]
    #[Groups(['category_products'])]
    private Collection $products;

    #[ORM\ManyToMany(targetEntity: Feature::class, inversedBy: 'categories')]
    #[Groups(['category_features'])]
    private Collection $features;

    #[ORM\Column(nullable: false)]
    #[Groups(['category_basic'])]
    private ?bool $active = true;

    #[ORM\Column]
    #[Groups(['category_basic'])]
    private ?bool $leaf = false;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Choice(self::validTypes)]
    #[Groups(['product_basic'])]
    private ?string $type = null;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->features = new ArrayCollection();
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

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): ?self
    {
        if ($parent && $parent->isLeaf()) return null;
        $this->parent = $parent;

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
            $product->setCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getCategory() === $this) {
                $product->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Feature>
     */
    public function getFeatures(): Collection
    {
        return $this->features;
    }

    public function addFeature(Feature $feature): self
    {
        if (!$this->features->contains($feature)) {
            $this->features->add($feature);
        }

        return $this;
    }

    public function removeFeature(Feature $feature): self
    {
        $this->features->removeElement($feature);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

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

    public function isLeaf(): ?bool
    {
        return $this->leaf;
    }

    public function setLeaf(bool $leaf): ?self
    {
        if ($leaf == true && $this->parent == null) return null;
        if ($leaf == true && $this->children->count() != 0) return null;
        if ($leaf == false && $this->products->count() != 0) return null;

        $this->leaf = $leaf;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): ?self
    {
        if ($this->isLeaf() == false && $type == null) return null;
        $this->type = $type;

        return $this;
    }
}
