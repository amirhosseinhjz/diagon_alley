<?php

namespace App\Service\Payment;

use App\Service\CartService\CartServiceInterface;
final class PortalFactory
{
    //TODO: change factory method
    private static $PortalTypes = [
        'Saman' => SamanPortalService::class,
    ];

    public static function create(string $type, CartServiceInterface $cartService)
    {
        if (isset(self::$PortalTypes[$type])) {
            $newPortal = self::$PortalTypes[$type];

            return new $newPortal($cartService);
        } else
            throw (new \Exception("This type is not valid"));
    }
}
