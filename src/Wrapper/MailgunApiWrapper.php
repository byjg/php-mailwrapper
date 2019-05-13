<?php

namespace ByJG\Mail\Wrapper;

use ByJG\Mail\Envelope;
use ByJG\Mail\Exception\InvalidEMailException;
use ByJG\Mail\Exception\MailApiException;
use ByJG\Util\CurlException;
use ByJG\Util\MultiPartItem;
use ByJG\Util\WebRequest;

class MailgunApiWrapper extends PHPMailerWrapper
{

    private $regions = [
        'us' => 'api.mailgun.net',
        'eu' => 'api.eu.mailgun.net',
    ];

    /**
     * @return \ByJG\Util\WebRequest
     */
    public function getRequestObject()
    {
        $domainName = $this->uri->getHost();
        $apiUri = $this->getApiUri();
        $request = new WebRequest("https://$apiUri/v3/$domainName/messages");
        $request->setCredentials('api', $this->uri->getUsername());

        return $request;
    }

    /**
     * malgun://api:APIKEY@DOMAINNAME
     *
     * @param Envelope $envelope
     * @return bool
     * @throws MailApiException
     * @throws InvalidEMailException
     * @throws CurlException
     */
    public function send(Envelope $envelope)
    {
        $this->validate($envelope);

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
                $attachment['disposition'],
                file_get_contents($attachment['content']),
                $name,
                $attachment['content-type']
            );
        }

        $request = $this->getRequestObject();
        $result = $request->postMultiPartForm($message);
        $resultJson = json_decode($result, true);
        if (!isset($resultJson['id'])) {
            throw new MailApiException('Mailgun: ' . $resultJson['message']);
        }

        return true;
    }

    private function getApiUri()
    {
        $query = $this->uri->getQueryPart('region');
        if (isset($this->regions[$query])) {
            return $this->regions[$query];
        }

        return $this->regions['us'];
    }

}
