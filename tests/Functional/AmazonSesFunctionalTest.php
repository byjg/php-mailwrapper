<?php

namespace Tests\Functional;

use ByJG\Mail\Exception\InvalidMailHandlerException;
use ByJG\Mail\Exception\ProtocolNotRegisteredException;
use ByJG\Mail\MailerFactory;
use ByJG\Mail\Wrapper\AmazonSesWrapper;
use Override;

class AmazonSesFunctionalTest extends FunctionalBase
{
    /**
     * @throws InvalidMailHandlerException
     * @throws ProtocolNotRegisteredException
     */
    #[Override]
    public function setUp(): void
    {
        MailerFactory::registerMailer(AmazonSesWrapper::class);

        $this->mailerName = "Aws Ses";
        $this->toEmail = getenv('AWSSES_TOEMAIL');
        $this->uri = getenv('AWSSES_URI');
        $this->from = getenv('AWSSES_FROM');
        parent::setUp();
    }
}
