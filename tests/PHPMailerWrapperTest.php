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
     * @return PHPMailerOverride
     * @throws InvalidEMailException
     * @throws MailApiException
     * @throws Exception
     */
    public function doMockedRequest($envelope): PHPMailerOverride
    {
        $mock = $this->getMockBuilder(PHPMailerOverride::class)
            ->onlyMethods(['send'])
            ->setConstructorArgs([true])
            ->getMock();
        $mock->expects($this->once())
            ->method('send')
            ->will($this->returnValue(true));


        $object = $this->getMockBuilder(PHPMailerWrapper::class)
            ->onlyMethods(['getMailer'])
            ->setConstructorArgs([new Uri('smtp://username:password@host:25')])
            ->getMock();

        $object->expects($this->once())
            ->method('getMailer')
            ->will($this->returnValue($mock));

        $object->send($envelope);

        return $mock;
    }

    protected function send($envelope, $rawEmail)
    {
        $mock = $this->doMockedRequest($envelope);
        $expected = $this->fixVariableFields(file_get_contents(__DIR__ . '/resources/' . $rawEmail . '.eml'));
        $result = $this->fixVariableFields($mock->getFullMessageEnvelope());

        $this->assertEquals($expected, $result);
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
