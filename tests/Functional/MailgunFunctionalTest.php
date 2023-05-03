<?php

namespace Tests\Functional;

use ByJG\Mail\MailerFactory;
use ByJG\Mail\Wrapper\MailgunApiWrapper;

require_once "FunctionalBase.php";

class MailgunFunctionalTest extends FunctionalBase
{
    /**
     * @throws \ByJG\Mail\Exception\InvalidMailHandlerException
     * @throws \ByJG\Mail\Exception\ProtocolNotRegisteredException
     */
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
