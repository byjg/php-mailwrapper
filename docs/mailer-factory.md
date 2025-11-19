---
sidebar_position: 4
---

# Mailer Factory

The MailerFactory is responsible for creating mailer instances based on connection strings. It uses a registration pattern that allows you to register which mailer wrappers are available in your application.

## Registering Mailers

Before creating a mailer, you need to register the wrapper classes you want to use:

```php
// Register individual wrappers
\ByJG\Mail\MailerFactory::registerMailer(\ByJG\Mail\Wrapper\PHPMailerWrapper::class);
\ByJG\Mail\MailerFactory::registerMailer(\ByJG\Mail\Wrapper\MailgunApiWrapper::class);
\ByJG\Mail\MailerFactory::registerMailer(\ByJG\Mail\Wrapper\AmazonSesWrapper::class);
```

You only need to register the wrappers you plan to use. This keeps your application lightweight by not loading unnecessary dependencies.

## Creating Mailers

Once registered, create a mailer using a connection string:

```php
$mailer = \ByJG\Mail\MailerFactory::create('smtp://username:password@host:587');
```

The factory automatically selects the appropriate wrapper based on the connection string's scheme.

### Using URI Objects

You can also pass a URI object instead of a string:

```php
$uri = new \ByJG\Util\Uri('tls://username:password@smtp.example.com:587');
$mailer = \ByJG\Mail\MailerFactory::create($uri);
```

## Creating Mailers Directly

You can bypass the factory and instantiate wrappers directly:

```php
$mailer = new \ByJG\Mail\Wrapper\MailgunApiWrapper(
    new \ByJG\Util\Uri('mailgun://YOUR_API_KEY@YOUR_DOMAIN')
);
```

This approach is useful when:
- You only use a single mailer type
- You want to avoid the registration step
- You need more control over instantiation

## Sending Emails

All mailers implement the same interface:

```php
$envelope = new \ByJG\Mail\Envelope(
    'from@example.com',
    'to@example.com',
    'Subject',
    'Body content'
);

$result = $mailer->send($envelope);

if ($result->success) {
    echo "Email sent! Message ID: " . $result->id;
} else {
    echo "Failed to send email";
}
```

## Send Result

The `send()` method returns a `SendResult` object with two properties:

```php
class SendResult
{
    public readonly bool $success;      // true if email was sent
    public readonly ?string $id;        // Message ID (if available)
}
```

Example:

```php
$result = $mailer->send($envelope);

if ($result->success) {
    // Email was sent successfully
    if ($result->id !== null) {
        // Some services provide a message ID for tracking
        log("Email sent with ID: " . $result->id);
    }
}
```

## Exception Handling

The factory may throw exceptions:

```php
use ByJG\Mail\Exception\ProtocolNotRegisteredException;
use ByJG\Mail\Exception\InvalidMailHandlerException;

try {
    $mailer = \ByJG\Mail\MailerFactory::create('unknown://host');
} catch (ProtocolNotRegisteredException $e) {
    // The scheme 'unknown' hasn't been registered
    echo "Protocol not registered: " . $e->getMessage();
}
```

## Best Practices

### Configuration Management

Store connection strings in configuration files or environment variables:

```php
// .env file
MAIL_CONNECTION=tls://user:pass@smtp.example.com:587

// In your application
$mailer = \ByJG\Mail\MailerFactory::create($_ENV['MAIL_CONNECTION']);
```

### Single Registration Point

Register all mailers once during application bootstrap:

```php
// bootstrap.php
$wrappers = [
    \ByJG\Mail\Wrapper\PHPMailerWrapper::class,
    \ByJG\Mail\Wrapper\MailgunApiWrapper::class,
    \ByJG\Mail\Wrapper\AmazonSesWrapper::class,
    \ByJG\Mail\Wrapper\SendMailWrapper::class,
];

foreach ($wrappers as $wrapper) {
    \ByJG\Mail\MailerFactory::registerMailer($wrapper);
}
```

### Testing

Use FakeSender for testing:

```php
// Test configuration
if ($isTestEnvironment) {
    $mailer = \ByJG\Mail\MailerFactory::create('fakesender://localhost');
} else {
    $mailer = \ByJG\Mail\MailerFactory::create($productionConnection);
}
```
