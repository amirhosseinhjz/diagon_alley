<?php

namespace App\Factory\Portal;

final class PortalFactory
{
    public static function create(string $type,$em)
    {
        $newPortal = "App\Service\Portal\\" . ucfirst($type) . "PortalService";

        if (class_exists($newPortal))
            return new $newPortal($em);
        else
            throw (new \Exception('Invalid portal type'));
    }
}
