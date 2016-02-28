# Mail Wrapper
[![Build Status](https://travis-ci.org/byjg/mailwrapper.svg?branch=master)](https://travis-ci.org/byjg/mailwrapper)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/e2d6c644-6c2b-4cdd-a84b-94d6b0d1bba5/mini.png)](https://insight.sensiolabs.com/projects/e2d6c644-6c2b-4cdd-a84b-94d6b0d1bba5)

## Description

A lightweight wrapper for send mail. The interface is tottaly decoupled from the sender. The motivation is
create a single interface for sending mail doesn't matter the sender. There are three options available:
- SMTP (with SSL/TLS)
- AWS SES (using API directly)
- Mandrill (using API directly)

## Examples

```php
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
```

### The connection url

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

| Protocol   | Description         |
|:-----------|:--------------------|
| smtp       | SMTP over insecure connection      |
| tls        | SMTP over secure TLS connection    |
| ssl        | SMTP over secure SSL connection    |
| mandrill   | Sending Email using Mandrill API   |
| mailgun    | Sending Email using Mailgun API    |
| ses        | Sending Email using Amazon AWS API |


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


### Mandrill API specifics

The connection url for the mandrill api is:

```
mandrill://mandril_password
```

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
mailgun://api:YOUR_API_KEY@YOUR_DOMAIN
```

The YOUR_API_KEY and YOUR_DOMAIN are defined at Mailgun Control Panel. 


## Install

Just type: `composer require "byjg/mailwrapper=~1.0"`

## Running Tests

```php
cd tests
phpunit --bootstrap bootstrap.php .
```

