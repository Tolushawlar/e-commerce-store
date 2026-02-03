# E-commerce Platform - Complete Architecture

## System Overview

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         E-COMMERCE PLATFORM                             │
│                                                                         │
│  ┌───────────────┐  ┌───────────────┐  ┌────────────────┐             │
│  │   CUSTOMERS   │  │ STORE OWNERS  │  │     ADMINS     │             │
│  │   (Public)    │  │   (Clients)   │  │  (Platform)    │             │
│  └───────┬───────┘  └───────┬───────┘  └────────┬───────┘             │
│          │                  │                     │                     │
└──────────┼──────────────────┼─────────────────────┼─────────────────────┘
           │                  │                     │
           ▼                  ▼                     ▼
    ┌─────────────┐   ┌─────────────┐    ┌──────────────┐
    │   PUBLIC    │   │   CLIENT    │    │    ADMIN     │
    │  FRONTEND   │   │   PANEL     │    │    PANEL     │
    └─────────────┘   └─────────────┘    └──────────────┘
           │                  │                     │
           └──────────────────┴─────────────────────┘
                             │
                             ▼
                    ┌─────────────────┐
                    │   REST API      │
                    │   (Backend)     │
                    └─────────────────┘
                             │
                             ▼
                    ┌─────────────────┐
                    │     DATABASE    │
                    │     (MySQL)     │
                    └─────────────────┘
```

---

## Customer Journey (Public-Facing)

```
START: Customer visits site
    │
    ├─→ app/index.php (Homepage)
    │    • Browse all stores
    │    • Search functionality
    │    │
    │    └─→ Click on store
    │         │
    ├─→ app/store/view.php?id=X (Store Frontend)
    │    • View products
    │    • Filter by category
    │    • Add to cart
    │    │
    │    ├─→ Cart Badge Updates
    │    │    • Real-time count
    │    │    • LocalStorage/API
    │    │
    │    └─→ Click "View Cart"
    │         │
    ├─→ app/store/cart.php?store_id=X (Shopping Cart)
    │    • View all items
    │    • Update quantities
    │    • Remove items
    │    • See totals
    │    │
    │    └─→ Click "Proceed to Checkout"
    │         │
    ├─→ app/store/checkout.php?store_id=X (Checkout)
    │    │
    │    ├─→ Step 1: Contact Info
    │    │    • Name, Email, Phone
    │    │
    │    ├─→ Step 2: Shipping Address
    │    │    • Saved addresses (if logged in)
    │    │    • New address form
    │    │
    │    ├─→ Step 3: Payment & Review
    │    │    • Payment method
    │    │    • Order notes
    │    │    • Place Order
    │    │
    │    └─→ Submit Order
    │         │
    ├─→ app/store/order-success.php?order_id=X (Confirmation)
    │    • Order number
    │    • Email confirmation
    │    • Order summary
    │    │
    │    └─→ Click "Track Order"
    │         │
    └─→ app/store/order-tracking.php (Order Tracking)
         • Search by Order ID
         • Visual timeline
         • Order status updates
         • Shipping details
```

---

## Store Owner Journey (Client Panel)

```
START: Store owner logs in
    │
    ├─→ /auth/login.php
    │    • Client credentials
    │    │
    │    └─→ Authenticated
    │         │
    ├─→ app/client/dashboard.php (Dashboard)
    │    • Store statistics
    │    • Quick actions
    │    │
    │    └─→ SIDEBAR NAVIGATION:
    │         │
    │         ├─→ Dashboard
    │         ├─→ My Stores
    │         ├─→ Products
    │         ├─→ Orders ✅ NEW
    │         └─→ Store Settings
    │              │
    ├─→ app/client/orders.php (Order Management) ✅
    │    │
    │    ├─→ Select Store (if multiple)
    │    │
    │    ├─→ View Statistics Dashboard
    │    │    • Total orders
    │    │    • Total revenue
    │    │    • Pending orders
    │    │    • Completed orders
    │    │
    │    ├─→ Filter Orders
    │    │    • By status
    │    │    • By payment status
    │    │    • By date range
    │    │    • By customer (search)
    │    │
    │    ├─→ View Orders Table
    │    │    • Pagination
    │    │    • Bulk selection
    │    │    │
    │    │    └─→ Click on Order
    │    │         │
    │    ├─→ Order Details Modal
    │    │    • Customer info
    │    │    • Shipping address
    │    │    • Order items
    │    │    • Payment details
    │    │    │
    │    │    └─→ Actions:
    │    │         ├─→ Update order status
    │    │         ├─→ Update payment status
    │    │         ├─→ Add tracking number
    │    │         └─→ Print order
    │    │
    │    └─→ Bulk Actions
    │         • Select multiple orders
    │         • Update status in batch
    │         • Export orders
```

---

## Data Flow Diagram

```
CUSTOMER ACTIONS                API ENDPOINTS                   DATABASE
─────────────────              ─────────────────              ──────────

Browse Stores      ──→  GET /api/stores              ──→    stores table
                                                             ├─ id
                                                             ├─ name
                                                             ├─ description
                                                             ├─ status
                                                             └─ colors

View Store         ──→  GET /api/stores/{id}         ──→    stores table
                        GET /api/stores/{id}/products  ──→   products table
                        GET /api/stores/{id}/categories ──→  categories table

Add to Cart        ──→  POST /api/stores/{id}/cart/items ──→ cart_items table
(Authenticated)                                              ├─ cart_id
                                                             ├─ product_id
                                                             ├─ quantity
                                                             └─ price

Add to Cart        ──→  [No API Call]                
(Guest)                 localStorage.setItem()              Browser Storage
                                                             'cart_{storeId}'

View Cart          ──→  GET /api/stores/{id}/cart    ──→    cart_items table
(Authenticated)         

View Cart          ──→  [No API Call]
(Guest)                 localStorage.getItem()              Browser Storage

Place Order        ──→  POST /api/stores/{id}/checkout ──→  orders table
                                                             ├─ id
                                                             ├─ store_id
                                                             ├─ customer_name
                                                             ├─ customer_email
                                                             ├─ total_amount
                                                             ├─ status
                                                             └─ payment_status
                                                             
                                                             order_items table
                                                             ├─ order_id
                                                             ├─ product_id
                                                             ├─ quantity
                                                             └─ price

Track Order        ──→  GET /api/stores/{id}/orders/{id}/track ──→ orders table
                                                                   order_items
                                                                   addresses


STORE OWNER ACTIONS           API ENDPOINTS                   DATABASE
───────────────────          ─────────────────              ──────────

View Orders        ──→  GET /api/stores/{id}/orders   ──→    orders table
                        ?page=1&limit=20                      (filtered)
                        &status=pending
                        &payment_status=paid
                        &from_date=2024-01-01
                        &search=customer

View Statistics    ──→  GET /api/stores/{id}/orders/stats ──→ orders table
                                                               (aggregated)

Update Status      ──→  PUT /api/stores/{id}/orders/{id}/status ──→ orders table
                                                                     UPDATE status

Update Payment     ──→  PUT /api/stores/{id}/orders/{id}/payment ──→ orders table
                                                                      UPDATE payment_status

Add Tracking       ──→  PUT /api/stores/{id}/orders/{id}/tracking ──→ orders table
                                                                       UPDATE tracking_number

Bulk Update        ──→  PUT /api/stores/{id}/orders/bulk    ──→    orders table
                        Body: {order_ids: [1,2,3],                  UPDATE multiple rows
                               status: 'shipped'}
```

---

## File Organization

```
project-root/
│
├── app/                              ← Frontend Applications
│   │
│   ├── index.php                     ← PUBLIC: Homepage (store listings)
│   │
│   ├── store/                        ← PUBLIC: Customer-facing
│   │   ├── view.php                  ← Individual store frontend
│   │   ├── cart.php                  ← Shopping cart
│   │   ├── checkout.php              ← 3-step checkout
│   │   ├── order-success.php         ← Order confirmation
│   │   └── order-tracking.php        ← Order tracking
│   │
│   ├── client/                       ← AUTHENTICATED: Store owners
│   │   ├── dashboard.php             ← Client dashboard
│   │   ├── stores.php                ← Manage stores
│   │   ├── products.php              ← Manage products
│   │   ├── orders.php ✅             ← Manage orders (NEW)
│   │   └── store-settings.php        ← Store customization
│   │
│   ├── admin/                        ← AUTHENTICATED: Platform admins
│   │   ├── dashboard.php
│   │   ├── stores.php
│   │   ├── clients.php
│   │   ├── categories.php
│   │   ├── templates.php
│   │   └── orders.php
│   │
│   ├── auth/                         ← Authentication
│   │   ├── login.php
│   │   └── register.php
│   │
│   ├── assets/js/
│   │   ├── api.js                    ← Base API client
│   │   ├── auth.js                   ← Auth service
│   │   └── services/
│   │       ├── cart.js ✅            ← Cart operations (NEW)
│   │       ├── checkout.js ✅        ← Checkout operations (NEW)
│   │       ├── client-orders.js ✅   ← Client order mgmt (NEW)
│   │       └── admin-orders.js       ← Admin order mgmt
│   │
│   └── shared/
│       ├── header-client.php ✅      ← Updated with Orders link
│       ├── header-admin.php
│       ├── footer-client.php
│       └── footer-admin.php
│
├── backend/                          ← Backend API
│   ├── controllers/
│   │   ├── StoreController.php
│   │   ├── ProductController.php
│   │   ├── CategoryController.php
│   │   ├── CartController.php
│   │   ├── CheckoutController.php
│   │   ├── OrderController.php
│   │   ├── AdminOrderController.php
│   │   ├── CustomerController.php
│   │   └── AddressController.php
│   │
│   ├── models/
│   │   ├── Store.php
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Cart.php
│   │   ├── Order.php
│   │   └── Address.php
│   │
│   └── services/
│       └── ...
│
├── api/
│   ├── index.php                     ← API entry point
│   ├── router.php                    ← Route definitions
│   └── openapi.json                  ← Swagger documentation
│
└── store-templates/                  ← HTML Templates
    ├── classic-ecommerce.html
    ├── minimal-clean.html
    ├── premium-luxury.html
    ├── bold-modern.html
    └── campmart-style.html
```

---

## Authentication & Access Control

```
┌──────────────────────────────────────────────────────────┐
│                    USER TYPES                            │
└──────────────────────────────────────────────────────────┘

1. GUEST (Not logged in)
   Access:
   ✅ Browse stores (/)
   ✅ View products (/store/view.php)
   ✅ Add to cart (localStorage)
   ✅ Checkout (as guest)
   ✅ Track orders (with order ID)
   
   Restrictions:
   ❌ Cannot save addresses
   ❌ Cannot view order history
   ❌ Cart not synced across devices

2. CUSTOMER (Logged in)
   Access:
   ✅ All guest features +
   ✅ Saved addresses
   ✅ Order history
   ✅ Synced cart (across devices)
   ✅ Profile management
   
   Restrictions:
   ❌ Cannot access admin/client panels

3. CLIENT (Store Owner - Logged in)
   Access:
   ✅ Client panel (/client/*)
   ✅ Manage own stores
   ✅ Manage products
   ✅ View/manage orders ✅ NEW
   ✅ Store customization
   ✅ Store statistics
   
   Restrictions:
   ❌ Cannot access admin panel
   ❌ Can only see own stores' orders

4. ADMIN (Platform Admin - Logged in)
   Access:
   ✅ Admin panel (/admin/*)
   ✅ All stores
   ✅ All orders
   ✅ All clients
   ✅ Platform settings
   ✅ Categories & templates
   
   Full Access:
   ✅ Everything
```

---

## Key URLs Reference

### Public (Customers)
```
/                                     Homepage (store listings)
/store/view.php?id={X}               Store frontend
/store/cart.php?store_id={X}         Shopping cart
/store/checkout.php?store_id={X}     Checkout process
/store/order-success.php?order_id={X} Order confirmation
/store/order-tracking.php            Order tracking
```

### Client Panel (Store Owners)
```
/auth/login.php                      Login
/client/dashboard.php                Dashboard
/client/stores.php                   My Stores
/client/products.php                 Products
/client/orders.php ✅                Orders (NEW)
/client/store-settings.php           Settings
```

### Admin Panel
```
/admin/dashboard.php                 Dashboard
/admin/stores.php                    All Stores
/admin/clients.php                   All Clients
/admin/orders.php                    All Orders
/admin/categories.php                Categories
/admin/templates.php                 Templates
```

---

## Summary

✅ **Customer Flow:** Complete end-to-end shopping experience  
✅ **Client Access:** Sidebar menu → "Orders" → Full order management  
✅ **Template Integration:** Dynamic store frontend with branding  
✅ **Guest Support:** LocalStorage for cart, no login required  
✅ **Authentication:** Optional for customers, required for store owners  
✅ **API Integration:** RESTful API connecting all parts  

**All systems are connected and operational!** 🎉
