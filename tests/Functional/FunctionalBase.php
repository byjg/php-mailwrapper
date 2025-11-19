<?php

namespace Tests\Functional;

use ByJG\Mail\Envelope;
use ByJG\Mail\Exception\ProtocolNotRegisteredException;
use ByJG\Mail\MailerFactory;
use ByJG\Mail\Wrapper\MailWrapperInterface;
use ByJG\Util\Uri;
use Override;
use PHPUnit\Framework\TestCase;

abstract class FunctionalBase extends TestCase
{
    protected Uri|string|false $uri;
    protected string|null $from;
    protected string|null $toEmail;
    protected MailWrapperInterface|null $mailer;
    protected Envelope|null $envelope;
    protected string $mailerName;

    /**
     * @throws ProtocolNotRegisteredException
     */
    #[Override]
    public function setUp(): void
    {
        if (!$this->uri || !$this->from || !$this->toEmail) {
            return;
        }

        $this->mailer = MailerFactory::create($this->uri);

        // Create the email envelope
        $this->envelope = new Envelope();
        $this->envelope->setFrom($this->from, '[' . $this->mailerName . '] Automated, Automação');
        $this->envelope->addTo($this->toEmail);
        $this->envelope->setSubject('Automated Tests / Automação');
        $this->envelope->setBody(
            '<pre>' .
            "*** Automated Email ***\n" .
            '</pre>' .
            '<h2>Test Email</h2>' .
            '<div>This is an automated email sent by the MailWrapper functinal test</div>'
        );
    }

    #[Override]
    public function tearDown(): void
    {
        $this->mailer = null;
        $this->from = null;
        $this->toEmail = null;
        $this->envelope = null;
    }

    /**
     * @return void
     */
    public function testSendEmail()
    {
        if (empty($this->mailer)) {
            $this->markTestSkipped('Environment Variables not set');
        }
        $result = $this->mailer->send($this->envelope);
        $this->assertTrue($result->success);
    }
}
