<?php

namespace ByJG\Mail\Override;

use ByJG\Mail\Exception\InvalidMessageFormatException;

class PHPMailerOverride extends \PHPMailer
{
    public function getFullMessageEnvelope()
    {
        $parts = $this->getMessageEnvelopeParts();

        return $parts['header'] . $parts['body'];
    }

    public function getMessageEnvelopeParts()
    {
        if (!$this->preSend()) {
            throw new InvalidMessageFormatException('Invalid Message Format');
        }

        return ["header" => $this->MIMEHeader, "body" => $this->MIMEBody];
    }
}
