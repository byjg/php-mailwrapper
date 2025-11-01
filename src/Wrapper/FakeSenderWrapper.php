<?php

namespace ByJG\Mail\Wrapper;

use ByJG\Mail\Envelope;
use ByJG\Mail\SendResult;

class FakeSenderWrapper extends BaseWrapper
{
    #[\Override]
    public static function schema(): array
    {
        return ['fake', 'fakesender'];
    }

    #[\Override]
    public function send(Envelope $envelope): SendResult
    {
        return new SendResult(true, 'fake-id-123');
    }
}
