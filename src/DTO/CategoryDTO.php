<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CategoryDTO
{
//    private ?int $id = null;

    #[Assert\Length(max:255)]
    public ?string $name = null;

    public ?bool $isLeaf = false;
}
