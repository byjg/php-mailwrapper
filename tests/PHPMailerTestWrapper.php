<?php

namespace Tests;

use ByJG\Mail\Envelope;
use ByJG\Mail\Exception\InvalidEMailException;
use ByJG\Mail\Exception\MailApiException;
use ByJG\Mail\Override\PHPMailerOverride;
use ByJG\Mail\Wrapper\PHPMailerWrapper;
use ByJG\Util\Uri;
use PHPMailer\PHPMailer\Exception;

class PHPMailerTestWrapper extends BaseTestWrapper
{
    /**
     * @param Envelope $envelope
     * @return array
     * @throws Exception
     * @throws InvalidEMailException
     * @throws MailApiException
     */
    public function doMockedRequest(Envelope $envelope): array
    {
        $mock = $this->getMockBuilder(PHPMailerOverride::class)
            ->onlyMethods(['send', 'getLastMessageID'])
            ->setConstructorArgs([true])
            ->getMock();

        $mock->expects($this->once())
            ->method('send')
            ->willReturn(true);

        $mock->expects($this->once())
            ->method('getLastMessageID')
            ->willReturn('mocked-message-id');

        $object = $this->getMockBuilder(PHPMailerWrapper::class)
            ->onlyMethods(['getMailer'])
            ->setConstructorArgs([new Uri('smtp://username:password@host:25')])
            ->getMock();

        $object->expects($this->once())
            ->method('getMailer')
            ->willReturn($mock);

        $sendResult = $object->send($envelope);

        return [$mock, $sendResult];
    }

    protected function send(Envelope $envelope, string $rawEmail): void
    {
        [$mock, $sendResult] = $this->doMockedRequest($envelope);
        $expected = $this->fixVariableFields(file_get_contents(__DIR__ . '/resources/' . $rawEmail . '.eml'));
        $result = $this->fixVariableFields($mock->getFullMessageEnvelope());

        $this->assertEquals($expected, $result);
        $this->assertTrue($sendResult->success);
        $this->assertEquals('mocked-message-id', $sendResult->id);
    }

    public function testBasicEnvelope(): void
    {
        $envelope = $this->getBasicEnvelope();
        $this->send($envelope, 'basicenvelope');
    }

    public function testFullEnvelope(): void
    {
        $envelope = $this->getFullEnvelope();
        $this->send($envelope, 'fullenvelope');
    }

    public function testAttachmentEnvelope(): void
    {
        $envelope = $this->getAttachmentEnvelope();
        $this->send($envelope, 'attachmentenvelope');
    }

    public function testEmbedImageEnvelope(): void
    {
        $envelope = $this->getEmbedImageEnvelope();
        $this->send($envelope, 'embedenvelope');
    }
}
