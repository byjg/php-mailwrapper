<?php

namespace ByJG\Mail\Wrapper;

use Aws\Credentials\Credentials;
use Aws\Ses\SesClient;
use ByJG\Mail\Envelope;

class AmazonSesWrapper extends PHPMailerWrapper
{

    /**
     * ses://accessid:aswsecret@region
     *
     * @param Envelope $envelope
     * @return bool
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

        //Send the message (which must be base 64 encoded):
        $ses = new SesClient([
            'credentials' => new Credentials(
                $this->connection->getUsername(),
                $this->connection->getPassword()
            ),
            'region' => $this->connection->getServer(),
            'version' => '2010-12-01'
        ]);

        $ses->sendRawEmail(
            [
                'RawMessage' => [
                    'Data' => base64_encode($message),
                ]
            ]
        );

        return true;
    }
}
