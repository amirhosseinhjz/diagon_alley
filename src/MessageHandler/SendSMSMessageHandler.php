<?php

namespace App\MessageHandler;

use App\Message\SendSMSMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Melipayamak\MelipayamakApi;
use GuzzleHttp;


final class SendSMSMessageHandler implements MessageHandlerInterface
{
    public function __invoke(SendSMSMessage $message)
    {
        try{
        $this->send2($message);
        }catch(\Exception $e){
            dump($e->getMessage());
        }
    }


    private function send(SendSMSMessage $message)
    {
        $username = $_ENV['MELLI_USERNAME'];
        $password = $_ENV['MELLI_PASSWORD'];
        $api = new MelipayamakApi($username,$password);
        $sms = $api->sms('soap');
        $to = [$message->getNumber()];
        $from = $_ENV['MELLI_FROM_NUMBER'];
        $text = $message->getMessage();
        $isFlash = true;
        dump('sending sms to '.json_encode($to));
        dump($sms->send(json_encode($to),$from,$text,$isFlash));
    }

    public function send2(SendSMSMessage $message)
    {
        try{
            $username = $_ENV['MELLI_USERNAME'];
            $password = $_ENV['MELLI_PASSWORD'];
            $api = new MelipayamakApi($username,$password);
            $sms = $api->sms();
            $to = [$message->getNumber()];
            $from = $_ENV['MELLI_FROM_NUMBER'];
            $text = $message->getMessage();
            $response = $sms->send($to,$from,$text);
            $json = json_decode($response);
            dump($json->Value);
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
}
