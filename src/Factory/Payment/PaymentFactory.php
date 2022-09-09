<?php

namespace App\Factory\Payment;

use App\Interface\Payment\paymentInterface;

final class PaymentFactory
{
    public static function create(string $method): ?paymentInterface
    {
        $newPayment = "App\Service\\" . ucfirst($method) . "\\" . ucfirst($method) . "Service";

        if (class_exists($newPayment))
            return new $newPayment();
        else
            throw (new \Exception('Invalid portal type'));
    }
}
