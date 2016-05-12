<?php

namespace ByJG\Mail\Wrapper;

use ByJG\Mail\Envelope;

class SendMailWrapper extends PHPMailerWrapper
{

    public function send(Envelope $envelope)
    {
        $mail = $this->prepareMailer($envelope);

        // Call the preSend to set all PHPMailer variables and get the correct header and body;
        $messageParts = $mail->getMessageEnvelopeParts();

        // Fix BCC header because PHPMailer does not send to us
        foreach ((array)$envelope->getBCC() as $bccEmail) {
            $messageParts['header'] .= 'Bcc: ' . $bccEmail . "\n";
        }

        mail($envelope->getTo(), $envelope->getSubject(), $messageParts['body'], $messageParts['header']);

        return true;
    }
}
