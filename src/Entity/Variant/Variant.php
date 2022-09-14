<?php

namespace App\Entity\Variant;

use App\Entity\Feature\FeatureValue;
use App\Entity\User\Seller;
use App\Entity\Product\Product;
use App\Repository\VariantRepository\VariantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: VariantRepository::class)]
#[ORM\Index(columns: ["serial"], name: "idx_serial")]
class Variant
{
    public const STATUS_VALIDATE_SUCCESS = 1;
    public const STATUS_VALIDATE_PENDING = 0;
    const validTypes = ['digital', 'physical'];
    const defaultType = 'physical';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['showVariant', 'Cart.read'])]
    private ?string $serial = null;

    #[ORM\Column(type: Types::BIGINT)]
    #[Groups(['showVariant' , 'VariantOAUpdate', 'Cart.read'])]
    private ?int $price = null;

    #[ORM\Column]
    #[Groups(['showVariant' , 'VariantOAUpdate'])]
    private ?int $quantity = null;

    #[ORM\Column]
    #[Groups(['showVariant'])]
    private ?bool $valid = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['showVariant'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['showVariant'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[Groups(['showVariant'])]
    #[ORM\ManyToMany(targetEntity: FeatureValue::class, mappedBy: 'variants')]
    private Collection $featureValues;

    #[ORM\Column]
    #[Groups(['showVariant'])]
    private ?int $soldNumber = null;

    #[ORM\ManyToOne(inversedBy: 'variants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Seller $seller = null;

    #[ORM\Column(length: 30)]
    #[Groups(['showVariant'])]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'variants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column]
    #[Groups(['showVariant'])]
    private ?int $deliveryEstimate = null;

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

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    public function getValid(): bool
    {
        return $this->valid;
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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {

        $this->product = $product;

        return $this;
    }

    public function getSoldNumber(): ?int
    {
        return $this->soldNumber;
    }

    public function setSoldNumber(int $soldNumber): self
    {
        $this->soldNumber = $soldNumber;

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
    
    public function getSeller(): ?Seller
    {
        return $this->seller;
    }

    public function setSeller(?Seller $seller): self
    {
        $this->seller = $seller;

        return $this;
    }

    public function getDeliveryEstimate(): ?int
    {
        return $this->deliveryEstimate;
    }

    public function setDeliveryEstimate(int $deliveryEstimate): self
    {
        $this->deliveryEstimate = $deliveryEstimate;
        return $this;
    }
    
    public function increaseQuantity(int $quantity): self
    {
        $this->quantity += $quantity;
        return $this;
    }

    public function decreaseQuantity(int $quantity): self
    {
        $this->quantity -= $quantity;
        return $this;
    }
}
