<?php

namespace App\MessageHandler;

use App\Message\SendEmailMessage;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

#[AsMessageHandler]
final class SendEmailMessageHandler implements MessageHandlerInterface
{
    private $mailerService;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailerService = $mailer;
    }

    public function __invoke(SendEmailMessage $message)
    {
        $email = $this->emailPreparation(new TemplatedEmail(), $message);
        $this->mailerService->send($email);
    }

    private function emailPreparation(TemplatedEmail $email,SendEmailMessage $message)
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
