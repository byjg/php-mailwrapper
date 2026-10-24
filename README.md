---
sidebar_key: mailwrapper
tags: [php, email]
---

# Mail Wrapper

One interface to send email, whatever the service behind it. Change the provider, or add a new one, without changing your code.

[![Sponsor](https://img.shields.io/badge/Sponsor-%23ea4aaa?logo=githubsponsors&logoColor=white&labelColor=0d1117)](https://github.com/sponsors/byjg)
[![Build Status](https://github.com/byjg/php-mailwrapper/actions/workflows/phpunit.yml/badge.svg?branch=master)](https://github.com/byjg/php-mailwrapper/actions/workflows/phpunit.yml)
[![Opensource ByJG](https://img.shields.io/badge/opensource-byjg-success.svg)](https://opensource.byjg.com)
[![Install MCP Server](https://img.shields.io/badge/Install-MCP_Server-8A2BE2?logo=modelcontextprotocol&logoColor=white)](https://opensource.byjg.com/docs/ai/mcpserver-byjg-docs/)
[![GitHub source](https://img.shields.io/badge/Github-source-informational?logo=github)](https://github.com/byjg/php-mailwrapper/)
[![GitHub license](https://img.shields.io/github/license/byjg/php-mailwrapper.svg)](https://opensource.byjg.com/license/)
[![GitHub release](https://img.shields.io/github/release/byjg/php-mailwrapper.svg)](https://github.com/byjg/php-mailwrapper/releases/)

## Why

SMTP, Amazon SES and Mailgun each have their own SDK, their own message object and their
own errors. An application written against one of them is tied to it: switching provider,
or sending nothing in a test, means touching every place that sends mail.

Mail Wrapper defines the contract once. You build an `Envelope` and call `send()` on a
`MailWrapperInterface`; the provider is chosen by the connection string:

```php
$mailer = MailerFactory::create('smtp://user:pass@smtp.example.com:587');  // SMTP
$mailer = MailerFactory::create('ses://key:secret@us-east-1');             // Amazon SES
$mailer = MailerFactory::create('fakesender://localhost');                 // tests

$mailer->send($envelope);
```

- **Change the provider without touching the code.** The connection string usually lives
  in configuration, so it is a deploy change, not a code change.
- **Test without sending.** `FakeSender` sends nothing and keeps the envelopes for
  assertions.
- **Add a provider by implementing one interface.** A wrapper is `send()` and a URI
  scheme; see [Custom Wrappers](docs/custom-wrappers.md).

## Available Wrappers

- **SMTP** - SMTP with SSL/TLS support
- **AWS SES** - Amazon Simple Email Service (using API directly)
- **Mailgun** - Mailgun API (using API directly)
- **SendMail** - PHP's built-in mail() function
- **FakeSender** - For testing (sends nothing, keeps the envelopes for assertions)

## Installation

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
| fake       | Alias of `fakesender`              | `fake://localhost`                       |

See [Connection Strings](docs/connection-strings.md) for detailed configuration examples.

## Running Tests

```shell
./vendor/bin/phpunit
```

----
[Open source ByJG](https://opensource.byjg.com)
