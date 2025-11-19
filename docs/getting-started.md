---
sidebar_position: 1
---

# Getting Started

A lightweight wrapper for sending email. The interface is totally decoupled from the sender, providing a single interface for sending mail regardless of the underlying mail service.

## Installation

```shell
composer require "byjg/mailwrapper"
```

## Quick Start

Here's a simple example to get you started:

```php
<?php
require "vendor/autoload.php";

// Create the email envelope
$envelope = new \ByJG\Mail\Envelope();
$envelope->setFrom('johndoe@example.com', 'John Doe');
$envelope->addTo('jane@example.com');
$envelope->setSubject('Email Subject');
$envelope->setBody('<h1>Hello World</h1><p>This is an HTML email</p>');

// Register the available mailers
\ByJG\Mail\MailerFactory::registerMailer(\ByJG\Mail\Wrapper\PHPMailerWrapper::class);
\ByJG\Mail\MailerFactory::registerMailer(\ByJG\Mail\Wrapper\MailgunApiWrapper::class);

// Create the mailer based on the connection string
$mailer = \ByJG\Mail\MailerFactory::create('smtp://username:password@smtp.example.com:587');

// Send the email
$result = $mailer->send($envelope);

if ($result->success) {
    echo "Email sent successfully! ID: " . $result->id;
}
```

## Architecture

MailWrapper is organized into three main components:

- **The Envelope**: The mail envelope. Defines the mail sender, recipients, body, subject, etc.
- **The Mailer**: Responsible for the process of sending the envelope
- **The Factory**: Registers and creates the available Mailers in the system

## Available Wrappers

- **SMTP** - Standard SMTP with SSL/TLS support
- **AWS SES** - Amazon Simple Email Service (using API directly)
- **Mailgun** - Mailgun API
- **SendMail** - PHP's built-in mail() function
- **FakeSender** - For testing (does nothing)

## Running Tests

```shell
./vendor/bin/phpunit
```
