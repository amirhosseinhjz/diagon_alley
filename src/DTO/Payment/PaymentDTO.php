<?php

namespace App\DTO\Payment;

use App\Entity\Cart;
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
    #[Assert\Choice(['PENDING', 'FAILED', 'SUCCESS'])]
    public readonly ?string $status;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public readonly ?string $code;

    public function __construct($cart,$type, ValidatorInterface $validator)
    {
        $this->type = $type;
        $this->paidAmount = $cart->getTotalPrice();
        $this->status = "PENDING";
        $this->code = "000000";
        $this->cart = $cart;

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
