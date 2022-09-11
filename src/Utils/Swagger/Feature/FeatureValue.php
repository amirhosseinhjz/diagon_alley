<?php

namespace App\Utils\Swagger\Feature;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class FeatureValue
{
    #[Assert\NotNull]
    public readonly ?string $featureId;
}
