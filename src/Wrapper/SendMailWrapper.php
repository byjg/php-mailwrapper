<?php

namespace ByJG\Mail\Wrapper;

class SendMailWrapper implements MailWrapperInterface
{
    private $connection;

    public function __construct(\ByJG\Mail\MailConnection $connection)
    {
        $this->connection = $connection;
    }

    public function send(\ByJG\Mail\Envelope $envelope)
    {
        mail($envelope->getTo(), $envelope->getSubject(), $envelope->getBody());
    }

}
