<?php

namespace App\MessageHandler;

use App\Message\SendSMSMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendSMSMessageHandler implements MessageHandlerInterface
{
    public function __invoke(SendSMSMessage $message)
    {
        // do something with your message
    }
}
