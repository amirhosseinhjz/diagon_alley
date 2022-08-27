<?php

namespace App\DTO\Payment;

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
    public readonly ?string $status;

    
    public readonly ?string $code;

    use PaymentTrait;

    public function __construct($fields,$type, ValidatorInterface $validator)
    {
        $this->type = $type;
        $this->paidAmount = $this->calculatePaidAmount($fields["price"],$fields["discount"]);
        $this->status = "PENDING";
        //TODO: change default code
        $this->code = "123";


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
