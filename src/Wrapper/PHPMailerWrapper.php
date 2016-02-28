<?php

namespace ByJG\Mail\Wrapper;

use ByJG\Convert\FromUTF8;
use ByJG\Mail\Envelope;
use ByJG\Mail\MailConnection;
use ByJG\Mail\Override\PHPMailerOverride;
use ByJG\Mail\Util;
use Exception;
use PHPMailer;

class PHPMailerWrapper implements MailWrapperInterface
{
    /**
     * @var MailConnection
     */
    protected $connection = null;

    public function __construct(MailConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     *
     * @param Envelope $envelope
     * @return PHPMailer
     */
    protected function prepareMailer(Envelope $envelope)
    {
        $mail = new PHPMailerOverride(true); // the true param means it will throw exceptions on errors, which we need to catch
        $mail->Subject = FromUTF8::toIso88591Email($envelope->getSubject());
        $mail->CharSet = "utf-8";
        if ($envelope->isHtml()) {
            $mail->msgHTML($envelope->getBody());
        } else {
            $mail->Body = $envelope->getBodyText();
        }

        $mail->isSMTP(); // telling the class to use SMTP

        if ($this->connection->getProtocol() != "smtp") {
            $mail->SMTPSecure = $this->connection->getProtocol(); // ssl ou tls!
        }

        $replyTo = Util::decomposeEmail($envelope->getReplyTo());
        $mail->addReplyTo($replyTo["email"], $replyTo["name"]);

        // Define From email
        $from = Util::decomposeEmail($envelope->getFrom());
        $mail->setFrom($from["email"], $from["name"]);

        // Add Recipients
        foreach ((array) $envelope->getTo() as $toItem) {
            $to = Util::decomposeEmail($toItem);
            $mail->addAddress($to["email"], $to["name"]);
        }

        // Add Carbon Copy
        foreach ((array) $envelope->getCC() as $ccItem) {
            $cc = Util::decomposeEmail($ccItem);
            $mail->addCC($cc["email"], $cc["name"]);
        }

        // Add Blind Carbon Copy
        foreach ((array) $envelope->getBCC() as $bccItem) {
            $bcc = Util::decomposeEmail($bccItem);
            $mail->addBCC($bcc["email"], $bcc["name"]);
        }

        // Attachments
        foreach ((array) $envelope->getAttachments() as $name => $value) {
            $mail->addAttachment($value['content'], $name, 'base64', $value['content-type']);
        }

        return $mail;
    }

    /**
     *
     * @param Envelope $envelope
     * @throws Exception
     */
    public function send(Envelope $envelope)
    {
        $mail = $this->prepareMailer($envelope);

        $mail->Host = $this->connection->getServer();
        $mail->Port = $this->connection->getPort();

        if (!empty($this->connection->getUsername())) {
            $mail->SMTPAuth = true;
            $mail->Username = $this->connection->getUsername(); // SMTP account username
            $mail->Password = $this->connection->getPassword();        // SMTP account password
        }

        if (!$mail->Send()) {
            throw new Exception($mail->ErrorInfo);
        }

        return true;
    }
}
