---
sidebar_position: 7
---

# Exceptions

MailWrapper defines several exception classes to handle different error conditions. All exceptions are in the `ByJG\Mail\Exception` namespace.

## Exception Hierarchy

All MailWrapper exceptions extend standard PHP exceptions, allowing you to catch them individually or as a group.

```text
\Exception
├── InvalidEMailException
├── InvalidMailHandlerException
├── InvalidMessageFormatException
├── MailApiException
└── ProtocolNotRegisteredException
```

## Exception Types

### InvalidEMailException

Thrown when email validation fails.

**Common causes:**
- Missing sender address
- Missing recipient address
- Invalid email format

**Example:**

```php
use ByJG\Mail\Exception\InvalidEMailException;

try {
    $envelope = new \ByJG\Mail\Envelope();
    // No from or to addresses set
    $mailer->send($envelope);
} catch (InvalidEMailException $e) {
    echo "Email validation failed: " . $e->getMessage();
    // Output: "Destination Email was not provided"
}
```

### ProtocolNotRegisteredException

Thrown when trying to create a mailer for an unregistered protocol scheme.

**Common causes:**
- Forgetting to register a wrapper with `MailerFactory::registerMailer()`
- Using an invalid or misspelled scheme in the connection string
- Wrapper not installed or available

**Example:**

```php
use ByJG\Mail\Exception\ProtocolNotRegisteredException;

try {
    // Forgot to register the SMTP wrapper
    $mailer = \ByJG\Mail\MailerFactory::create('smtp://user:pass@host');
} catch (ProtocolNotRegisteredException $e) {
    echo "Protocol error: " . $e->getMessage();
    // Output: "Protocol not found/registered!"
}
```

**Solution:**

```php
// Register the wrapper before creating the mailer
\ByJG\Mail\MailerFactory::registerMailer(\ByJG\Mail\Wrapper\PHPMailerWrapper::class);
$mailer = \ByJG\Mail\MailerFactory::create('smtp://user:pass@host');
```

### InvalidMailHandlerException

Thrown when trying to register an invalid wrapper class.

**Common causes:**
- Registering a class that doesn't implement `MailWrapperInterface`
- Passing a non-existent class name

**Example:**

```php
use ByJG\Mail\Exception\InvalidMailHandlerException;

try {
    \ByJG\Mail\MailerFactory::registerMailer(\MyApp\InvalidClass::class);
} catch (InvalidMailHandlerException $e) {
    echo "Registration error: " . $e->getMessage();
    // Output: "Class not implements ConnectorInterface!"
}
```

### InvalidMessageFormatException

Thrown when the message format is invalid or incomplete.

**Common causes:**
- Invalid message structure
- Missing required fields
- Malformed content

**Example:**

```php
use ByJG\Mail\Exception\InvalidMessageFormatException;

// This exception might be thrown by custom wrappers with additional validation
try {
    // Some wrapper that validates subject is not empty
    $mailer->send($envelope);
} catch (InvalidMessageFormatException $e) {
    echo "Message format error: " . $e->getMessage();
}
```

### MailApiException

Thrown when an email API returns an error.

**Common causes:**
- Invalid API credentials
- API rate limiting
- Network connectivity issues
- Service outages
- Invalid email addresses rejected by the API

**Example:**

```php
use ByJG\Mail\Exception\MailApiException;

try {
    $mailer = \ByJG\Mail\MailerFactory::create('mailgun://invalid_key@domain.com');
    $mailer->send($envelope);
} catch (MailApiException $e) {
    echo "API error: " . $e->getMessage();
    // Might include API-specific error details
}
```

## Exception Handling Best Practices

### Catch Specific Exceptions

Handle different exception types appropriately:

```php
use ByJG\Mail\Exception\{
    InvalidEMailException,
    ProtocolNotRegisteredException,
    MailApiException
};

try {
    $mailer = \ByJG\Mail\MailerFactory::create($connection);
    $result = $mailer->send($envelope);

} catch (InvalidEMailException $e) {
    // Validation error - fix the envelope data
    error_log("Invalid email configuration: " . $e->getMessage());

} catch (ProtocolNotRegisteredException $e) {
    // Configuration error - register the protocol
    error_log("Missing protocol registration: " . $e->getMessage());

} catch (MailApiException $e) {
    // API error - might be temporary, consider retrying
    error_log("Mail service API error: " . $e->getMessage());

} catch (\Exception $e) {
    // Catch-all for unexpected errors
    error_log("Unexpected error: " . $e->getMessage());
}
```

### Validate Before Sending

Validate the envelope before attempting to send:

```php
$envelope = new \ByJG\Mail\Envelope();

// Validate manually if needed
if (empty($envelope->getFrom())) {
    throw new \ByJG\Mail\Exception\InvalidEMailException('From address required');
}

if (count($envelope->getTo()) === 0) {
    throw new \ByJG\Mail\Exception\InvalidEMailException('At least one recipient required');
}
```

### Logging and Debugging

Include context in error handling:

```php
try {
    $result = $mailer->send($envelope);

    if (!$result->success) {
        error_log(sprintf(
            'Failed to send email to %s: %s',
            implode(', ', $envelope->getTo()),
            'Unknown error'
        ));
    }

} catch (\Exception $e) {
    error_log(sprintf(
        'Exception sending email to %s: %s - %s',
        implode(', ', $envelope->getTo()),
        get_class($e),
        $e->getMessage()
    ));

    // Optionally include stack trace in development
    if (DEBUG_MODE) {
        error_log($e->getTraceAsString());
    }
}
```

### Retry Logic

Implement retry logic for transient failures:

```php
use ByJG\Mail\Exception\MailApiException;

function sendWithRetry($mailer, $envelope, $maxAttempts = 3): bool
{
    $attempt = 0;

    while ($attempt < $maxAttempts) {
        try {
            $result = $mailer->send($envelope);
            return $result->success;

        } catch (MailApiException $e) {
            $attempt++;

            if ($attempt >= $maxAttempts) {
                throw $e;
            }

            // Exponential backoff
            sleep(pow(2, $attempt));
        }
    }

    return false;
}
```

### Graceful Degradation

Consider fallback mechanisms:

```php
$primaryMailer = \ByJG\Mail\MailerFactory::create('mailgun://key@domain');
$fallbackMailer = \ByJG\Mail\MailerFactory::create('smtp://user:pass@host:587');

try {
    $result = $primaryMailer->send($envelope);
} catch (\Exception $e) {
    error_log('Primary mailer failed, using fallback: ' . $e->getMessage());
    try {
        $result = $fallbackMailer->send($envelope);
    } catch (\Exception $e2) {
        error_log('Fallback mailer also failed: ' . $e2->getMessage());
        throw $e2;
    }
}
```
