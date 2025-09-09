<?php

namespace ByJG\Mail\Wrapper;

use ByJG\Mail\Envelope;
use ByJG\Mail\SendResult;

class FakeSenderWrapper extends BaseWrapper
{
    public static function schema(): array
    {
        return ['fake', 'fakesender'];
    }

    public function send(Envelope $envelope): SendResult
    {
        return new SendResult(true, 'fake-id-123');
    }
}
