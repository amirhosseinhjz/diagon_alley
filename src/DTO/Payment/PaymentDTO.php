<?php

namespace App\DTO\Payment;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PaymentDTO
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    // todo -> set enum
    public readonly ?string $type;

    #[Assert\PositiveOrZero]
    #[Assert\NotNull]
    public readonly ?int $paidAmount;

    // #[Assert\NotNull]
    // public readonly ?\DateTimeImmutable $createdAt;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    //todo -> set enum
    public readonly ?string $status;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    //todo -> length of code
    public readonly ?string $code;

    public function __construct(array $fields, ValidatorInterface $validator)
    {
        foreach ($fields as $field => $value) {
            if (property_exists($this, $field)) {
                $this->{$field} = $value;
            }
        }

        // $this->createdAt = new \DateTimeImmutable();

        $this->validate($validator);
    }

    private function validate(ValidatorInterface $validator)
    {
        $errors = $validator->validate($this);

        if(count($errors)>0)
        {
            //TODO
            //$payment not valid
            // dd((string)$errors);
            throw new \Exception();
        }
    }
}
