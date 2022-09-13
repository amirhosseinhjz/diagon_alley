<?php

namespace App\MessageHandler;

use App\Message\SendSMSMessage;
use GuzzleHttp\Client;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Melipayamak\MelipayamakApi;


final class SendSMSMessageHandler implements MessageHandlerInterface
{
    public function __invoke(SendSMSMessage $message)
    {
        try{
        $this->send3($message);
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

    public function send3(SendSMSMessage $message)
    {
        $url = 'https://api.payamak-panel.com/post/Send.asmx/SendSimpleSMS2?username=&password=&to=&from=&text=&isflash=';
        $url = str_replace('username=', 'username='.$_ENV['MELLI_USERNAME'], $url);
        $url = str_replace('password=', 'password='.$_ENV['MELLI_PASSWORD'], $url);
        $url = str_replace('to=', 'to='.$message->getNumber(), $url);
        $url = str_replace('from=', 'from='.$_ENV['MELLI_FROM_NUMBER'], $url);
        $url = str_replace('text=', 'text='.$message->getMessage(), $url);
        $url = str_replace('isflash=', 'isflash=true', $url);
        dump($url);
        $client = new Client();
        $response = $client->get($url);
//        $response = http_get($url);
        dump($response);
    }
}
