<?php

namespace App\DTO\Payment;

use App\Entity\Cart\Cart;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PaymentDTO
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    public readonly ?string $type;

    #[Assert\PositiveOrZero]
    #[Assert\NotNull]
    public ?int $paidAmount=0;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Choice(['PENDING', 'FAILED', 'SUCCESS'])]
    public readonly ?string $status;

    #[Assert\NotNull]
    public ?Cart $cart=null;

    public function __construct($cart, $price, $type, ValidatorInterface $validator)
    {
        $this->type = $type;
        $this->paidAmount = $price;
        $this->status = "PENDING";
        $this->code = "000000";
        $this->cart = $cart;

        $this->validate($validator);
    }

    private function validate(ValidatorInterface $validator)
    {
        $errors = $validator->validate($this);

        if (count($errors) > 0) {
            throw (new \Exception(json_encode($errors)));
        }
    }
}
