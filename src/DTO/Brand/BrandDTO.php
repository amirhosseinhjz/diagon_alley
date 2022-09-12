<?php

namespace App\DTO\Brand;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BrandDTO
{
    public ?string $name = null;

    public ?string $description = null;

    private function validate(ValidatorInterface $validator)
    {
        $errors = $validator->validate($this);

        if (count($errors) > 0) {
            throw (new \Exception(json_encode($errors)));
        }
    }
}
