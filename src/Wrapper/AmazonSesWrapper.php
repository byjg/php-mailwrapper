<?php

namespace ByJG\Mail\Wrapper;

use Aws\Credentials\Credentials;
use Aws\Ses\SesClient;
use ByJG\Mail\Envelope;
use ByJG\Mail\Exception\InvalidEMailException;
use ByJG\Mail\Exception\InvalidMessageFormatException;

class AmazonSesWrapper extends PHPMailerWrapper
{

    public static function schema()
    {
        return ['ses'];
    }

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
     * @throws InvalidEMailException
     * @throws InvalidMessageFormatException
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function send(Envelope $envelope)
    {
        $this->validate($envelope);

        $mail = $this->prepareMailer($envelope);

        // Call the preSend to set all PHPMailer variables and get the correct header and body;
        $message = $mail->getFullMessageEnvelope();

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
