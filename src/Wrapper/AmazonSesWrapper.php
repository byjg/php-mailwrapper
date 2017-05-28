<?php

namespace ByJG\Mail\Wrapper;

use Aws\Credentials\Credentials;
use Aws\Ses\SesClient;
use ByJG\Mail\Envelope;

class AmazonSesWrapper extends PHPMailerWrapper
{
    /**
     * @return SesClient
     */
    public function getSesClient()
    {
        //Send the message (which must be base 64 encoded):
        return new SesClient([
            'credentials' => new Credentials(
                $this->uri->getUsername(),
                $this->uri->getPassword()
            ),
            'region' => $this->uri->getHost(),
            'version' => '2010-12-01'
        ]);
    }

    /**
     * ses://accessid:aswsecret@region
     *
     * @param Envelope $envelope
     * @return bool
     */
    public function send(Envelope $envelope)
    {
        $this->validate($envelope);

        $mail = $this->prepareMailer($envelope);

        // Call the preSend to set all PHPMailer variables and get the correct header and body;
        $message = $mail->getFullMessageEnvelope();

        // Fix BCC header because PHPMailer does not send to us
        foreach ((array)$envelope->getBCC() as $bccEmail) {
            $message = 'Bcc: ' . $bccEmail . "\n" . $message;
        }

        $ses = $this->getSesClient();

        $ses->sendRawEmail(
            [
                'RawMessage' => [
                    'Data' => $message,
                ]
            ]
        );

        return true;
    }
}
