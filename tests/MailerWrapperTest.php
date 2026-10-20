<?php

namespace Tests;

use ByJG\Mail\Exception\InvalidMailHandlerException;
use ByJG\Mail\Exception\ProtocolNotRegisteredException;
use ByJG\Mail\MailerFactory;
use ByJG\Util\Uri;
use PHPUnit\Framework\TestCase;
use ByJG\Mail\Wrapper\AmazonSesWrapper;
use ByJG\Mail\Wrapper\BaseWrapper;
use ByJG\Mail\Wrapper\MailgunApiWrapper;
use ByJG\Mail\Wrapper\PHPMailerWrapper;
use ByJG\Mail\Wrapper\SendMailWrapper;

class MailerWrapperTest extends TestCase
{

    /**
     * @throws InvalidMailHandlerException
     */
    public function testRegisterMailer(): void
    {
        MailerFactory::registerMailer(PHPMailerWrapper::class);
        MailerFactory::registerMailer(SendMailWrapper::class);
        MailerFactory::registerMailer(MailgunApiWrapper::class);
        MailerFactory::registerMailer(AmazonSesWrapper::class);

        // If there is no error above, the test is OK.
        $this->assertTrue(true);
    }

    /**
     * @throws InvalidMailHandlerException
     */
    public function testRegisterMailerFail(): void
    {
        $this->expectException(InvalidMailHandlerException::class);
        MailerFactory::registerMailer(MailerWrapperTest::class);
    }

    /**
     * @throws InvalidMailHandlerException
     * @throws ProtocolNotRegisteredException
     */
    public function testCreate(): void
    {
        MailerFactory::registerMailer(PHPMailerWrapper::class);
        MailerFactory::create('smtp://localhost');

        // If there is no error above the test is OK.
        $this->assertTrue(true);
    }

    /**
     * @throws InvalidMailHandlerException
     * @throws ProtocolNotRegisteredException
     */
    public function testCreateWithAnotherPsr7Uri(): void
    {
        MailerFactory::registerMailer(PHPMailerWrapper::class);
        $mailer = MailerFactory::create(new \GuzzleHttp\Psr7\Uri('smtp://user:secret@mail.example.com:587'));

        $this->assertInstanceOf(PHPMailerWrapper::class, $mailer);
        /** @var Uri $uri */
        $uri = (new \ReflectionProperty(BaseWrapper::class, 'uri'))->getValue($mailer);
        $this->assertSame('mail.example.com', $uri->getHost());
        $this->assertSame(587, $uri->getPort());
        $this->assertSame('user', $uri->getUsername());
        $this->assertSame('secret', $uri->getPassword());
    }

    /**
     * @throws ProtocolNotRegisteredException
     * @throws InvalidMailHandlerException
     */
    public function testCreateFail(): void
    {
        $this->expectException(ProtocolNotRegisteredException::class);
        MailerFactory::registerMailer(PHPMailerWrapper::class);
        MailerFactory::create('some://localhost');
    }
}
