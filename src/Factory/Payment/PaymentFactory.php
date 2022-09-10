<?php

namespace App\Factory\Payment;

use App\Interface\Payment\paymentInterface;

final class PaymentFactory
{
    public static function create(string $method,$em,$validator): ?paymentInterface
    {
        $newPayment = "App\Service\\" . ucfirst($method) . "\\" . ucfirst($method) . "Service";

        if (class_exists($newPayment))
            return new $newPayment($em,$validator);
        else
            throw (new \Exception('Invalid portal method'));
    }
}
