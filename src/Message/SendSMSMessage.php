<?php

namespace App\Message;

final class SendSMSMessage
{
    private string $number;
    private string $message;

    public function __construct(string $number, string $message)
    {
        $this->number = $number;
        $this->message = $message;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
