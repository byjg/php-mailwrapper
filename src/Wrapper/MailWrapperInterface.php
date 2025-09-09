<?php

namespace ByJG\Mail\Wrapper;

use ByJG\Mail\Envelope;
use ByJG\Mail\SendResult;

interface MailWrapperInterface
{
    public static function schema(): array;

    public function send(Envelope $envelope): SendResult;
}
