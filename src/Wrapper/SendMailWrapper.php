<?php

namespace ByJG\Mail\Wrapper;

use ByJG\Mail\Envelope;

class SendMailWrapper implements MailWrapperInterface
{

    public function send(Envelope $envelope)
    {
        mail($envelope->getTo(), $envelope->getSubject(), $envelope->getBody());
    }
}
