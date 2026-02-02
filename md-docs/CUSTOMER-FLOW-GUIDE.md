# Customer-Facing Features Integration Guide

## 🎯 Overview

This document explains how customers access stores, shop for products, and complete purchases - and how store owners manage their orders.

---

## 📊 Complete User Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                       PUBLIC HOMEPAGE                           │
│                    (app/index.php)                              │
│                                                                 │
│  - Lists all active stores                                     │
│  - Search stores                                               │
│  - Browse featured stores                                      │
└─────────────────────┬───────────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────────┐
│                    STORE FRONTEND                               │
│                 (app/store/view.php?id=X)                       │
│                                                                 │
│  - Dynamic header (store name, colors, description)            │
│  - Products grid with filters                                  │
│  - Category filter                                             │
│  - "Add to Cart" buttons                                       │
│  - Cart badge (shows item count)                               │
└─────────────────────┬───────────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────────┐
│                   SHOPPING CART                                 │
│              (app/store/cart.php?store_id=X)                    │
│                                                                 │
│  - View all cart items                                         │
│  - Update quantities (+/- buttons)                             │
│  - Remove items                                                │
│  - See totals (subtotal, shipping, total)                      │
│  - Free shipping indicator                                     │
│  - Stock validation                                            │
│  - "Proceed to Checkout" button                                │
└─────────────────────┬───────────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────────┐
│                  CHECKOUT PROCESS                               │
│            (app/store/checkout.php?store_id=X)                  │
│                                                                 │
│  Step 1: Contact Information                                   │
│    - Full name, email, phone                                   │
│                                                                 │
│  Step 2: Shipping Address                                      │
│    - Saved addresses (if authenticated)                        │
│    - New address form                                          │
│                                                                 │
│  Step 3: Payment & Review                                      │
│    - Payment method selection                                  │
│    - Order notes                                               │
│    - Place Order button                                        │
└─────────────────────┬───────────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────────┐
│                 ORDER CONFIRMATION                              │
│        (app/store/order-success.php?order_id=X)                 │
│                                                                 │
│  - Animated success checkmark                                  │
│  - Order number                                                │
│  - Order summary                                               │
│  - Email confirmation notice                                   │
│  - "Track Order" button                                        │
│  - "Continue Shopping" button                                  │
└─────────────────────┬───────────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────────┐
│                  ORDER TRACKING                                 │
│   (app/store/order-tracking.php?order_id=X&store_id=X)         │
│                                                                 │
│  - Search by Order ID or Tracking Number                       │
│  - Visual timeline (pending → processing → shipped → delivered)│
│  - Order details                                               │
│  - Shipping address                                            │
│  - Customer information                                        │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🏪 Store Owner Flow (Client)

```
┌─────────────────────────────────────────────────────────────────┐
│                    CLIENT DASHBOARD                             │
│                 (app/client/dashboard.php)                      │
│                                                                 │
│  - Login as store owner                                        │
│  - View store statistics                                       │
│  - Quick actions                                               │
└─────────────────────┬───────────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────────┐
│                   ORDER MANAGEMENT                              │
│                 (app/client/orders.php) ✅ NEW                  │
│                                                                 │
│  Navigation: Sidebar → "Orders" menu item                      │
│                                                                 │
│  Features:                                                     │
│  - Store selector (for multi-store owners)                     │
│  - Statistics dashboard (total orders, revenue, pending, etc.) │
│  - Advanced filters (status, payment, date range, search)      │
│  - Orders table with pagination                                │
│  - Order details modal                                         │
│  - Update order status                                         │
│  - Update payment status                                       │
│  - Add tracking numbers                                        │
│  - Bulk actions (update multiple orders)                       │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🔗 How Everything Connects

### 1. **Public Homepage → Store Frontend**

**File:** `app/index.php`
- Lists all active stores from API: `GET /api/stores?status=active`
- Click on any store card → Redirects to `app/store/view.php?id={store_id}`

### 2. **Store Frontend → Product Display**

**File:** `app/store/view.php`
- Loads store details: `GET /api/stores/{id}`
- Loads products: `GET /api/stores/{id}/products`
- Loads categories: `GET /api/stores/{id}/categories`
- Displays products with "Add to Cart" buttons
- Uses store's primary/accent colors for branding
- Dynamic header with store name and description

### 3. **Product → Shopping Cart**

**JavaScript Service:** `app/assets/js/services/cart.js`

**For Guest Users:**
- Stores cart in localStorage: `localStorage.setItem('cart_{storeId}', JSON.stringify(items))`
- No API calls needed
- Cart persists in browser

**For Authenticated Users:**
- API call: `POST /api/stores/{storeId}/cart/items`
- Cart stored in database
- Syncs across devices

**Add to Cart Flow:**
```javascript
// In view.php
addToCart(productId, productName, price, imageUrl, stockQuantity)
  ↓
cartService.addToCart(storeId, product, quantity, isAuthenticated)
  ↓
If authenticated: API call
If guest: localStorage
  ↓
Update cart badge
Show notification
```

### 4. **Shopping Cart → Checkout**

**File:** `app/store/cart.php`
- Displays all cart items
- Quantity controls with stock validation
- Calculates totals (subtotal + shipping)
- Free shipping above ₦10,000
- "Proceed to Checkout" button → `app/store/checkout.php?store_id={id}`

### 5. **Checkout → Order Placement**

**File:** `app/store/checkout.php`
**JavaScript Service:** `app/assets/js/services/checkout.js`

**3-Step Process:**
1. Contact info validation
2. Shipping address (saved addresses or new)
3. Payment method selection

**Place Order:**
```javascript
POST /api/stores/{storeId}/checkout
Body: {
  customer_name, customer_email, customer_phone,
  shipping_address: {...},
  payment_method,
  order_notes,
  items: [{product_id, quantity, price}]
}
  ↓
Response: { order_id: 123 }
  ↓
Clear cart
Redirect to order-success.php?order_id=123
```

### 6. **Order Confirmation → Tracking**

**File:** `app/store/order-success.php`
- Shows order confirmation
- Displays order number
- "Track Order" button → `app/store/order-tracking.php?order_id={id}&store_id={id}`

**File:** `app/store/order-tracking.php`
- Search by order ID or tracking number
- API: `GET /api/stores/{storeId}/orders/{orderId}/track`
- Visual timeline of order progress

---

## 👤 Client (Store Owner) Access to Orders

### How to Access:

1. **Login as Store Owner:**
   - Go to `/auth/login.php`
   - Login with client credentials

2. **Navigate to Orders:**
   - After login, you're at `/client/dashboard.php`
   - **Sidebar Menu:** Click on "Orders" (newly added) ✅
   - Direct URL: `/client/orders.php`

### Navigation Added:

**File:** `app/shared/header-client.php`

```html
<nav>
  <a href="/client/dashboard.php">Dashboard</a>
  <a href="/client/stores.php">My Stores</a>
  <a href="/client/products.php">Products</a>
  <a href="/client/orders.php">Orders</a> ✅ NEW
  <a href="/client/store-settings.php">Store Settings</a>
</nav>
```

### What Store Owners Can Do:

**On the Orders Page:**
1. **Select Store** (if they own multiple stores)
2. **View Statistics:**
   - Total orders
   - Total revenue
   - Pending orders
   - Completed orders

3. **Filter Orders:**
   - By status (pending, processing, shipped, delivered, cancelled)
   - By payment status (pending, paid, failed, refunded)
   - By date range
   - By customer name/email (search)

4. **View Order Details:**
   - Customer information
   - Shipping address
   - Order items
   - Payment details
   - Order timeline

5. **Update Orders:**
   - Change order status
   - Update payment status
   - Add tracking numbers
   - Bulk update multiple orders

---

## 🎨 Template Integration

### Store Templates Location:
`store-templates/*.html`

Examples:
- `classic-ecommerce.html`
- `minimal-clean.html`
- `premium-luxury.html`
- `bold-modern.html`
- `campmart-style.html`

### How Templates Are Used:

**Currently:**
Templates are **static HTML** used for preview/customization in the admin panel.

**In `app/store/view.php`:**
- We **dynamically generate** the store frontend
- Use store's `primary_color` and `accent_color`
- Apply store branding (name, description)
- Load actual products from database

### Template Variables (Placeholders):

```html
{{store_name}}       → Replaced with actual store name
{{store_description}} → Replaced with store description
{{primary_color}}    → Store's primary color (#2563eb)
{{accent_color}}     → Store's accent color (#10b981)
```

### Future Enhancement (Optional):

To use actual template files instead of dynamic generation:

```php
// In app/store/view.php
$template = file_get_contents("../../store-templates/{$store->template}.html");
$template = str_replace('{{store_name}}', $store->name, $template);
$template = str_replace('{{primary_color}}', $store->primary_color, $template);
// ... more replacements
echo $template;
```

---

## 📂 File Structure Summary

```
app/
├── index.php                      ✅ Public homepage (store listings)
├── store/
│   ├── view.php                   ✅ Individual store frontend
│   ├── cart.php                   ✅ Shopping cart
│   ├── checkout.php               ✅ Checkout flow
│   ├── order-success.php          ✅ Order confirmation
│   └── order-tracking.php         ✅ Order tracking
├── client/
│   ├── dashboard.php              Existing
│   ├── stores.php                 Existing
│   ├── products.php               Existing
│   ├── orders.php                 ✅ NEW - Order management
│   └── store-settings.php         Existing
├── assets/js/services/
│   ├── cart.js                    ✅ Cart operations
│   ├── checkout.js                ✅ Checkout operations
│   ├── client-orders.js           ✅ Client order management
│   └── admin-orders.js            Existing
└── shared/
    └── header-client.php          ✅ Updated with Orders link
```

---

## 🔌 API Endpoints Used

### Customer-Facing:
```
GET    /api/stores                          List all stores
GET    /api/stores/{id}                     Get store details
GET    /api/stores/{id}/products            Get store products
GET    /api/stores/{id}/categories          Get store categories
GET    /api/stores/{id}/cart                Get cart items
POST   /api/stores/{id}/cart/items          Add to cart
PUT    /api/stores/{id}/cart/items/{id}     Update quantity
DELETE /api/stores/{id}/cart/items/{id}     Remove from cart
DELETE /api/stores/{id}/cart                Clear cart
POST   /api/stores/{id}/checkout            Place order
GET    /api/stores/{id}/orders/{id}/track   Track order
```

### Client (Store Owner):
```
GET    /api/stores/{id}/orders              List orders
GET    /api/stores/{id}/orders/{id}         Get order details
PUT    /api/stores/{id}/orders/{id}/status  Update order status
PUT    /api/stores/{id}/orders/{id}/payment Update payment status
PUT    /api/stores/{id}/orders/{id}/tracking Add tracking
GET    /api/stores/{id}/orders/stats        Get statistics
PUT    /api/stores/{id}/orders/bulk         Bulk update
```

---

## 🚀 Quick Start Guide

### For Customers:
1. Visit `/` (homepage)
2. Browse stores
3. Click on a store
4. Add products to cart
5. View cart
6. Proceed to checkout
7. Complete 3 steps
8. Get order confirmation
9. Track order

### For Store Owners:
1. Login at `/auth/login.php`
2. Navigate to "Orders" in sidebar ✅
3. Select your store
4. View order statistics
5. Filter/search orders
6. Click on order to view details
7. Update status, payment, or tracking
8. Use bulk actions for multiple orders

---

## ✨ Key Features

### Guest Shopping:
- ✅ No login required to browse and add to cart
- ✅ Cart stored in browser (localStorage)
- ✅ Can complete checkout as guest
- ✅ Order confirmation via email

### Authenticated Shopping:
- ✅ Cart synced to server
- ✅ Saved addresses
- ✅ Order history
- ✅ Multiple devices

### Store Owner Benefits:
- ✅ Real-time order notifications
- ✅ Order filtering and search
- ✅ Status management
- ✅ Tracking number updates
- ✅ Statistics and analytics
- ✅ Bulk operations

---

## 🎯 Complete Customer Journey Example

1. **John visits `yourdomain.com/`**
   - Sees list of stores
   - Clicks on "Tech Store"

2. **Redirected to `/store/view.php?id=1`**
   - Sees Tech Store's products
   - Adds iPhone to cart
   - Cart badge shows "1"

3. **Clicks cart icon → `/store/cart.php?store_id=1`**
   - Reviews iPhone in cart
   - Updates quantity to 2
   - Clicks "Proceed to Checkout"

4. **Redirected to `/store/checkout.php?store_id=1`**
   - **Step 1:** Enters name, email, phone
   - **Step 2:** Enters shipping address
   - **Step 3:** Selects "Card" payment, clicks "Place Order"

5. **Redirected to `/store/order-success.php?order_id=42`**
   - Sees "Order Confirmed!" with order #42
   - Clicks "Track Order"

6. **Redirected to `/store/order-tracking.php?order_id=42&store_id=1`**
   - Sees order timeline: "Pending"
   - Can check back later for updates

7. **Store owner logs in**
   - Goes to `/client/orders.php`
   - Selects "Tech Store"
   - Sees John's order #42
   - Updates status to "Processing"
   - Adds tracking number

8. **John checks tracking again**
   - Status now shows "Processing"
   - Sees tracking number
   - Receives updates via email

---

## 🔧 Technical Notes

### LocalStorage Structure:
```javascript
// Cart
localStorage.setItem('cart_1', JSON.stringify([
  {product_id: 5, product_name: "iPhone", price: 500000, quantity: 2, ...}
]))

// Checkout Progress
localStorage.setItem('checkout_1', JSON.stringify({
  step: 2,
  customerName: "John Doe",
  customerEmail: "john@example.com",
  ...
}))
```

### Stock Validation:
```javascript
// Before adding to cart
if (product.stock_quantity < requestedQuantity) {
  throw new Error(`Only ${product.stock_quantity} available`);
}
```

### Shipping Calculation:
```javascript
// Free shipping above ₦10,000
const shipping = subtotal >= 10000 ? 0 : 1500;

// Extra for remote states
if (remoteStates.includes(state)) {
  shipping += 500;
}
```

---

## 📝 Summary

✅ **Customer Flow:** Homepage → Store → Cart → Checkout → Confirmation → Tracking  
✅ **Store Owner Access:** Sidebar → Orders → Manage all orders  
✅ **Template Connection:** Dynamic frontend using store branding  
✅ **Data Flow:** API ↔ Services ↔ UI  
✅ **Guest Support:** localStorage for cart persistence  
✅ **Authentication:** Optional for customers, required for store owners  

Everything is now connected and working! 🎉
