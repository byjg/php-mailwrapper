---
sidebar_position: 6
---

# Custom Wrappers

You can implement your own mail wrapper to integrate with any email service or custom mail handling logic.

## Creating a Custom Wrapper

To create a custom wrapper, extend the `BaseWrapper` class and implement the required methods:

```php
<?php

namespace MyApp\Mail;

use ByJG\Mail\Envelope;
use ByJG\Mail\SendResult;
use ByJG\Mail\Wrapper\BaseWrapper;

class MyCustomWrapper extends BaseWrapper
{
    /**
     * Define which URI schemes this wrapper handles
     */
    public static function schema(): array
    {
        return ['myservice', 'mycustom'];
    }

    /**
     * Implement the send logic
     */
    public function send(Envelope $envelope): SendResult
    {
        // Access connection details from $this->uri
        $apiKey = $this->uri->getUsername();
        $apiSecret = $this->uri->getPassword();
        $host = $this->uri->getHost();

        // Validate the envelope
        $this->validate($envelope);

        // Your custom logic to send the email
        $messageId = $this->sendViaMyService($envelope, $apiKey, $apiSecret, $host);

        // Return the result
        return new SendResult(true, $messageId);
    }

    private function sendViaMyService(Envelope $envelope, string $apiKey, string $apiSecret, string $host): string
    {
        // Implement your email sending logic here
        // This might involve:
        // - Making API calls
        // - Formatting the email data
        // - Handling attachments
        // - Error handling

        return 'message-id-123';
    }
}
```

## Implementing the Schema Method

The `schema()` method defines which URI schemes your wrapper handles. Return an array of scheme names:

```php
public static function schema(): array
{
    return ['myservice'];  // Handles myservice://...
}
```

You can support multiple schemes:

```php
public static function schema(): array
{
    return ['myservice', 'myservice-ssl', 'myservice-tls'];
}
```

## Implementing the Send Method

The `send()` method must:

1. Accept an `Envelope` parameter
2. Return a `SendResult` object
3. Handle the actual email delivery

```php
public function send(Envelope $envelope): SendResult
{
    // Validate the envelope (optional but recommended)
    $this->validate($envelope);

    try {
        // Your sending logic
        $messageId = $this->performSend($envelope);

        // Return success
        return new SendResult(true, $messageId);

    } catch (\Exception $e) {
        // Handle errors - you might throw an exception or return failure
        throw new \ByJG\Mail\Exception\MailApiException(
            'Failed to send email: ' . $e->getMessage()
        );
    }
}
```

## Accessing Connection Details

The URI is available via `$this->uri`:

```php
// Get URI components
$scheme = $this->uri->getScheme();        // e.g., 'myservice'
$username = $this->uri->getUsername();    // e.g., 'api-key'
$password = $this->uri->getPassword();    // e.g., 'secret'
$host = $this->uri->getHost();            // e.g., 'api.myservice.com'
$port = $this->uri->getPort();            // e.g., 443
$query = $this->uri->getQuery();          // e.g., 'region=us'

// Get query parameters
$region = $this->uri->getQueryPart('region'); // Extract specific parameter
```

## Custom Validation

You can add custom validation by overriding the `validate()` method:

```php
public function validate(Envelope $envelope): void
{
    // Call parent validation
    parent::validate($envelope);

    // Add your custom validation
    if (empty($envelope->getSubject())) {
        throw new \ByJG\Mail\Exception\InvalidMessageFormatException(
            'Subject is required'
        );
    }

    if (!$envelope->isHtml() && !empty($envelope->getAttachments())) {
        throw new \ByJG\Mail\Exception\InvalidMessageFormatException(
            'Attachments require HTML mode'
        );
    }
}
```

The base `validate()` method checks:
- At least one recipient exists
- From address is set

## Handling Attachments

Process attachments from the envelope:

```php
private function processAttachments(Envelope $envelope): array
{
    $processed = [];

    foreach ($envelope->getAttachments() as $name => $attachment) {
        $filePath = $attachment['content'];
        $mimeType = $attachment['content-type'];
        $disposition = $attachment['disposition']; // 'attachment' or 'inline'

        // Read file content
        $content = file_get_contents($filePath);
        $encoded = base64_encode($content);

        $processed[] = [
            'name' => $name,
            'type' => $mimeType,
            'content' => $encoded,
            'disposition' => $disposition,
        ];
    }

    return $processed;
}
```

## Registering Your Wrapper

Register your custom wrapper with the factory:

```php
\ByJG\Mail\MailerFactory::registerMailer(\MyApp\Mail\MyCustomWrapper::class);

// Now you can use it
$mailer = \ByJG\Mail\MailerFactory::create('myservice://api-key:secret@api.example.com');
```

## Complete Example

Here's a complete example implementing a simple HTTP API wrapper:

```php
<?php

namespace MyApp\Mail;

use ByJG\Mail\Envelope;
use ByJG\Mail\SendResult;
use ByJG\Mail\Wrapper\BaseWrapper;
use ByJG\Mail\Exception\MailApiException;

class HttpApiWrapper extends BaseWrapper
{
    public static function schema(): array
    {
        return ['httpapi'];
    }

    public function send(Envelope $envelope): SendResult
    {
        $this->validate($envelope);

        $apiUrl = sprintf(
            'https://%s/send',
            $this->uri->getHost()
        );

        $payload = [
            'from' => $envelope->getFrom(),
            'to' => $envelope->getTo(),
            'subject' => $envelope->getSubject(),
            'html' => $envelope->getBody(),
            'api_key' => $this->uri->getUsername(),
        ];

        if (!empty($envelope->getCC())) {
            $payload['cc'] = $envelope->getCC();
        }

        if (!empty($envelope->getBCC())) {
            $payload['bcc'] = $envelope->getBCC();
        }

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new MailApiException(
                sprintf('API returned error: %d - %s', $httpCode, $response)
            );
        }

        $data = json_decode($response, true);

        return new SendResult(true, $data['message_id'] ?? null);
    }
}
```

Usage:

```php
\ByJG\Mail\MailerFactory::registerMailer(\MyApp\Mail\HttpApiWrapper::class);

$mailer = \ByJG\Mail\MailerFactory::create('httpapi://YOUR_API_KEY@api.emailservice.com');
$mailer->send($envelope);
```

## Best Practices

1. **Error Handling**: Always handle errors gracefully and throw appropriate exceptions
2. **Validation**: Call `parent::validate()` before your custom validation
3. **Return Values**: Always return a `SendResult` object with appropriate success status
4. **Message IDs**: Include message IDs when available for tracking
5. **Testing**: Create unit tests for your wrapper using the FakeSender as a reference
6. **Documentation**: Document your wrapper's connection string format and requirements
