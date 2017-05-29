<?php

namespace ByJG\Mail;

use PHPUnit\Framework\TestCase;

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}

class EnvelopeTest extends TestCase
{

    /**
     * @var Envelope
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Envelope;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    public function testGetFrom()
    {
        $this->object->setFrom('some@email.com');
        $this->assertEquals('some@email.com', $this->object->getFrom());

        $this->object->setFrom('some@email.com', 'John Doe');
        $this->assertEquals('"John Doe" <some@email.com>', $this->object->getFrom());
    }

    public function testGetTo()
    {
        $this->object->addTo('some@email.com');
        $this->assertEquals(['some@email.com'], $this->object->getTo());

        $this->object->addTo('some@email.com', 'John Doe');
        $this->assertEquals(['some@email.com', '"John Doe" <some@email.com>'], $this->object->getTo());

        $this->object->setTo('new@email.com', 'Only This');
        $this->assertEquals(['"Only This" <new@email.com>'], $this->object->getTo());
    }

    public function testGetSubject()
    {
        $this->object->setSubject('Test');
        $this->assertEquals('Test', $this->object->getSubject());
    }

    public function testGetCC()
    {
        $this->object->addCC('some@email.com');
        $this->assertEquals(['some@email.com'], $this->object->getCC());

        $this->object->addCC('some@email.com', 'John Doe');
        $this->assertEquals(['some@email.com', '"John Doe" <some@email.com>'], $this->object->getCC());

        $this->object->setCC('new@email.com', 'Only This');
        $this->assertEquals(['"Only This" <new@email.com>'], $this->object->getCC());
    }

    public function testGetBCC()
    {
        $this->object->addBCC('some@email.com');
        $this->assertEquals(['some@email.com'], $this->object->getBCC());

        $this->object->addBCC('some@email.com', 'John Doe');
        $this->assertEquals(['some@email.com', '"John Doe" <some@email.com>'], $this->object->getBCC());

        $this->object->setBCC('new@email.com', 'Only This');
        $this->assertEquals(['"Only This" <new@email.com>'], $this->object->getBCC());
    }


    public function testGetBody()
    {
        $this->object->setBody('<p><b>Some title</b></p><p>Other test<br/>Break</p>');
        $this->assertEquals('<p><b>Some title</b></p><p>Other test<br/>Break</p>', $this->object->getBody());
        $this->assertEquals("Some title\nOther test\nBreak\n", $this->object->getBodyText());
    }


    public function testGetAttachments()
    {
        $this->object->addAttachment('name1', '/path/to/file', 'mime/type');
        $this->assertEquals(
            [
                'name1' => [
                    'content' => '/path/to/file',
                    'content-type' => 'mime/type',
                    'disposition' => 'attachment'
                ]
            ],
            $this->object->getAttachments()
        );

        $this->object->addAttachment('name2', '/path/to/file2', 'mime/type2');
        $this->assertEquals(
            [
                'name1' => [
                    'content' => '/path/to/file',
                    'content-type' => 'mime/type',
                    'disposition' => 'attachment'
                ],
                'name2' => [
                    'content' => '/path/to/file2',
                    'content-type' => 'mime/type2',
                    'disposition' => 'attachment'
                ]
            ],
            $this->object->getAttachments()
        );
    }
}
