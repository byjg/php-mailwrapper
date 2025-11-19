<?php

namespace Tests;




use ByJG\Mail\Envelope;
use ByJG\Mail\SendResult;
use ByJG\Mail\Wrapper\FakeSenderWrapper;
use ByJG\Util\Uri;

class FakeSenderTestWrapper extends BaseTestWrapper
{
    /**
     * @param Envelope $envelope
     * @return SendResult
     */
    public function doFakeSend(Envelope $envelope): SendResult
    {
        $uri = new Uri('fake://user:password@domain.com');
        $wrapper = new FakeSenderWrapper($uri);

        return $wrapper->send($envelope);
    }

    public function testBasicEnvelope(): void
    {
        $envelope = $this->getBasicEnvelope();

        $result = $this->doFakeSend($envelope);

        $this->assertTrue($result->success);
        $this->assertEquals('fake-id-123', $result->id);
    }

    public function testFullEnvelope(): void
    {
        $envelope = $this->getFullEnvelope();

        $result = $this->doFakeSend($envelope);

        $this->assertTrue($result->success);
        $this->assertEquals('fake-id-123', $result->id);
    }

    public function testAttachmentEnvelope(): void
    {
        $envelope = $this->getAttachmentEnvelope();

        $result = $this->doFakeSend($envelope);

        $this->assertTrue($result->success);
        $this->assertEquals('fake-id-123', $result->id);
    }

    public function testEmbedImageEnvelope(): void
    {
        $envelope = $this->getEmbedImageEnvelope();

        $result = $this->doFakeSend($envelope);

        $this->assertTrue($result->success);
        $this->assertEquals('fake-id-123', $result->id);
    }
}
