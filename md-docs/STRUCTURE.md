# Frontend Structure Documentation

## Overview

The frontend has been restructured to follow a scalable, modular architecture with clear separation of concerns.

## Directory Structure

```
frontend/
├── index.php                 # Root redirect to login
├── auth/                     # Authentication pages
│   ├── login.php            # Login page (Admin/Client)
│   └── register.php         # Client registration
├── admin/                    # Admin dashboard pages
│   ├── dashboard.php        # Admin dashboard
│   ├── clients.php          # Client management
│   ├── stores.php           # Store management
│   ├── products.php         # Product management
│   ├── orders.php           # Order management
│   └── templates.php        # Template management
├── client/                   # Client dashboard pages
│   ├── dashboard.php        # Client dashboard
│   ├── stores.php           # My stores
│   ├── products.php         # My products
│   └── orders.php           # My orders
├── shared/                   # Shared layouts & components
│   ├── header-admin.php     # Admin layout header
│   ├── footer-admin.php     # Admin layout footer
│   ├── header-client.php    # Client layout header
│   └── footer-client.php    # Client layout footer
└── assets/                   # Static assets
    └── js/
        ├── core/            # Core functionality
        │   ├── api.js       # API client
        │   └── auth.js      # Authentication service
        ├── services/        # API service modules
        │   ├── client.service.js
        │   ├── store.service.js
        │   ├── product.service.js
        │   └── order.service.js
        └── utils/           # Utility functions
            ├── helpers.js   # Helper functions
            └── components.js # UI components
```

## Key Features

### 1. **Modular Architecture**

- Clear separation between Admin and Client interfaces
- Reusable shared components
- Service-based API communication

### 2. **API Integration**

All services use documented API endpoints from OpenAPI specification:

**Authentication** (`/assets/js/core/auth.js`)

- POST `/api/auth/admin/login` - Admin login
- POST `/api/auth/client/login` - Client login
- POST `/api/auth/register` - Client registration
- POST `/api/auth/logout` - Logout
- GET `/api/auth/me` - Get current user
- POST `/api/auth/refresh` - Refresh token
- POST `/api/auth/password/reset-request` - Request password reset
- POST `/api/auth/password/reset` - Reset password
- PUT `/api/auth/password/change` - Change password

**Clients** (`/assets/js/services/client.service.js`)

- GET `/api/clients` - Get all clients (paginated)
- GET `/api/clients/{id}` - Get single client
- POST `/api/clients` - Create client
- PUT `/api/clients/{id}` - Update client
- DELETE `/api/clients/{id}` - Delete client

**Stores** (`/assets/js/services/store.service.js`)

- GET `/api/stores` - Get all stores (paginated)
- GET `/api/stores/{id}` - Get single store
- POST `/api/stores` - Create store
- PUT `/api/stores/{id}` - Update store
- DELETE `/api/stores/{id}` - Delete store
- POST `/api/stores/{id}/generate` - Generate store files

**Products** (`/assets/js/services/product.service.js`)

- GET `/api/products` - Get all products (paginated)
- GET `/api/products/{id}` - Get single product
- POST `/api/products` - Create product
- PUT `/api/products/{id}` - Update product
- DELETE `/api/products/{id}` - Delete product
- GET `/api/products/low-stock` - Get low stock products

**Orders** (`/assets/js/services/order.service.js`)

- GET `/api/orders` - Get all orders (paginated)
- GET `/api/orders/{id}` - Get single order
- POST `/api/orders` - Create order
- PUT `/api/orders/{id}/status` - Update order status
- GET `/api/orders/stats` - Get order statistics

### 3. **Authentication & Authorization**

- JWT token-based authentication
- Role-based access control (Admin/Client)
- Automatic redirection for unauthorized access
- Persistent sessions via localStorage

### 4. **Reusable Components** (`/assets/js/utils/components.js`)

- Loading spinners
- Empty states
- Error states
- Status badges
- Pagination
- Modals
- Tables

### 5. **Helper Utilities** (`/assets/js/utils/helpers.js`)

- Date formatting
- Currency formatting
- Number formatting
- Text truncation
- Status styling
- Debounce function
- Toast notifications
- Confirm dialogs
- Clipboard operations
- Email validation
- Query parameter management

## Usage Examples

### Making API Calls

```javascript
// Using service classes
const clients = await clientService.getAll({ page: 1, limit: 20 });
const store = await storeService.getById(1);
const products = await productService.getAll({ store_id: 5, status: "active" });
const orders = await orderService.getStats({ store_id: 5 });

// Direct API calls
const response = await api.get("/api/products", { category: "Electronics" });
const newProduct = await api.post("/api/products", productData);
```

### Using Helper Functions

```javascript
// Format date
utils.formatDate("2026-01-26T10:30:00"); // "Jan 26, 2026"

// Format currency
utils.formatCurrency(149.99); // "$149.99"

// Show toast notification
utils.toast("Product created successfully!", "success");

// Get status badge class
const badgeClass = utils.getStatusClass("active"); // "bg-green-100 text-green-800"
```

### Creating Pages

Admin page example:

```php
<?php
$pageTitle = 'Page Title';
$pageDescription = 'Page description';
include '../shared/header-admin.php';
?>

<!-- Your content here -->

<script src="/assets/js/services/client.service.js"></script>
<script>
    // Your page logic
</script>

<?php include '../shared/footer-admin.php'; ?>
```

## Server Configuration

### Development Setup

Run two separate PHP servers:

```bash
# Backend API (Port 8000)
cd backend/public
php -S localhost:8000 router.php

# Frontend (Port 3000)
cd frontend
php -S localhost:3000
```

### Access Points

- Frontend: http://localhost:3000
- Backend API: http://localhost:8000/api/...
- API Documentation: http://localhost:8000/docs.html

## Best Practices

1. **Always use service classes** for API calls instead of direct `api.request()`
2. **Use utility functions** for common operations (formatting, validation, etc.)
3. **Leverage shared layouts** to maintain consistency
4. **Handle errors gracefully** with try-catch blocks and user-friendly messages
5. **Check authentication** on protected pages using `auth.requireAuth()`, `auth.requireAdmin()`, or `auth.requireClient()`
6. **Use components** for consistent UI elements

## Security

- All API calls automatically include JWT token in Authorization header
- Pages check authentication and role before rendering
- Tokens stored securely in localStorage
- Automatic logout on token expiration
- CORS configured for localhost development

## Scalability

The structure supports easy addition of new features:

1. **New Pages**: Add to `/admin/` or `/client/` folders
2. **New Services**: Create in `/assets/js/services/`
3. **New Components**: Add to `/assets/js/utils/components.js`
4. **New Utilities**: Add to `/assets/js/utils/helpers.js`

## Migration from Old Structure

Old files to remove:

- `/frontend/login.php` (moved to `/frontend/auth/login.php`)
- `/frontend/assets/js/api.js` (moved to `/frontend/assets/js/core/api.js`)
- `/frontend/assets/js/auth.js` (moved to `/frontend/assets/js/core/auth.js`)
- Root-level `/super-admin/` folder (migrate to `/frontend/admin/`)
- Root-level `/client-dashboard/` folder (migrate to `/frontend/client/`)
