<?php

namespace App\Listener\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ExceptionCustomizeListener
{
    public function onChange(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $message = sprintf(
            '%s',
            $exception->getMessage(),
        );
        $response = new Response();
        if ($exception instanceof AccessDeniedHttpException) {
            $response->setContent($message);
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
            $event->setResponse($response);
        }
    }
}
