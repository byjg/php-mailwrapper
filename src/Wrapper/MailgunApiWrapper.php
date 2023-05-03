<?php

namespace ByJG\Mail\Wrapper;

use ByJG\Mail\Envelope;
use ByJG\Mail\Exception\InvalidEMailException;
use ByJG\Mail\Exception\MailApiException;
use ByJG\Util\CurlException;
use ByJG\Util\Helper\RequestMultiPart;
use ByJG\Util\HttpClient;
use ByJG\Util\MultiPartItem;
use ByJG\Util\Psr7\Request;
use ByJG\Util\Uri;

class MailgunApiWrapper extends PHPMailerWrapper
{
    private $client;

    private $regions = [
        'us' => 'api.mailgun.net',
        'eu' => 'api.eu.mailgun.net',
    ];

    public static function schema()
    {
        return ['mailgun'];
    }

    public function __construct(Uri $uri, HttpClient $client = null)
    {
        parent::__construct($uri);

        $this->client = $client;
        if (is_null($client)) {
            $this->client = new HttpClient();
        }
    }

    /**
     * @return Request
     * @throws \ByJG\Util\Psr7\MessageException
     */
    public function getRequestObject()
    {
        $domainName = $this->uri->getHost();
        $apiUri = $this->getApiUri();

        $uri = Uri::getInstanceFromString("https://$apiUri/v3/$domainName/messages")
            ->withUserInfo('api', $this->uri->getUsername());

        return Request::getInstance($uri)->withMethod("POST");
    }

    /**
     * malgun://api:APIKEY@DOMAINNAME
     *
     * @param Envelope $envelope
     * @return bool
     * @throws MailApiException
     * @throws InvalidEMailException
     * @throws CurlException
     * @throws \ByJG\Util\Psr7\MessageException
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

        $request = RequestMultiPart::buildMultiPart($message, $this->getRequestObject());

        $result = $this->client->sendRequest($request);
        if ($result->getStatusCode() != 200) {
            throw new MailApiException("Mailgun result code is " . $result->getStatusCode());
        }
        $resultJson = json_decode($result->getBody()->getContents(), true);
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
