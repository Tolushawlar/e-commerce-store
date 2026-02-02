# Customer Shopping Features Integration Guide

## Overview
This document explains how the customer-facing shopping cart and checkout features are integrated with the store generation system.

## Architecture

### Store Structure
Stores are **static HTML files** generated and stored at:
```
/api/stores/store-{id}/
├── index.html           # Main store page (products listing)
├── product.html         # Product detail page
├── cart.html            # Shopping cart page
├── checkout.html        # Checkout flow page
├── order-success.html   # Order confirmation page
├── config.json          # Store configuration
├── store.js             # Product loading logic
├── product-detail.js    # Product detail logic
├── cart.js              # Cart service
└── checkout.js          # Checkout service
```

### Access URL
Stores are accessed via:
```
http://localhost:8000/api/stores/store-{id}/index.html
```

## Integration Flow

### 1. Store Generation Process

When a store is created or updated via the API, the `StoreGeneratorService` automatically generates all necessary files:

**StoreGeneratorService.php** performs these steps:
1. Creates/updates store directory
2. Generates `index.html` from template with store branding
3. Generates `product.html` with store branding
4. Generates `cart.html` from template
5. Generates `checkout.html` from template
6. Generates `order-success.html` from template
7. Copies JavaScript services:
   - `store.js` - Product loading
   - `product-detail.js` - Product details
   - `cart.js` - Cart management
   - `checkout.js` - Checkout operations
8. Creates `config.json` with store settings

### 2. Template System

**Templates Location:** `/store-templates/`
- `cart.html` - Cart page template
- `checkout.html` - Checkout page template
- `order-success.html` - Success page template

**Placeholder Replacement:**
The `StoreGeneratorService::replacePlaceholders()` method replaces these placeholders:
- `{{store_name}}` - Store name
- `{{store_description}}` - Store description
- `{{primary_color}}` - Primary brand color
- `{{accent_color}}` - Accent brand color
- `{{logo_url}}` - Store logo URL
- `{{tagline}}` - Store tagline
- `{{store_id}}` - Store ID for API calls

### 3. Shopping Cart Flow

#### Customer Journey:
```
index.html → product.html → cart.html → checkout.html → order-success.html
   ↓             ↓              ↓             ↓                ↓
Browse      View Details   Review Cart   Enter Info    Confirmation
```

#### Cart Implementation:

**a) Add to Cart (index.html & product.html):**
```javascript
// Customer clicks "Add to Cart"
addToCart(productId)
  ↓
CartService.addItem(productId, quantity)
  ↓
Store in localStorage: { productId, quantity, addedAt }
  ↓
Update cart badge count
```

**b) View Cart (cart.html):**
```javascript
// Page loads
CartService.getCartWithDetails()
  ↓
For each cart item, fetch full product details from API
  ↓
Display products with quantity controls
  ↓
Calculate totals (subtotal, tax, shipping)
```

**c) Checkout (checkout.html):**
```javascript
// 3-step wizard
Step 1: Contact Information (name, email, phone)
  ↓
Step 2: Shipping Address (address, city, state, postal code)
  ↓
Step 3: Payment Method (card, transfer, COD)
  ↓
CheckoutService.placeOrder()
  ↓
POST /api/orders with full order data
  ↓
Clear cart on success → Redirect to order-success.html
```

### 4. JavaScript Services

#### CartService (cart.js)
**Purpose:** Manage shopping cart operations

**Key Methods:**
- `getCart()` - Retrieve cart from localStorage
- `addItem(productId, quantity)` - Add product to cart
- `updateQuantity(productId, quantity)` - Update item quantity
- `removeItem(productId)` - Remove item from cart
- `clearCart()` - Empty the cart
- `getCartWithDetails()` - Get cart with full product info from API
- `calculateTotals()` - Calculate subtotal, tax, shipping, total
- `updateCartBadge()` - Update badge counter in UI

**Storage Format (localStorage):**
```json
[
  {
    "productId": 123,
    "quantity": 2,
    "addedAt": "2026-02-02T10:30:00Z"
  }
]
```

#### CheckoutService (checkout.js)
**Purpose:** Handle checkout and order placement

**Key Methods:**
- `validateContact(data)` - Validate contact information
- `validateShipping(data)` - Validate shipping address
- `placeOrder(orderData)` - Submit order to API
- `getOrder(orderId)` - Fetch order details
- `trackOrder(orderNumber, email)` - Track order status
- `calculateShipping(state, subtotal)` - Calculate shipping cost

**Order Data Structure:**
```json
{
  "store_id": 1,
  "customer_email": "customer@email.com",
  "customer_phone": "08012345678",
  "customer_name": "John Doe",
  "shipping_address": "123 Main St",
  "shipping_city": "Lagos",
  "shipping_state": "Lagos",
  "shipping_postal_code": "100001",
  "payment_method": "card",
  "items": [
    {
      "product_id": 123,
      "quantity": 2,
      "unit_price": 15000,
      "total_price": 30000
    }
  ],
  "subtotal": 30000,
  "shipping_cost": 1500,
  "tax_amount": 2250,
  "total_amount": 33750
}
```

### 5. Navigation Integration

**Header Cart Button:**
All generated store pages include:
```html
<a href="cart.html" class="relative">
  <span class="material-symbols-outlined">shopping_cart</span>
  <span id="cart-badge" class="badge hidden">0</span>
</a>
```

**Badge Update:**
- Automatically updated when `cart.js` loads
- Updated after adding/removing items
- Shows total item count

### 6. API Integration

**Endpoints Used:**

```
GET  /api/products?store_id={id}     - List products
GET  /api/products/{id}                - Get product details
GET  /api/categories?store_id={id}     - List categories
POST /api/orders                       - Create order
GET  /api/orders/{id}                  - Get order details
GET  /api/orders/track?order_number={num}&email={email} - Track order
```

### 7. Store Configuration

**config.json Example:**
```json
{
  "store_id": 1,
  "store_name": "Prodevx Tech Shop",
  "store_slug": "prodevx-tech-hub",
  "colors": {
    "primary": "#35e212",
    "accent": "#d3853c"
  },
  "settings": {
    "font_family": "Plus Jakarta Sans",
    "button_style": "square",
    "product_grid_columns": 3,
    "show_search": true,
    "show_cart": true,
    "show_wishlist": false
  }
}
```

**Usage in JavaScript:**
```javascript
const storeConfig = {
  store_id: 1,
  storeId: 1,
  apiUrl: window.location.origin + '/api'
};
window.storeConfig = storeConfig;
```

## How It All Connects

### 1. Customer Discovers Store
```
Customer → Browse /api/stores/store-1/index.html
         → Products loaded via store.js from API
         → Styled with store's colors and branding
```

### 2. Customer Shops
```
View Product → Click product card → product.html?id=123
            → Product loaded via product-detail.js
            → Click "Add to Cart"
            → CartService.addItem(123, 1)
            → Cart badge updates
```

### 3. Customer Checks Out
```
Click Cart Badge → cart.html
                → CartService loads cart with product details
                → Click "Proceed to Checkout"
                → checkout.html
                → 3-step wizard (Contact → Shipping → Payment)
                → CheckoutService.placeOrder()
                → order-success.html?order=456
```

### 4. Order Confirmation
```
order-success.html → CheckoutService.getOrder(456)
                  → Display order details
                  → Cart cleared
                  → Customer can print receipt or continue shopping
```

## Key Features

### Guest Checkout
- No account required
- Cart stored in localStorage
- Order tracked via email + order number

### Real-time Inventory
- Stock validation during checkout
- Out-of-stock warnings
- Low stock alerts

### Dynamic Pricing
- Tax calculation (7.5% VAT)
- Shipping by location
- Free shipping over ₦50,000

### Responsive Design
- Mobile-first approach
- Tailwind CSS styling
- Material Icons

## Regenerating Stores

To apply updates to existing stores, trigger store regeneration:

**API Call:**
```bash
PUT /api/stores/{id}
```

This will:
1. Re-generate all HTML files with latest templates
2. Copy updated JavaScript services
3. Preserve store-specific branding and settings

## Testing the Integration

### 1. Create a Test Store
```bash
POST /api/stores
{
  "client_id": 1,
  "store_name": "Test Store",
  "store_slug": "test-store",
  "primary_color": "#064E3B",
  "accent_color": "#BEF264"
}
```

### 2. Access the Store
```
http://localhost:8000/api/stores/store-{id}/index.html
```

### 3. Test Shopping Flow
1. Browse products
2. Click "Add to Cart" on a product
3. Verify cart badge updates
4. Click cart icon → verify cart.html loads
5. Click "Proceed to Checkout"
6. Fill out all 3 steps
7. Submit order
8. Verify order-success.html shows order details

## Troubleshooting

### Cart Badge Not Updating
- Ensure `cart.js` is loaded before `store.js`
- Check browser console for errors
- Verify localStorage is enabled

### Products Not Loading
- Check API endpoint is accessible
- Verify `storeConfig.store_id` is set correctly
- Check network tab for failed requests

### Checkout Fails
- Verify all required fields are filled
- Check API `/orders` endpoint is working
- Ensure cart has valid items

## Future Enhancements

- [ ] Wishlist functionality
- [ ] Product search within store
- [ ] Order tracking page
- [ ] Customer account system
- [ ] Payment gateway integration
- [ ] Email notifications
- [ ] Multi-currency support

---

**Last Updated:** February 2, 2026
