<?php

namespace ByJG\Mail\Wrapper;

use ByJG\Mail\Envelope;

interface MailWrapperInterface
{
    public static function schema();

    public function send(Envelope $envelope);
}
