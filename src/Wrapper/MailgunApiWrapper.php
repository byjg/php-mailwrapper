<?php

namespace ByJG\Mail\Wrapper;

use ByJG\Mail\Envelope;
use ByJG\Mail\Exception\MailApiException;
use ByJG\Util\UploadFile;
use ByJG\Util\WebRequest;
use Mailgun\Mailgun;

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
        $message = [
            'from'    => $envelope->getFrom(),
            'to'      => $envelope->getTo(),
            'subject' => $envelope->getSubject(),
            'html'    => $envelope->getBody(),
        ];

        if (!empty($envelope->getBCC())) {
            $message['bcc'] = $envelope->getBCC();
        }

        if (!empty($envelope->getReplyTo())) {
            $message['replyTo'] = $envelope->getReplyTo();
        }

        if (!empty($envelope->getCC())) {
            $message['cc'] = $envelope->getCC();
        }

        if (!empty($envelope->getAttachments())) {
            $message['attatchments'] = $envelope->getAttachments();
        }

        try {
            $mailgun = new Mailgun($this->connection->getPassword());
            $mailgun->sendMessage($this->connection->getServer(), $message);
        } catch (\Exception $e) {
            throw new MailApiException('Mailgun: ' . $e->getMessage());
        }

        return true;
    }
}
