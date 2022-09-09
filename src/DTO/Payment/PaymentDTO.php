<?php

namespace App\DTO\Payment;

use Symfony\Component\Validator\Constraints as Assert;

class PaymentDTO
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Choice(['WALLET', 'PORTAL'], message: 'This method is not available.')]
    public readonly ?string $method;

    #[Assert\PositiveOrZero]
    #[Assert\NotNull]
    public ?int $paidAmount = 0;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Choice(['PENDING', 'FAILED', 'SUCCESS'], message: 'This method is not available.')]
    public ?string $status = "PENDING";
}
