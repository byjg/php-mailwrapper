<?php

namespace ByJG\Mail\Wrapper;

use ByJG\Mail\Envelope;
use ByJG\Mail\MailConnection;
use Exception;

class MandrillApiWrapper implements MailWrapperInterface
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
     * mandril://APIKEY
     *
     * @param Envelope $envelope
     * @throws Exception
     */
    public function send(Envelope $envelope)
    {
        $from = \ByJG\Mail\Util::decomposeEmail($envelope->getFrom());
        $fromName = $from['name'];
        $fromEmail = $from['email'];

        $bodyHtml = $envelope->getBody();

        $params = array();
        $params["key"] = $this->connection->getServer();
        $params["message"] = [
            'html' => $bodyHtml,
            'subject' => $envelope->getSubject(),
            'from_name' => $fromName,
            'from_email' => $fromEmail,
            'to' => [],
            'headers' => [
                'Reply-To' => $envelope->getReplyTo()
            ],
            "important" => false,
            "track_opens" => true,
            "track_clicks" => true,
            "auto_text" => true,
            "auto_html" => true,
        ];
        $params["async"] = true;
        $params["ip_pool"] = "Main Pool";

        $sendTo = array_unique(array_merge((array) $envelope->getTo(), (array) $envelope->getCC()));
        foreach ($sendTo as $email) {
            $params['message']['to'][] = [ 'email' => $email, 'type' => 'to'];
        }

        foreach ((array) $envelope->getBCC() as $email) {
            $params['message']['bcc_address'] = $email;
        }

        $json = json_encode($params);

        $request = new \ByJG\Util\WebRequest('https://mandrillapp.com/api/1.0/messages/send.json');
        $result = $request->postPayload($json, 'application/json');

        if (!$result) {
            throw new Exception('Cannot connect to Mandrill Api');
        } else {
            $resultJson = json_decode($result, true);
            if ($resultJson[0]['status'] == 'error') {
                throw new Exception('Mandrill: '.$resultJson[0]['message']);
            }
        }

        return true;
    }
}
