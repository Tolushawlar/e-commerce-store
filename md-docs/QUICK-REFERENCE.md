# Frontend Quick Reference Card

## Server Commands

```bash
# Start Backend API
cd backend/public && php -S localhost:8000 router.php

# Start Frontend
cd frontend && php -S localhost:3000
```

## URLs

- Frontend: `http://localhost:3000`
- Login: `http://localhost:3000/auth/login.php`
- Admin: `http://localhost:3000/admin/dashboard.php`
- Client: `http://localhost:3000/client/dashboard.php`
- API Docs: `http://localhost:8000/docs.html`

## Default Credentials

| Role   | Email               | Password    |
| ------ | ------------------- | ----------- |
| Admin  | admin@livepetal.com | admin123    |
| Client | client@example.com  | password123 |

## File Structure

```
frontend/
├── auth/           # Login, Register
├── admin/          # Admin pages
├── client/         # Client pages
├── shared/         # Layouts
└── assets/js/
    ├── core/       # api.js, auth.js
    ├── services/   # *.service.js
    └── utils/      # helpers.js, components.js
```

## Service Usage

```javascript
// Authentication
await auth.adminLogin(email, password);
await auth.clientLogin(email, password);
await auth.register(data);
await auth.logout();
auth.isAuthenticated();
auth.isAdmin();
auth.requireAuth();

// Clients
await clientService.getAll({ page, limit, status });
await clientService.getById(id);
await clientService.create(data);
await clientService.update(id, data);
await clientService.delete(id);

// Stores
await storeService.getAll({ client_id, status });
await storeService.getById(id);
await storeService.create(data);
await storeService.update(id, data);
await storeService.delete(id);
await storeService.generate(id);

// Products
await productService.getAll({ store_id, category });
await productService.getById(id);
await productService.create(data);
await productService.update(id, data);
await productService.delete(id);
await productService.getLowStock({ threshold });

// Orders
await orderService.getAll({ store_id, status });
await orderService.getById(id);
await orderService.create(data);
await orderService.updateStatus(id, status);
await orderService.getStats({ store_id });
```

## Utilities

```javascript
// Formatting
utils.formatDate(dateString);
utils.formatDateTime(dateString);
utils.formatCurrency(amount);
utils.formatNumber(number);
utils.truncate(text, length);

// UI
utils.toast(message, type); // success, error, warning, info
utils.confirm(message, onConfirm, onCancel);
utils.getStatusClass(status);
utils.debounce(func, wait);

// Components
components.spinner;
components.emptyState(message, icon);
components.errorState(message);
components.statusBadge(status);
components.pagination(page, total, callback);
components.openModal(id);
components.closeModal(id);
```

## Page Template

### Admin Page

```php
<?php
$pageTitle = 'Title';
$pageDescription = 'Description';
include '../shared/header-admin.php';
?>

<div id="content"></div>

<script src="/assets/js/services/client.service.js"></script>
<script>
    auth.requireAdmin();
    // Your code
</script>

<?php include '../shared/footer-admin.php'; ?>
```

### Client Page

```php
<?php
$pageTitle = 'Title';
include '../shared/header-client.php';
?>

<div id="content"></div>

<script src="/assets/js/services/store.service.js"></script>
<script>
    auth.requireClient();
    // Your code
</script>

<?php include '../shared/footer-client.php'; ?>
```

## Common Patterns

### Load Data with Pagination

```javascript
let currentPage = 1;

async function loadData(page = 1) {
  currentPage = page;
  try {
    const response = await clientService.getAll({
      page,
      limit: 20,
    });
    displayData(response.data.data);
    displayPagination(response.data.pagination);
  } catch (error) {
    utils.toast(error.message, "error");
  }
}
```

### Search with Debounce

```javascript
let searchTimeout;

function handleSearch() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    loadData(1);
  }, 500);
}
```

### CRUD Modal

```javascript
// Create
function showCreateModal() {
  document.getElementById("form").reset();
  document.getElementById("id").value = "";
  components.openModal("modal");
}

// Edit
async function edit(id) {
  const item = await service.getById(id);
  // Populate form
  components.openModal("modal");
}

// Delete
function deleteItem(id, name) {
  utils.confirm(`Delete "${name}"?`, async () => {
    await service.delete(id);
    utils.toast("Deleted!", "success");
    loadData();
  });
}

// Submit
async function handleSubmit(e) {
  e.preventDefault();
  const id = document.getElementById("id").value;
  const data = {
    /* form data */
  };

  try {
    if (id) {
      await service.update(id, data);
    } else {
      await service.create(data);
    }
    utils.toast("Saved!", "success");
    components.closeModal("modal");
    loadData();
  } catch (error) {
    utils.toast(error.message, "error");
  }
}
```

## Tailwind Classes

### Buttons

```html
<button class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90">
  <button
    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200"
  >
    <button
      class="px-4 py-2 border border-gray-200 rounded-lg hover:bg-gray-50"
    ></button>
  </button>
</button>
```

### Inputs

```html
<input
  class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary"
/>
<select class="w-full px-4 py-3 border border-gray-200 rounded-xl"></select>
```

### Cards

```html
<div class="bg-white rounded-xl border border-gray-200 p-6"></div>
```

### Grid

```html
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6"></div>
```

## Status Classes

| Status     | Class                         |
| ---------- | ----------------------------- |
| active     | bg-green-100 text-green-800   |
| inactive   | bg-gray-100 text-gray-800     |
| suspended  | bg-red-100 text-red-800       |
| pending    | bg-yellow-100 text-yellow-800 |
| processing | bg-blue-100 text-blue-800     |
| completed  | bg-green-100 text-green-800   |
| cancelled  | bg-red-100 text-red-800       |

## Troubleshooting

| Issue             | Solution                                        |
| ----------------- | ----------------------------------------------- |
| Route not found   | Check backend server is running with router.php |
| API calls fail    | Verify API base URL is http://localhost:8000    |
| Auth not working  | Clear localStorage and re-login                 |
| Pages not loading | Check frontend server is running on port 3000   |

## Documentation

- **Architecture**: `/frontend/STRUCTURE.md`
- **Setup Guide**: `/frontend/SETUP-GUIDE.md`
- **API Docs**: `http://localhost:8000/docs.html`
- **Summary**: `/FRONTEND-RESTRUCTURE-SUMMARY.md`
