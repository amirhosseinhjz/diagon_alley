<?php

namespace App\MessageHandler;

use App\Message\SendSMSMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Melipayamak\MelipayamakApi;


final class SendSMSMessageHandler implements MessageHandlerInterface
{
    public function __invoke(SendSMSMessage $message)
    {
        $this->send($message);
    }


    private function send(SendSMSMessage $message)
    {
        $username = "env('SMS_SERVICE_USERNAME')";
        $password = "env('SMS_SERVICE_PASSWORD')";
        $api = new MelipayamakApi($username,$password);
        $sms = $api->sms();
        $to = $message->getNumber();
        $from = "env('SMS_SERVICE_FROM')";
        $text = $message->getMessage();
        $sms->send($to,$from,$text);
    }
}
