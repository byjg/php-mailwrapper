<?php

require "vendor/autoload.php";

// Create a connection URL (see below)
$connection = new \ByJG\Mail\MailConnection('protocol://username:password/smtpserver:port');

// Create the proper mailer based on the connection
$mailer = ByJG\Mail\Envelope::mailerFactory($connection);

// Create the email envelope
$envelope = new ByJG\Mail\Envelope();
$envelope->setFrom('johndoe@example.com', 'John Doe');
$envelope->addTo('jane@example.com');
$envelope->setSubject('Email Subject');
$envelope->setBody('html text body');

// The the email with the selected mailer.
$envelope->send($mailer);

