<?php

namespace ByJG\Mail;

use ByJG\Mail\Wrapper\AmazonSesWrapper;
use ByJG\Mail\Wrapper\MailWrapperInterface;
use ByJG\Mail\Wrapper\MandrillApiWrapper;
use ByJG\Mail\Wrapper\PHPMailerWrapper;
use ErrorException;
use InvalidArgumentException;

class Envelope
{
    protected $_from = "";
    protected $_to = [];
    protected $_subject = "";
    protected $_replyTo = "";
    protected $_cc = [];
    protected $_bcc = [];
    protected $_body = "";
    protected $_isHtml = false;
    protected $_isEmbbed = false;
    protected $_attachment = [];

    public function __construct($from = "", $to = "", $subject = "", $body = "", $isHtml = true)
    {
        $this->_from = Util::getFullEmail($from);
        $this->_subject = $subject;
        $this->_isHtml = $isHtml;
        $this->_body = $body;

        if (!empty($to)) {
            $this->addTo($to);
        }
    }

    public function addAttachment($name, $value, $contentType)
    {
        $this->_attachment[$name] = [ 'content' => $value, 'content-type' => $contentType];
    }

    public function getFrom()
    {
        return $this->_from;
    }

    public function setFrom($email, $name = null)
    {
        $this->_from = Util::getFullEmail($email, $name);
    }

    public function getTo()
    {
        return $this->_to;
    }

    public function addTo($email, $name = "")
    {
        $this->_to[] = Util::getFullEmail($email, $name);
    }

    public function getSubject()
    {
        return $this->_subject;
    }

    public function setSubject($value)
    {
        $this->_subject = $value;
    }

    public function getReplyTo()
    {
        return $this->_replyTo == "" ? $this->getFrom() : $this->_replyTo;
    }

    public function setReplyTo($email)
    {
        $this->_replyTo = Util::getFullEmail($email);
    }

    public function getCC()
    {
        return $this->_cc;
    }

    public function addCC($email, $name = null)
    {
        $this->_cc[] = Util::getFullEmail($email, $name);
    }

    public function getBCC()
    {
        return $this->_bcc;
    }

    public function addBCC($email, $name = null)
    {
        $this->_bcc[] = Util::getFullEmail($email, $name);
    }

    public function getBody()
    {
        return $this->_body;
    }

    public function setBody($value)
    {
        $this->_body = $value;
    }

    public function getBodyText()
    {
        $body = preg_replace(
            [
            '~<div.*?>(.*?)</div>~',
            '~<p.*?>(.*?)</p>~',
            '~<br.*?>~'
            ], [
            "$1\n",
            "$1\n",
            "\n"
            ], $this->_body
        );

        return strip_tags($body);
    }

    public function isHtml($value = null)
    {
        if (!is_null($value) && is_bool($value)) {
            $this->_isHtml = $value;
        } else {
            return $this->_isHtml;
        }
    }

    public function isEmbbed($value = null)
    {
        if (!is_null($value) && is_bool($value)) {
            $this->_isEmbbed = $value;
        } else {
            return $this->_isEmbbed;
        }
    }

    public function getAttachments()
    {
        return $this->_attachment;
    }

    public function send(MailWrapperInterface $mailer, $to = "")
    {
        if (0 === count($this->getTo()) && $to == "") {
            throw new ErrorException("Destination Email was not provided");
        } elseif ($to != "") {
            $this->addTo($to);
        }

        if ($this->getFrom() == "") {
            throw new ErrorException("Source Email was not provided");
        }

        $mailer->send($this);
    }

    /**
     *
     * @param MailConnection $connection
     * @return MailWrapperInterface
     * @throws InvalidArgumentException
     */
    public static function mailerFactory(MailConnection $connection)
    {
        $protocol = $connection->getProtocol();
        if (in_array($protocol, [ 'smtp', 'ssl', 'tls'])) {
            $mail = new PHPMailerWrapper($connection);
        } elseif ($protocol === "ses") {
            $mail = new AmazonSesWrapper($connection);
        } elseif ($protocol === "mandrill") {
            $mail = new MandrillApiWrapper($connection);
        } else {
            throw new InvalidArgumentException("Connection '".$connection->getProtocol()."' is not valid");
        }

        return $mail;
    }
}
