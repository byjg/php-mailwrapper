<?php

namespace ByJG\Mail\Wrapper;

use ByJG\Mail\Envelope;
use ByJG\Mail\SendResult;

/**
 * Sends nothing and always succeeds. Every envelope it is given is kept, so a test can
 * check what the application would have sent with getSent().
 */
class FakeSenderWrapper extends BaseWrapper
{
    /** @var list<Envelope> */
    protected static array $sent = [];

    #[\Override]
    public static function schema(): array
    {
        return ['fake', 'fakesender'];
    }

    #[\Override]
    public function send(Envelope $envelope): SendResult
    {
        self::$sent[] = $envelope;
        return new SendResult(true, 'fake-id-123');
    }

    /**
     * Every envelope sent through any FakeSenderWrapper since the last clear(), oldest first.
     *
     * @return list<Envelope>
     */
    public static function getSent(): array
    {
        return self::$sent;
    }

    /**
     * Forget the envelopes sent so far. Call it before the code under test sends.
     */
    public static function clear(): void
    {
        self::$sent = [];
    }
}
