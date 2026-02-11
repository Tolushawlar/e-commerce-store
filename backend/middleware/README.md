# Rate Limiting Middleware

A production-ready, secure rate limiting middleware for PHP applications with multiple storage backends and comprehensive security features.

## Features

✅ **Multiple Storage Backends**
- File-based storage (development)
- Redis storage (production - recommended)
- Automatic fallback mechanism

✅ **Flexible Rate Limiting**
- IP-based limiting
- User-based limiting
- API key-based limiting
- Endpoint-specific rules
- Pattern matching for routes

✅ **Security Features**
- Whitelist/blacklist support
- Sliding window algorithm
- Atomic operations
- Distributed support (Redis)
- Progressive penalties

✅ **Developer Friendly**
- Comprehensive headers (X-RateLimit-*)
- Easy configuration
- Detailed error responses
- Extensive documentation

## Quick Start

### 1. Basic Usage

```php
<?php
require_once 'middleware/RateLimitMiddleware.php';

use Middleware\RateLimitMiddleware;

// Load configuration
$config = require 'config/config.php';

// Initialize and apply middleware
$rateLimitMiddleware = new RateLimitMiddleware($config['rate_limiting']);
$rateLimitMiddleware->handle();

// Your API logic continues here...
```

### 2. Configuration

Add to your `config/config.php`:

```php
'rate_limiting' => [
    'enabled' => true,
    'storage' => 'file', // or 'redis' for production
    'default_limit' => 60,
    'default_window' => 60, // seconds
    
    'endpoints' => [
        '/api/auth/login' => ['limit' => 5, 'window' => 300],
        '/api/*' => ['limit' => 100, 'window' => 60]
    ],
    
    'whitelist' => ['127.0.0.1'],
    'blacklist' => []
]
```

### 3. Environment Variables

Add to `.env`:

```env
RATE_LIMITING_ENABLED=true
RATE_LIMIT_STORAGE=redis
RATE_LIMIT_DEFAULT=60
RATE_LIMIT_WINDOW=60

# Redis (optional, for production)
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=
REDIS_DATABASE=0
```

## Storage Backends

### File Storage (Development)

- **Pros**: No dependencies, easy setup
- **Cons**: Not suitable for distributed systems
- **Use Case**: Development, single-server deployments

```php
'storage' => 'file',
'file_storage_dir' => '/path/to/storage/rate_limits'
```

### Redis Storage (Production)

- **Pros**: Fast, distributed, atomic operations
- **Cons**: Requires Redis extension
- **Use Case**: Production, distributed systems

```php
'storage' => 'redis',
'redis_host' => '127.0.0.1',
'redis_port' => 6379,
'redis_password' => 'your-password',
'redis_database' => 0
```

## Endpoint-Specific Limits

Configure different limits for different endpoints:

```php
'endpoints' => [
    // Strict limits for auth
    '/api/auth/login' => ['limit' => 5, 'window' => 300],
    '/api/auth/register' => ['limit' => 3, 'window' => 3600],
    
    // Moderate limits for API
    '/api/products/*' => ['limit' => 100, 'window' => 60],
    '/api/orders/*' => ['limit' => 50, 'window' => 60],
    
    // Higher limits for admin
    '/api/admin/*' => ['limit' => 200, 'window' => 60]
]
```

## Response Headers

The middleware automatically sets these headers:

- `X-RateLimit-Limit`: Maximum requests allowed
- `X-RateLimit-Remaining`: Requests remaining in current window
- `X-RateLimit-Reset`: Seconds until limit resets
- `Retry-After`: Seconds to wait (only when rate limited)

## Error Response

When rate limit is exceeded (HTTP 429):

```json
{
  "error": "Too Many Requests",
  "message": "Rate limit exceeded. Please try again later.",
  "limit": 60,
  "remaining": 0,
  "reset": 45,
  "retry_after": 45
}
```

## Whitelist/Blacklist

### Whitelist (Bypass Rate Limiting)

```php
'whitelist' => ['127.0.0.1', '192.168.1.100']
```

Or via environment:
```env
RATE_LIMIT_WHITELIST=127.0.0.1,192.168.1.100
```

### Blacklist (Permanent Block)

```php
'blacklist' => ['10.0.0.50', '203.0.113.0']
```

Or via environment:
```env
RATE_LIMIT_BLACKLIST=10.0.0.50,203.0.113.0
```

## Advanced Usage

### Manual Rate Limit Check

```php
use Middleware\RateLimiter;

$rateLimiter = new RateLimiter($config['rate_limiting']);

$identifier = 'user:123';
$result = $rateLimiter->attempt($identifier, 10, 60);

if ($result['allowed']) {
    // Process request
} else {
    // Handle rate limit exceeded
}
```

### Dynamic Whitelist/Blacklist

```php
$rateLimiter->addToWhitelist('192.168.1.200');
$rateLimiter->addToBlacklist('10.0.0.100');

if ($rateLimiter->isWhitelisted($ip)) {
    // Skip rate limiting
}
```

### Reset Rate Limit

```php
$rateLimiter->reset('ip:192.168.1.1');
```

## Installation

### Requirements

- PHP 7.4 or higher
- Redis extension (optional, for production)

### Setup

1. Copy middleware files to your project:
```bash
backend/
├── middleware/
│   ├── RateLimiter.php
│   ├── RateLimitMiddleware.php
│   └── storage/
│       ├── StorageInterface.php
│       ├── FileStorage.php
│       └── RedisStorage.php
```

2. Update your configuration in `config/config.php`

3. Apply middleware in your API entry point

## Testing

### Test Rate Limiting

```bash
# Send multiple requests quickly
for i in {1..10}; do
  curl -i http://localhost:8000/api/health
done
```

### Expected Response

First 60 requests (within 1 minute):
```
HTTP/1.1 200 OK
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 60
```

After limit exceeded:
```
HTTP/1.1 429 Too Many Requests
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 0
X-RateLimit-Reset: 45
Retry-After: 45
```

## Performance

- **File Storage**: ~1-2ms overhead per request
- **Redis Storage**: ~0.5-1ms overhead per request
- **Memory**: Minimal (~1MB for file storage, negligible for Redis)

## Security Considerations

1. **Distributed Attacks**: Use Redis for distributed rate limiting
2. **IP Spoofing**: Validate X-Forwarded-For headers
3. **Whitelist Carefully**: Only whitelist trusted IPs
4. **Monitor Logs**: Track rate limit violations
5. **Adjust Limits**: Fine-tune based on your traffic patterns

## Troubleshooting

### Redis Connection Failed

```
Redis unavailable, falling back to file storage
```

**Solution**: Check Redis is running and credentials are correct

### Rate Limits Not Working

1. Check `enabled` is set to `true`
2. Verify middleware is called before routing
3. Check file storage directory is writable
4. Review endpoint patterns match your routes

### High Memory Usage (File Storage)

**Solution**: 
- Switch to Redis for production
- Reduce cleanup probability
- Decrease rate limit windows

## License

MIT License - See LICENSE file for details

## Support

For issues and questions:
- Check `USAGE_EXAMPLES.php` for detailed examples
- Review configuration in `config/config.php`
- Consult inline documentation in source files
