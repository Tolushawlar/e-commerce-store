# API Documentation - E-commerce Platform

## Overview

RESTful API for managing a multi-tenant e-commerce platform.

**Base URL:** `/backend/public/api`  
**Version:** 2.0.0  
**Response Format:** JSON

---

## Authentication

_Currently in development_

Future authentication will use JWT tokens:

```http
Authorization: Bearer <your-jwt-token>
```

---

## Response Structure

### Success Response

```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... },
  "timestamp": "2026-01-26 10:30:00"
}
```

### Error Response

```json
{
  "success": false,
  "message": "Error description",
  "errors": { ... },
  "timestamp": "2026-01-26 10:30:00"
}
```

---

## Endpoints

### Authentication

#### Super Admin Login

```http
POST /api/auth/admin/login
```

**Request Body:**

```json
{
  "email": "admin@platform.com",
  "password": "password123"
}
```

**Response:**

```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "user": {
      "id": 1,
      "username": "admin",
      "email": "admin@platform.com",
      "role": "admin"
    }
  }
}
```

#### Client Login

```http
POST /api/auth/client/login
```

**Request Body:**

```json
{
  "email": "client@example.com",
  "password": "password123"
}
```

**Response:**

```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "client@example.com",
      "company_name": "Acme Corp",
      "subscription_plan": "pro",
      "role": "client"
    }
  }
}
```

#### Client Registration

```http
POST /api/auth/client/register
```

**Request Body:**

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "SecurePass123",
  "company_name": "Acme Corp",
  "phone": "+234...",
  "subscription_plan": "basic"
}
```

**Response:** Same as login (includes token and user data)

#### Verify Token

```http
GET /api/auth/verify
```

**Headers:**

```http
Authorization: Bearer <token>
```

**Response:**

```json
{
  "success": true,
  "message": "Token is valid",
  "data": {
    "valid": true,
    "user": { ... },
    "payload": {
      "user_id": 1,
      "email": "user@example.com",
      "role": "client",
      "iat": 1706265600,
      "exp": 1706272800
    }
  }
}
```

#### Refresh Token

```http
POST /api/auth/refresh
```

**Headers:**

```http
Authorization: Bearer <current-token>
```

**Response:**

```json
{
  "success": true,
  "message": "Token refreshed",
  "data": {
    "token": "new-jwt-token..."
  }
}
```

#### Logout

```http
POST /api/auth/logout
```

**Response:**

```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

#### Change Password

```http
POST /api/auth/change-password
```

**Headers:**

```http
Authorization: Bearer <token>
```

**Request Body:**

```json
{
  "current_password": "OldPass123",
  "new_password": "NewPass456",
  "confirm_password": "NewPass456"
}
```

---

### Clients

**Note:** All client endpoints require authentication.

#### Get All Clients

```http
GET /api/clients
```

**Query Parameters:**

- `page` (int, optional) - Page number (default: 1)
- `limit` (int, optional) - Items per page (default: 20)
- `status` (string, optional) - Filter by status (active|inactive|suspended)

**Response:**

```json
{
  "success": true,
  "message": "Success",
  "data": {
    "clients": [
      {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "company_name": "Acme Corp",
        "phone": "+234...",
        "subscription_plan": "pro",
        "status": "active",
        "store_count": 5,
        "order_count": 120,
        "created_at": "2026-01-15 08:30:00"
      }
    ],
    "pagination": {
      "page": 1,
      "limit": 20,
      "total": 45,
      "pages": 3
    }
  }
}
```

#### Get Client by ID

```http
GET /api/clients/{id}
```

**Response:**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "company_name": "Acme Corp",
    "stores": [...]
  }
}
```

#### Create Client

```http
POST /api/clients
```

**Request Body:**

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "SecurePass123",
  "company_name": "Acme Corp",
  "phone": "+234...",
  "subscription_plan": "pro"
}
```

**Validation Rules:**

- `name`: required, min:2, max:100
- `email`: required, email, unique
- `password`: required, min:8
- `subscription_plan`: required (basic|pro|enterprise)

#### Update Client

```http
PUT /api/clients/{id}
```

**Request Body:** (all fields optional)

```json
{
  "name": "John Doe Updated",
  "company_name": "New Company",
  "subscription_plan": "enterprise"
}
```

#### Delete Client

```http
DELETE /api/clients/{id}
```

---

### Stores

#### Get All Stores

```http
GET /api/stores
```

**Query Parameters:**

- `page` (int, optional)
- `limit` (int, optional)
- `client_id` (int, optional) - Filter by client
- `status` (string, optional) - Filter by status

#### Get Store by ID

```http
GET /api/stores/{id}
```

**Query Parameters:**

- `include` (string, optional) - Set to "customization" to include full customization data

**Response:**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "client_id": 1,
    "store_name": "Tech Store",
    "store_slug": "tech-store",
    "domain": null,
    "primary_color": "#064E3B",
    "accent_color": "#BEF264",
    "product_grid_columns": 4,
    "font_family": "Plus Jakarta Sans",
    "button_style": "rounded",
    "show_search": true,
    "show_cart": true,
    "show_wishlist": false,
    "client_name": "John Doe",
    "created_at": "2026-01-20 10:00:00"
  }
}
```

#### Create Store

```http
POST /api/stores
```

**Request Body:**

```json
{
  "client_id": 1,
  "store_name": "My Awesome Store",
  "store_slug": "my-awesome-store",
  "description": "Best products online",
  "primary_color": "#064E3B",
  "accent_color": "#BEF264",
  "product_grid_columns": 4,
  "font_family": "Plus Jakarta Sans",
  "button_style": "rounded"
}
```

**Validation Rules:**

- `client_id`: required, numeric
- `store_name`: required, min:2, max:100
- `store_slug`: required, min:2, max:100, unique, lowercase alphanumeric with hyphens only

#### Update Store

```http
PUT /api/stores/{id}
```

**Request Body:** (all fields optional)

```json
{
  "store_name": "Updated Store Name",
  "primary_color": "#FF5733",
  "tagline": "Your one-stop shop"
}
```

#### Delete Store

```http
DELETE /api/stores/{id}
```

#### Generate Store Files

```http
POST /api/stores/{id}/generate
```

**Response:**

```json
{
  "success": true,
  "message": "Store generated successfully",
  "data": {
    "store_id": 1,
    "store_url": "/stores/store-1/",
    "files_generated": ["index.html", "config.json"]
  }
}
```

---

### Products

#### Get All Products

```http
GET /api/products
```

**Query Parameters:**

- `store_id` (int, **required**) - Store ID
- `category` (string, optional) - Filter by category
- `status` (string, optional) - Filter by status
- `search` (string, optional) - Search in name and description
- `limit` (int, optional) - Max results

**Response:**

```json
{
  "success": true,
  "data": {
    "products": [
      {
        "id": 1,
        "store_id": 1,
        "name": "Wireless Headphones",
        "description": "Premium sound quality",
        "price": "15000.00",
        "category": "Electronics",
        "image_url": "/uploads/product1.jpg",
        "stock_quantity": 50,
        "status": "active",
        "created_at": "2026-01-22 14:00:00"
      }
    ]
  }
}
```

#### Get Product by ID

```http
GET /api/products/{id}
```

**Response includes product images:**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Wireless Headphones",
    "images": [
      {
        "id": 1,
        "image_url": "/uploads/product1-main.jpg",
        "is_primary": true
      }
    ]
  }
}
```

#### Create Product

```http
POST /api/products
```

**Request Body:**

```json
{
  "store_id": 1,
  "name": "New Product",
  "description": "Product description",
  "price": 9999.99,
  "category": "Electronics",
  "image_url": "/uploads/image.jpg",
  "stock_quantity": 100
}
```

**Validation Rules:**

- `store_id`: required, numeric
- `name`: required, min:2, max:200
- `price`: required, numeric

#### Update Product

```http
PUT /api/products/{id}
```

#### Delete Product

```http
DELETE /api/products/{id}
```

#### Get Low Stock Products

```http
GET /api/products/low-stock
```

**Query Parameters:**

- `store_id` (int, **required**)
- `threshold` (int, optional, default:10)

---

### Orders

#### Get All Orders

```http
GET /api/orders
```

**Query Parameters:**

- `store_id` (int, **required**)
- `status` (string, optional) - pending|processing|shipped|delivered|cancelled
- `from_date` (date, optional) - YYYY-MM-DD
- `to_date` (date, optional) - YYYY-MM-DD
- `limit` (int, optional)

#### Get Order by ID

```http
GET /api/orders/{id}
```

**Response includes order items:**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "store_id": 1,
    "customer_name": "Jane Smith",
    "customer_email": "jane@example.com",
    "total_amount": "45000.00",
    "status": "processing",
    "items": [
      {
        "id": 1,
        "product_id": 5,
        "product_name": "Laptop",
        "quantity": 1,
        "price": "45000.00"
      }
    ],
    "created_at": "2026-01-25 11:00:00"
  }
}
```

#### Create Order

```http
POST /api/orders
```

**Request Body:**

```json
{
  "store_id": 1,
  "customer_name": "Jane Smith",
  "customer_email": "jane@example.com",
  "customer_phone": "+234...",
  "total_amount": 45000.0
}
```

#### Update Order Status

```http
PUT /api/orders/{id}/status
```

**Request Body:**

```json
{
  "status": "shipped"
}
```

**Valid statuses:** pending, processing, shipped, delivered, cancelled

#### Get Order Statistics

```http
GET /api/orders/stats
```

**Query Parameters:**

- `store_id` (int, **required**)

**Response:**

```json
{
  "success": true,
  "data": {
    "total_orders": 150,
    "total_revenue": "2500000.00",
    "average_order_value": "16666.67",
    "pending_orders": 10,
    "delivered_orders": 120
  }
}
```

---

## Error Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `409` - Conflict (e.g., duplicate email)
- `422` - Validation Error
- `500` - Internal Server Error

---

## Rate Limiting

_To be implemented_

Planned: 100 requests per minute per IP

---

## Changelog

### v2.0.0 (2026-01-26)

- Complete MVC refactoring
- RESTful API implementation
- Separated backend/frontend
- Added comprehensive validation
- Improved error handling
