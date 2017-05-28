<?php

namespace Test;

use ByJG\Mail\Wrapper\MailgunApiWrapper;
use ByJG\Util\MultiPartItem;
use ByJG\Util\Uri;

require_once 'BaseWrapperTest.php';
require_once 'MockSender.php';

class MailgunWrapperTest extends BaseWrapperTest
{
    /**
     * @param $envelope
     * @return \Test\MockSender
     */
    public function getMock($envelope)
    {
        $object = $this->getMockBuilder(MailgunApiWrapper::class)
            ->setMethods(['getRequestObject'])
            ->setConstructorArgs([new Uri('mailgun://YOUR_API_KEY@YOUR_DOMAIN')])
            ->getMock();

        $mock = new MockSender();
        $object->expects($this->once())
            ->method('getRequestObject')
            ->will($this->returnValue($mock));

        $object->send($envelope);

        return $mock;
    }

    public function testGetRequest()
    {
        $wrapper = new MailgunApiWrapper(new Uri('mailgun://YOUR_API_KEY@YOUR_DOMAIN'));
        $request = $wrapper->getRequestObject();

        $this->assertEquals($request->getCurlOption(CURLOPT_HTTPAUTH), CURLAUTH_BASIC);
        $this->assertEquals($request->getCurlOption(CURLOPT_USERPWD), "api:YOUR_API_KEY");
    }

    public function testBasicEnvelope()
    {
        $envelope = $this->getBasicEnvelope();

        $mock = $this->getMock($envelope);

        $expected = [
            new MultiPartItem('from', 'from@email.com'),
            new MultiPartItem('subject', 'Subject'),
            new MultiPartItem('html', '<h1>Title</h1>Body'),
            new MultiPartItem('text', "# Title\n\nBody"),
            new MultiPartItem('to', 'to@email.com'),
            new MultiPartItem('h:Reply-To', 'from@email.com')
        ];

        $this->assertEquals($expected, $mock->result);
    }
}
