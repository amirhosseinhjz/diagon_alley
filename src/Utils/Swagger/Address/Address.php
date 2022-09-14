<?php

namespace App\Utils\Swagger\Address;

use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class Address
{
    #[Assert\Length(min: 3, max: 255)]
    #[Groups(['address.pro','address.city'])]
    public ?string $name = null;

    #[Assert\Length(min: 3, max: 255)]
    #[Groups(['address.city'])]
    public ?string $province = null;

    #[Groups(['address'])]
    #[Assert\Length(min: 3, max: 255)]
    public ?string $city = null;

    #[Groups(['address'])]
    #[Assert\Length(min: 3, max: 255)]
    public ?string $description = null;

    #[Assert\Regex(pattern: '/^[0-9]{4,10}$/', message: "The postCode '{{ value }}' is not a valid postCode.")]
    #[Assert\Length(min: 3, max: 255)]
    #[Groups(['address'])]
    public ?string $postCode = null;

    #[Assert\GreaterThanOrEqual(-90, message: "Latitude should be Greater than -90")]
    #[Assert\LessThanOrEqual(90, message: "Latitude should be Less than 90")]
    #[Groups(['address'])]
    public ?float $lat = 0;

    #[Assert\GreaterThanOrEqual(-180, message: "Longitude should be Greater than -180")]
    #[Assert\LessThanOrEqual(180, message: "Longitude should be Less than 180")]
    #[Groups(['address'])]
    public ?float $lng = 0;
}