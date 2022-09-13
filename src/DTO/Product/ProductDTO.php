<?php

namespace App\DTO\Product;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductDTO
{
    public ?string $name = null;

    public ?string $description = null;

    public ?bool $active = null;

    public int $viewCount = 0;

    private function validate(ValidatorInterface $validator)
    {
        $errors = $validator->validate($this);

        if (count($errors) > 0) {
            throw (new \Exception(json_encode($errors)));
        }
    }
}