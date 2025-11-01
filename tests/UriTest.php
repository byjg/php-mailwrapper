<?php

namespace ByJG\Mail;

use ByJG\Util\Uri;
use PHPUnit\Framework\TestCase;

class UriTest extends TestCase
{
    public function testGetFull(): void
    {
        $object = new Uri("smtp://user:pass@server:1234");
        $this->assertEquals('smtp', $object->getScheme());
        $this->assertEquals('user', $object->getUsername());
        $this->assertEquals('pass', $object->getPassword());
        $this->assertEquals('server', $object->getHost());
        $this->assertEquals('1234', $object->getPort());

        $object2 = new Uri("smtp://us876sdj!@#8er:jayyts!@#445@server:1234");
        $this->assertEquals('smtp', $object2->getScheme());
        $this->assertEquals('us876sdj!@#8er', $object2->getUsername());
        $this->assertEquals('jayyts!@#445', $object2->getPassword());
        $this->assertEquals('server', $object2->getHost());
        $this->assertEquals('1234', $object2->getPort());

        $object3 = new Uri("ses://user:pass@us-east-1");
        $this->assertEquals('ses', $object3->getScheme());
        $this->assertEquals('user', $object3->getUsername());
        $this->assertEquals('pass', $object3->getPassword());
        $this->assertEquals('us-east-1', $object3->getHost());
        $this->assertEquals(null, $object3->getPort());

        $object4 = new Uri("mandrill://u181298wiuqsd9sakuj1239821qsd");
        $this->assertEquals('mandrill', $object4->getScheme());
        $this->assertEquals(null, $object4->getUsername());
        $this->assertEquals(null, $object4->getPassword());
        $this->assertEquals('u181298wiuqsd9sakuj1239821qsd', $object4->getHost());
        $this->assertEquals(null, $object4->getPort());

        $object7 = new Uri('mandrill://_akaka_S3ksksksg3ew');
        $this->assertEquals('mandrill', $object7->getScheme());
        $this->assertEquals(null, $object7->getUsername());
        $this->assertEquals(null, $object7->getPassword());
        $this->assertEquals('_akaka_S3ksksksg3ew', $object7->getHost());
        $this->assertEquals(null, $object7->getPort());

        $object8 = new Uri('smtp://us#$%er:pa!*&$ss@host.com.br:45');
        $this->assertEquals('smtp', $object8->getScheme());
        $this->assertEquals('us#$%er', $object8->getUsername());
        $this->assertEquals('pa!*&$ss', $object8->getPassword());
        $this->assertEquals('host.com.br', $object8->getHost());
        $this->assertEquals('45', $object8->getPort());

        $object9 = new Uri("smtp://us:er:pass@host.com.br:45");
        $this->assertEquals('smtp', $object9->getScheme());
        $this->assertEquals('us', $object9->getUsername());
        $this->assertEquals('er:pass', $object9->getPassword());
        $this->assertEquals('host.com.br', $object9->getHost());
        $this->assertEquals('45', $object9->getPort());
    }
}
