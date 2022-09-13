<?php

namespace App\Utils\Swagger\Auth;
use Symfony\Component\Validator\Constraints as Assert;

class OTPToken
{
    #[Assert\NotNull]
    public ?int $token = null;
}