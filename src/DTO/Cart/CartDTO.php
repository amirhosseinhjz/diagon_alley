<?php

namespace App\DTO\Cart;

use App\Entity\Cart\Cart;
use Symfony\Component\Validator\Constraints as Assert;

#? is finalizedat really needed here?

class CartDTO
{

    public $id;

    #[Assert\DateTime]
    private ?\DateTimeInterface $finalizedAt = null;   #ToDo check the time range

    #ToDo: extra validation
    #[Assert\Collection]
    public array $items;

    #[Assert\Choice(
        choices: [Cart::STATUS_INIT,Cart::STATUS_PENDING,Cart::STATUS_SUCCESS,Cart::STATUS_EXPIRED],
        message: 'invalid status value.',
    )]
    private ?string $status = null;


    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function __construct()
    {
        $this->items = array();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(): ?int
    {
        return $this->id;
    }

    public function getFinalizedAt(): ?\DateTimeInterface
    {
        return $this->finalizedAt;
    }

    public function setFinalizedAt(\DateTimeInterface $finalizedAt): self
    {
        $this->finalizedAt = $finalizedAt;

        return $this;
    }

    public function setItems(array $items)
    {
        $this->items = $items;

        return $this;
    }

    public function getItems(){
        return $this->items;
    }

}