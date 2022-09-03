<?php

namespace App\Entity\ProductItem;

use App\Repository\ProductItem\DefineFeatureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DefineFeatureRepository::class)]
class DefineFeature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['showDefineFeature' , 'showItemFeature'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['showDefineFeature' , 'showItemFeature'])]
    private ?string $value = null;

    #[ORM\Column]
    #[Groups(['showItemFeature'])]
    private ?bool $status = null;

    #[ORM\ManyToOne(targetEntity: ItemFeature::class , inversedBy: 'defineFeatures')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('showDefineFeature')]
    private ?ItemFeature $itemFeature = null;

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

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

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
