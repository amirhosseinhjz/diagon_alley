<?php

namespace App\Utils\Swagger\Feature;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class Feature
{
    #[Assert\NotNull]
    #[OA\Property(
        type: "array",
        items: new OA\Items(
            type: "string"
        )
    )]
    public readonly ?string $features;
}