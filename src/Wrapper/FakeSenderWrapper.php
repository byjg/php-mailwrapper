<?php

namespace ByJG\Mail\Wrapper;

use ByJG\Mail\Envelope;

class FakeSenderWrapper extends BaseWrapper
{
    public function send(Envelope $envelope)
    {
        // Do nothing
    }
}
