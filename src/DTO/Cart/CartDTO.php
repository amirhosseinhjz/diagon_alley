<?php
#ToDo: write transformer
namespace App\DTO\Cart;

use Symfony\Component\Validator\Constraints as Assert;
#ToDo: revise
class CartDTO
{

    public $id;

    #[Assert\DateTime]
    private ?\DateTimeInterface $finalizedAt = null;   #check if it is in the past

    #ToDo: extra validation
    #[Assert\Collection]
    public array $items;

    #[Assert\Choice(
        choices: ['INIT', 'PENDING', 'EXPIRED', 'SUCCESS'],
        message: 'Choose a valid genre.',
    )]
    private ?string $status = null;

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