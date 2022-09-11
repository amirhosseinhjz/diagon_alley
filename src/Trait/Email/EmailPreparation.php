<?php

namespace App\Trait\Email;

use App\Message\Email\SendEmailMessages;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

trait EmailPreparation
{
    private function emailPreparation(TemplatedEmail $email,SendEmailMessages $message)
    {
        $email = $email->from($message->getEmailFrom())->to($message->getEmailTo());

        if ($message->getSubject())
        {
            $email = $email->subject($message->getSubject());
        }

        if ($message->getHtmlTemplatePath())
        {
            $email = $email->htmlTemplate($message->getHtmlTemplatePath());
        }

        if ($message->getTextTemplate())
        {
            $email = $email->textTemplate($message->getTextTemplate());
        }

        if ($message->getContext())
        {
            $email = $email->context($message->getContext());
        }

        if ($message->getAttachFromPath())
        {
            $email = $email->attachFromPath
            (
                $message->getAttachFromPath()['path'],
                array_key_exists('name',$message->getAttachFromPath()) ?: null
            );
        }

        if ($message->getEmbedFromPath())
        {
            $email = $email->embedFromPath
            (
                $message->getEmbedFromPath()['path'],
                array_key_exists('name',$message->getEmbedFromPath()) ?: null
            );
        }

        if ($message->getBcc())
        {
            $email = $email->bcc($message->getContext());
        }

        if ($message->getCc())
        {
            $email = $email->cc($message->getContext());
        }

        if ($message->getText())
        {
            $email = $email->text($message->getText());
        }

        return $email;
    }
}