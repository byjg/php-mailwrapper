<?php

namespace ByJG\Mail;

class Envelope
{
    protected string $from = "";
    protected array $to = [];
    protected string $subject = "";
    protected string $replyTo = "";
    protected array $cc = [];
    protected array $bcc = [];
    protected string $body = "";
    protected bool $isHtml = false;
    protected array $attachment = [];

    /**
     *
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string $body
     * @param bool $isHtml
     */
    public function __construct(string $from = "", string $to = "", string $subject = "", string $body = "", bool $isHtml = true)
    {
        $this->from = Util::getFullEmail($from);
        $this->subject = $subject;
        $this->isHtml = $isHtml;
        $this->body = $body;

        if (!empty($to)) {
            $this->addTo($to);
        }
    }

    /**
     * @param string $contentName
     * @param string $filePath
     * @param string $contentType
     */
    public function addAttachment(string $contentName, string $filePath, string $contentType): void
    {
        $this->attachment[$contentName] = [
            'content' => $filePath,
            'content-type' => $contentType,
            'disposition' => 'attachment'
        ];
    }

    /**
     * @param string $contentName
     * @param string $filePath
     * @param string $contentType
     */
    public function addEmbedImage(string $contentName, string $filePath, string $contentType): void
    {
        $this->attachment[$contentName] = [
            'content' => $filePath,
            'content-type' => $contentType,
            'disposition' => 'inline'
        ];
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function setFrom(string $email, ?string $name = null): void
    {
        $this->from = Util::getFullEmail($email, $name);
    }

    public function getTo(): array
    {
        return $this->to;
    }

    public function setTo(string $email, string $name = ""): void
    {
        $this->to = [Util::getFullEmail($email, $name)];
    }

    public function addTo(string $email, string $name = ""): void
    {
        $this->to[] = Util::getFullEmail($email, $name);
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $value): void
    {
        $this->subject = $value;
    }

    public function getReplyTo(): string
    {
        return $this->replyTo == "" ? $this->getFrom() : $this->replyTo;
    }

    public function setReplyTo(string $email): void
    {
        $this->replyTo = Util::getFullEmail($email);
    }

    public function getCC(): array
    {
        return $this->cc;
    }

    public function setCC(string $email, ?string $name = null): void
    {
        $this->cc = [Util::getFullEmail($email, $name)];
    }

    public function addCC(string $email, ?string $name = null): void
    {
        $this->cc[] = Util::getFullEmail($email, $name);
    }

    public function getBCC(): array
    {
        return $this->bcc;
    }

    public function setBCC(string $email, ?string $name = null): void
    {
        $this->bcc = [Util::getFullEmail($email, $name)];
    }

    public function addBCC(string $email, ?string $name = null): void
    {
        $this->bcc[] = Util::getFullEmail($email, $name);
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $value): void
    {
        $this->body = $value;
    }

    public function getBodyText(): string
    {
        $body = preg_replace(
            [
                '~<h.*?>(.*?)</h.*?>~',
                '~<div.*?>(.*?)</div>~',
                '~<p.*?>(.*?)</p>~',
                '~<br.*?>~'
            ],
            [
                "# $1\n\n",
                "$1\n",
                "$1\n",
                "\n"
            ],
            $this->body
        );

        return strip_tags($body);
    }

    public function isHtml(?bool $bool = null): bool
    {
        if (is_bool($bool)) {
            $this->isHtml = $bool;
        }

        return $this->isHtml;
    }

    public function getAttachments(): array
    {
        return $this->attachment;
    }
}
