<?php

namespace App\Entity\ProductItem;

use App\Repository\ProductItem\VarientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: VarientRepository::class)]
class Varient
{
    public const STATUS_VALIDATE_SUCCESS = 1;
    public const STATUS_VALIDATE_PENDING = 0;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('showVarient')]
    private ?string $serial = null;

    #[ORM\Column(type: Types::BIGINT)]
    #[Groups('showVarient')]
    private ?int $price = null;

    #[ORM\Column]
    #[Groups('showVarient')]
    private ?int $quantity = null;

    #[ORM\Column]
    #[Groups('showVarient')]
    private ?bool $status = null;

    #[Groups('showVarient')]
    #[ORM\OneToMany(mappedBy: 'varient', targetEntity: ItemValue::class, orphanRemoval: true)]
    private Collection $itemValues;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('showVarient')]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups('showVarient')]
    private ?\DateTimeImmutable $createdAt = null;

//     #[ORM\ManyToOne(inversedBy: 'variants')]
//     #[ORM\JoinColumn(nullable: false)]
//     private ?Product $product = null;

    public function __construct()
    {
        $this->itemValues = new ArrayCollection();
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
            $itemValue->setVarient($this);
        }

        return $this;
    }

    public function removeItemValue(ItemValue $itemValue): self
    {
        if ($this->itemValues->removeElement($itemValue)) {
            // set the owning side to null (unless already changed)
            if ($itemValue->getVarient() === $this) {
                $itemValue->setVarient(null);
            }
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
}
