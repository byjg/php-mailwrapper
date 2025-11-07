---
sidebar_position: 2
---

# Envelope

The Envelope class represents an email message. It's totally decoupled from the Mailer engine, so you can also use it as a DTO (Data Transfer Object).

## Creating an Envelope

### Basic Constructor

```php
$envelope = new \ByJG\Mail\Envelope();
```

### Constructor with Parameters

```php
$envelope = new \ByJG\Mail\Envelope(
    'from@example.com',      // From address
    'to@example.com',        // To address
    'Email Subject',         // Subject
    '<p>HTML Body</p>',      // Body
    true                     // isHtml (default: true)
);
```

## Setting Email Properties

### From Address

```php
// Simple from address
$envelope->setFrom('johndoe@example.com');

// From address with name
$envelope->setFrom('johndoe@example.com', 'John Doe');
```

### Recipients

```php
// Set a single recipient (replaces existing)
$envelope->setTo('jane@example.com', 'Jane Doe');

// Add multiple recipients
$envelope->addTo('user1@example.com');
$envelope->addTo('user2@example.com', 'User Two');
```

### CC and BCC

```php
// Carbon Copy
$envelope->addCC('manager@example.com', 'Manager');
$envelope->setCC('manager@example.com'); // Replaces all CC

// Blind Carbon Copy
$envelope->addBCC('admin@example.com');
$envelope->setBCC('admin@example.com'); // Replaces all BCC
```

### Subject and Body

```php
// Set subject
$envelope->setSubject('Important Notice');

// Set HTML body
$envelope->setBody('<h1>Hello</h1><p>This is an HTML email</p>');
$envelope->isHtml(true);

// Set plain text body
$envelope->setBody('This is plain text');
$envelope->isHtml(false);
```

### Reply-To

```php
$envelope->setReplyTo('support@example.com');

// If not set, defaults to the From address
$replyTo = $envelope->getReplyTo();
```

## Getting Properties

All properties have corresponding getter methods:

```php
$from = $envelope->getFrom();
$to = $envelope->getTo();           // Returns array
$subject = $envelope->getSubject();
$body = $envelope->getBody();
$cc = $envelope->getCC();           // Returns array
$bcc = $envelope->getBCC();         // Returns array
$isHtml = $envelope->isHtml();
```

## Text Body Generation

The Envelope can automatically generate a plain text version of an HTML body:

```php
$envelope->setBody('<h1>Title</h1><p>Paragraph</p>');
$textBody = $envelope->getBodyText();
// Returns: "# Title\n\nParagraph\n"
```

This is useful for multipart emails that include both HTML and plain text versions.
