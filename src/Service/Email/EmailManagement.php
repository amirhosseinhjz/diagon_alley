<?php
namespace App\Service\Email;

use App\Interface\Email\EmailManagementInterface;
use App\Message\SendEmailMessage;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class EmailManagement implements EmailManagementInterface
{
    private array $parameters;

    protected $bus;

    private string $eventClassName;

    public function __construct(MessageBusInterface $bus)
    {
        $this->reset();
        $this->bus = $bus;
    }

   private function reset()
   {
       $this->parameters = [];
       $this->eventClassName = SendEmailMessage::class;
   }

    public function setEmailTo(string $emailTo): self
    {
         $this->parameters['emailTo'] = $emailTo;
        return $this;
    }

    public function setSubject(string $subject): self
    {
        $this->parameters['subject'] = $subject;
        return $this;
    }

    public function setHtmlTemplatePath(string $htmlTemplatePath): self
    {
        $this->parameters['htmlTemplatePath'] = $htmlTemplatePath;
        return $this;
    }

    public function setContext(array $context): self
    {
        $this->parameters['context'] = $context;
        return $this;
    }

    public function setAttachFromPath(array $attachFromPath): self
    {
        $this->parameters['attachFromPath'] = $attachFromPath;
        return $this;
    }

    public function setEmbedFromPath(array $embedFromPath): self
    {
        $this->parameters['embedFromPath'] = $embedFromPath;
        return $this;
    }

    public function setEmailFrom($emailFrom): self
    {
        $this->parameters['emailFrom'] = $emailFrom;
        return $this;
    }

    public function setTextTemplate(string $textTemplate): self
    {
        $this->parameters['textTemplate'] = $textTemplate;
        return $this;
    }

    public function setBcc(string $bcc): self
    {
        $this->parameters['bcc'] = $bcc;
        return $this;
    }

    public function setCc(string $cc): self
    {
        $this->parameters['cc'] = $cc;
        return $this;
    }

    public function setText(string $text): self
    {
        $this->parameters['text'] = $text;
        return $this;
    }

    public function eventEmailClass(string $eventClassName): self
    {
        $this->eventClassName = $eventClassName;
        return $this;
    }

    public function send()
    {
        $this->bus->dispatch(new $this->eventClassName($this->parameters));
    }
}
