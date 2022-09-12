<?php

namespace App\Utils\Swagger\Address;

use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class Address
{
    #[Assert\Length(min: 3, max: 255)]
    #[Serializer\Groups(['address.pro'])]
    private ?string $name = null;
}