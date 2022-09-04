<?php
namespace App\DTO\CartDTO;

use Symfony\Component\Validator\Constraints as Assert;

class CartDTO
{

    private ?int $id= null;
    public ?int $User_Id = null;

    #[Assert\DateTime]
    private ?\DateTimeInterface $finalizedAt = null;   #check if it is in the past

    
    #[Assert\Collection(
        fields: [
            'varient_id' => new Assert\Type('int'),
            'title' => [new Assert\NotBlank, New Assert\Type('string')],  #check with varient code
            'count' => [
                new Assert\Positive,
                new Assert\Type('int')
            ],
            'price' => new Assert\PositiveOrZero

        ],
        allowMissingFields: false,
    )]
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