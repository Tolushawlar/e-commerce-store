# OpenAPI Attributes Guide

## Using PHP 8 Attributes (Recommended)

Since you're on PHP 8.2, you can use attributes instead of annotations for OpenAPI documentation.

### Example: Converting Annotations to Attributes

**Old way (Annotations - deprecated):**
```php
/**
 * @OA\Post(
 *     path="/api/auth/login",
 *     tags={"Authentication"},
 *     summary="User Login"
 * )
 */
public function login() { }
```

**New way (Attributes - recommended):**
```php
use OpenApi\Attributes as OA;

#[OA\Post(
    path: '/api/auth/login',
    tags: ['Authentication'],
    summary: 'User Login'
)]
public function login() { }
```

### Base OpenAPI Configuration

Create a simple class for base configuration:

```php
<?php

namespace App;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '2.0.0',
    title: 'LivePetal E-Commerce Platform API',
    description: 'Multi-tenant e-commerce platform with JWT authentication'
)]
#[OA\Server(url: 'http://localhost:8000/api', description: 'Local Development')]
#[OA\Server(url: 'https://api.livepetal.com', description: 'Production')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]
class OpenApiConfig
{
}
```

### Complete Controller Example

```php
<?php

namespace App\Controllers;

use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/auth/login',
        tags: ['Authentication'],
        summary: 'User Login',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string', format: 'password')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'token', type: 'string'),
                        new OA\Property(property: 'user', type: 'object')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Invalid credentials')
        ]
    )]
    public function login()
    {
        // Implementation
    }
}
```

## Current Status

✅ Your current annotations are working fine
✅ No need to convert immediately
📝 When creating new endpoints, consider using attributes
🔄 Migrate gradually when convenient

## Regenerating OpenAPI Spec

Always use this command to avoid encoding issues:

```bash
php api/generate-openapi.php | Set-Content -Path api/openapi.json -Encoding UTF8
```

Or add it to your package.json scripts:
```json
{
  "scripts": {
    "openapi": "php api/generate-openapi.php > api/openapi.json"
  }
}
```
