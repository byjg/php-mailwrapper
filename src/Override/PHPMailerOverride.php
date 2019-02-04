<?php

namespace ByJG\Mail\Override;

use ByJG\Mail\Exception\InvalidMessageFormatException;
use PHPMailer\PHPMailer\PHPMailer;

class PHPMailerOverride extends PHPMailer
{
    public function __construct($exceptions = null)
    {
        parent::__construct($exceptions);
        $this->XMailer = 'PHPMailer (https://github.com/PHPMailer/PHPMailer)';
    }

    /**
     * @return string
     * @throws InvalidMessageFormatException
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function getFullMessageEnvelope()
    {
        $parts = $this->getMessageEnvelopeParts();

        return $parts['header'] . $parts['body'];
    }

    /**
     * @return array
     * @throws InvalidMessageFormatException
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function getMessageEnvelopeParts()
    {
        if (!$this->preSend()) {
            throw new InvalidMessageFormatException('Invalid Message Format');
        }

        return ["header" => $this->MIMEHeader, "body" => $this->MIMEBody];
    }
}
