<?php

namespace Tests\Functional;

use ByJG\Mail\MailerFactory;
use ByJG\Mail\Wrapper\PHPMailerWrapper;

require_once "FunctionalBase.php";

class PHPMailerFunctionalTest extends FunctionalBase
{
    /**
     * @throws \ByJG\Mail\Exception\InvalidMailHandlerException
     * @throws \ByJG\Mail\Exception\ProtocolNotRegisteredException
     */
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
