<?php

namespace Tests\Functional;

use ByJG\Mail\Exception\InvalidMailHandlerException;
use ByJG\Mail\Exception\ProtocolNotRegisteredException;
use ByJG\Mail\MailerFactory;
use ByJG\Mail\Wrapper\MailgunApiWrapper;
use Override;

class MailgunFunctionalTest extends FunctionalBase
{
    /**
     * @throws InvalidMailHandlerException
     * @throws ProtocolNotRegisteredException
     */
    #[Override]
    public function setUp(): void
    {
        MailerFactory::registerMailer(MailgunApiWrapper::class);

        $this->mailerName = "Mailgun";
        $this->toEmail = getenv('MAILGUN_TOEMAIL');
        $this->uri = getenv('MAILGUN_URI');
        $this->from = getenv('MAILGUN_FROM');
        parent::setUp();
    }
}
