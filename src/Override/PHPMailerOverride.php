<?php

namespace ByJG\Mail\Override;

class PHPMailerOverride extends \PHPMailer
{
    public function getFullMessageEnvelope()
    {
        if (!$this->preSend()) {
            throw new \Exception('Invalid Format Message');
        }

        return $this->MIMEHeader . $this->MIMEBody;
    }
}
