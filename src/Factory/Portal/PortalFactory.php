<?php

namespace App\Factory\Portal;

final class PortalFactory
{
    public static function create(string $type)
    {
        $newPortal = "App\Service\Portal\\" . ucfirst($type) . "PortalService";

        if (class_exists($newPortal))
            return new $newPortal();
        else
            throw (new \Exception('Invalid portal type'));
    }
}
