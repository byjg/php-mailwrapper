<?php

namespace Tests;

use Aws\Credentials\Credentials;
use ByJG\Mail\Exception\InvalidEMailException;
use ByJG\Mail\Exception\InvalidMessageFormatException;
use ByJG\Mail\SendResult;
use ByJG\Mail\Wrapper\AmazonSesWrapper;
use ByJG\Util\Uri;
use PHPMailer\PHPMailer\Exception;

class AmazonSesWrapperTest extends BaseWrapperTest
{
    /**
     * @param $envelope
     * @return array
     * @throws InvalidEMailException
     * @throws InvalidMessageFormatException
     * @throws Exception
     */
    public function doMockedRequest($envelope): array
    {
        $object = $this->getMockBuilder(AmazonSesWrapper::class)
            ->onlyMethods(['getSesClient'])
            ->setConstructorArgs([new Uri('ses://ACCESS_KEY_ID:SECRET_KEY@REGION')])
            ->getMock();

        $mock = new MockSender();
        $object->expects($this->once())
            ->method('getSesClient')
            ->will($this->returnValue($mock));

        $result = $object->send($envelope);

        return [$mock, $result];
    }

    public function testGetSesClient()
    {
        $sesWrapper = new AmazonSesWrapper(new Uri('ses://ACCESS_KEY_ID:SECRET_KEY@REGION'));
        $sesClient = $sesWrapper->getSesClient();

        $credentials = $sesClient->getCredentials()->wait(true);
        $this->assertEquals(
            new Credentials(
                'ACCESS_KEY_ID',
                'SECRET_KEY'
            ),
            $credentials
        );
        $this->assertEquals('REGION', $sesClient->getRegion());
        $this->assertEquals('2010-12-01', $sesClient->getApi()->getApiVersion());
    }

    /**
     * @throws Exception
     * @throws InvalidMessageFormatException
     * @throws InvalidEMailException
     */
    protected function send($envelope, $rawEmail): SendResult
    {
        [$mock, $result] = $this->doMockedRequest($envelope);
        $mimeMessage = $this->fixVariableFields(file_get_contents(__DIR__ . '/resources/' . $rawEmail . '.eml'));
        $mock->result['RawMessage']['Data'] = $this->fixVariableFields($mock->result['RawMessage']['Data']);

        $expected = [
            'RawMessage' => [
                'Data' => $mimeMessage
            ]
        ];

        $this->assertEquals($expected, $mock->result);

        return $result;
    }

    /**
     * @throws Exception
     * @throws InvalidMessageFormatException
     * @throws InvalidEMailException
     */
    public function testBasicEnvelope()
    {
        $envelope = $this->getBasicEnvelope();
        $result = $this->send($envelope, 'basicenvelope');

        $this->assertTrue($result->success);
        $this->assertEquals('EXAMPLEf3f73d99b-c63fb06f-d263-41f8-a0fb-d0dc67d56c07-000000', $result->id);
    }

    /**
     * @throws Exception
     * @throws InvalidMessageFormatException
     * @throws InvalidEMailException
     */
    public function testFullEnvelope()
    {
        $envelope = $this->getFullEnvelope();
        $result = $this->send($envelope, 'fullenvelope');

        $this->assertTrue($result->success);
        $this->assertEquals('EXAMPLEf3f73d99b-c63fb06f-d263-41f8-a0fb-d0dc67d56c07-000000', $result->id);
    }

    /**
     * @throws Exception
     * @throws InvalidMessageFormatException
     * @throws InvalidEMailException
     */
    public function testAttachmentEnvelope()
    {
        $envelope = $this->getAttachmentEnvelope();
        $result = $this->send($envelope, 'attachmentenvelope');

        $this->assertTrue($result->success);
        $this->assertEquals('EXAMPLEf3f73d99b-c63fb06f-d263-41f8-a0fb-d0dc67d56c07-000000', $result->id);
    }

    /**
     * @throws Exception
     * @throws InvalidMessageFormatException
     * @throws InvalidEMailException
     */
    public function testEmbedImageEnvelope()
    {
        $envelope = $this->getEmbedImageEnvelope();
        $result = $this->send($envelope, 'embedenvelope');

        $this->assertTrue($result->success);
        $this->assertEquals('EXAMPLEf3f73d99b-c63fb06f-d263-41f8-a0fb-d0dc67d56c07-000000', $result->id);
    }
}
