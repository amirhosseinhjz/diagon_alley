<?php

namespace App\Utils\Swagger\Variant;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class Variant
{
    #[Assert\NotNull]
    #[OA\Property(
        properties: [
            new OA\Property(
                property: 'price'
            ),
            new OA\Property(
                property: 'quantity'
            ),
            new OA\Property(
                property: 'description'
            ),
            new OA\Property(
                property: 'productId'
            ),
            new OA\Property(
                property: 'deliveryEstimate'
            ),
            new OA\Property(
                property: 'type'
            )
        ],
        type: "object",
    )]
    public readonly ?string $variant;

    #[Assert\NotNull]
    #[OA\Property(
        properties: [
            new OA\Property(
                property: 'FeatureId'
            )
        ],
        type: "object",
    )]
    public readonly ?string $feature;
}