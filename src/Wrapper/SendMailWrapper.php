<?php

namespace ByJG\Mail\Wrapper;

use ByJG\Mail\Envelope;
use ByJG\Mail\Exception\InvalidEMailException;
use ByJG\Mail\Exception\InvalidMessageFormatException;
use ByJG\Mail\SendResult;
use PHPMailer\PHPMailer\Exception;

/**
 * Class SendMailWrapper
 *
 * sendmail://localhost
 *
 * @package ByJG\Mail\Wrapper
 */
class SendMailWrapper extends PHPMailerWrapper
{

    #[\Override]
    public static function schema(): array
    {
        return ['sendmail'];
    }

    /**
     * @param Envelope $envelope
     * @return SendResult
     * @throws Exception
     * @throws InvalidEMailException
     * @throws InvalidMessageFormatException
     */
    #[\Override]
    public function send(Envelope $envelope): SendResult
    {
        $this->validate($envelope);

        $mail = $this->prepareMailer($envelope);

        // Call the preSend to set all PHPMailer variables and get the correct header and body;
        $messageParts = $mail->getMessageEnvelopeParts();

        // Fix BCC header because PHPMailer does not send to us
        foreach ($envelope->getBCC() as $bccEmail) {
            $messageParts['header'] .= 'Bcc: ' . $bccEmail . "\n";
        }

        foreach ($envelope->getTo() as $toEmail) {
            mail($toEmail, $envelope->getSubject(), $messageParts['body'], $messageParts['header']);
        }

        return new SendResult(true, $mail->getLastMessageID());
    }
}
