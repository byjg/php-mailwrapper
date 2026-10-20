# Changelog for Version 6.0

Version 6.0 represents a major update to the MailWrapper library, focusing on improved type safety, modern PHP standards, and enhanced null-safety checks. This release includes several breaking changes that require attention when upgrading from version 5.x.

## New Features

### Enhanced Type Safety
- Added comprehensive type declarations throughout the codebase, including parameter types, return types, and property types
- Introduced PHPDoc annotations with generic types (e.g., `@var array<string, class-string<MailWrapperInterface>>`)
- Added `#[\Override]` attributes to ensure proper method inheritance

### Improved Null Safety
- Refactored mail wrappers with null-coalescing operators for safer null handling
- Enhanced null-safety checks in `PHPMailerWrapper`, `AmazonSesWrapper`, and `MailgunApiWrapper`
- Improved handling of optional URI components (username, password, host, port, scheme)

### Documentation Improvements
- Comprehensive Docusaurus-compatible documentation structure
- New detailed documentation pages:
  - Getting Started guide
  - Envelope configuration guide
  - Connection Strings reference
  - Mailer Factory usage
  - Attachments handling
  - Custom Wrappers implementation
  - Exception handling guide

### Testing & CI Enhancements
- Updated PHPUnit configuration with improved structure and container options
- Enhanced GitHub Actions workflow configuration
- Added `composer test` and `composer psalm` scripts for easier development workflow

## Breaking Changes

| Aspect | Before (5.x) | After (6.0) | Description |
|--------|--------------|-------------|-------------|
| **PHP Version** | `>=8.1 <8.4` | `>=8.3 <8.6` | Minimum PHP version increased to 8.3, added support for PHP 8.4 and 8.5 |
| **byjg/convert** | `^5.0` | `^6.0` | Dependency upgraded to major version 6 |
| **byjg/webrequest** | `^5.0` | `^6.0` | Dependency upgraded to major version 6 |
| **PHPUnit** | `^9.6` | `^10.5\|^11.5` | Dev dependency upgraded to PHPUnit 10 or 11 |
| **Psalm** | `^5.9` | `^5.9\|^6.13` | Dev dependency supports Psalm 6 |
| **Test Files** | `*WrapperTest.php` | `*TestWrapper.php` | Test class files renamed from suffix pattern |
| **MailerFactory::create()** | `string $connection` | `UriInterface\|string $connection` | Now accepts PSR-7 UriInterface in addition to string |
| **MailerFactory::registerMailer()** | No return type | `void` return type | Explicit void return type added |
| **Type Strictness** | Relaxed null handling | Strict null-safety | All wrapper methods now use null-safe operators (`?->`) and null coalescing (`??`) |

### Additional API Changes

- **MailerFactory::registerMailer()**: Parameter now properly typed as `class-string<MailWrapperInterface>`
- **PHPMailerWrapper**: URI property access now uses null-safe operators throughout
- **Envelope**: Minor adjustments for stricter type checking
- **Test namespace**: Test classes use proper PSR-4 autoloading with `Tests\` namespace

## Path to Upgrade from 5.x to 6.0

### Step 1: Check PHP Version
Ensure your environment is running PHP 8.3 or higher:
```bash
php --version
```

If you're running PHP 8.1 or 8.2, you must upgrade to PHP 8.3+ before upgrading to MailWrapper 6.0.

### Step 2: Update Dependencies
Update your `composer.json`:
```json
{
    "require": {
        "byjg/mailwrapper": "^6.0"
    }
}
```

Then run:
```bash
composer update byjg/mailwrapper
```

This will automatically update the required dependencies (`byjg/convert` and `byjg/webrequest` to version 6).

### Step 3: Update Dev Dependencies (if applicable)
If you have PHPUnit or Psalm in your project:
```bash
composer update phpunit/phpunit vimeo/psalm --with-dependencies
```

### Step 4: Review Custom Implementations
If you have custom mail wrappers that implement `MailWrapperInterface`:

1. Add proper type declarations to all methods
2. Add `#[\Override]` attribute to interface methods
3. Ensure null-safety in URI handling:
   ```php
   // Before
   $host = $this->uri->getHost();

   // After
   $host = $this->uri?->getHost() ?? 'default-host';
   ```

### Step 5: Update MailerFactory Usage (Optional)
If you pass URI objects to `MailerFactory::create()`, ensure they implement `Psr\Http\Message\UriInterface`:
```php
use ByJG\Util\Uri;
use ByJG\Mail\MailerFactory;

// Both styles now work:
$mailer1 = MailerFactory::create('smtp://user:pass@smtp.example.com');
$mailer2 = MailerFactory::create(new Uri('smtp://user:pass@smtp.example.com'));
```

### Step 6: Run Tests
After upgrading, run your test suite to ensure compatibility:
```bash
vendor/bin/phpunit
```

If you use Psalm:
```bash
vendor/bin/psalm
```

### Step 7: Update Test Files (if extending MailWrapper tests)
If your project extends any MailWrapper test classes, note the renamed files:
- `BaseWrapperTest.php` → `BaseTestWrapper.php`
- `PHPMailerWrapperTest.php` → `PHPMailerTestWrapper.php`
- `AmazonSesWrapperTest.php` → `AmazonSesTestWrapper.php`
- `MailgunWrapperTest.php` → `MailgunTestWrapper.php`
- `FakeSenderWrapperTest.php` → `FakeSenderTestWrapper.php`

## Bug Fixes

- Fixed Psalm-related type issues for improved static analysis
- Improved null-safety checks preventing potential null pointer exceptions
- Enhanced SMTP authentication handling with proper null checks
- Fixed potential issues with missing URI components in wrapper configurations

## Notes

- All existing connection string formats remain compatible
- No changes to the public API for `Envelope` class usage
- Wrapper registration and usage patterns remain the same
- Documentation now available in comprehensive Docusaurus format

## Migration Support

If you encounter issues during migration, please:
1. Review the new documentation in the `docs/` directory
2. Check the updated examples in the README
3. File an issue on GitHub: https://github.com/byjg/php-mailwrapper/issues

---

**Full Changelog**: https://github.com/byjg/php-mailwrapper/compare/5.0.2...6.0
