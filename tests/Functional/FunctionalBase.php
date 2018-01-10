<?php

namespace Tests\Functional;

use ByJG\Mail\Envelope;
use ByJG\Mail\MailerFactory;

// backward compatibility
use PHPUnit\Framework\TestCase;

if (!class_exists('\PHPUnit\Framework\TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}

abstract class FunctionalBase extends TestCase
{
    protected $uri;
    protected $from;
    protected $toEmail;
    protected $mailer;
    protected $envelope;
    protected $mailerName;

    /**
     * @throws \ByJG\Mail\Exception\ProtocolNotRegisteredException
     */
    public function setUp()
    {
        if (!$this->uri || !$this->from || !$this->toEmail) {
            return;
        }

        $this->mailer = MailerFactory::create($this->uri);

        // Create the email envelope
        $this->envelope = new Envelope();
        $this->envelope->setFrom($this->from, '[' . $this->mailerName . '] Mailwrapper Automated Tests');
        $this->envelope->addTo($this->toEmail);
        $this->envelope->setSubject('Mailwrapper Automated Tests');
        $this->envelope->setBody(
            '<pre>' .
            "*** Automated Email ***\n" .
            '</pre>' .
            '<h2>Test Email</h2>' .
            '<div>This is an automated email sent by the MailWrapper functinal test</div>'
        );
    }

    public function tearDown()
    {
        $this->mailer = null;
        $this->from = null;
        $this->toEmail = null;
        $this->envelope = null;
    }

    public function testSendEmail()
    {
        if (empty($this->mailer)) {
            $this->markTestSkipped('Environment Variables not set');
            return;
        }
        $this->assertTrue($this->mailer->send($this->envelope));
    }
}
