# Mail Wrapper

[![Sponsor](https://img.shields.io/badge/Sponsor-%23ea4aaa?logo=githubsponsors&logoColor=white&labelColor=0d1117)](https://github.com/sponsors/byjg)
[![Build Status](https://github.com/byjg/php-mailwrapper/actions/workflows/phpunit.yml/badge.svg?branch=master)](https://github.com/byjg/php-mailwrapper/actions/workflows/phpunit.yml)
[![Opensource ByJG](https://img.shields.io/badge/opensource-byjg-success.svg)](http://opensource.byjg.com)
[![GitHub source](https://img.shields.io/badge/Github-source-informational?logo=github)](https://github.com/byjg/php-mailwrapper/)
[![GitHub license](https://img.shields.io/github/license/byjg/php-mailwrapper.svg)](https://opensource.byjg.com/opensource/licensing.html)
[![GitHub release](https://img.shields.io/github/release/byjg/php-mailwrapper.svg)](https://github.com/byjg/php-mailwrapper/releases/)

A lightweight wrapper for sending email. The interface is totally decoupled from the sender, providing a single interface for sending mail regardless of the underlying mail service.

## Available Wrappers

- **SMTP** - SMTP with SSL/TLS support
- **AWS SES** - Amazon Simple Email Service (using API directly)
- **Mailgun** - Mailgun API (using API directly)
- **SendMail** - PHP's built-in mail() function
- **FakeSender** - For testing (does nothing)

## Install

```shell
composer require "byjg/mailwrapper"
```

## Documentation

- **[Getting Started](docs/getting-started.md)** - Installation, quick start, and architecture overview
- **[Envelope](docs/envelope.md)** - Creating and configuring email messages
- **[Connection Strings](docs/connection-strings.md)** - URI patterns for different mail services (SMTP, Mailgun, SES, etc.)
- **[Mailer Factory](docs/mailer-factory.md)** - Registering and creating mailers
- **[Attachments](docs/attachments.md)** - Sending attachments and embedded images
- **[Custom Wrappers](docs/custom-wrappers.md)** - Implementing your own mail wrapper
- **[Exceptions](docs/exceptions.md)** - Error handling and exception types

## Quick Start

```php
<?php
require "vendor/autoload.php";

// Create the email envelope
$envelope = new \ByJG\Mail\Envelope();
$envelope->setFrom('johndoe@example.com', 'John Doe');
$envelope->addTo('jane@example.com');
$envelope->setSubject('Email Subject');
$envelope->setBody('<h1>Hello World</h1>');

// Register available mailers
\ByJG\Mail\MailerFactory::registerMailer(\ByJG\Mail\Wrapper\PHPMailerWrapper::class);
\ByJG\Mail\MailerFactory::registerMailer(\ByJG\Mail\Wrapper\MailgunApiWrapper::class);

// Create mailer from connection string
$mailer = \ByJG\Mail\MailerFactory::create('smtp://username:password@smtp.example.com:587');

// Send the email
$result = $mailer->send($envelope);
```

## Architecture

MailWrapper is organized into three main components:

- **The Envelope**: The mail message. Defines the sender, recipients, body, subject, attachments, etc.
- **The Mailer**: Responsible for the process of sending the envelope
- **The Factory**: Registers and creates the available Mailers in the system

## Connection URL Schemes

| Scheme     | Description                        | URI Pattern                              |
|:-----------|:-----------------------------------|:-----------------------------------------|
| smtp       | SMTP over insecure connection      | `smtp://username:password@host:25`       |
| tls        | SMTP over secure TLS connection    | `tls://username:password@host:587`       |
| ssl        | SMTP over secure SSL connection    | `ssl://username:password@host:465`       |
| sendmail   | PHP's built-in mail() function     | `sendmail://localhost`                   |
| mailgun    | Mailgun API                        | `mailgun://YOUR_API_KEY@YOUR_DOMAIN`     |
| ses        | Amazon SES API                     | `ses://ACCESS_KEY_ID:SECRET_KEY@REGION`  |
| fakesender | Testing (does nothing)             | `fakesender://localhost`                 |

See [Connection Strings](docs/connection-strings.md) for detailed configuration examples.

## Running Tests

```shell
./vendor/bin/phpunit
```

## Dependencies

```mermaid
flowchart TD
    byjg/mailwrapper --> byjg/convert
    byjg/mailwrapper --> byjg/webrequest
```

----
[Open source ByJG](http://opensource.byjg.com)
