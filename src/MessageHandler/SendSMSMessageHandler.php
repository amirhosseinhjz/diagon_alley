<?php

namespace App\MessageHandler;

use App\Message\SendSMSMessage;
use GuzzleHttp\Client;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;


final class SendSMSMessageHandler implements MessageHandlerInterface
{

    public function __construct()
    {
        $this->username = $_ENV['MELLI_USERNAME'];
        $this->password = $_ENV['MELLI_PASSWORD'];
        $this->from = $_ENV['MELLI_FROM_NUMBER'];
        $this->url = $_ENV['MELLI_URL'];
        $this->client = new Client();
    }

    public function __invoke(SendSMSMessage $message)
    {
        $this->send($message);
    }




    public function send(SendSMSMessage $message)
    {
        $url = $this->url;
        $url = str_replace('username=', 'username='.$this->username, $url);
        $url = str_replace('password=', 'password='.$this->password, $url);
        $url = str_replace('from=', 'from='.$this->from, $url);
        $url = str_replace('to=', 'to='.$message->getNumber(), $url);
        $url = str_replace('text=', 'text='.$message->getMessage(), $url);
        $url = str_replace('isflash=', 'isflash=true', $url);
        $this->client->get($url);
    }
}
