<?php

namespace App\DTO\Payment;

use App\Entity\Order\Purchase;
use App\Entity\Portal\Portal;
use Symfony\Component\Validator\Constraints as Assert;

class PaymentDTO
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Choice(['wallet', 'portal'], message: 'This method is not available.')]
    public ?string $method;

    #[Assert\PositiveOrZero]
    #[Assert\NotNull]
    public ?int $paidAmount = 0;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Choice(['pending', 'failed', 'success'], message: 'This type is not available.')]
    public ?string $status = "pending";

    public ?Purchase $purchase = null;

    public ?Portal $portal = null;
}
