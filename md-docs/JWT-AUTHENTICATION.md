# JWT Authentication Guide

## Overview

The platform now uses JWT (JSON Web Tokens) for secure authentication. Tokens are stateless and include user information and expiration time.

## Authentication Flow

### 1. Login

Users authenticate and receive a JWT token:

**Super Admin Login:**

```bash
POST /api/auth/admin/login
Content-Type: application/json

{
  "email": "admin@platform.com",
  "password": "your_password"
}
```

**Client Login:**

```bash
POST /api/auth/client/login
Content-Type: application/json

{
  "email": "client@example.com",
  "password": "your_password"
}
```

**Response:**

```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "eyJhbGc...",
    "user": {
      "id": 1,
      "email": "admin@platform.com",
      "role": "admin"
    }
  }
}
```

### 2. Use Token

Include the token in the Authorization header for protected routes:

```bash
GET /api/clients
Authorization: Bearer eyJhbGc...
```

Or as a query parameter:

```bash
GET /api/clients?token=eyJhbGc...
```

### 3. Token Verification

The middleware automatically:

- Validates token signature
- Checks expiration
- Extracts user information
- Makes it available in controllers via `$_REQUEST['auth_user']`

## Token Structure

**Payload includes:**

```json
{
  "user_id": 1,
  "email": "admin@platform.com",
  "role": "admin",
  "type": "super_admin",
  "iat": 1706270400,
  "exp": 1706277600
}
```

- `iat`: Issued at (timestamp)
- `exp`: Expiration time (timestamp)
- `role`: User role (admin/client)
- `type`: User type (super_admin/client)

## Token Lifetime

Default: **2 hours** (7200 seconds)

Configure in `backend/config/config.php`:

```php
'security' => [
    'session_lifetime' => 7200
]
```

## API Endpoints

### Public Endpoints (No Auth Required)

**Admin Login:**

```
POST /api/auth/admin/login
```

**Client Login:**

```
POST /api/auth/client/login
```

**Client Registration:**

```
POST /api/auth/client/register
Body: { name, email, password, company_name?, phone? }
```

### Protected Endpoints (Auth Required)

**Verify Token:**

```
GET /api/auth/verify
Authorization: Bearer <token>
```

**Refresh Token:**

```
POST /api/auth/refresh
Authorization: Bearer <token>
```

**Change Password:**

```
POST /api/auth/change-password
Authorization: Bearer <token>
Body: { current_password, new_password, confirm_password }
```

**Logout:**

```
POST /api/auth/logout
Authorization: Bearer <token>
```

## Role-Based Access

### Admin Only Routes

- All client management (`/api/clients/*`)
- Create/delete stores (`POST /api/stores`, `DELETE /api/stores/{id}`)

### Client Routes

- View their own stores
- Manage products in their stores
- View orders for their stores

### Implementation

**In routes:**

```php
// Admin only
$router->get('/api/clients', [ClientController::class, 'index'])
    ->middleware([AuthMiddleware::class, 'adminOnly']);

// Any authenticated user
$router->get('/api/products', [ProductController::class, 'index'])
    ->middleware([AuthMiddleware::class, 'handle']);

// Client only
$router->get('/api/my-profile', [ClientController::class, 'profile'])
    ->middleware([AuthMiddleware::class, 'clientOnly']);
```

## Frontend Integration

### JavaScript Example

```javascript
// Login
async function login(email, password) {
  const response = await fetch("/backend/public/api/auth/admin/login", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ email, password }),
  });

  const data = await response.json();

  if (data.success) {
    // Store token
    localStorage.setItem("auth_token", data.data.token);
    localStorage.setItem("user", JSON.stringify(data.data.user));
  }

  return data;
}

// Make authenticated request
async function getClients() {
  const token = localStorage.getItem("auth_token");

  const response = await fetch("/backend/public/api/clients", {
    headers: {
      Authorization: `Bearer ${token}`,
    },
  });

  return await response.json();
}

// Logout
function logout() {
  localStorage.removeItem("auth_token");
  localStorage.removeItem("user");
  window.location.href = "/login.php";
}

// Check if logged in
function isAuthenticated() {
  return localStorage.getItem("auth_token") !== null;
}
```

### Update API Client

Add to `frontend/assets/js/api.js`:

```javascript
class APIClient {
  constructor(baseURL = "/backend/public") {
    this.baseURL = baseURL;
  }

  async request(endpoint, options = {}) {
    const url = `${this.baseURL}${endpoint}`;

    // Add auth token
    const token = localStorage.getItem("auth_token");

    const config = {
      headers: {
        "Content-Type": "application/json",
        ...(token && { Authorization: `Bearer ${token}` }),
        ...options.headers,
      },
      ...options,
    };

    try {
      const response = await fetch(url, config);
      const data = await response.json();

      // Handle 401 (token expired)
      if (response.status === 401) {
        localStorage.removeItem("auth_token");
        window.location.href = "/login.php";
        throw new Error("Session expired");
      }

      if (!response.ok) {
        throw new Error(data.message || "Request failed");
      }

      return data;
    } catch (error) {
      console.error("API Error:", error);
      throw error;
    }
  }
}
```

## Security Best Practices

1. **Store Tokens Securely**
   - Use `localStorage` for web apps
   - Never log tokens
   - Clear on logout

2. **HTTPS Only**
   - Always use HTTPS in production
   - Tokens can be intercepted on HTTP

3. **Token Refresh**
   - Implement token refresh before expiration
   - Show warning before session expires

4. **Secret Key**
   - Change `jwt_secret` in config
   - Use strong, random string
   - Keep it secret (use environment variables)

5. **Error Handling**
   - Handle 401 responses (redirect to login)
   - Show user-friendly messages

## Testing

### Command Line Test

```bash
php test-jwt.php
```

### cURL Examples

**Login:**

```bash
curl -X POST http://localhost/backend/public/api/auth/admin/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@platform.com","password":"password"}'
```

**Get Clients (with token):**

```bash
curl http://localhost/backend/public/api/clients \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Verify Token:**

```bash
curl http://localhost/backend/public/api/auth/verify \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## Troubleshooting

### "No token provided"

- Check Authorization header format: `Bearer <token>`
- Ensure token is being sent from frontend

### "Invalid or expired token"

- Token may have expired (check `exp` claim)
- Token signature is invalid
- Wrong secret key in config

### "Insufficient permissions"

- User role doesn't match required role
- Check route middleware configuration

## Migration from Old System

1. Update login pages to use new auth endpoints
2. Store JWT token in localStorage
3. Update API calls to include Authorization header
4. Handle 401 responses (redirect to login)
5. Remove old session-based authentication

---

**Last Updated:** January 26, 2026  
**Version:** 2.0.0
