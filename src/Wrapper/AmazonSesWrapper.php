<?php

namespace ByJG\Mail\Wrapper;

use Aws\Common\Credentials\Credentials;
use Aws\Ses\SesClient;
use ByJG\Mail\Envelope;
use ByJG\Mail\MailConnection;

class AmazonSesWrapper extends PHPMailerWrapper
{
    /**
     * ses://accessid:aswsecret@region
     *
     * @param MailConnection $this->connection
     * @param Envelope $envelope
     */
    public function send(Envelope $envelope)
    {
        $mail = $this->prepareMailer($envelope);

        // Create body before headers in case body makes changes to headers (e.g. altering transfer encoding)
        $message = $mail->createHeader() . "\n\n" . $mail->createBody();

        //Send the message (which must be base 64 encoded):
        $ses = SesClient::factory([
                'credentials' => new Credentials($this->connection->getUsername(), $this->connection->getPassword()),
                'region' => $this->connection->getServer()
        ]);

        $ses->sendRawEmail(
            [
                'RawMessage' => [
                    'Data' => base64_encode($message),
                ]
            ]
        );
    }

}
