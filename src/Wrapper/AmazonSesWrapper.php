<?php

namespace ByJG\Mail\Wrapper;

use Aws\Common\Credentials\Credentials;
use Aws\Ses\SesClient;
use ByJG\Mail\Envelope;
use ByJG\Mail\MailConnection;
use Mail_mime;

class AmazonSesWrapper implements MailWrapperInterface
{
    /**
     * @var MailConnection
     */
    protected $connection = null;

    public function __construct(MailConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * ses://accessid:aswsecret@region
     *
     * @param MailConnection $this->connection
     * @param Envelope $envelope
     */
    public function send(Envelope $envelope)
    {

        $mailMime = new Mail_mime(array('eol' => "\n"));

        $mailMime->headers( ['Content-Type'  => 'text/html; charset=UTF-8'] );
        $mailMime->setFrom($envelope->getFrom());
        $mailMime->setSubject($envelope->getSubject());
        if ($envelope->getIsHtml())
        {
            $mailMime->setHTMLBody($envelope->getBody());
        }
        $mailMime->setTxtBody($envelope->getBodyText());
        foreach((array)$envelope->getAttachments() as $name => $attachment) {
            $mailMime->addAttachment($attachment['content'], $attachment['content-type'], $name);
        }

        $to = (array)$envelope->getTo();
        foreach ($to as $email) {
            $mailMime->addTo($email);
        }

        $cc = (array)$envelope->getCC();
        foreach ($cc as $email) {
            $mailMime->addCc($email);
        }

        $bcc = (array)$envelope->getBCC();
        foreach ($bcc as $email) {
            $mailMime->addBcc($email);
        }

        //Send the message (which must be base 64 encoded):
        $ses = SesClient::factory([
                'credentials' => new Credentials($this->connection->getUsername(), $this->connection->getPassword()),
                'region' => $this->connection->getServer()
        ]);

        $mime_params = array(
          'text_encoding' => '7bit',
          'text_charset'  => 'UTF-8',
          'html_charset'  => 'UTF-8',
          'head_charset'  => 'UTF-8'
        );

        $ses->sendRawEmail(
            [
                'RawMessage' => [
                    'Data' => base64_encode($mailMime->getMessage(null, $mime_params)),
                ]
            ]
        );
    }

}
