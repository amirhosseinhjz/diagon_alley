<?php

namespace App\MessageHandler;

use App\Message\SendBookMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendBookMessageHandler implements MessageHandlerInterface
{
    public function __construct()
    {

    }

    public function __invoke(SendBookMessage $message)
    {
        dump('hey');
    }
}
