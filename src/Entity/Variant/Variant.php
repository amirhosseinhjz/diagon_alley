<?php

namespace App\Entity\Variant;

use App\Entity\Feature\FeatureValue;
use App\Entity\Feature\ItemValue;
use App\Repository\VariantRepository\VariantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: VariantRepository::class)]
class Variant
{
    public const STATUS_VALIDATE_SUCCESS = 1;
    public const STATUS_VALIDATE_PENDING = 0;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('showVariant')]
    private ?string $serial = null;

    #[ORM\Column(type: Types::BIGINT)]
    #[Groups('showVariant')]
    private ?int $price = null;

    #[ORM\Column]
    #[Groups('showVariant')]
    private ?int $quantity = null;

    #[ORM\Column]
    #[Groups('showVariant')]
    private ?bool $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('showVariant')]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups('showVariant')]
    private ?\DateTimeImmutable $createdAt = null;

    #[Groups('showVariant')]
    #[ORM\ManyToMany(targetEntity: FeatureValue::class, mappedBy: 'variants')]
    private Collection $featureValues;

    #[ORM\Column]
    private ?int $soldNumber = null;

//     #[ORM\ManyToOne(inversedBy: 'variants')]
//     #[ORM\JoinColumn(nullable: false)]
//     private ?Product $product = null;

    public function __construct()
    {
        $this->featureValues = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSerial(): ?string
    {
        return $this->serial;
    }

    public function setSerial(string $serial): self
    {
        $this->serial = $serial;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
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
            $featureValue->addVariant($this);
        }

        return $this;
    }

    public function removeFeatureValue(FeatureValue $featureValue): self
    {
        if ($this->featureValues->removeElement($featureValue)) {
            $featureValue->removeVariant($this);
        }

        return $this;
    }

//     public function getProduct(): ?Product
//     {
//         return $this->product;
//     }
//
//     public function setProduct(?Product $product): self
//     {
//         $this->product = $product;
//
//         return $this;
//     }

public function getSoldNumber(): ?int
{
    return $this->soldNumber;
}

public function setSoldNumber(int $soldNumber): self
{
    $this->soldNumber = $soldNumber;

    return $this;
}
}
