<?php

namespace App\DTO\Category;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoryDTO
{
    public ?string $name = null;

    public ?bool $active = true;

    public ?bool $leaf = false;

    public ?string $type = null;

    private function validate(ValidatorInterface $validator)
    {
        $errors = $validator->validate($this);

        if (count($errors) > 0) {
            throw (new \Exception(json_encode($errors)));
        }
    }
}