<?php


namespace App\Utils\Swagger\Order;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class SubmitOrder
{
    #[Assert\NotNull]
    public readonly ?int $cartId;

    #[Assert\NotNull]
    public readonly ?int $addressId;
}