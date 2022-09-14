<?php


namespace App\Utils\Swagger\Brand;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class Brand
{
    #[Assert\NotNull]
    public ?string $name;

    public ?string $description;
}