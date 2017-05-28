<?php

namespace Test;

use Aws\Credentials\Credentials;
use ByJG\Mail\Wrapper\AmazonSesWrapper;
use ByJG\Util\Uri;

require_once 'BaseWrapperTest.php';
require_once 'MockSender.php';

class AmazonSesWrapperTest extends BaseWrapperTest
{
    /**
     * @return \Test\MockSender
     */
    public function getMock($envelope)
    {
        $object = $this->getMockBuilder(AmazonSesWrapper::class)
            ->setMethods(['getSesClient'])
            ->setConstructorArgs([new Uri('ses://ACCESS_KEY_ID:SECRET_KEY@REGION')])
            ->getMock();

        $mockSes = new MockSender();
        $object->expects($this->once())
            ->method('getSesClient')
            ->will($this->returnValue($mockSes));

        $object->send($envelope);

        return $mockSes;
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

    public function testBasicEnvelope()
    {
        $envelope = $this->getBasicEnvelope();

        $mockSes = $this->getMock($envelope);
        $expected = $this->fixVariableFields(file_get_contents(__DIR__ . '/resources/basicenvelope.txt'));
        $mockSes->result['RawMessage']['Data'] = $this->fixVariableFields($mockSes->result['RawMessage']['Data']);

        $return = [
            'RawMessage' => [
                'Data' => $expected
                ]
        ];

        $this->assertEquals($return, $mockSes->result);
    }
}
