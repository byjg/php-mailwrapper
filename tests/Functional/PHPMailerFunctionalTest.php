<?php

namespace Tests\Functional;

use ByJG\Mail\Exception\InvalidMailHandlerException;
use ByJG\Mail\Exception\ProtocolNotRegisteredException;
use ByJG\Mail\MailerFactory;
use ByJG\Mail\Wrapper\PHPMailerWrapper;
use Override;

class PHPMailerFunctionalTest extends FunctionalBase
{
    /**
     * @throws InvalidMailHandlerException
     * @throws ProtocolNotRegisteredException
     */
    #[Override]
    public function setUp(): void
    {
        MailerFactory::registerMailer(PHPMailerWrapper::class);

        $this->mailerName = "Smtp";
        $this->toEmail = getenv('PHPMAILER_TOEMAIL');
        $this->uri = getenv('PHPMAILER_URI');
        $this->from = getenv('PHPMAILER_FROM');
        parent::setUp();
    }
}
