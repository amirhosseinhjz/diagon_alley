<?php

namespace App\Service\Payment;

use App\Interface\Payment\BankPortalInterface;
use App\Repository\Payment\PaymentRepository;
use Symfony\Component\HttpFoundation\Request;


final class PotalFactory
{
    //TODO: change factory method
    private static $PortalTypes = [
        'SAMAN' => SamanPortalService::class,
    ];


    public static function create(string $type)
    {
        if( isset( self::$PortalTypes[$type] ) ){
            $newPortal = self::$PortalTypes[$type];
            return new $newPortal();
        }         
        else
            throw (new \Exception("This type is not valid"));
    }

}
