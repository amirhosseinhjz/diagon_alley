<?php

namespace App\Entity\ProductItem;

use App\Entity\ProductItem\DefineFeature;
use App\Repository\ProductItem\ItemFeatureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ItemFeatureRepository::class)]
class ItemFeature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['showDefineFeature' , 'showItemFeature'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['showVarient' , 'showDefineFeature' , 'showItemFeature'])]
    private ?string $label = null;

    #[ORM\Column]
    #[Groups(['showItemFeature'])]
    private ?bool $status = null;

    #[ORM\OneToMany(mappedBy: 'itemFeature', targetEntity: DefineFeature::class)]
    #[Groups(['showItemFeature'])]
    private Collection $defineFeatures;

    #[ORM\OneToMany(mappedBy: 'itemFeature', targetEntity: ItemValue::class)]
    private Collection $itemValues;

     #[ORM\ManyToMany(targetEntity: Category::class, mappedBy: 'features')]
     private Collection $categories;

    public function __construct()
    {
        $this->itemValues = new ArrayCollection();
        $this->defineFeatures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getStatus(): ?bool
    {
        return $this->status;
    }

    /**
     * @param bool|null $status
     */
    public function setStatus(?bool $status): void
    {
        $this->status = $status;
    }

     /**
      * @return Collection<int, Category>
      */
     public function getCategories(): Collection
     {
         return $this->categories;
     }

     public function addCategory(Category $category): self
     {
         if (!$this->categories->contains($category)) {
             $this->categories->add($category);
             $category->addFeature($this);
         }

         return $this;
     }

     public function removeCategory(Category $category): self
     {
         if ($this->categories->removeElement($category)) {
             $category->removeFeature($this);
         }

         return $this;
     }

    /**
     * @return Collection<int, DefineFeature>
     */
    public function getDefineFeatures(): Collection
    {
        return $this->defineFeatures;
    }

    public function addDefineFeature(DefineFeature $defineFeature): self
    {
        if (!$this->defineFeatures->contains($defineFeature)) {
            $this->defineFeatures->add($defineFeature);
            $defineFeature->setItemFeature($this);
        }
        return $this;
    }

    public function removeDefineFeature(DefineFeature $defineFeature): self
    {
        if ($this->defineFeatures->removeElement($defineFeature)) {
            // set the owning side to null (unless already changed)
            if ($defineFeature->getItemFeature() === $this) {
                $defineFeature->setItemFeature(null);
            }
        }

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
            $itemValue->setItemFeature($this);
        }

        return $this;
    }

    public function removeItemValue(ItemValue $itemValue): self
    {
        if ($this->itemValues->removeElement($itemValue)) {
            // set the owning side to null (unless already changed)
            if ($itemValue->getItemFeature() === $this) {
                $itemValue->setItemFeature(null);
            }
        }

        return $this;
    }
}
