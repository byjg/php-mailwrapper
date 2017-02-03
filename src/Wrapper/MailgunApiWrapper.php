<?php

namespace ByJG\Mail\Wrapper;

use ByJG\Mail\Envelope;
use ByJG\Mail\Exception\MailApiException;
use ByJG\Util\MultiPartItem;
use ByJG\Util\WebRequest;

class MailgunApiWrapper extends PHPMailerWrapper
{
    /**
     * malgun://api:APIKEY@DOMAINNAME
     *
     * @param Envelope $envelope
     * @return bool
     * @throws MailApiException
     */
    public function send(Envelope $envelope)
    {
        $message = [
            new MultiPartItem('from', $envelope->getFrom()),
            new MultiPartItem('subject', $envelope->getSubject()),
            new MultiPartItem('html', $envelope->getBody()),
            new MultiPartItem('text', $envelope->getBodyText()),
        ];


        foreach ((array)$envelope->getTo() as $to) {
            $message[] = new MultiPartItem('to', $to);
        }

        foreach ((array)$envelope->getBCC() as $bcc) {
            $message[] = new MultiPartItem('bcc', $bcc);
        }

        if (!empty($envelope->getReplyTo())) {
            $message[] = new MultiPartItem('h:Reply-To', $envelope->getReplyTo());
        }

        foreach ((array)$envelope->getCC() as $cc) {
            $message[] = new MultiPartItem('cc', $cc);
        }

        foreach ((array)$envelope->getAttachments() as $name => $attachment) {
            $message[] = new MultiPartItem(
                'attachment',
                file_get_contents($attachment['content']),
                $name,
                $attachment['content-type']
            );
        }

        $domainName = $this->connection->getServer();
        $request = new WebRequest("https://api.mailgun.net/v3/$domainName/messages");
        $request->setCredentials($this->connection->getUsername(), $this->connection->getPassword());

        $result = $request->postMultiPartForm($message);
        $resultJson = json_decode($result, true);
        if (!isset($resultJson['id'])) {
            throw new MailApiException('Mailgun: ' . $resultJson['message']);
        }

        return true;
    }
}
