# Swagger API Documentation Setup

## Overview

Complete OpenAPI 3.0 documentation for the LivePetal E-Commerce Platform API, accessible via interactive Swagger UI.

## Installation

### 1. Install Dependencies

```bash
cd "c:\Users\Dell\OneDrive\Documents\LivePetal Projects\e-commerce-store"
composer install
```

This installs `zircote/swagger-php` for OpenAPI generation.

## Accessing Documentation

### Interactive Swagger UI

Open your browser and navigate to:

```
http://localhost/backend/public/api/docs
```

**Features:**

- 🎨 Interactive API explorer
- 🔐 Built-in authentication (JWT Bearer tokens)
- 🧪 "Try it out" functionality to test endpoints
- 📋 Request/response examples
- 🔍 Search and filter endpoints
- 📥 Download OpenAPI specification

### OpenAPI JSON Specification

For programmatic access or import into other tools:

```
http://localhost/backend/public/api/openapi.json
```

## Using the Documentation

### 1. Authenticate

To test protected endpoints:

1. Click the **"Authorize"** button (lock icon) at the top
2. Login via API:
   - Use `/api/auth/admin/login` or `/api/auth/client/login`
   - Copy the `token` from the response
3. In the authorization dialog, enter:
   ```
   Bearer YOUR_TOKEN_HERE
   ```
4. Click **"Authorize"**

All subsequent requests will include your JWT token.

### 2. Test Endpoints

Each endpoint shows:

- **Summary** - Quick description
- **Parameters** - Query params, path variables
- **Request Body** - Expected JSON structure with examples
- **Responses** - All possible HTTP responses with examples

To test:

1. Click on any endpoint to expand
2. Click **"Try it out"**
3. Fill in required parameters
4. Click **"Execute"**
5. View response in real-time

### 3. View Models/Schemas

Scroll to **"Schemas"** section at the bottom to see:

- Client
- Store
- Product
- Order
- User
- Error/Success responses

## Documentation Structure

### Tags (Categories)

**Authentication** - 7 endpoints

- Admin Login
- Client Login
- Client Registration
- Verify Token
- Refresh Token
- Logout
- Change Password

**Clients** (Admin Only) - 5 endpoints

- List all clients with stats
- Get single client
- Create client
- Update client
- Delete client

**Stores** - 6 endpoints

- List all stores
- Get single store
- Create store
- Update store
- Delete store
- Generate static files

**Products** - 6 endpoints

- List all products
- Get single product
- Create product
- Update product
- Delete product
- Low stock alerts

**Orders** - 5 endpoints

- List all orders
- Get single order
- Create order
- Update order status
- Order statistics

## Importing into Other Tools

### Postman

1. Open Postman
2. Click **Import**
3. Enter URL: `http://localhost/backend/public/api/openapi.json`
4. Click **Import**

### Insomnia

1. Open Insomnia
2. Click **Create** → **Import from URL**
3. Enter: `http://localhost/backend/public/api/openapi.json`

### VS Code REST Client

Add to `.vscode/settings.json`:

```json
{
  "rest-client.environmentVariables": {
    "local": {
      "baseUrl": "http://localhost/backend/public",
      "token": "YOUR_JWT_TOKEN"
    }
  }
}
```

## Customization

### Update API Information

Edit [backend/swagger.php](backend/swagger.php):

```php
/**
 * @OA\Info(
 *     version="2.0.0",
 *     title="Your API Title",
 *     description="Your description"
 * )
 */
```

### Add New Server

```php
/**
 * @OA\Server(
 *     url="https://staging.example.com",
 *     description="Staging Server"
 * )
 */
```

### Customize UI Theme

Edit [backend/public/docs.html](backend/public/docs.html) CSS section.

## Maintenance

### Regenerate Documentation

Documentation auto-generates on each request to `/api/openapi.json`. No build step needed!

### Add Documentation to New Endpoint

1. **Add @OA annotation** above controller method:

```php
/**
 * @OA\Get(
 *     path="/api/your-endpoint",
 *     tags={"Your Tag"},
 *     summary="Brief description",
 *     description="Detailed description",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Success response"
 *     )
 * )
 */
public function yourMethod(): void
{
    // implementation
}
```

2. **Refresh** `/api/docs` - changes appear automatically!

## Troubleshooting

### "Failed to load API definition"

- Ensure Composer dependencies are installed: `composer install`
- Check PHP error logs for syntax errors in annotations
- Verify `backend/public/generate-openapi.php` is accessible

### "Unauthorized" on protected endpoints

- Click **Authorize** button
- Login via `/api/auth/admin/login` or `/api/auth/client/login`
- Copy token and paste in format: `Bearer YOUR_TOKEN`

### Annotations not appearing

- Check for syntax errors in @OA comments
- Ensure controller is in scan path (backend/controllers)
- Clear browser cache and refresh

### CORS errors when testing

- Update [backend/middleware/CorsMiddleware.php](backend/middleware/CorsMiddleware.php)
- Add your frontend domain to allowed origins

## Best Practices

✅ **Always document:**

- All parameters (path, query, body)
- All response codes (200, 400, 401, 404, 422, 500)
- Required vs optional fields
- Data types and formats

✅ **Use schema references:**

```php
@OA\Property(property="client", ref="#/components/schemas/Client")
```

✅ **Provide examples:**

```php
@OA\Property(property="email", type="string", example="user@example.com")
```

✅ **Group by tags:**
Keep related endpoints under the same tag for better organization.

## Security

⚠️ **Production Considerations:**

- Consider restricting `/api/docs` access in production
- Use environment-based server URLs
- Keep JWT secret secure
- Enable HTTPS only

### Disable docs in production:

In [backend/public/index.php](backend/public/index.php):

```php
if (config('app.env') !== 'production') {
    $router->get('/api/docs', ...);
    $router->get('/api/openapi.json', ...);
}
```

## Resources

- [OpenAPI Specification](https://swagger.io/specification/)
- [Swagger PHP Documentation](https://zircote.github.io/swagger-php/)
- [Swagger UI](https://swagger.io/tools/swagger-ui/)

---

**Documentation Version:** 1.0  
**Last Updated:** January 26, 2026  
**Maintainer:** LivePetal Development Team
