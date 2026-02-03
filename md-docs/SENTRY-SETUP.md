# Sentry Error Monitoring Setup Guide

Complete guide to set up error monitoring and logging with Sentry in your e-commerce platform.

## What is Sentry?

Sentry is a real-time error tracking and monitoring platform that helps you:
- Track errors in production
- Monitor application performance
- Get detailed error reports with stack traces
- Track error trends and frequency
- Set up alerts for critical errors

## Installation

### 1. Install Sentry PHP SDK

```bash
cd "C:\Users\Dell\OneDrive\Documents\LivePetal Projects\e-commerce-store"
composer install
```

This will install the Sentry PHP SDK (already added to composer.json).

### 2. Create Sentry Account

1. Go to [https://sentry.io/signup/](https://sentry.io/signup/)
2. Create a free account (free tier includes 5,000 errors/month)
3. Create a new project:
   - Choose **PHP** as the platform
   - Name it "ecommerce-platform" or similar
4. Copy your **DSN** (looks like: `https://xxxxx@xxxxx.ingest.sentry.io/xxxxx`)

### 3. Configure Environment Variables

Create `.env` file in project root:

```bash
cp .env.example .env
```

Edit `.env` and add your Sentry DSN:

```env
# Sentry Error Monitoring
SENTRY_DSN=https://your-dsn-here@sentry.io/project-id
SENTRY_ENVIRONMENT=development
```

**Production .env:**
```env
SENTRY_DSN=https://your-dsn-here@sentry.io/project-id
SENTRY_ENVIRONMENT=production
APP_ENV=production
APP_DEBUG=false
```

### 4. Load Environment Variables

Update `backend/config/config.php` to read from `.env`:

```php
// Load .env file
if (file_exists(__DIR__ . '/../../.env')) {
    $lines = file(__DIR__ . '/../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
        }
    }
}
```

## Backend Setup (Already Implemented)

The backend Sentry integration is already set up with:

### ✅ Features Implemented:

1. **Automatic Error Capture**:
   - All PHP errors, warnings, and exceptions are captured
   - Fatal errors are logged before shutdown
   - Uncaught exceptions trigger Sentry reports

2. **Custom Logger**:
   - `Logger::error()` - Log errors
   - `Logger::warning()` - Log warnings
   - `Logger::info()` - Log informational messages
   - `Logger::exception()` - Log exceptions with full stack trace
   - `Logger::critical()` - Log critical errors

3. **Sensitive Data Filtering**:
   - Passwords automatically removed from error reports
   - Authorization headers stripped
   - Custom filtering via `before_send` callback

4. **User Context**:
   - Track which user encountered an error
   - Helps identify user-specific issues

5. **Performance Monitoring**:
   - Track slow API requests
   - Monitor database query performance

### Usage Examples:

```php
use App\Helpers\Logger;

// Log an error
Logger::error('Payment processing failed', [
    'order_id' => $orderId,
    'amount' => $amount,
]);

// Log an exception
try {
    $result = $api->charge($payment);
} catch (Exception $e) {
    Logger::exception($e, [
        'payment_id' => $payment->id,
    ]);
}

// Set user context
Logger::setUser($userId, $userEmail, $username);

// Add breadcrumb (trail of events)
Logger::addBreadcrumb('User clicked checkout button', [
    'cart_total' => $cartTotal,
]);

// Start performance transaction
$transaction = Logger::startTransaction('Process Order', 'task');
// ... do work ...
$transaction->finish();
```

## Frontend Setup

### 1. Add Sentry JavaScript SDK

Add to your HTML layout files (`header-admin.php`, `header-client.php`):

```html
<!-- Sentry Error Monitoring -->
<script src="https://browser.sentry-cdn.com/7.91.0/bundle.min.js"
    integrity="sha384-vtF..." crossorigin="anonymous"></script>
<script src="/assets/js/utils/sentry.js"></script>
```

### 2. Configure Frontend DSN

Update `app/assets/js/utils/sentry.js`:

```javascript
const SENTRY_CONFIG = {
    dsn: 'https://your-frontend-dsn@sentry.io/project-id',
    environment: 'production',
    release: '2.0.0',
    // ... rest stays same
};
```

### 3. Usage in JavaScript

```javascript
// Set user after login
SentryHelper.setUser(userId, userEmail, username);

// Clear user on logout
SentryHelper.clearUser();

// Log errors
SentryHelper.logError('Failed to load products', {
    storeId: storeId,
    error: error.message,
});

// Capture exceptions
try {
    await api.createOrder(data);
} catch (error) {
    SentryHelper.captureException(error, {
        orderData: data,
    });
}

// Add breadcrumbs
SentryHelper.addBreadcrumb('User added item to cart', {
    productId: productId,
    quantity: quantity,
});

// Performance monitoring
const transaction = SentryHelper.startTransaction('Load Dashboard');
// ... load data ...
transaction.finish();
```

## Testing Sentry Integration

### Backend Test:

Create `test-sentry.php` in project root:

```php
<?php
require_once __DIR__ . '/backend/bootstrap.php';

use App\Helpers\Logger;

echo "Testing Sentry integration...\n\n";

// Test 1: Log info
Logger::info('Test info message', ['test' => true]);
echo "✓ Info logged\n";

// Test 2: Log warning
Logger::warning('Test warning message', ['test' => true]);
echo "✓ Warning logged\n";

// Test 3: Log error
Logger::error('Test error message', ['test' => true]);
echo "✓ Error logged\n";

// Test 4: Exception
try {
    throw new Exception('Test exception');
} catch (Exception $e) {
    Logger::exception($e, ['context' => 'test']);
    echo "✓ Exception logged\n";
}

echo "\nCheck your Sentry dashboard: https://sentry.io/\n";
```

Run it:
```bash
php test-sentry.php
```

### Frontend Test:

Open browser console and run:

```javascript
// Test error logging
SentryHelper.logError('Test error from console');

// Test exception capture
SentryHelper.captureException(new Error('Test exception'));

// Check Sentry dashboard for events
```

## Production Deployment

### 1. Update Environment Variables on Server

On cPanel or your server, set:

```bash
SENTRY_DSN=your-production-dsn
SENTRY_ENVIRONMENT=production
APP_ENV=production
APP_DEBUG=false
```

### 2. Configure Alerts

In Sentry dashboard:

1. Go to **Settings → Alerts**
2. Create alert rules:
   - Email on new error
   - Slack notification for high-frequency errors
   - Alert on regression (previously fixed errors)

### 3. Set Up Releases

Tag your deployments:

```bash
# Install Sentry CLI
npm install -g @sentry/cli

# Create a release
sentry-cli releases new "2.0.0"
sentry-cli releases set-commits "2.0.0" --auto
sentry-cli releases finalize "2.0.0"

# Deploy
sentry-cli releases deploys "2.0.0" new -e production
```

## Best Practices

### ✅ DO:

- Set meaningful error messages
- Add context to error logs
- Use breadcrumbs to track user actions
- Set user context after authentication
- Clear user context on logout
- Filter sensitive data (passwords, tokens)
- Use different DSNs for dev/staging/production
- Set up alert rules for critical errors

### ❌ DON'T:

- Log validation errors to Sentry (too noisy)
- Include passwords or tokens in error logs
- Capture every 404 error (use sampling)
- Forget to set environment (dev/staging/prod)
- Leave debug mode on in production

## Monitoring Best Practices

### Error Prioritization:

1. **Critical** (P0): Payment failures, data corruption
2. **High** (P1): Login failures, checkout errors
3. **Medium** (P2): Feature errors, UI glitches
4. **Low** (P3): Non-critical warnings

### Response Times:

- **P0**: Immediate (< 1 hour)
- **P1**: Same day (< 8 hours)
- **P2**: Within 3 days
- **P3**: Next sprint/release

## Troubleshooting

### Errors not appearing in Sentry:

1. Check DSN is correct in `.env`
2. Verify Sentry is initialized: `var_dump(\Sentry\State\HubAdapter::getCurrent());`
3. Check internet connectivity from server
4. Verify error_reporting is enabled
5. Check Sentry quota (5,000 events/month on free tier)

### Too many errors:

1. Use sampling: Set `traces_sample_rate` to 0.1 (10%)
2. Filter noisy errors: Add to `ignore_exceptions`
3. Upgrade Sentry plan if needed

### Performance impact:

- Sentry adds ~10-20ms per request (negligible)
- Use async transport in production
- Enable sampling for high-traffic apps

## Dashboard Tour

### Key Sections:

1. **Issues**: All errors grouped by type
2. **Performance**: Slow transactions and queries
3. **Releases**: Track errors by version
4. **Discover**: Query and analyze error data
5. **Alerts**: Configure notifications

## Cost & Limits

### Free Tier:
- 5,000 errors/month
- 10,000 performance units/month
- 30 days data retention

### Paid Plans:
- Start at $26/month
- More events, longer retention
- Advanced features (SAML SSO, custom integrations)

## Support

- **Sentry Docs**: https://docs.sentry.io/platforms/php/
- **Community**: https://forum.sentry.io/
- **Status**: https://status.sentry.io/

---

**Your platform now has enterprise-grade error monitoring!** 🎉
