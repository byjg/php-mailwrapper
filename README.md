# Mail Wrapper
[![Build Status](https://travis-ci.org/byjg/mailwrapper.svg?branch=master)](https://travis-ci.org/byjg/mailwrapper)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/byjg/mailwrapper/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/byjg/mailwrapper/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/e2d6c644-6c2b-4cdd-a84b-94d6b0d1bba5/mini.png)](https://insight.sensiolabs.com/projects/e2d6c644-6c2b-4cdd-a84b-94d6b0d1bba5)

A lightweight wrapper for send mail. The interface is tottaly decoupled from the sender. The motivation is
create a single interface for sending mail doesn't matter the sender. There are three options available:
- SMTP (with SSL/TLS)
- AWS SES (using API directly)
- Mailgun (using API directly)

## How to use

### Envelope Class

MailWrapper provides a envelope class with all the basic necessary attributes to create an email.
As this Envelope class are totally decoupled from the Mailer engine, you can use it also as DTO.
See an example below:

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
\ByJG\Mail\MailerFactory::registerMailer('smtp', \ByJG\Mail\Wrapper\PHPMailerWrapper::class);
\ByJG\Mail\MailerFactory::registerMailer('mailgun', \ByJG\Mail\Wrapper\MailgunApiWrapper::class);

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
        'mailgun://api:YOUR_API_KEY@YOUR_DOMAIN'
    )
);

// Send the email: 
$mailer->send($envelope);
```


## The connection url

To create a new sender you have to define a URL like that:

```
protocol://username:password/smtpserver:port
```

The options are:

| Part       | Description         |
|:-----------|:--------------------|
| protocol   | The email protocol: smtp, ssl, tls, mandrill and ses. Note that mandrill and ses use your own private api |
| username   | The username        |
| password   | The password        |
| smtpserver | The SMTP Host       |
| port       | The SMTP Port       |

The protocols available are:

| Protocol   | Description                        | URI Pattern
|:-----------|:-----------------------------------|:---------------
| smtp       | SMTP over insecure connection      | smtp://username:password@host:25
| tls        | SMTP over secure TLS connection    | tls://username:password@host:587
| ssl        | SMTP over secure SSL connection    | ssl://username:password@host:587
| sendmail   | Sending Email using PHP mail()     | sendmail://localhost
| mailgun    | Sending Email using Mailgun API    | mailgun://YOUR_API_KEY@YOUR_DOMAIN
| ses        | Sending Email using Amazon AWS API | ses://ACCESS_KEY_ID:SECRET_KEY@REGION


### Gmail specifics

From December 2014, Google started imposing an authentication mechanism called 
XOAUTH2 based on OAuth2 for access to their apps, including Gmail. 
This change can break both SMTP and IMAP access to gmail, and you may receive 
authentication failures (often "5.7.14 Please log in via your web browser") 
from many email clients, including PHPMailer, Apple Mail, Outlook, Thunderbird and others. 
The error output may include a link to 
https://support.google.com/mail/bin/answer.py?answer=78754, which 
gives a list of possible remedies. 

There are two main solutions:

**Sending through SMTP**

You have to enable the option "Allow less secure apps". 
It does not really make your app significantly less secure. 
Reportedly, changing this setting may take an hour or more to take effect, 
so don't expect an immediate fix. You can start changing 
[here](https://www.google.com/settings/security/lesssecureapps)

The connection string for sending emails using SMTP through GMAIL is:

```
tls://YOUREMAIL@gmail.com:YOURPASSWORD@smtp.gmail.com:587
```

**Sending Through XOAuth2**

This option is currently unsupported. 

Further information and documentation on how to set up can be found on this 
[wiki](https://github.com/PHPMailer/PHPMailer/wiki/Using-Gmail-with-XOAUTH2) page.


The mandrill_password is the API password created at mandrill website.


### Amazon SES API specifics

The connection url for the AWS SES api is:

```
ses://ACCESS_KEY_ID:SECRET_KEY@REGION
```

The access_key_id and secret_key are created at AWS Control Panel. The region can be us-east-1, etc. 

### Mailgun API specifics

The connection url for the Mailgun api is:

```
mailgun://YOUR_API_KEY@YOUR_DOMAIN
```

The YOUR_API_KEY and YOUR_DOMAIN are defined at Mailgun Control Panel. 

### Sendmail Specifics

The connection url for the Sendmail is:

```
sendmail://localhost
```

You need to setup in the `php.ini` the email relay.


## Install

Just type: `composer require "byjg/mailwrapper=~1.0"`

## Running Tests

```php
phpunit
```
