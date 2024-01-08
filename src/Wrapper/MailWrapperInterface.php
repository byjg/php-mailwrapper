<?php

namespace ByJG\Mail\Wrapper;

use ByJG\Mail\Envelope;

interface MailWrapperInterface
{
    public static function schema(): array;

    public function send(Envelope $envelope): bool;
}
