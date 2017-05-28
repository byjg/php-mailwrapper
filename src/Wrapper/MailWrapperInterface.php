<?php

namespace ByJG\Mail\Wrapper;

use ByJG\Mail\Envelope;

interface MailWrapperInterface
{
    public function send(Envelope $envelope);
}
