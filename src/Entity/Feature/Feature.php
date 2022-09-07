<?php

namespace App\Entity\Feature;

use App\Entity\Category\Category;
use App\Repository\FeatureRepository\FeatureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FeatureRepository::class)]
class Feature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['showFeatureValue' , 'showFeature'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['showVariant' , 'showFeatureValue' , 'showFeature'])]
    private ?string $label = null;

    #[ORM\Column]
    #[Groups(['showFeature'])]
    private ?bool $status = null;

    #[ORM\OneToMany(mappedBy: 'feature', targetEntity: FeatureValue::class)]
    #[Groups(['showFeature'])]
    private Collection $featureValues;

     #[ORM\ManyToMany(targetEntity: Category::class, mappedBy: 'features')]
     private Collection $categories;

    public function __construct()
    {
        $this->featureValues = new ArrayCollection();
        $this->categories = new ArrayCollection();
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
     * @return Collection<int, FeatureValue>
     */
    public function getFeatureValues(): Collection
    {
        return $this->featureValues;
    }

    public function addFeatureValue(FeatureValue $featureValue): self
    {
        if (!$this->featureValues->contains($featureValue)) {
            $this->featureValues->add($featureValue);
            $featureValue->setFeature($this);
        }

        return $this;
    }

    public function removeFeatureValue(FeatureValue $featureValue): self
    {
        if ($this->featureValues->removeElement($featureValue)) {
//             set the owning side to null (unless already changed)
            if ($featureValue->getFeature() === $this) {
                $featureValue->setFeature(null);
            }
        }

        return $this;
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
}
