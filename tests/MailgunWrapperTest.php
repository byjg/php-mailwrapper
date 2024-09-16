<?php

namespace Tests;

use ByJG\Mail\Envelope;
use ByJG\Mail\Exception\InvalidEMailException;
use ByJG\Mail\Exception\MailApiException;
use ByJG\Mail\Wrapper\MailgunApiWrapper;
use ByJG\WebRequest\Exception\MessageException;
use ByJG\WebRequest\Exception\NetworkException;
use ByJG\WebRequest\Exception\RequestException;
use ByJG\WebRequest\MockClient;
use ByJG\WebRequest\Psr7\Response;
use ByJG\Util\Uri;
use ByJG\WebRequest\Psr7\MemoryStream;
use Psr\Http\Client\ClientExceptionInterface;

class MailgunWrapperTest extends BaseWrapperTest
{

    /**
     * @param Envelope $envelope
     * @param MockClient $mock
     * @return bool
     * @throws InvalidEMailException
     * @throws MailApiException
     * @throws MessageException
     * @throws NetworkException
     * @throws RequestException
     * @throws ClientExceptionInterface
     */
    public function doMockedRequest(Envelope $envelope, MockClient $mock): bool
    {
        $object = new MailgunApiWrapper(new Uri('mailgun://YOUR_API_KEY@YOUR_DOMAIN'), $mock);
        return $object->send($envelope);
    }

    public function testGetRequest()
    {
        $wrapper = new MailgunApiWrapper(new Uri('mailgun://YOUR_API_KEY@YOUR_DOMAIN'));
        $request = $wrapper->getRequestObject();

        $this->assertEquals("api:YOUR_API_KEY", $request->getUri()->getUserInfo());
    }

    public function testBasicEnvelope()
    {
        $expectedResponse = new Response(200);
        $expectedResponse = $expectedResponse->withBody(new MemoryStream('{"id":"12345"}'));
        $mock = new MockClient($expectedResponse);

        $envelope = $this->getBasicEnvelope();

        $result = $this->doMockedRequest($envelope, $mock);
        $expected = $this->fixRequestBody(file_get_contents(__DIR__ . "/resources/basicenvelope-request.txt"));
        $this->assertEquals($expected, $this->fixRequestBody($mock->getRequestedObject()->getBody()->getContents()));
    }

    public function testFullEnvelope()
    {
        $expectedResponse = new Response(200);
        $expectedResponse = $expectedResponse->withBody(new MemoryStream('{"id":"12345"}'));
        $mock = new MockClient($expectedResponse);

        $envelope = $this->getFullEnvelope();

        $result = $this->doMockedRequest($envelope, $mock);
        $expected = $this->fixRequestBody(file_get_contents(__DIR__ . "/resources/fullenvelope-request.txt"));
        $this->assertEquals($expected, $this->fixRequestBody($mock->getRequestedObject()->getBody()->getContents()));
    }

    public function testAttachmentEnvelope()
    {
        $expectedResponse = new Response(200);
        $expectedResponse = $expectedResponse->withBody(new MemoryStream('{"id":"12345"}'));
        $mock = new MockClient($expectedResponse);

        $envelope = $this->getAttachmentEnvelope();

        $result = $this->doMockedRequest($envelope, $mock);
        $expected = $this->fixRequestBody(file_get_contents(__DIR__ . "/resources/attachmentenvelope-request.txt"));
        $this->assertEquals($expected, $this->fixRequestBody($mock->getRequestedObject()->getBody()->getContents()));
    }

    public function testEmbedImageEnvelope()
    {
        $expectedResponse = new Response(200);
        $expectedResponse = $expectedResponse->withBody(new MemoryStream('{"id":"12345"}'));
        $mock = new MockClient($expectedResponse);

        $envelope = $this->getEmbedImageEnvelope();

        $result = $this->doMockedRequest($envelope, $mock);
        $expected = $this->fixRequestBody(file_get_contents(__DIR__ . "/resources/embedenvelope-request.txt"));
        $this->assertEquals($expected, $this->fixRequestBody($mock->getRequestedObject()->getBody()->getContents()));
    }
}
