<?php

namespace Tests;

use ByJG\Mail\Exception\InvalidEMailException;
use ByJG\Mail\Exception\MailApiException;
use ByJG\Mail\Override\PHPMailerOverride;
use ByJG\Mail\Wrapper\PHPMailerWrapper;
use ByJG\Util\Uri;
use PHPMailer\PHPMailer\Exception;

class PHPMailerWrapperTest extends BaseWrapperTest
{
    /**
     * @param $envelope
     * @return array
     * @throws Exception
     * @throws InvalidEMailException
     * @throws MailApiException
     */
    public function doMockedRequest($envelope): array
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

    protected function send($envelope, $rawEmail)
    {
        [$mock, $sendResult] = $this->doMockedRequest($envelope);
        $expected = $this->fixVariableFields(file_get_contents(__DIR__ . '/resources/' . $rawEmail . '.eml'));
        $result = $this->fixVariableFields($mock->getFullMessageEnvelope());

        $this->assertEquals($expected, $result);
        $this->assertTrue($sendResult->success);
        $this->assertEquals('mocked-message-id', $sendResult->id);
    }

    public function testBasicEnvelope()
    {
        $envelope = $this->getBasicEnvelope();
        $this->send($envelope, 'basicenvelope');
    }

    public function testFullEnvelope()
    {
        $envelope = $this->getFullEnvelope();
        $this->send($envelope, 'fullenvelope');
    }

    public function testAttachmentEnvelope()
    {
        $envelope = $this->getAttachmentEnvelope();
        $this->send($envelope, 'attachmentenvelope');
    }

    public function testEmbedImageEnvelope()
    {
        $envelope = $this->getEmbedImageEnvelope();
        $this->send($envelope, 'embedenvelope');
    }
}
