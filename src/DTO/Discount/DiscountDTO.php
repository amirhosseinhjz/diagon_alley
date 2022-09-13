<?php

namespace App\DTO\Discount;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\Common\Collections\Collection;

class DiscountDTO
{
    #ToDo: needed? not for creation: just for update
    public ?int $id = null;

    #[Assert\PositiveOrZero]
    #[Assert\LessThan(100.0)]
    public ?float $percent = null;

    #[Assert\NotBlank()]
    public ?string $code = null;

    #[Assert\NotNull]
    public ?bool $isActive = null;

    #ToDo: or zero?
    #[Assert\PositiveOrZero]
    private ?int $maxUsageTimes = null;

    #ToDo: or zero?
    #[Assert\PositiveOrZero]
    private ?int $maxUsageTimesPerUser = null;

    #ToDo: assert
    private ?\DateInterval $timePeriod = null;

    #[Assert\PositiveOrZero]
    private ?float $minPurchaseValue = null;

    #[Assert\PositiveOrZero]
    private ?float $maxDiscountedValue = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return DiscountDTO
     */
    public function setId(?int $id): DiscountDTO
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getPercent(): ?float
    {
        return $this->percent;
    }

    /**
     * @param float|null $percent
     * @return DiscountDTO
     */
    public function setPercent(?float $percent): DiscountDTO
    {
        $this->percent = $percent;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getAppliedTo(): Collection
    {
        return $this->appliedTo;
    }

    /**
     * @param Collection $appliedTo
     * @return DiscountDTO
     */
    public function setAppliedTo(Collection $appliedTo): DiscountDTO
    {
        $this->appliedTo = $appliedTo;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string|null $code
     * @return DiscountDTO
     */
    public function setCode(?string $code): DiscountDTO
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getUsedOn(): Collection
    {
        return $this->usedOn;
    }

    /**
     * @param Collection $usedOn
     * @return DiscountDTO
     */
    public function setUsedOn(Collection $usedOn): DiscountDTO
    {
        $this->usedOn = $usedOn;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    /**
     * @param bool|null $isActive
     * @return DiscountDTO
     */
    public function setIsActive(?bool $isActive): DiscountDTO
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMaxUsageTimes(): ?int
    {
        return $this->maxUsageTimes;
    }

    /**
     * @param int|null $maxUsageTimes
     * @return DiscountDTO
     */
    public function setMaxUsageTimes(?int $maxUsageTimes): DiscountDTO
    {
        $this->maxUsageTimes = $maxUsageTimes;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMaxUsageTimesPerUser(): ?int
    {
        return $this->maxUsageTimesPerUser;
    }

    /**
     * @param int|null $maxUsageTimesPerUser
     * @return DiscountDTO
     */
    public function setMaxUsageTimesPerUser(?int $maxUsageTimesPerUser): DiscountDTO
    {
        $this->maxUsageTimesPerUser = $maxUsageTimesPerUser;
        return $this;
    }

    /**
     * @return \DateInterval|null
     */
    public function getTimePeriod(): ?\DateInterval
    {
        return $this->timePeriod;
    }

    /**
     * @param \DateInterval|null $timePeriod
     * @return DiscountDTO
     */
    public function setTimePeriod(?\DateInterval $timePeriod): DiscountDTO
    {
        $this->timePeriod = $timePeriod;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMinPurchaseValue(): ?float
    {
        return $this->minPurchaseValue;
    }

    /**
     * @param float|null $minPurchaseValue
     * @return DiscountDTO
     */
    public function setMinPurchaseValue(?float $minPurchaseValue): DiscountDTO
    {
        $this->minPurchaseValue = $minPurchaseValue;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMaxDiscountedValue(): ?float
    {
        return $this->maxDiscountedValue;
    }

    /**
     * @param float|null $maxDiscountedValue
     * @return DiscountDTO
     */
    public function setMaxDiscountedValue(?float $maxDiscountedValue): DiscountDTO
    {
        $this->maxDiscountedValue = $maxDiscountedValue;
        return $this;
    }

}