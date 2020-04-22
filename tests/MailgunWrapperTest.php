<?php

namespace Test;

use ByJG\Mail\Envelope;
use ByJG\Mail\Wrapper\MailgunApiWrapper;
use ByJG\Util\MockClient;
use ByJG\Util\MultiPartItem;
use ByJG\Util\Psr7\Request;
use ByJG\Util\Uri;
use MintWare\Streams\MemoryStream;

require_once 'BaseWrapperTest.php';
require_once 'MockSender.php';

class MailgunWrapperTest extends BaseWrapperTest
{

    /**
     * @param $envelope
     * @return bool
     * @throws \ByJG\Mail\Exception\InvalidEMailException
     * @throws \ByJG\Mail\Exception\MailApiException
     * @throws \ByJG\Util\CurlException
     * @throws \ByJG\Util\Psr7\MessageException
     */
    public function doMockedRequest(Envelope $envelope, MockClient $mock)
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
        $expectedResponse = new \ByJG\Util\Psr7\Response(200);
        $expectedResponse->withBody(new MemoryStream('{"id":"12345"}'));
        $mock = new MockClient($expectedResponse);

        $envelope = $this->getBasicEnvelope();

        $result = $this->doMockedRequest($envelope, $mock);
        $expected = $this->fixRequestBody(file_get_contents(__DIR__ . "/resources/basicenvelope-request.txt"));
        $this->assertEquals($expected, $this->fixRequestBody($mock->getRequestedObject()->getBody()->getContents()));
    }

    public function testFullEnvelope()
    {
        $expectedResponse = new \ByJG\Util\Psr7\Response(200);
        $expectedResponse->withBody(new MemoryStream('{"id":"12345"}'));
        $mock = new MockClient($expectedResponse);

        $envelope = $this->getFullEnvelope();

        $result = $this->doMockedRequest($envelope, $mock);
        $expected = $this->fixRequestBody(file_get_contents(__DIR__ . "/resources/fullenvelope-request.txt"));
        $this->assertEquals($expected, $this->fixRequestBody($mock->getRequestedObject()->getBody()->getContents()));
    }

    public function testAttachmentEnvelope()
    {
        $expectedResponse = new \ByJG\Util\Psr7\Response(200);
        $expectedResponse->withBody(new MemoryStream('{"id":"12345"}'));
        $mock = new MockClient($expectedResponse);

        $envelope = $this->getAttachmentEnvelope();

        $result = $this->doMockedRequest($envelope, $mock);
        $expected = $this->fixRequestBody(file_get_contents(__DIR__ . "/resources/attachmentenvelope-request.txt"));
        $this->assertEquals($expected, $this->fixRequestBody($mock->getRequestedObject()->getBody()->getContents()));
    }

    public function testEmbedImageEnvelope()
    {
        $expectedResponse = new \ByJG\Util\Psr7\Response(200);
        $expectedResponse->withBody(new MemoryStream('{"id":"12345"}'));
        $mock = new MockClient($expectedResponse);

        $envelope = $this->getEmbedImageEnvelope();

        $result = $this->doMockedRequest($envelope, $mock);
        $expected = $this->fixRequestBody(file_get_contents(__DIR__ . "/resources/embedenvelope-request.txt"));
        $this->assertEquals($expected, $this->fixRequestBody($mock->getRequestedObject()->getBody()->getContents()));
    }
}
