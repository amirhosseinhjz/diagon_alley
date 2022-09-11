<?php

namespace App\Interface\Email;

interface EmailManagementInterface
{
    public function setEmailTo(string $emailTo): self;

    public function setSubject(string $subject): self;

    public function setHtmlTemplatePath(string $htmlTemplatePath): self;

    public function setContext(array $context): self;

    public function setAttachFromPath(array $attachFromPath): self;

    public function setEmbedFromPath(array $embedFromPath): self;

    public function setEmailFrom($emailFrom): self;

    public function setTextTemplate(string $textTemplate): self;

    public function setBcc(string $bcc): self;

    public function setCc(string $cc): self;

    public function setText(string $text): self;

    public function eventEmailClass(string $eventClassName): self;

    public function send();
}