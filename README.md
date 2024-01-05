# Mail Wrapper

[![Build Status](https://github.com/byjg/php-mailwrapper/actions/workflows/phpunit.yml/badge.svg?branch=master)](https://github.com/byjg/php-mailwrapper/actions/workflows/phpunit.yml)
[![Opensource ByJG](https://img.shields.io/badge/opensource-byjg-success.svg)](http://opensource.byjg.com)
[![GitHub source](https://img.shields.io/badge/Github-source-informational?logo=github)](https://github.com/byjg/php-mailwrapper/)
[![GitHub license](https://img.shields.io/github/license/byjg/php-mailwrapper.svg)](https://opensource.byjg.com/opensource/licensing.html)
[![GitHub release](https://img.shields.io/github/release/byjg/php-mailwrapper.svg)](https://github.com/byjg/php-mailwrapper/releases/)

A lightweight wrapper for send mail. The interface is tottaly decoupled from the sender. The motivation is
create a single interface for sending mail doesn't matter the sender. There are three options available:

- SMTP (with SSL/TLS)
- AWS SES (using API directly)
- Mailgun (using API directly)

## How to use

The MailWrapper has your classes totally decoupled in three parts:

- The Envelope: the mail envelope. Defines the mail sender, recipients, body, subject, etc;
- The Mailer: the responsible to deal with the process of send the envelope
- The Register: Will register the available Mailers in the system.

### Envelope Class

MailWrapper provides a envelope class with all the basic necessary attributes to create an email.
As this Envelope class are totally decoupled from the Mailer engine, you can use it also as DTO.
See an example below: (do not forget `require "vendor/autoload.php"`)

```php
<?php
// Create the email envelope
$envelope = new ByJG\Mail\Envelope();
$envelope->setFrom('johndoe@example.com', 'John Doe');
$envelope->addTo('jane@example.com');
$envelope->setSubject('Email Subject');
$envelope->setBody('html text body');
```

### Sending the email

Once you have created the envelope you can send the email. Basically you have to register in the fabric all mailer
you intend to use and then create the mailer:

```php
<?php
// Register the available class
\ByJG\Mail\MailerFactory::registerMailer(\ByJG\Mail\Wrapper\PHPMailerWrapper::class);
\ByJG\Mail\MailerFactory::registerMailer(\ByJG\Mail\Wrapper\MailgunApiWrapper::class);

// Create the proper mailer based on the scheme
// In the example below will find the "mailgun" scheme
$mailer = \ByJG\Mail\MailerFactory::create('mailgun://api:YOUR_API_KEY@YOUR_DOMAIN');

// Send the email:
$mailer->send($envelope);
```

You can create the mailer directly without the factory:

```php
<?php
$mailer = new \ByJG\Mail\Wrapper\MailgunApiWrapper(
    new \ByJG\Util\Uri(
        'mailgun://your_api_key@YOUR_DOMAIN'
    )
);

// Send the email:
$mailer->send($envelope);
```

### Sending attachment

```php
<?php
$envelope = new \ByJG\Mail\Envelope('from@email.com', 'to@email.com', 'Subject', 'Body');
$envelope->addAttachment('name_of_attachement', '/path/to/file', 'mime/type');
$mailer->send($envelope);
```

### Adding attachment as Embed Image

Adding an image as a inline attachment (or Embed) your mail reader will not show as download but you can
use it as an local image in your email.

See the example:

```php
<?php
$envelope = new \ByJG\Mail\Envelope('from@email.com', 'to@email.com', 'Subject');
$envelope->addEmbedImage('mycontentname', '/path/to/image', 'mime/type');
$envelope->setBody('<img src="cid:mycontentname" />');
$mailer->send($envelope);
```

## The connection url

To create a new sender you have to define a URL like that:

```text
scheme://username:password/smtpserver:port
```

The options are:

| Part       | Description         |
|:-----------|:--------------------|
| scheme     | The email scheme: smtp, ssl, tls, mandrill and ses. Note that mandrill and ses use your own private api |
| username   | The username        |
| password   | The password        |
| smtpserver | The SMTP Host       |
| port       | The SMTP Port       |

The protocols available are:

| Scheme     | Description                        | URI Pattern                          | Mailer Object
|:-----------|:-----------------------------------|:-------------------------------------|:-------------------
| smtp       | SMTP over insecure connection      | smtp://username:password@host:25     | PHPMailerWrapper
| tls        | SMTP over secure TLS connection    | tls://username:password@host:587     | PHPMailerWrapper
| ssl        | SMTP over secure SSL connection    | ssl://username:password@host:587     | PHPMailerWrapper
| sendmail   | Sending Email using PHP mail()     | sendmail://localhost                 | SendMailWrapper
| mailgun    | Sending Email using Mailgun API    | mailgun://YOUR_API_KEY@YOUR_DOMAIN   | MailgunApiWrapper
| ses        | Sending Email using Amazon AWS API | ses://ACCESS_KEY_ID:SECRET_KEY@REGION| AmazonSesWrapper
| fakesender | Do nothing                         | fakesender://anything                | FakeSenderWrapper

### Gmail specifics

From December 2014, Google started imposing an authentication mechanism called
XOAUTH2 based on OAuth2 for access to their apps, including Gmail.
This change can break both SMTP and IMAP access to gmail, and you may receive
authentication failures (often "5.7.14 Please log in via your web browser")
from many email clients, including PHPMailer, Apple Mail, Outlook, Thunderbird and others.
The error output may include a link to
<https://support.google.com/mail/bin/answer.py?answer=78754>, which
gives a list of possible remedies.

There are two main solutions:

#### Sending through SMTP

You have to enable the option "Allow less secure apps".
It does not really make your app significantly less secure.
Reportedly, changing this setting may take an hour or more to take effect,
so don't expect an immediate fix. You can start changing
[here](https://www.google.com/settings/security/lesssecureapps)

The connection string for sending emails using SMTP through GMAIL is:

```text
tls://YOUREMAIL@gmail.com:YOURPASSWORD@smtp.gmail.com:587
```

#### Sending Through XOAuth2

This option is currently unsupported.

Further information and documentation on how to set up can be found on this
[wiki](https://github.com/PHPMailer/PHPMailer/wiki/Using-Gmail-with-XOAUTH2) page.

### Amazon SES API specifics

The connection url for the AWS SES api is:

```text
ses://ACCESS_KEY_ID:SECRET_KEY@REGION
```

The access_key_id and secret_key are created at AWS Control Panel. The region can be us-east-1, etc.

### Mailgun API specifics

The connection url for the Mailgun api is:

```text
mailgun://YOUR_API_KEY@YOUR_DOMAIN
```

The YOUR_API_KEY and YOUR_DOMAIN are defined at Mailgun Control Panel.

The Region of the endpoint can be configured by query parameter "region" (example: mailgun://api-key@mg.domain.cz?region=eu)

Valid values are: us and eu.

### Sendmail Specifics

The connection url for the Sendmail is:

```text
sendmail://localhost
```

You need to setup in the `php.ini` the email relay.

## Implementing your Own Wrappers

To implement your own wrapper you have to create a class inherited from: `ByJG\Mail\Wrapper\BaseWrapper`  and implement
how to send in the method: `public function send(Envelope $envelope);`

```php
class MyWrapper extends \ByJG\Mail\Wrapper\BaseWrapper
{
    public static function schema()
    {
        return ['mywrapper'];
    }

    public function send(Envelope $envelope)
    {
        // Do how to send the email using your library
    }

    // You can create your own validation methods.
    public function validate(Envelope $envelope)
    {
        parent::validate($envelope);
    }
}
```

## Install

```shell
composer require "byjg/mailwrapper"
```

## Running Tests

```shell
./vendor/bin/phpunit
```

## Dependencies

```mermaid
flowchart TD
    byjg/mailwrapper --> ext-curl
    byjg/mailwrapper --> byjg/convert
    byjg/mailwrapper --> byjg/webrequest
    byjg/mailwrapper --> aws/aws-sdk-php
    byjg/mailwrapper --> phpmailer/phpmailer
```

----
[Open source ByJG](http://opensource.byjg.com)
