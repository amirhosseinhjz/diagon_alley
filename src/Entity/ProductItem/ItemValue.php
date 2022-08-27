<?php

namespace App\Entity\ProductItem;

use App\Repository\ProductItem\ItemValueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private ?Varient $varient = null;

    #[ORM\Column(length: 255)]
    #[Groups('show')]
    private ?string $value = null;

    #[ORM\ManyToOne(inversedBy: 'itemValues')]
    #[Groups('show')]
    private ?ItemFeature $itemFeature = null;

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

    public function getItemFeature(): ?ItemFeature
    {
        return $this->itemFeature;
    }

    public function setItemFeature(?ItemFeature $itemFeature): self
    {
        $this->itemFeature = $itemFeature;

        return $this;
    }
}
