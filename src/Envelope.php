<?php

namespace ByJG\Mail;

class Envelope
{
    protected $from = "";
    protected $to = [];
    protected $subject = "";
    protected $replyTo = "";
    protected $cc = [];
    protected $bcc = [];
    protected $body = "";
    protected $isHtml = false;
    protected $isEmbbed = false;
    protected $attachment = [];

    /**
     *
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string $body
     * @param bool $isHtml
     */
    public function __construct($from = "", $to = "", $subject = "", $body = "", $isHtml = true)
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
     *
     * @param string $contentName
     * @param string $filePath
     * @param string $contentType
     */
    public function addAttachment($contentName, $filePath, $contentType)
    {
        $this->attachment[$contentName] = ['content' => $filePath, 'content-type' => $contentType];
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function setFrom($email, $name = null)
    {
        $this->from = Util::getFullEmail($email, $name);
    }

    public function getTo()
    {
        return $this->to;
    }

    public function setTo($email, $name = "")
    {
        $this->to = [Util::getFullEmail($email, $name)];
    }

    public function addTo($email, $name = "")
    {
        $this->to[] = Util::getFullEmail($email, $name);
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setSubject($value)
    {
        $this->subject = $value;
    }

    public function getReplyTo()
    {
        return $this->replyTo == "" ? $this->getFrom() : $this->replyTo;
    }

    public function setReplyTo($email)
    {
        $this->replyTo = Util::getFullEmail($email);
    }

    public function getCC()
    {
        return $this->cc;
    }

    public function setCC($email, $name = null)
    {
        $this->cc = [Util::getFullEmail($email, $name)];
    }

    public function addCC($email, $name = null)
    {
        $this->cc[] = Util::getFullEmail($email, $name);
    }

    public function getBCC()
    {
        return $this->bcc;
    }

    public function setBCC($email, $name = null)
    {
        $this->bcc = [Util::getFullEmail($email, $name)];
    }

    public function addBCC($email, $name = null)
    {
        $this->bcc[] = Util::getFullEmail($email, $name);
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($value)
    {
        $this->body = $value;
    }

    public function getBodyText()
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

    public function isHtml($value = null)
    {
        if (!is_null($value) && is_bool($value)) {
            $this->isHtml = $value;
        }

        return $this->isHtml;
    }

    public function isEmbbed($value = null)
    {
        if (!is_null($value) && is_bool($value)) {
            $this->isEmbbed = $value;
        }

        return $this->isEmbbed;
    }

    public function getAttachments()
    {
        return $this->attachment;
    }
}
