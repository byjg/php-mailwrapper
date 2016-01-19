<?php

namespace ByJG\Mail\Wrapper;

use ByJG\Mail\Envelope;

interface MailWrapperInterface
{
    function send(Envelope $envelope);
}
