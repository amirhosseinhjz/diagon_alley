<?php

namespace App\DTO\Payment;

use App\Entity\Cart;
use App\Service\Payment\PaymentService;
use App\Trait\PaymentTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PaymentDTO
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    public readonly ?string $type;

    #[Assert\PositiveOrZero]
    #[Assert\NotNull]
    public readonly ?int $paidAmount;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #TODO: ENUM
    public readonly ?string $status;

    
    public readonly ?string $code;

    public readonly ?int $c;

    use PaymentTrait;

    public function __construct($cartId,$price,$type, ValidatorInterface $validator)
    {
        $this->type = $type;
        $this->paidAmount = $price;
        $this->status = "PENDING";
        $this->cartId = $cartId;


        $this->validate($validator);
    }

    private function validate(ValidatorInterface $validator)
    {
        $errors = $validator->validate($this);

        if(count($errors)>0)
        {
            throw (new \Exception(json_encode($errors)));
        }
    }
}
