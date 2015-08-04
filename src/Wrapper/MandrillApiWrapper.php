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


        foreach ((array) $envelope->getTo() as $email) {
            $params['message']['to'][] = [ 'email' => $email, 'type' => 'to'];
        }

        foreach ((array) $envelope->getCC() as $email) {
            $params['message']['to'][] = [ 'email' => $email, 'type' => 'to'];
        }

        foreach ((array) $envelope->getBCC() as $email) {
            $params['message']['bcc_address'] = $email;
        }

        $json = json_encode($params);

        $ch = curl_init('https://mandrillapp.com/api/1.0/messages/send.json');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Content-Length: ' . strlen($json))
        );

        $result = curl_exec($ch);

        if (!$result) {
            throw new Exception('Cannot connect to Mandrill Api');
        } else {
            $resultJson = json_decode($result, true);
            if ($resultJson[0]['status'] == 'error') {
                throw new Exception('Mandrill: ' . $resultJson[0]['message']);
            }
        }
    }
}
