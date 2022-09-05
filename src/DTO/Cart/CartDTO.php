<?php
namespace App\DTO\Cart;

use Symfony\Component\Validator\Constraints as Assert;

class CartDTO
{

    public ?int $id= null;
    public ?int $User_Id = null;

    #[Assert\DateTime]
    public ?\DateTimeInterface $finalizedAt = null;   #check if it is in the past

    
    #[Assert\Collection]
    public array $items;

    #[Assert\Choice(
        choices: ['INIT', 'PENDING', 'EXPIRED', 'SUCCESS'],
        message: 'Choose a valid genre.',
    )]
    public ?string $status = null;

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     */
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

    public function getUserId(): ?int
    {
        return $this->User_Id;
    }

    public function setUserId(int $User_Id): self
    {
        $this->User_Id = $User_Id;

        return $this;
    }

    public function getFinalizedAt(): ?\DateTimeInterface
    {
        return $this->finalizedAt;
    }

    public function setFinalizedAt(\DateTimeInterface $finalizedAt)
    {
        $this->finalizedAt = $finalizedAt;
    }

    public function setItems(array $items)
    {
        $this->items = $items;
    }

    public function getItems(){
        return $this->items;
    }

}