<?php

namespace App\Entity\Discount;

use App\Repository\Discount\DiscountRepository;
use App\Entity\Order\Purchase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#ToDo: important! remove relation from purchaseItem, change the mapping value of the one in Purchase entity
//
//Discount is the entity defining simple discount codes that are applied to orders
//
#[ORM\Entity(repositoryClass: DiscountRepository::class)]
class Discount
{

    public const PERCENT_DISCOUNT = "PERCENT_DISCOUNT_TYPE";
    public const FIXED_DISCOUNT = "FIXED_VALUE_DISCOUNT";


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #ToDo: define different types
    #check: float?
    #[ORM\Column]
    private ?float $percent = null;

    #ToDo: must be unique
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $code = null;

    #ToDo: add rules
    #[ORM\OneToMany(mappedBy: 'discount', targetEntity: Purchase::class)]
    private Collection $affectedOrders;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\Column(nullable: true)]
    private ?int $maxUsageTimes = null;

    #[ORM\Column]
    private ?int $maxUsageTimesPerUser = null;


    #[ORM\Column(nullable: true)]
    private ?float $minPurchaseValue = null;

    #[ORM\Column(nullable: true)]
    private ?float $maxDiscountValue = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $ActivationTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $expirationTime = null;

    public function __construct()
    {
        $this->appliedTo = new ArrayCollection();
        $this->affectedOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPercent(): ?float
    {
        return $this->percent;
    }

    public function setPercent(float $percent): self
    {
        $this->percent = $percent;

        return $this;
    }
    
    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection<int, Purchase>
     */
    public function getAffectedOrders(): Collection
    {
        return $this->affectedOrders;
    }

    public function addAffectedOrder(Purchase $purchase): self
    {
        if (!$this->affectedOrders->contains($purchase)) {
            $this->affectedOrders->add($purchase);
            $purchase->setDiscount($this);
        }

        return $this;
    }

    public function removeAffectedOrder(Purchase $purchase): self
    {
        if ($this->affectedOrders->removeElement($purchase)) {
            // set the owning side to null (unless already changed)
            if ($purchase->getDiscount() === $this) {
                $purchase->setDiscount(null);
            }
        }

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setActivity(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getMaxUsageTimes(): ?int
    {
        return $this->maxUsageTimes;
    }

    public function setMaxUsageTimes(?int $maxUsageTimes): self
    {
        $this->maxUsageTimes = $maxUsageTimes;

        return $this;
    }

    public function getMaxUsageTimesPerUser(): ?int
    {
        return $this->maxUsageTimesPerUser;
    }

    public function setMaxUsageTimesPerUser(int $maxUsageTimesPerUser): self
    {
        $this->maxUsageTimesPerUser = $maxUsageTimesPerUser;

        return $this;
    }

    public function getMinPurchaseValue(): ?float
    {
        return $this->minPurchaseValue;
    }

    public function setMinPurchaseValue(?float $minPurchaseValue): self
    {
        $this->minPurchaseValue = $minPurchaseValue;

        return $this;
    }

    public function getMaxDiscountValue(): ?float
    {
        return $this->maxDiscountValue;
    }

    public function setMaxDiscountValue(?float $maxDiscountValue): self
    {
        $this->maxDiscountValue = $maxDiscountValue;

        return $this;
    }

    public function getActivationTime(): ?\DateTimeInterface
    {
        return $this->ActivationTime;
    }

    public function setActivationTime(?\DateTimeInterface $ActivationTime): self
    {
        $this->ActivationTime = $ActivationTime;

        return $this;
    }

    public function getExpirationTime(): ?\DateTimeInterface
    {
        return $this->expirationTime;
    }

    public function setExpirationTime(?\DateTimeInterface $expirationTime): self
    {
        $this->expirationTime = $expirationTime;

        return $this;
    }
}
