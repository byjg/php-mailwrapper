---
sidebar_position: 3
---

# Connection Strings

Connection strings define how to connect to mail services. They follow a URI format:

```text
scheme://username:password@host:port
```

## URI Components

| Part       | Description                                                                    |
|:-----------|:-------------------------------------------------------------------------------|
| scheme     | The email scheme: smtp, ssl, tls, sendmail, mailgun, ses, fakesender          |
| username   | The username for authentication                                                |
| password   | The password for authentication                                                |
| host       | The SMTP host or service endpoint                                             |
| port       | The SMTP port                                                                  |

## Available Schemes

| Scheme     | Description                        | URI Pattern                              | Wrapper Class      |
|:-----------|:-----------------------------------|:-----------------------------------------|:-------------------|
| smtp       | SMTP over insecure connection      | `smtp://username:password@host:25`       | PHPMailerWrapper   |
| tls        | SMTP over secure TLS connection    | `tls://username:password@host:587`       | PHPMailerWrapper   |
| ssl        | SMTP over secure SSL connection    | `ssl://username:password@host:465`       | PHPMailerWrapper   |
| sendmail   | PHP's built-in mail() function     | `sendmail://localhost`                   | SendMailWrapper    |
| mailgun    | Mailgun API                        | `mailgun://YOUR_API_KEY@YOUR_DOMAIN`     | MailgunApiWrapper  |
| ses        | Amazon SES API                     | `ses://ACCESS_KEY_ID:SECRET_KEY@REGION`  | AmazonSesWrapper   |
| fakesender | Testing (does nothing)             | `fakesender://localhost`                 | FakeSenderWrapper  |

## Examples

### SMTP with TLS

```php
$mailer = \ByJG\Mail\MailerFactory::create(
    'tls://username:password@smtp.example.com:587'
);
```

### Gmail

```php
$mailer = \ByJG\Mail\MailerFactory::create(
    'tls://your.email@gmail.com:your_password@smtp.gmail.com:587'
);
```

:::info
Gmail requires you to enable "Allow less secure apps" in your Google account settings.
Visit [https://www.google.com/settings/security/lesssecureapps](https://www.google.com/settings/security/lesssecureapps) to enable this option.

Changes may take up to an hour to take effect.
:::

### Mailgun API

```php
$mailer = \ByJG\Mail\MailerFactory::create(
    'mailgun://YOUR_API_KEY@YOUR_DOMAIN'
);
```

You can specify the region using a query parameter:

```php
// EU region
$mailer = \ByJG\Mail\MailerFactory::create(
    'mailgun://YOUR_API_KEY@YOUR_DOMAIN?region=eu'
);
```

Valid regions: `us` (default), `eu`

### Amazon SES

```php
$mailer = \ByJG\Mail\MailerFactory::create(
    'ses://ACCESS_KEY_ID:SECRET_KEY@us-east-1'
);
```

The region can be any valid AWS region (e.g., `us-east-1`, `eu-west-1`, etc.).

### SendMail (PHP mail() function)

```php
$mailer = \ByJG\Mail\MailerFactory::create(
    'sendmail://localhost'
);
```

:::warning
You need to configure your email relay in `php.ini` for SendMail to work properly.
:::

### FakeSender (Testing)

```php
$mailer = \ByJG\Mail\MailerFactory::create(
    'fakesender://localhost'
);
```

The FakeSender wrapper does nothing and always returns success. It's useful for testing without actually sending emails.
