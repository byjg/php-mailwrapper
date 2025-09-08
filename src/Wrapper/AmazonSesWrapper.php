<?php

namespace ByJG\Mail\Wrapper;

use Aws\Credentials\Credentials;
use Aws\Ses\SesClient;
use ByJG\Mail\Envelope;
use ByJG\Mail\Exception\InvalidEMailException;
use ByJG\Mail\Exception\InvalidMessageFormatException;
use ByJG\Mail\SendResult;
use PHPMailer\PHPMailer\Exception;

class AmazonSesWrapper extends PHPMailerWrapper
{

    public static function schema(): array
    {
        return ['ses'];
    }

    /**
     * @return mixed
     */
    public function getSesClient(): mixed
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
     * @return SendResult
     * @throws Exception
     * @throws InvalidEMailException
     * @throws InvalidMessageFormatException
     */
    public function send(Envelope $envelope): SendResult
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

        $messageId = null;
        $messageLines = explode("\n", str_replace(["\r\n", "\r"], "\n", $message));

        foreach ($messageLines as $line) {
            if (stripos($line, 'Message-ID:') === 0) {
                $value = trim(substr($line, strlen('Message-ID:')));
                $messageId = trim($value, "<> \t\r\n");
                break;
            }
        }

        return new SendResult(true, $messageId);
    }
}
