<?php

namespace Tests;

use Aws\Credentials\Credentials;
use Aws\MockHandler;
use Aws\Result;
use Aws\Ses\SesClient;
use ByJG\Mail\Envelope;
use ByJG\Mail\Exception\InvalidEMailException;
use ByJG\Mail\Exception\InvalidMessageFormatException;
use ByJG\Mail\SendResult;
use ByJG\Mail\Wrapper\AmazonSesWrapper;
use ByJG\Util\Uri;
use PHPMailer\PHPMailer\Exception;

class AmazonSesWrapperTest extends BaseTestWrapper
{
    /**
     * Send through a real SesClient whose HTTP layer is the SDK's MockHandler: it answers
     * with a queued result and keeps the command, so the test can check what was sent.
     *
     * @return array{0: array<string, mixed>, 1: SendResult} The SendRawEmail arguments and the send result
     * @throws InvalidEMailException
     * @throws InvalidMessageFormatException
     * @throws Exception
     */
    public function doMockedRequest(Envelope $envelope): array
    {
        $handler = new MockHandler();
        $handler->append(new Result([
            'MessageId' => 'EXAMPLEf3f73d99b-c63fb06f-d263-41f8-a0fb-d0dc67d56c07-000000',
        ]));
        $sesClient = new SesClient([
            'credentials' => new Credentials('ACCESS_KEY_ID', 'SECRET_KEY'),
            'region' => 'us-east-1',
            'version' => '2010-12-01',
            'handler' => $handler,
        ]);

        $object = $this->getMockBuilder(AmazonSesWrapper::class)
            ->onlyMethods(['getSesClient'])
            ->setConstructorArgs([new Uri('ses://ACCESS_KEY_ID:SECRET_KEY@REGION')])
            ->getMock();
        $object->expects($this->once())
            ->method('getSesClient')
            ->willReturn($sesClient);

        $result = $object->send($envelope);

        return [$handler->getLastCommand()->toArray(), $result];
    }

    public function testGetSesClient(): void
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
    protected function send(Envelope $envelope, string $rawEmail): SendResult
    {
        [$sent, $result] = $this->doMockedRequest($envelope);
        $mimeMessage = $this->fixVariableFields(file_get_contents(__DIR__ . '/resources/' . $rawEmail . '.eml'));
        $sent = ['RawMessage' => ['Data' => $this->fixVariableFields($sent['RawMessage']['Data'])]];

        $expected = [
            'RawMessage' => [
                'Data' => $mimeMessage
            ]
        ];

        $this->assertEquals($expected, $sent);

        return $result;
    }

    /**
     * @throws Exception
     * @throws InvalidMessageFormatException
     * @throws InvalidEMailException
     */
    public function testBasicEnvelope(): void
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
    public function testFullEnvelope(): void
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
    public function testAttachmentEnvelope(): void
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
    public function testEmbedImageEnvelope(): void
    {
        $envelope = $this->getEmbedImageEnvelope();
        $result = $this->send($envelope, 'embedenvelope');

        $this->assertTrue($result->success);
        $this->assertEquals('EXAMPLEf3f73d99b-c63fb06f-d263-41f8-a0fb-d0dc67d56c07-000000', $result->id);
    }
}
