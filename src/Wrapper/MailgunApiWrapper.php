<?php

namespace ByJG\Mail\Wrapper;

use ByJG\Mail\Envelope;
use ByJG\Mail\Exception\MailApiException;
use ByJG\Util\UploadFile;
use ByJG\Util\WebRequest;

class MailgunApiWrapper extends PHPMailerWrapper
{
    /**
     * malgun://APIKEY@DOMAINNAME
     *
     * @param Envelope $envelope
     * @return bool
     * @throws MailApiException
     */
    public function send(Envelope $envelope)
    {
        $mail = $this->prepareMailer($envelope);

        // Call the preSend to set all PHPMailer variables and get the correct header and body;
        $message = $mail->getFullMessageEnvelope();

        // Fix BCC header because PHPMailer does not send to us
        foreach ((array)$envelope->getBCC() as $bccEmail) {
            $message = 'Bcc: ' . $bccEmail . "\n" . $message;
        }

        $domainName = $this->connection->getServer();

        $request = new WebRequest("https://api.mailgun.net/v3/$domainName/messages.mime");
        $request->setCredentials($this->connection->getUsername(), $this->connection->getPassword());

        $upload = [
            new UploadFile('message', $message, 'message.mime')
        ];
        // Add "To;"
        foreach ((array)$envelope->getTo() as $toEmail) {
            $upload[] = new UploadFile('to', $toEmail);
        }

        $result = $request->postUploadFile($upload);

        $resultJson = json_decode($result, true);
        if (!isset($resultJson['id'])) {
            throw new MailApiException('Mailgun: ' . $resultJson['message']);
        }

        return true;
    }
}
