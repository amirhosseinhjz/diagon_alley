<?php

namespace App\Entity;

use App\Repository\DiscountRepository;
use App\Entity\Order\PurchaseItem;
use App\Entity\Order\Purchase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

//
//Discount is the entity defining simple discount codes that are applied to orders
//
#[ORM\Entity(repositoryClass: DiscountRepository::class)]
class Discount
{

//    public const PERCENT_DISCOUNT = "PERCENT_DISCOUNT_TYPE";
//    public const FIXED_DISCOUNT = "FIXED_VALUE_DISCOUNT";
//
//    public const PER_ORDER = "ORDER_DISCOUNT";
//    public const PER_ITEM = "ITEM_DISCOUNT";

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #check: float?
    #[ORM\Column]
    private ?float $percent = null;

    #ToDo: must be unique
    #ToDo: generate
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $code = null;

    #ToDo: add rules
    #[ORM\OneToMany(mappedBy: 'discount', targetEntity: Purchase::class)]
    private Collection $affectedOrders;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\Column(nullable: true)]
    private ?int $maxUsageTimes = null;

    #[ORM\Column(nullable: true)]
    private ?int $maxUsageTimesPerUser = null;

    public function __construct()
    {
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

    #ToDo:is this a proper name?
    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
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
}
