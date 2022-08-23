<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class BrandDTO
{
//    private ?int $id = null;

    #[Assert\Length(max:255)]
    public ?string $name = null;

    #[Assert\Length(max:511)]
    public ?string $description = null;
}
