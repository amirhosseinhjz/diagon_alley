<?php

namespace App\Entity\Feature;

use App\Entity\Variant\Variant;
use App\Repository\FeatureRepository\ItemValueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ItemValueRepository::class)]
class ItemValue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'itemValues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Variant $variant = null;

    #[ORM\Column(length: 255)]
    #[Groups('showVariant')]
    private ?string $value = null;

    #[ORM\ManyToOne(inversedBy: 'itemValues')]
    #[Groups('showVariant')]
    private ?Feature $feature = null;

//    public function __construct()
//    {
//        $this->itemFeatures = new ArrayCollection();
//    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVariant(): ?Variant
    {
        return $this->variant;
    }

    public function setVariant(?Variant $variant): self
    {
        $this->variant = $variant;

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

    public function getFeature(): ?Feature
    {
        return $this->feature;
    }

    public function setFeature(?Feature $feature): self
    {
        $this->feature = $feature;

        return $this;
    }
}
