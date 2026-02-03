# Admin Order Management System

Complete order management system for store owners and administrators.

---

## 📋 Overview

This system allows store owners to:
- View and manage all orders
- Update order statuses
- Update payment statuses
- Add tracking numbers
- View order analytics
- Bulk update orders
- Filter and search orders

---

## 🔐 Authentication

All admin order endpoints require Admin or Client JWT token.

```javascript
headers: {
  'Authorization': 'Bearer YOUR_ADMIN_OR_CLIENT_TOKEN'
}
```

**Access Control:**
- Admin users can access orders for all stores
- Client users can only access orders for their own stores

---

## 📡 API Endpoints

### 1. Get All Orders (Admin View)

Get paginated list of all orders for a store with filtering options.

**Endpoint:** `GET /api/stores/{store_id}/admin/orders`

**Query Parameters:**
- `status` (optional) - Filter by order status: `pending`, `processing`, `shipped`, `delivered`, `cancelled`
- `payment_status` (optional) - Filter by payment status: `pending`, `paid`, `failed`, `refunded`
- `from_date` (optional) - Filter from date (YYYY-MM-DD)
- `to_date` (optional) - Filter to date (YYYY-MM-DD)
- `search` (optional) - Search by customer email, name, or order ID
- `page` (optional, default: 1) - Page number
- `limit` (optional, default: 20) - Items per page

**Request:**
```bash
GET /api/stores/1/admin/orders?status=pending&page=1&limit=20
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

**Response:**
```json
{
  "success": true,
  "data": {
    "orders": [
      {
        "id": 1,
        "store_id": 1,
        "customer_id": 5,
        "customer_name": "John Doe",
        "customer_email": "john@example.com",
        "customer_phone": "+234123456789",
        "total_amount": "25000.00",
        "shipping_cost": "2000.00",
        "payment_method": "cash_on_delivery",
        "payment_status": "pending",
        "status": "pending",
        "order_notes": "Please call before delivery",
        "tracking_number": null,
        "created_at": "2026-02-02 10:30:00",
        "updated_at": "2026-02-02 10:30:00"
      }
    ],
    "pagination": {
      "total": 45,
      "page": 1,
      "limit": 20,
      "pages": 3
    },
    "stats": {
      "total_orders": 45,
      "total_revenue": "1250000.00",
      "average_order_value": "27777.78",
      "pending_orders": 12,
      "delivered_orders": 28
    }
  }
}
```

---

### 2. Get Order Details (Admin View)

Get complete order details including items and addresses.

**Endpoint:** `GET /api/stores/{store_id}/admin/orders/{order_id}`

**Request:**
```bash
GET /api/stores/1/admin/orders/15
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 15,
    "store_id": 1,
    "customer_id": 5,
    "customer_name": "John Doe",
    "customer_email": "john@example.com",
    "customer_phone": "+234123456789",
    "shipping_address_id": 8,
    "billing_address_id": 8,
    "total_amount": "25000.00",
    "shipping_cost": "2000.00",
    "payment_method": "cash_on_delivery",
    "payment_status": "pending",
    "status": "pending",
    "order_notes": "Please call before delivery",
    "tracking_number": null,
    "created_at": "2026-02-02 10:30:00",
    "updated_at": "2026-02-02 10:30:00",
    "items": [
      {
        "id": 20,
        "order_id": 15,
        "product_id": 10,
        "product_name": "Wireless Headphones",
        "product_image": "https://res.cloudinary.com/...",
        "product_description": "High-quality wireless...",
        "quantity": 2,
        "price": "10000.00"
      },
      {
        "id": 21,
        "order_id": 15,
        "product_id": 12,
        "product_name": "Phone Case",
        "product_image": "https://res.cloudinary.com/...",
        "product_description": "Protective phone case",
        "quantity": 1,
        "price": "5000.00"
      }
    ],
    "shipping_address": {
      "id": 8,
      "full_name": "John Doe",
      "phone": "+234123456789",
      "address_line1": "123 Main Street",
      "address_line2": "Apt 4B",
      "city": "Lagos",
      "state": "Lagos",
      "postal_code": "100001",
      "country": "Nigeria"
    },
    "billing_address": {
      "id": 8,
      "full_name": "John Doe",
      "phone": "+234123456789",
      "address_line1": "123 Main Street",
      "address_line2": "Apt 4B",
      "city": "Lagos",
      "state": "Lagos",
      "postal_code": "100001",
      "country": "Nigeria"
    }
  }
}
```

---

### 3. Update Order Status

Update the status of an order.

**Endpoint:** `PUT /api/stores/{store_id}/admin/orders/{order_id}/status`

**Valid Statuses:**
- `pending` - Order placed, awaiting processing
- `processing` - Order is being prepared
- `shipped` - Order has been shipped
- `delivered` - Order has been delivered
- `cancelled` - Order has been cancelled (stock will be restored)

**Request:**
```bash
PUT /api/stores/1/admin/orders/15/status
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
Content-Type: application/json

{
  "status": "processing"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "message": "Order status updated successfully",
    "order": {
      "id": 15,
      "status": "processing",
      "updated_at": "2026-02-02 11:00:00"
    }
  }
}
```

**Business Rules:**
- Cannot update status of `delivered` or `cancelled` orders
- Cancelling an order automatically restores product stock

---

### 4. Update Payment Status

Update the payment status of an order.

**Endpoint:** `PUT /api/stores/{store_id}/admin/orders/{order_id}/payment-status`

**Valid Payment Statuses:**
- `pending` - Payment not yet received
- `paid` - Payment confirmed
- `failed` - Payment failed
- `refunded` - Payment has been refunded

**Request:**
```bash
PUT /api/stores/1/admin/orders/15/payment-status
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
Content-Type: application/json

{
  "payment_status": "paid"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "message": "Payment status updated successfully",
    "order": {
      "id": 15,
      "payment_status": "paid",
      "updated_at": "2026-02-02 11:15:00"
    }
  }
}
```

---

### 5. Add Tracking Number

Add or update tracking number for an order.

**Endpoint:** `PUT /api/stores/{store_id}/admin/orders/{order_id}/tracking`

**Request:**
```bash
PUT /api/stores/1/admin/orders/15/tracking
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
Content-Type: application/json

{
  "tracking_number": "DHL123456789"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "message": "Tracking number added successfully",
    "order": {
      "id": 15,
      "tracking_number": "DHL123456789",
      "status": "shipped",
      "updated_at": "2026-02-02 11:30:00"
    }
  }
}
```

**Automatic Behavior:**
- If order status is `processing`, it will automatically be updated to `shipped`

---

### 6. Get Order Statistics

Get comprehensive order statistics for a store.

**Endpoint:** `GET /api/stores/{store_id}/admin/orders/stats`

**Query Parameters:**
- `from_date` (optional, default: 30 days ago) - Start date (YYYY-MM-DD)
- `to_date` (optional, default: today) - End date (YYYY-MM-DD)

**Request:**
```bash
GET /api/stores/1/admin/orders/stats?from_date=2026-01-01&to_date=2026-02-02
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

**Response:**
```json
{
  "success": true,
  "data": {
    "overview": {
      "total_orders": 150,
      "total_revenue": "4500000.00",
      "average_order_value": "30000.00",
      "pending_orders": 12,
      "delivered_orders": 120
    },
    "recent_orders": [
      {
        "id": 150,
        "customer_name": "Jane Smith",
        "total_amount": "45000.00",
        "status": "pending",
        "created_at": "2026-02-02 14:30:00"
      }
    ],
    "daily_stats": [
      {
        "date": "2026-02-02",
        "order_count": 5,
        "revenue": "125000.00",
        "delivered_count": 3,
        "cancelled_count": 0
      },
      {
        "date": "2026-02-01",
        "order_count": 8,
        "revenue": "240000.00",
        "delivered_count": 6,
        "cancelled_count": 1
      }
    ]
  }
}
```

---

### 7. Bulk Update Orders

Update status for multiple orders at once.

**Endpoint:** `POST /api/stores/{store_id}/admin/orders/bulk-update`

**Request:**
```bash
POST /api/stores/1/admin/orders/bulk-update
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
Content-Type: application/json

{
  "order_ids": [15, 16, 17, 18],
  "status": "processing"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "message": "Updated 4 orders",
    "updated": 4,
    "failed": 0
  }
}
```

**Restrictions:**
- Cannot bulk update `delivered` or `cancelled` orders
- Orders must belong to the specified store
- If updating to `cancelled`, stock will be restored for each order

---

## 📊 Order Status Flow

```
pending → processing → shipped → delivered
   ↓
cancelled (restores stock)
```

**Status Descriptions:**
- **pending** - New order, not yet processed
- **processing** - Order is being prepared for shipment
- **shipped** - Order has been dispatched
- **delivered** - Order successfully delivered to customer
- **cancelled** - Order cancelled (can only cancel before shipped)

---

## 💳 Payment Status Flow

```
pending → paid
   ↓
failed
   ↓
refunded
```

**Payment Status Descriptions:**
- **pending** - Payment not yet confirmed
- **paid** - Payment successfully received
- **failed** - Payment attempt failed
- **refunded** - Payment has been returned to customer

---

## 🔍 Search & Filtering

### Search Capabilities

Search orders by:
- Customer email (partial match)
- Customer name (partial match)
- Order ID (exact match)

Example:
```bash
GET /api/stores/1/admin/orders?search=john@example.com
GET /api/stores/1/admin/orders?search=John%20Doe
GET /api/stores/1/admin/orders?search=15
```

### Combined Filters

Combine multiple filters for precise results:

```bash
GET /api/stores/1/admin/orders?status=pending&payment_status=paid&from_date=2026-01-01&to_date=2026-02-02&search=john
```

---

## 📈 Analytics Features

### Available Statistics

1. **Overview Stats:**
   - Total orders
   - Total revenue
   - Average order value
   - Pending orders count
   - Delivered orders count

2. **Daily Stats:**
   - Order count per day
   - Revenue per day
   - Delivered count per day
   - Cancelled count per day

3. **Recent Orders:**
   - Last 10 orders placed
   - Quick overview of customer and amount

---

## 🛡️ Security & Access Control

### Authorization

```javascript
// Check if user has access to store
if (user.role === 'admin') {
  // Admin can access all stores
  return true;
}

if (user.role === 'client') {
  // Client can only access their own stores
  return store.client_id === user.id;
}
```

### Ownership Verification

All endpoints verify:
1. User is authenticated (valid JWT token)
2. User has access to the specified store
3. Order belongs to the specified store

---

## 📝 Usage Examples

### Example 1: View Pending Orders

```javascript
const response = await fetch('/api/stores/1/admin/orders?status=pending', {
  headers: {
    'Authorization': `Bearer ${adminToken}`
  }
});

const result = await response.json();
console.log('Pending orders:', result.data.orders);
```

### Example 2: Process an Order

```javascript
// 1. View order details
const order = await fetch('/api/stores/1/admin/orders/15', {
  headers: { 'Authorization': `Bearer ${adminToken}` }
}).then(r => r.json());

// 2. Update to processing
await fetch('/api/stores/1/admin/orders/15/status', {
  method: 'PUT',
  headers: {
    'Authorization': `Bearer ${adminToken}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({ status: 'processing' })
});

// 3. Add tracking number (auto-updates to shipped)
await fetch('/api/stores/1/admin/orders/15/tracking', {
  method: 'PUT',
  headers: {
    'Authorization': `Bearer ${adminToken}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({ tracking_number: 'DHL123456789' })
});

// 4. Mark as delivered
await fetch('/api/stores/1/admin/orders/15/status', {
  method: 'PUT',
  headers: {
    'Authorization': `Bearer ${adminToken}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({ status: 'delivered' })
});
```

### Example 3: Bulk Process Orders

```javascript
// Get all pending paid orders
const pendingPaid = await fetch(
  '/api/stores/1/admin/orders?status=pending&payment_status=paid',
  {
    headers: { 'Authorization': `Bearer ${adminToken}` }
  }
).then(r => r.json());

// Extract order IDs
const orderIds = pendingPaid.data.orders.map(o => o.id);

// Bulk update to processing
await fetch('/api/stores/1/admin/orders/bulk-update', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${adminToken}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    order_ids: orderIds,
    status: 'processing'
  })
});
```

### Example 4: View Statistics

```javascript
const stats = await fetch(
  '/api/stores/1/admin/orders/stats?from_date=2026-01-01&to_date=2026-02-02',
  {
    headers: { 'Authorization': `Bearer ${adminToken}` }
  }
).then(r => r.json());

console.log('Total revenue:', stats.data.overview.total_revenue);
console.log('Average order:', stats.data.overview.average_order_value);
console.log('Daily breakdown:', stats.data.daily_stats);
```

---

## 🚨 Error Handling

### Common Errors

**401 Unauthorized**
```json
{
  "success": false,
  "message": "Unauthorized"
}
```
*Solution:* Provide valid admin/client JWT token

**403 Access Denied**
```json
{
  "success": false,
  "message": "Access denied to this store"
}
```
*Solution:* User doesn't own this store (clients only)

**404 Not Found**
```json
{
  "success": false,
  "message": "Order not found"
}
```
*Solution:* Order ID doesn't exist or doesn't belong to store

**400 Bad Request**
```json
{
  "success": false,
  "message": "Invalid status. Must be one of: pending, processing, shipped, delivered, cancelled"
}
```
*Solution:* Provide valid status value

**400 Cannot Update**
```json
{
  "success": false,
  "message": "Cannot update status of delivered orders"
}
```
*Solution:* Order is in final state (delivered/cancelled)

---

## 💡 Best Practices

### 1. Order Processing Workflow

```
1. New order arrives (pending)
2. Admin reviews order details
3. Update status to "processing"
4. Prepare order for shipment
5. Add tracking number (auto-updates to "shipped")
6. When delivered, mark as "delivered"
```

### 2. Payment Confirmation

```
1. Order placed with payment_status: "pending"
2. Verify payment received
3. Update payment_status to "paid"
4. Proceed with order processing
```

### 3. Handling Cancellations

```
1. Check if order can be cancelled (not shipped/delivered)
2. Update status to "cancelled"
3. Stock automatically restored
4. If payment was made, initiate refund
5. Update payment_status to "refunded"
```

### 4. Monitoring Performance

```javascript
// Check stats daily
const today = new Date().toISOString().split('T')[0];
const stats = await getOrderStats(storeId, today, today);

// Monitor pending orders
const pending = await getOrders(storeId, { status: 'pending' });

// Alert if too many pending
if (pending.data.orders.length > 20) {
  alert('High number of pending orders!');
}
```

---

## 🎯 Integration with Frontend

### Admin Dashboard Example

```html
<!-- Order Management Dashboard -->
<div class="order-management">
  <div class="stats-cards">
    <div class="stat-card">
      <h3 id="total-orders">0</h3>
      <p>Total Orders</p>
    </div>
    <div class="stat-card">
      <h3 id="total-revenue">₦0</h3>
      <p>Total Revenue</p>
    </div>
    <div class="stat-card">
      <h3 id="pending-orders">0</h3>
      <p>Pending Orders</p>
    </div>
  </div>

  <div class="filters">
    <select id="status-filter">
      <option value="">All Statuses</option>
      <option value="pending">Pending</option>
      <option value="processing">Processing</option>
      <option value="shipped">Shipped</option>
      <option value="delivered">Delivered</option>
    </select>

    <input type="search" id="search" placeholder="Search by email, name, or ID">
    <button onclick="loadOrders()">Filter</button>
  </div>

  <table id="orders-table">
    <thead>
      <tr>
        <th>Order ID</th>
        <th>Customer</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Payment</th>
        <th>Date</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
</div>

<script>
const storeId = 1;
const token = localStorage.getItem('admin_token');

async function loadOrders() {
  const status = document.getElementById('status-filter').value;
  const search = document.getElementById('search').value;
  
  let url = `/api/stores/${storeId}/admin/orders?`;
  if (status) url += `status=${status}&`;
  if (search) url += `search=${encodeURIComponent(search)}&`;
  
  const response = await fetch(url, {
    headers: { 'Authorization': `Bearer ${token}` }
  });
  
  const result = await response.json();
  
  if (result.success) {
    displayOrders(result.data.orders);
    displayStats(result.data.stats);
  }
}

function displayOrders(orders) {
  const tbody = document.querySelector('#orders-table tbody');
  tbody.innerHTML = orders.map(order => `
    <tr>
      <td>#${order.id}</td>
      <td>${order.customer_name}<br><small>${order.customer_email}</small></td>
      <td>₦${parseFloat(order.total_amount).toLocaleString()}</td>
      <td><span class="badge status-${order.status}">${order.status}</span></td>
      <td><span class="badge payment-${order.payment_status}">${order.payment_status}</span></td>
      <td>${new Date(order.created_at).toLocaleDateString()}</td>
      <td>
        <button onclick="viewOrder(${order.id})">View</button>
        <button onclick="updateOrderStatus(${order.id})">Update</button>
      </td>
    </tr>
  `).join('');
}

function displayStats(stats) {
  document.getElementById('total-orders').textContent = stats.total_orders;
  document.getElementById('total-revenue').textContent = 
    '₦' + parseFloat(stats.total_revenue).toLocaleString();
  document.getElementById('pending-orders').textContent = stats.pending_orders;
}

async function updateOrderStatus(orderId) {
  const newStatus = prompt('Enter new status (pending/processing/shipped/delivered/cancelled):');
  
  if (!newStatus) return;
  
  const response = await fetch(
    `/api/stores/${storeId}/admin/orders/${orderId}/status`,
    {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ status: newStatus })
    }
  );
  
  const result = await response.json();
  
  if (result.success) {
    alert('Order status updated!');
    loadOrders();
  } else {
    alert('Error: ' + result.message);
  }
}

// Load orders on page load
loadOrders();
</script>
```

---

## ✅ Testing Checklist

- [ ] View all orders for a store
- [ ] Filter orders by status
- [ ] Filter orders by payment status
- [ ] Search orders by email
- [ ] Search orders by name
- [ ] Search orders by order ID
- [ ] View single order with full details
- [ ] Update order status (pending → processing)
- [ ] Update order status (processing → shipped)
- [ ] Add tracking number (auto-ships order)
- [ ] Mark order as delivered
- [ ] Cancel order (verify stock restored)
- [ ] Update payment status to paid
- [ ] Bulk update multiple orders
- [ ] View order statistics
- [ ] Client can only access their stores
- [ ] Admin can access all stores
- [ ] Cannot update delivered orders
- [ ] Cannot update cancelled orders
- [ ] Pagination works correctly

---

## 🎊 Summary

Admin Order Management provides complete control over:
- ✅ Order viewing and filtering
- ✅ Status management
- ✅ Payment tracking
- ✅ Shipping coordination
- ✅ Performance analytics
- ✅ Bulk operations
- ✅ Stock restoration on cancellation

**The system is production-ready for managing customer orders!**
