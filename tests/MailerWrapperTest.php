<?php

namespace ByJG\Mail;

use ByJG\Mail\Exception\InvalidMailHandlerException;
use ByJG\Mail\Exception\ProtocolNotRegisteredException;
use \PHPUnit\Framework\TestCase;
use ByJG\Mail\Wrapper\AmazonSesWrapper;
use ByJG\Mail\Wrapper\MailgunApiWrapper;
use ByJG\Mail\Wrapper\PHPMailerWrapper;
use ByJG\Mail\Wrapper\SendMailWrapper;

class MailerWrapperTest extends TestCase
{

    /**
     * @throws \ByJG\Mail\Exception\InvalidMailHandlerException
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
     * @throws \ByJG\Mail\Exception\InvalidMailHandlerException
     */
    public function testRegisterMailerFail(): void
    {
        $this->expectException(InvalidMailHandlerException::class);
        MailerFactory::registerMailer(MailerWrapperTest::class);
    }

    /**
     * @throws \ByJG\Mail\Exception\InvalidMailHandlerException
     * @throws \ByJG\Mail\Exception\ProtocolNotRegisteredException
     */
    public function testCreate(): void
    {
        MailerFactory::registerMailer(PHPMailerWrapper::class);
        MailerFactory::create('smtp://localhost');

        // If there is no error above the test is OK.
        $this->assertTrue(true);
    }

    /**
     * @throws \ByJG\Mail\Exception\ProtocolNotRegisteredException
     * @throws \ByJG\Mail\Exception\InvalidMailHandlerException
     */
    public function testCreateFail(): void
    {
        $this->expectException(ProtocolNotRegisteredException::class);
        MailerFactory::registerMailer(PHPMailerWrapper::class);
        MailerFactory::create('some://localhost');
    }
}
