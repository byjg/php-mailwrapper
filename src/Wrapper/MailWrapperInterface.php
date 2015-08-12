<?php

namespace ByJG\Mail\Wrapper;

use ByJG\Mail\Envelope;
use ByJG\Mail\MailConnection;

interface MailWrapperInterface
{

    function __construct(MailConnection $connection);

    function send(Envelope $envelope);
}
