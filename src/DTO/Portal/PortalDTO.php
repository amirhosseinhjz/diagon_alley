<?php

namespace App\DTO\Portal;

use App\Entity\Payment\Payment;
use Symfony\Component\Validator\Constraints as Assert;

class PortalDTO
{
    #[Assert\Choice(['saman'], message: 'This portal is not available.')]
    public ?string $type = null;

    #[Assert\NotBlank(message: 'Code can not be blank.')]
    #[Assert\NotNull(message: 'Code can not be null.')]
    public ?string $code = "000000";

    #[Assert\NotNull(message: 'payment can not be null.')]
    public ?Payment $payment = null;
}
