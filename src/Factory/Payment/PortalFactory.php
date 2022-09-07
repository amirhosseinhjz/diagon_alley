<?php

namespace App\Factory\Payment;

use App\Service\CartService\CartServiceInterface;

final class PortalFactory
{
    public static function create(string $type, CartServiceInterface $cartService)
    {
        $newPortal = "App\Service\Payment\\".$type."PortalService";

        if(class_exists($newPortal))
            return new $newPortal($cartService);
        else
            throw (new \Exception('Invalid portal type'));
    }
}
