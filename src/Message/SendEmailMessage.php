<?php

namespace App\Message;

final class SendEmailMessage
{
    private const DEFINED_EMAIL='diagonAlley@test.com';

    private string $emailFrom;

//    #[Assert\n]
    private string $emailTo;

    private string $subject;

    private string $htmlTemplatePath;

    private array $context;

    private array $attachFromPath;

    private array $embedFromPath;

    private string $textTemplate;

    private string $bcc;

    private string $cc;

    private string $text;

    public function __construct(array $parameters)
    {
        $this->emailFrom = $parameters['emailFrom'] ?: self::DEFINED_EMAIL;
        $this->emailTo = $parameters['emailTo'];
        $this->subject = array_key_exists('subject',$parameters) ? $parameters['subject'] : '';
        $this->htmlTemplatePath = array_key_exists('htmlTemplatePath',$parameters) ? $parameters['htmlTemplatePath'] : '';
        $this->context = array_key_exists('context',$parameters) ? $parameters['context'] : [];
        $this->attachFromPath = array_key_exists('attachFromPath',$parameters) ? $parameters['attachFromPath'] : [];
        $this->embedFromPath = array_key_exists('embedFromPath',$parameters) ? $parameters['embedFromPath'] : [];
        $this->textTemplate = array_key_exists('textTemplate',$parameters) ? $parameters['textTemplate'] : '';
        $this->bcc = array_key_exists('bcc',$parameters) ? $parameters['bcc'] : '';
        $this->cc = array_key_exists('cc',$parameters) ? $parameters['cc'] : '';
        $this->text = array_key_exists('text',$parameters) ? $parameters['text'] : '';
    }

    /**
     * @return mixed
     */
    public function getEmailTo()
    {
        return $this->emailTo;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return mixed
     */
    public function getHtmlTemplatePath()
    {
        return $this->htmlTemplatePath;
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return mixed
     */
    public function getAttachFromPath()
    {
        return $this->attachFromPath;
    }

    /**
     * @return mixed
     */
    public function getEmbedFromPath()
    {
        return $this->embedFromPath;
    }

    /**
     * @return string
     */
    public function getEmailFrom(): string
    {
        return $this->emailFrom;
    }

    /**
     * @return mixed
     */
    public function getTextTemplate()
    {
        return $this->textTemplate;
    }

    /**
     * @return mixed
     */
    public function getBcc(): mixed
    {
        return $this->bcc;
    }

    /**
     * @return mixed
     */
    public function getCc(): mixed
    {
        return $this->cc;
    }

    public function getText()
    {
        return $this->text;
    }
}
