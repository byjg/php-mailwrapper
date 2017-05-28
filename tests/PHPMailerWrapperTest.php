<?php

namespace Test;

use Aws\Credentials\Credentials;
use ByJG\Mail\Override\PHPMailerOverride;
use ByJG\Mail\Wrapper\AmazonSesWrapper;
use ByJG\Mail\Wrapper\PHPMailerWrapper;
use ByJG\Util\Uri;

require_once 'BaseWrapperTest.php';
require_once 'MockSender.php';

class PHPMailerWrapperTest extends BaseWrapperTest
{
    /**
     * @return \Test\MockSender
     */
    public function getMock($envelope)
    {
        $mock = $this->getMockBuilder(PHPMailerOverride::class)
            ->setMethods(['send'])
            ->setConstructorArgs([true])
            ->getMock();
        $mock->expects($this->once())
            ->method('send')
            ->will($this->returnValue(true));


        $object = $this->getMockBuilder(PHPMailerWrapper::class)
            ->setMethods(['getMailer'])
            ->setConstructorArgs([new Uri('smtp://username:password@host:25')])
            ->getMock();

        $object->expects($this->once())
            ->method('getMailer')
            ->will($this->returnValue($mock));

        $object->send($envelope);

        return $mock;
    }

    public function testBasicEnvelope()
    {
        $envelope = $this->getBasicEnvelope();

        $mock = $this->getMock($envelope);
        $expected = $this->fixVariableFields(file_get_contents(__DIR__ . '/resources/basicenvelope.txt'));
        $result = $this->fixVariableFields($mock->getFullMessageEnvelope());

        $this->assertEquals($expected, $result);
    }
}
