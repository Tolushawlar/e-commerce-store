# Customer System Implementation Guide

## Overview

The customer system allows store visitors to create accounts, login, manage their profile, and shop from stores. It supports both **guest checkout** (buy without account) and **registered customers** (create account for order history and saved cart).

## Features Implemented

✅ Store-specific customer accounts  
✅ Guest checkout support  
✅ Customer registration & login  
✅ JWT-based authentication (7-day token lifetime)  
✅ Persistent shopping cart for registered users  
✅ Customer profile management  
✅ Password change  
✅ Multiple addresses per customer  
✅ Order history tracking  
✅ Cart sync on login  

---

## Database Schema

### Tables Created

1. **`store_customers`** - Customer accounts per store
2. **`customer_addresses`** - Shipping/billing addresses
3. **`shopping_carts`** - Persistent cart items
4. **`orders`** - Updated with customer references

### Migration

Run the migration to create customer tables:

```bash
mysql -u username -p ecommerce_platform < backend/database/add_customer_system.sql
```

---

## API Endpoints

All customer endpoints are **public** (no admin/client auth required).

### Base URL Pattern
```
/api/stores/{store_id}/...
```

### Authentication Endpoints

#### 1. Register Customer
```http
POST /api/stores/1/customers/register

{
  "email": "customer@example.com",
  "password": "password123",
  "first_name": "John",
  "last_name": "Doe",
  "phone": "+234123456789"
}

Response:
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "customer": { ... },
    "token": "eyJhbGc..."
  }
}
```

#### 2. Customer Login
```http
POST /api/stores/1/customers/login

{
  "email": "customer@example.com",
  "password": "password123",
  "session_cart": [] // Optional: sync cart from localStorage
}

Response:
{
  "success": true,
  "data": {
    "customer": { ... },
    "token": "eyJhbGc...",
    "cart_count": 3
  }
}
```

#### 3. Logout
```http
POST /api/stores/1/customers/logout
Authorization: Bearer {token}
```

---

### Profile Management

#### Get Current Customer
```http
GET /api/stores/1/customers/me
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": {
    "id": 1,
    "email": "customer@example.com",
    "first_name": "John",
    "last_name": "Doe",
    "addresses": [...],
    "order_count": 5,
    "total_spent": 150000,
    "cart_count": 2
  }
}
```

#### Update Profile
```http
PUT /api/stores/1/customers/me
Authorization: Bearer {token}

{
  "first_name": "Jane",
  "last_name": "Smith",
  "phone": "+234987654321"
}
```

#### Change Password
```http
POST /api/stores/1/customers/change-password
Authorization: Bearer {token}

{
  "current_password": "oldpass123",
  "new_password": "newpass456"
}
```

---

### Shopping Cart

#### Get Cart
```http
GET /api/stores/1/cart
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": {
    "items": [
      {
        "id": 1,
        "product_id": 5,
        "product_name": "Laptop",
        "product_price": 50000,
        "quantity": 2,
        "subtotal": 100000,
        "product_image": "...",
        "stock_quantity": 10
      }
    ],
    "totals": {
      "item_count": 1,
      "total_items": 2,
      "total_amount": 100000
    },
    "issues": [] // Stock availability issues
  }
}
```

#### Add to Cart
```http
POST /api/stores/1/cart
Authorization: Bearer {token}

{
  "product_id": 5,
  "quantity": 2
}
```

#### Update Cart Item
```http
PUT /api/stores/1/cart/1
Authorization: Bearer {token}

{
  "quantity": 3  // Set to 0 to remove
}
```

#### Remove from Cart
```http
DELETE /api/stores/1/cart/1
Authorization: Bearer {token}
```

#### Clear Cart
```http
DELETE /api/stores/1/cart
Authorization: Bearer {token}
```

#### Sync Cart (on login)
```http
POST /api/stores/1/cart/sync
Authorization: Bearer {token}

{
  "items": [
    { "product_id": 5, "quantity": 2 },
    { "product_id": 8, "quantity": 1 }
  ]
}
```

---

## Frontend Integration

### 1. Store Token in localStorage

```javascript
// After login/register
const response = await fetch('/api/stores/1/customers/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ email, password })
});

const data = await response.json();

// Save token
localStorage.setItem('customer_token', data.data.token);
localStorage.setItem('customer', JSON.stringify(data.data.customer));
```

### 2. Include Token in Requests

```javascript
const token = localStorage.getItem('customer_token');

const response = await fetch('/api/stores/1/cart', {
  headers: {
    'Authorization': `Bearer ${token}`
  }
});
```

### 3. Cart Management (Guest vs Logged In)

```javascript
class StoreCart {
  constructor(storeId) {
    this.storeId = storeId;
    this.token = localStorage.getItem('customer_token');
  }

  async addToCart(productId, quantity) {
    if (this.token) {
      // Registered user: Add to database
      return fetch(`/api/stores/${this.storeId}/cart`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${this.token}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ product_id: productId, quantity })
      });
    } else {
      // Guest: Add to localStorage
      let cart = JSON.parse(localStorage.getItem('guest_cart') || '[]');
      const existing = cart.find(item => item.product_id === productId);
      
      if (existing) {
        existing.quantity += quantity;
      } else {
        cart.push({ product_id: productId, quantity });
      }
      
      localStorage.setItem('guest_cart', JSON.stringify(cart));
    }
  }

  async syncCart() {
    // When user logs in, sync localStorage cart to database
    const guestCart = JSON.parse(localStorage.getItem('guest_cart') || '[]');
    
    if (guestCart.length > 0 && this.token) {
      await fetch(`/api/stores/${this.storeId}/cart/sync`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${this.token}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ items: guestCart })
      });

      // Clear guest cart
      localStorage.removeItem('guest_cart');
    }
  }
}
```

---

## Key Features

### Store-Specific Customers

- Same email can be used across different stores
- Each store has isolated customer database
- `unique_store_email` constraint ensures email uniqueness per store

### Guest Support

- `is_guest = true` for customers who haven't registered
- Guest customers have `password_hash = NULL`
- Can be upgraded to registered account later

### Security

- Passwords hashed with bcrypt
- JWT tokens valid for 7 days
- Separate token type (`store_customer`) from admin/client tokens
- Token includes store_id to prevent cross-store access

### Cart Behavior

- **Registered users**: Cart saved to database, persists across sessions
- **Guest users**: Cart in localStorage, sync on login
- Automatic stock validation
- Out-of-stock detection

---

## Next Steps

To complete the customer order flow:

1. ✅ Customer accounts - **DONE**
2. ✅ Shopping cart - **DONE**
3. ⏳ Customer addresses management (controller needed)
4. ⏳ Checkout & order placement (next phase)
5. ⏳ Order tracking
6. ⏳ Payment integration

---

## Testing

### Test Customer Registration
```bash
curl -X POST http://localhost:8000/api/stores/1/customers/register \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123",
    "first_name": "Test",
    "last_name": "User"
  }'
```

### Test Login
```bash
curl -X POST http://localhost:8000/api/stores/1/customers/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

### Test Add to Cart
```bash
curl -X POST http://localhost:8000/api/stores/1/cart \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "quantity": 2
  }'
```

---

## Models Available

- `StoreCustomer` - Customer account operations
- `CustomerAddress` - Address management
- `ShoppingCart` - Cart operations

## Services Available

- `CustomerJWTService` - Customer token generation/verification

## Controllers Available

- `CustomerController` - Registration, login, profile
- `CartController` - Shopping cart management
