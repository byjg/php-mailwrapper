<?php

namespace ByJG\Mail\Wrapper;

use ByJG\Mail\Envelope;

class FakeSenderWrapper extends BaseWrapper
{
    public static function schema()
    {
        return ['fake', 'fakesender'];
    }

    public function send(Envelope $envelope)
    {
        // Do nothing
    }
}
