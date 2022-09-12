<?php


namespace App\Utils\Swagger\Cart;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class RemoveItem
{
    #[Assert\NotNull]
    public readonly ?int $cartId;

    #[Assert\NotNull]
    public readonly ?int $cartItemId;

    #[Assert\NotNull]
    public readonly ?int $quantity;
}