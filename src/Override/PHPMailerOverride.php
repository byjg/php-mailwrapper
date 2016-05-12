<?php

namespace ByJG\Mail\Override;

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
            throw new \Exception('Invalid Format Message');
        }

        return ["header" => $this->MIMEHeader, "body" => $this->MIMEBody];
    }
}
