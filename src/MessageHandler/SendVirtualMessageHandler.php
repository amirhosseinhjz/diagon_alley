<?php

namespace App\MessageHandler;

use App\Message\SendVirtualMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendVirtualMessageHandler implements MessageHandlerInterface
{
    public function __construct()
    {
    }

    public function __invoke(SendVirtualMessage $message)
    {
        dump('sending an email');
    }
}
