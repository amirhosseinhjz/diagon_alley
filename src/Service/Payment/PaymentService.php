<?php

namespace App\Service\Payment;

use App\Interface\Payment\BankPortalInterface;
use App\Repository\Payment\PaymentRepository;

class PaymentService
{
    public BankPortalInterface $portal;

    private const PortalTypes = [
        'SAMAN' => SamanPortalService::class,
    ];

    public function __construct(
        private readonly PaymentRepository $repository,
        string $type)
    {
        // dd($type);
        if( isset( self::PortalTypes[$type] ) ){
            $newPortal = self::PortalTypes[$type];
            $this->portal = new $newPortal($repository);
        }         
        else
            throw (new \Exception("This type is not valid"));
    }
}
