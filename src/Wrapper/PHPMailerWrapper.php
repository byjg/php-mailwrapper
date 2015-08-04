<?php

namespace ByJG\Mail\Wrapper;

use ByJG\Convert\FromUTF8;
use ByJG\Mail\Envelope;
use ByJG\Mail\MailConnection;
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

    public function send(Envelope $envelope)
    {
		$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
		$mail->Subject = FromUTF8::toIso88591Email($envelope->getSubject());
		$mail->CharSet = "utf-8";
		if ($envelope->getIsHtml())
		{
			$mail->MsgHTML($envelope->getBody());
		}
		else
		{
			$mail->Body = $envelope->getBodyText();
		}


        $mail->IsSMTP(); // telling the class to use SMTP

        $mail->Host = $this->connection->getServer();
        $mail->Port = $this->connection->getPort();

        if ($this->connection->getUsername() !== false)
        {
            $mail->SMTPAuth = true;
            $mail->Username = $this->connection->getUsername(); // SMTP account username
            $mail->Password = $this->connection->getPassword();        // SMTP account password
        }

        if ($this->connection->getProtocol() != "smtp")
        {
            $mail->SMTPSecure = $this->connection->getProtocol(); // ssl ou tls!
        }

		$replyTo = Util::decomposeEmail($envelope->getReplyTo());
		$mail->AddReplyTo($replyTo["email"], $replyTo["name"]);

		// Define From email
		$from = Util::decomposeEmail($envelope->getFrom());
		$mail->SetFrom($from["email"], $from["name"]);

		// Add Recipients
        foreach((array)$envelope->getTo() as $toItem)
        {
            $to = Util::decomposeEmail($toItem);
            $mail->AddAddress($to["email"], $to["name"]);
        }

		// Add Carbon Copy
        foreach((array)$envelope->getCC() as $ccItem)
        {
            $cc = Util::decomposeEmail($ccItem);
            $mail->AddCC($cc["email"], $cc["name"]);
        }

		// Add Blind Carbon Copy
        foreach((array)$envelope->getBCC() as $bccItem)
        {
            $bcc = Util::decomposeEmail($bccItem);
            $mail->AddBCC($bcc["email"], $bcc["name"]);
        }

		// Attachments
        foreach ((array)$envelope->getAttachments() as $name=>$value)
        {
            $mail->AddAttachment($value['content'], $name, 'base64', $value['content-type']);
        }

		if (!$mail->Send())
		{
			throw new Exception($mail->ErrorInfo);
		}

    }
}
