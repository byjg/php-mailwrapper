---
sidebar_position: 5
---

# Attachments

The Envelope class supports two types of attachments: regular attachments and embedded images.

## Regular Attachments

Regular attachments appear as downloadable files in the email client.

### Adding Attachments

```php
$envelope = new \ByJG\Mail\Envelope('from@email.com', 'to@email.com', 'Subject', 'Body');

$envelope->addAttachment(
    'filename.pdf',              // Name shown in email
    '/path/to/file.pdf',         // Path to file on disk
    'application/pdf'            // MIME type
);

// Add multiple attachments
$envelope->addAttachment('document.docx', '/path/to/doc.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
$envelope->addAttachment('image.png', '/path/to/image.png', 'image/png');

$mailer->send($envelope);
```

### Common MIME Types

| File Type | MIME Type |
|:----------|:----------|
| PDF | `application/pdf` |
| Word (.docx) | `application/vnd.openxmlformats-officedocument.wordprocessingml.document` |
| Excel (.xlsx) | `application/vnd.openxmlformats-officedocument.spreadsheetml.sheet` |
| PNG | `image/png` |
| JPEG | `image/jpeg` |
| GIF | `image/gif` |
| ZIP | `application/zip` |
| Plain Text | `text/plain` |
| CSV | `text/csv` |
| JSON | `application/json` |

## Embedded Images

Embedded images (inline attachments) are displayed directly in the email body rather than as downloadable files. This is useful for including logos, diagrams, or other images in your HTML email.

### Adding Embedded Images

```php
$envelope = new \ByJG\Mail\Envelope('from@email.com', 'to@email.com', 'Subject');

// Add the image as an embedded attachment
$envelope->addEmbedImage(
    'company_logo',              // Content ID (used in HTML)
    '/path/to/logo.png',         // Path to image file
    'image/png'                  // MIME type
);

// Reference the image in HTML using cid: protocol
$envelope->setBody('<html><body><img src="cid:company_logo" alt="Logo"/></body></html>');

$mailer->send($envelope);
```

### Multiple Embedded Images

```php
$envelope = new \ByJG\Mail\Envelope('from@email.com', 'to@email.com', 'Newsletter');

// Add multiple images
$envelope->addEmbedImage('header', '/path/to/header.png', 'image/png');
$envelope->addEmbedImage('chart', '/path/to/chart.jpg', 'image/jpeg');
$envelope->addEmbedImage('footer', '/path/to/footer.png', 'image/png');

// Use all images in the HTML body
$html = '
<html>
<body>
    <img src="cid:header" alt="Header" style="width: 100%;" />
    <p>Check out our latest statistics:</p>
    <img src="cid:chart" alt="Chart" style="width: 600px;" />
    <img src="cid:footer" alt="Footer" style="width: 100%;" />
</body>
</html>';

$envelope->setBody($html);
$mailer->send($envelope);
```

## Combining Both Types

You can use both regular attachments and embedded images in the same email:

```php
$envelope = new \ByJG\Mail\Envelope('from@email.com', 'to@email.com', 'Report');

// Embed logo in email body
$envelope->addEmbedImage('logo', '/path/to/logo.png', 'image/png');

// Attach PDF report for download
$envelope->addAttachment('monthly_report.pdf', '/path/to/report.pdf', 'application/pdf');

$html = '
<html>
<body>
    <img src="cid:logo" alt="Company Logo" />
    <h1>Monthly Report</h1>
    <p>Please find the attached monthly report.</p>
</body>
</html>';

$envelope->setBody($html);
$mailer->send($envelope);
```

## Getting Attachments

You can retrieve all attachments from an envelope:

```php
$attachments = $envelope->getAttachments();

// Returns an array like:
// [
//     'filename.pdf' => [
//         'content' => '/path/to/file.pdf',
//         'content-type' => 'application/pdf',
//         'disposition' => 'attachment'
//     ],
//     'logo' => [
//         'content' => '/path/to/logo.png',
//         'content-type' => 'image/png',
//         'disposition' => 'inline'
//     ]
// ]
```

The `disposition` field indicates whether it's a regular attachment (`attachment`) or an embedded image (`inline`).

## Best Practices

### File Paths

- Use absolute paths to ensure files are found regardless of the current working directory
- Verify files exist before adding them as attachments
- Consider file size limits imposed by email servers (typically 25MB total)

### Content IDs

- Use descriptive content IDs for embedded images (e.g., `company_logo`, `header_image`)
- Keep content IDs alphanumeric (avoid special characters)
- Content IDs are case-sensitive

### MIME Types

- Always specify the correct MIME type for better compatibility
- Use `image/png` for PNG files, `image/jpeg` for JPEG files
- For unknown types, `application/octet-stream` is a safe fallback

### Email Client Compatibility

- Not all email clients handle embedded images the same way
- Some clients may show embedded images as attachments in addition to displaying them inline
- Always include `alt` attributes for images for accessibility
