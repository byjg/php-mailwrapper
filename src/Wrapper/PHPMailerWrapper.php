<?php

namespace ByJG\Mail\Wrapper;

use ByJG\Mail\Envelope;
use ByJG\Mail\Exception\InvalidEMailException;
use ByJG\Mail\Exception\MailApiException;
use ByJG\Mail\Override\PHPMailerOverride;
use ByJG\Mail\SendResult;
use ByJG\Mail\Util;
use InvalidArgumentException;
use PHPMailer\PHPMailer\Exception;

class PHPMailerWrapper extends BaseWrapper
{
    #[\Override]
    public static function schema(): array
    {
        return ['smtp', 'tls', 'ssl'];
    }

    /**
     * @return PHPMailerOverride
     */
    public function getMailer(): PHPMailerOverride
    {
        // the true param means it will throw exceptions on errors, which we need to catch
        return new PHPMailerOverride(true);
    }

    /**
     * @param Envelope $envelope
     * @return PHPMailerOverride
     * @throws Exception
     */
    protected function prepareMailer(Envelope $envelope): PHPMailerOverride
    {
        $mail = $this->getMailer();
        $mail->Subject = $envelope->getSubject();
        $mail->CharSet = "utf-8";
        $mail->Body = $envelope->getBody();
        if ($envelope->isHtml()) {
            $mail->msgHTML($envelope->getBody());
            $mail->AltBody = $envelope->getBodyText();
        }

        $mail->isSMTP(); // telling the class to use SMTP

        if ($this->uri->getScheme() != "smtp") {
            $mail->SMTPSecure = $this->uri->getScheme(); // ssl ou tls!
        }

        $replyTo = Util::decomposeEmail($envelope->getReplyTo());
        $mail->addReplyTo($replyTo["email"], $replyTo["name"]);

        // Define From email
        $from = Util::decomposeEmail($envelope->getFrom());
        $mail->setFrom($from["email"], $from["name"]);

        // Add Recipients
        foreach ($envelope->getTo() as $toItem) {
            $to = Util::decomposeEmail($toItem);
            $mail->addAddress($to["email"], $to["name"]);
        }

        // Add Carbon Copy
        foreach ($envelope->getCC() as $ccItem) {
            $cc = Util::decomposeEmail($ccItem);
            $mail->addCC($cc["email"], $cc["name"]);
        }

        // Add Blind Carbon Copy
        foreach ($envelope->getBCC() as $bccItem) {
            $bcc = Util::decomposeEmail($bccItem);
            $mail->addCustomHeader("Bcc: " . $bcc["email"]);
        }

        // Attachments
        foreach ($envelope->getAttachments() as $name => $value) {
            switch ($value['disposition']) {
                case 'attachment':
                    $mail->addAttachment(
                        $value['content'],
                        $name,
                        'base64',
                        $value['content-type'],
                        'attachment'
                    );
                    break;

                case 'inline':
                    $mail->addEmbeddedImage($value['content'], $name, $name, 'base64', $value['content-type']);
                    break;

                default:
                    throw new InvalidArgumentException('Invalid attachment type');
            }
        }

        return $mail;
    }

    /**
     * @param Envelope $envelope
     * @return SendResult
     * @throws Exception
     * @throws InvalidEMailException
     * @throws MailApiException
     */
    #[\Override]
    public function send(Envelope $envelope): SendResult
    {
        $this->validate($envelope);

        $mail = $this->prepareMailer($envelope);

        $mail->Host = $this->uri->getHost();
        $mail->Port = $this->uri->getPort();

        if (!empty($this->uri->getUsername())) {
            $mail->SMTPAuth = true;
            $mail->Username = $this->uri->getUsername(); // SMTP account username
            $mail->Password = $this->uri->getPassword();        // SMTP account password
        }

        if (!$mail->send()) {
            throw new MailApiException($mail->ErrorInfo);
        }

        return new SendResult(true, $mail->getLastMessageID());
    }
}
