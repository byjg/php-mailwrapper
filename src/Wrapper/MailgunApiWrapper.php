<?php

namespace ByJG\Mail\Wrapper;

use ByJG\Mail\Envelope;
use ByJG\Mail\Exception\InvalidEMailException;
use ByJG\Mail\Exception\MailApiException;
use ByJG\Mail\SendResult;
use ByJG\WebRequest\Exception\MessageException;
use ByJG\WebRequest\Exception\NetworkException;
use ByJG\WebRequest\Exception\RequestException;
use ByJG\WebRequest\Helper\RequestMultiPart;
use ByJG\WebRequest\HttpClient;
use ByJG\WebRequest\MultiPartItem;
use ByJG\WebRequest\Psr7\Request;
use ByJG\Util\Uri;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

class MailgunApiWrapper extends PHPMailerWrapper
{
    private ClientInterface $client;

    private array $regions = [
        'us' => 'api.mailgun.net',
        'eu' => 'api.eu.mailgun.net',
    ];

    #[\Override]
    public static function schema(): array
    {
        return ['mailgun'];
    }

    public function __construct(Uri $uri, ?ClientInterface $client = null)
    {
        parent::__construct($uri);

        if (is_null($client)) {
            $this->client = new HttpClient();
        } else {
            $this->client = $client;
        }
    }

    /**
     * @return Request
     * @throws MessageException
     * @throws RequestException
     */
    public function getRequestObject(): RequestInterface
    {
        $domainName = $this->uri->getHost();
        $apiUri = $this->getApiUri();

        $uri = Uri::getInstance("https://$apiUri/v3/$domainName/messages")
            ->withUserInfo('api', $this->uri->getUsername());

        return Request::getInstance($uri)->withMethod("POST");
    }

    /**
     * malgun://api:APIKEY@DOMAINNAME
     *
     * @param Envelope $envelope
     * @return SendResult
     * @throws ClientExceptionInterface
     * @throws InvalidEMailException
     * @throws MailApiException
     * @throws MessageException
     * @throws NetworkException
     * @throws RequestException
     */
    #[\Override]
    public function send(Envelope $envelope): SendResult
    {
        $this->validate($envelope);

        $message = [
            new MultiPartItem('from', $envelope->getFrom()),
            new MultiPartItem('subject', $envelope->getSubject()),
            new MultiPartItem('html', $envelope->getBody()),
            new MultiPartItem('text', $envelope->getBodyText()),
        ];


        foreach ($envelope->getTo() as $to) {
            $message[] = new MultiPartItem('to', $to);
        }

        foreach ($envelope->getBCC() as $bcc) {
            $message[] = new MultiPartItem('bcc', $bcc);
        }

        if (!empty($envelope->getReplyTo())) {
            $message[] = new MultiPartItem('h:Reply-To', $envelope->getReplyTo());
        }

        foreach ($envelope->getCC() as $cc) {
            $message[] = new MultiPartItem('cc', $cc);
        }

        foreach ($envelope->getAttachments() as $name => $attachment) {
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

        $messageId = $resultJson['id'];

        return new SendResult(true, $messageId);
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
