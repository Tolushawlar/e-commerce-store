# Frontend Setup & Usage Guide

## Quick Start

### Starting the Application

```bash
# Terminal 1 - Backend API Server (Port 8000)
cd backend/public
php -S localhost:8000 router.php

# Terminal 2 - Frontend Server (Port 3000)
cd frontend
php -S localhost:3000
```

### Access Points

- **Frontend**: http://localhost:3000
- **Login Page**: http://localhost:3000/auth/login.php
- **Backend API**: http://localhost:8000/api/...
- **API Documentation**: http://localhost:8000/docs.html

## Default Credentials

### Super Admin

- **Email**: admin@livepetal.com
- **Password**: admin123
- **Dashboard**: http://localhost:3000/admin/dashboard.php

### Test Client

- **Email**: client@example.com
- **Password**: password123
- **Dashboard**: http://localhost:3000/client/dashboard.php

## Frontend Structure

```
frontend/
├── index.php                    # Root redirect to login
├── auth/                        # Authentication
│   ├── login.php               # Login page
│   └── register.php            # Registration page
├── admin/                       # Admin Dashboard
│   ├── dashboard.php           # Overview with stats
│   ├── clients.php             # Client management (CRUD)
│   ├── stores.php              # Store management
│   ├── products.php            # Product management
│   ├── orders.php              # Order management
│   └── templates.php           # Template management
├── client/                      # Client Dashboard
│   ├── dashboard.php           # Client overview
│   ├── stores.php              # My stores
│   ├── products.php            # My products
│   └── orders.php              # My orders
├── shared/                      # Shared Layouts
│   ├── header-admin.php        # Admin layout header with sidebar
│   ├── footer-admin.php        # Admin layout footer
│   ├── header-client.php       # Client layout header with sidebar
│   └── footer-client.php       # Client layout footer
└── assets/js/
    ├── core/                    # Core functionality
    │   ├── api.js              # HTTP client with JWT support
    │   └── auth.js             # Authentication & session management
    ├── services/                # API service classes
    │   ├── client.service.js   # Client CRUD operations
    │   ├── store.service.js    # Store CRUD operations
    │   ├── product.service.js  # Product CRUD operations
    │   └── order.service.js    # Order CRUD operations
    └── utils/                   # Helper utilities
        ├── helpers.js          # Date, currency, formatting helpers
        └── components.js       # Reusable UI components
```

## API Integration

### Available Services

All services are automatically available on pages that include the service file:

```html
<script src="/assets/js/services/client.service.js"></script>
<script src="/assets/js/services/store.service.js"></script>
<script src="/assets/js/services/product.service.js"></script>
<script src="/assets/js/services/order.service.js"></script>
```

### Usage Examples

#### Authentication

```javascript
// Login as admin
await auth.adminLogin("admin@livepetal.com", "admin123");

// Login as client
await auth.clientLogin("client@example.com", "password123");

// Register new client
await auth.register({
  name: "John Doe",
  email: "john@example.com",
  password: "password123",
  subscription_plan: "standard",
});

// Logout
await auth.logout();

// Check if authenticated
if (auth.isAuthenticated()) {
  // User is logged in
}

// Check user role
if (auth.isAdmin()) {
  // User is admin
}
```

#### Client Management (Admin Only)

```javascript
// Get all clients with pagination
const response = await clientService.getAll({
  page: 1,
  limit: 20,
  status: "active",
});

// Get single client
const client = await clientService.getById(1);

// Create client
await clientService.create({
  name: "Acme Corp",
  email: "contact@acme.com",
  password: "secure123",
  subscription_plan: "premium",
  company_name: "Acme Corporation",
  phone: "+1234567890",
});

// Update client
await clientService.update(1, {
  status: "inactive",
  subscription_plan: "basic",
});

// Delete client
await clientService.delete(1);
```

#### Store Management

```javascript
// Get all stores
const stores = await storeService.getAll({
  client_id: 5,
  status: "active",
});

// Get single store
const store = await storeService.getById(1);

// Create store
await storeService.create({
  client_id: 1,
  name: "My Online Store",
  domain: "mystore.com",
  template: "default",
  status: "active",
});

// Update store
await storeService.update(1, {
  name: "Updated Store Name",
  status: "maintenance",
});

// Delete store
await storeService.delete(1);

// Generate store files
await storeService.generate(1);
```

#### Product Management

```javascript
// Get all products
const products = await productService.getAll({
  store_id: 1,
  category: "Electronics",
  status: "active",
});

// Get low stock products
const lowStock = await productService.getLowStock({
  threshold: 10,
  store_id: 1,
});

// Create product
await productService.create({
  store_id: 1,
  name: "Premium Widget",
  price: 29.99,
  stock_quantity: 100,
  sku: "WIDGET-001",
  category: "Electronics",
  status: "active",
});

// Update product
await productService.update(1, {
  price: 24.99,
  stock_quantity: 150,
});

// Delete product
await productService.delete(1);
```

#### Order Management

```javascript
// Get all orders
const orders = await orderService.getAll({
  store_id: 1,
  status: "pending",
  from_date: "2026-01-01",
  to_date: "2026-01-31",
});

// Get single order
const order = await orderService.getById(1);

// Create order
await orderService.create({
  store_id: 1,
  customer_name: "Jane Smith",
  customer_email: "jane@example.com",
  total_amount: 149.99,
  payment_method: "credit_card",
  shipping_address: "123 Main St",
  status: "pending",
});

// Update order status
await orderService.updateStatus(1, "processing");

// Get order statistics
const stats = await orderService.getStats({
  store_id: 1,
  from_date: "2026-01-01",
  to_date: "2026-01-31",
});
```

## Utility Functions

### Helpers (`/assets/js/utils/helpers.js`)

```javascript
// Date formatting
utils.formatDate("2026-01-26T10:30:00"); // "Jan 26, 2026"
utils.formatDateTime("2026-01-26T10:30:00"); // "Jan 26, 2026, 10:30 AM"

// Currency formatting
utils.formatCurrency(149.99); // "$149.99"

// Number formatting
utils.formatNumber(1234567); // "1,234,567"

// Text truncation
utils.truncate("Long text here", 20); // "Long text here..."

// Status badge class
const badgeClass = utils.getStatusClass("active"); // "bg-green-100 text-green-800"

// Toast notifications
utils.toast("Operation successful!", "success");
utils.toast("Something went wrong", "error");
utils.toast("Please wait...", "warning");
utils.toast("Information message", "info");

// Confirm dialog
utils.confirm(
  "Are you sure?",
  () => console.log("Confirmed"),
  () => console.log("Cancelled"),
);

// Copy to clipboard
await utils.copyToClipboard("Text to copy");

// Email validation
utils.isValidEmail("test@example.com"); // true

// Query parameters
const id = utils.getQueryParam("id");
utils.setQueryParam("page", 2);

// Debounce
const debouncedSearch = utils.debounce(searchFunction, 500);
```

### Components (`/assets/js/utils/components.js`)

```javascript
// Loading spinner
document.getElementById("container").innerHTML = components.spinner;

// Empty state
document.getElementById("container").innerHTML = components.emptyState(
  "No items found",
  "inbox",
);

// Error state
document.getElementById("container").innerHTML = components.errorState(
  "Failed to load data",
);

// Status badge
const badge = components.statusBadge("active");

// Pagination
const pagination = components.pagination(currentPage, totalPages, "loadData");

// Modals
components.openModal("myModal");
components.closeModal("myModal");
```

## Creating New Pages

### Admin Page Template

```php
<?php
$pageTitle = 'Page Title';
$pageDescription = 'Page description text';
include '../shared/header-admin.php';
?>

<!-- Your page content here -->
<div class="bg-white rounded-xl border p-6">
    <h2 class="text-xl font-bold mb-4">Section Title</h2>
    <div id="content"></div>
</div>

<!-- Include required service files -->
<script src="/assets/js/services/client.service.js"></script>

<script>
    // Page-specific JavaScript
    async function loadData() {
        try {
            const response = await clientService.getAll();
            // Process and display data
        } catch (error) {
            utils.toast(error.message, 'error');
        }
    }

    // Initialize on page load
    loadData();
</script>

<?php include '../shared/footer-admin.php'; ?>
```

### Client Page Template

```php
<?php
$pageTitle = 'Page Title';
$pageDescription = 'Page description text';
include '../shared/header-client.php';
?>

<!-- Your page content here -->

<script src="/assets/js/services/store.service.js"></script>
<script>
    // Require client authentication
    auth.requireClient();

    // Your page logic
</script>

<?php include '../shared/footer-client.php'; ?>
```

## Security

### Authentication Checks

```javascript
// Require any authentication
auth.requireAuth();

// Require admin role
auth.requireAdmin();

// Require client role
auth.requireClient();

// Manual checks
if (!auth.isAuthenticated()) {
  window.location.href = "/auth/login.php";
}

if (!auth.isAdmin()) {
  window.location.href = "/client/dashboard.php";
}
```

### JWT Token Management

- Tokens are automatically included in all API requests
- Stored securely in localStorage
- Auto-cleared on logout
- Can be refreshed using `auth.refreshToken()`

## Best Practices

1. **Always use service classes** instead of direct API calls
2. **Handle errors gracefully** with try-catch blocks
3. **Show user feedback** using toast notifications
4. **Validate forms** before submission
5. **Use shared components** for consistency
6. **Check authentication** on protected pages
7. **Format data** using utility functions
8. **Debounce search inputs** to reduce API calls
9. **Use pagination** for large datasets
10. **Display loading states** during API calls

## Troubleshooting

### Common Issues

**1. "Route not found" error**

- Ensure backend server is running on port 8000
- Check that router.php is being used: `php -S localhost:8000 router.php`

**2. API calls failing**

- Verify API base URL in `/assets/js/core/api.js` is `http://localhost:8000`
- Check browser console for CORS errors
- Ensure backend server is running

**3. Authentication not working**

- Clear localStorage: `localStorage.clear()`
- Check token in browser DevTools → Application → Local Storage
- Verify credentials are correct

**4. Pages not loading**

- Ensure frontend server is running on port 3000
- Check file paths are correct (case-sensitive)
- Verify PHP server is serving from `frontend/` directory

**5. UI not updating**

- Check browser console for JavaScript errors
- Ensure all required service files are included
- Verify function names match between HTML and JS

## Next Steps

1. Complete CRUD pages for Stores, Products, and Orders
2. Add file upload for product images and store logos
3. Implement real-time order notifications
4. Add data export functionality (CSV/PDF)
5. Create analytics dashboards with charts
6. Implement advanced search and filtering
7. Add bulk operations for products and orders

## Support

For detailed API documentation, visit: http://localhost:8000/docs.html

For backend architecture details, see: `/backend/README.md`

For migration from old structure, see: `MIGRATION-GUIDE.md`
