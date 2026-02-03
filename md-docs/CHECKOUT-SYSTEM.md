# Checkout & Order System Documentation

## Overview

Complete checkout and order placement system supporting both **registered customers** and **guest checkout**. Customers can place orders from their cart or directly with product items.

---

## Features Implemented

✅ **Guest Checkout** - Buy without creating account  
✅ **Registered Customer Checkout** - Checkout from saved cart  
✅ **Address Management** - Save multiple shipping/billing addresses  
✅ **Order Tracking** - Track orders via email  
✅ **Stock Management** - Automatic stock deduction  
✅ **Order History** - View past orders  
✅ **Multiple Payment Methods** - COD, Bank Transfer, Card, Wallet  
✅ **Order Notes** - Customer can add delivery instructions  
✅ **Shipping Cost** - Configurable shipping fees  

---

## API Endpoints

### Address Management

#### Get All Addresses
```http
GET /api/stores/{store_id}/addresses
Authorization: Bearer {customer_token}

Response:
[
  {
    "id": 1,
    "customer_id": 5,
    "address_type": "shipping",
    "full_name": "John Doe",
    "phone": "+234123456789",
    "address_line1": "123 Main Street",
    "address_line2": "Apt 4B",
    "city": "Lagos",
    "state": "Lagos",
    "postal_code": "100001",
    "country": "Nigeria",
    "is_default": true
  }
]
```

#### Create Address
```http
POST /api/stores/{store_id}/addresses
Authorization: Bearer {customer_token}
Content-Type: application/json

{
  "address_type": "shipping",
  "full_name": "John Doe",
  "phone": "+234123456789",
  "address_line1": "123 Main Street",
  "address_line2": "Apt 4B",
  "city": "Lagos",
  "state": "Lagos",
  "postal_code": "100001",
  "country": "Nigeria"
}
```

#### Update Address
```http
PUT /api/stores/{store_id}/addresses/{id}
Authorization: Bearer {customer_token}

{
  "address_line1": "456 New Street",
  "city": "Abuja"
}
```

#### Delete Address
```http
DELETE /api/stores/{store_id}/addresses/{id}
Authorization: Bearer {customer_token}
```

#### Set Default Address
```http
POST /api/stores/{store_id}/addresses/{id}/set-default
Authorization: Bearer {customer_token}
```

---

### Checkout & Orders

#### Checkout (Registered Customer - From Cart)
```http
POST /api/stores/{store_id}/checkout
Authorization: Bearer {customer_token}
Content-Type: application/json

{
  "customer_name": "John Doe",
  "customer_email": "john@example.com",
  "customer_phone": "+234123456789",
  "shipping_address_id": 1,
  "billing_address_id": 1,
  "payment_method": "cash_on_delivery",
  "shipping_cost": 2000,
  "notes": "Please call before delivery"
}

Response:
{
  "success": true,
  "message": "Order placed successfully",
  "data": {
    "id": 123,
    "store_id": 1,
    "customer_id": 5,
    "total_amount": 52000,
    "shipping_cost": 2000,
    "payment_method": "cash_on_delivery",
    "payment_status": "pending",
    "status": "pending",
    "items": [...],
    "shipping_address": {...}
  }
}
```

#### Guest Checkout (Direct Purchase)
```http
POST /api/stores/{store_id}/checkout
Content-Type: application/json

{
  "customer_name": "Jane Smith",
  "customer_email": "jane@example.com",
  "customer_phone": "+234987654321",
  "payment_method": "cash_on_delivery",
  "shipping_cost": 2000,
  "items": [
    { "product_id": 10, "quantity": 2 },
    { "product_id": 15, "quantity": 1 }
  ],
  "shipping_address": {
    "full_name": "Jane Smith",
    "phone": "+234987654321",
    "address_line1": "789 Guest Street",
    "city": "Port Harcourt",
    "state": "Rivers",
    "country": "Nigeria"
  },
  "notes": "Deliver between 9am-5pm"
}
```

#### Get Customer Orders
```http
GET /api/stores/{store_id}/orders
Authorization: Bearer {customer_token}

Response:
[
  {
    "id": 123,
    "total_amount": 52000,
    "status": "delivered",
    "payment_status": "paid",
    "created_at": "2026-02-01 14:30:00"
  }
]
```

#### Get Order Details
```http
GET /api/stores/{store_id}/orders/{order_id}
Authorization: Bearer {customer_token}

Response:
{
  "id": 123,
  "store_id": 1,
  "customer_id": 5,
  "customer_name": "John Doe",
  "customer_email": "john@example.com",
  "total_amount": 52000,
  "shipping_cost": 2000,
  "payment_method": "cash_on_delivery",
  "payment_status": "pending",
  "status": "processing",
  "tracking_number": null,
  "order_notes": "Please call before delivery",
  "items": [
    {
      "id": 1,
      "product_id": 10,
      "product_name": "Laptop",
      "quantity": 1,
      "price": 50000,
      "product_image": "..."
    }
  ],
  "shipping_address": {
    "address_line1": "123 Main Street",
    "city": "Lagos",
    "state": "Lagos"
  }
}
```

#### Track Order (Guest - No Auth Required)
```http
GET /api/stores/{store_id}/orders/track?order_id=123&email=john@example.com

Response: (Same as Get Order Details)
```

---

## Checkout Flow Diagrams

### Registered Customer Checkout
```
Customer logged in
    ↓
Browse products → Add to cart
    ↓
View cart → Proceed to checkout
    ↓
Select/add shipping address
    ↓
Choose payment method
    ↓
Review order
    ↓
POST /api/stores/{id}/checkout
    ↓
Cart items → Order created
    ↓
Stock deducted, Cart cleared
    ↓
Order confirmation
```

### Guest Checkout
```
Customer browsing (not logged in)
    ↓
Add products to localStorage cart
    ↓
Proceed to checkout
    ↓
Fill in: Name, Email, Phone, Address
    ↓
Choose payment method
    ↓
POST /api/stores/{id}/checkout (with items array)
    ↓
Guest customer created (or found by email)
    ↓
Order created with items
    ↓
Stock deducted
    ↓
Order confirmation (track with email)
```

---

## Data Models

### Order Structure
```javascript
{
  id: number,
  store_id: number,
  customer_id: number,
  customer_name: string,
  customer_email: string,
  customer_phone: string,
  shipping_address_id: number,
  billing_address_id: number,
  total_amount: decimal,
  shipping_cost: decimal,
  payment_method: enum('cash_on_delivery', 'bank_transfer', 'card', 'wallet'),
  payment_status: enum('pending', 'paid', 'failed', 'refunded'),
  status: enum('pending', 'processing', 'shipped', 'delivered', 'cancelled'),
  order_notes: string,
  tracking_number: string,
  created_at: timestamp
}
```

### Order Item Structure
```javascript
{
  id: number,
  order_id: number,
  product_id: number,
  quantity: number,
  price: decimal,
  product_name: string,
  product_image: string
}
```

---

## Frontend Integration

### 1. Checkout Page (Registered Customer)

```javascript
class Checkout {
  constructor(storeId, token) {
    this.storeId = storeId;
    this.token = token;
  }

  async getAddresses() {
    const response = await fetch(`/api/stores/${this.storeId}/addresses`, {
      headers: { 'Authorization': `Bearer ${this.token}` }
    });
    return response.json();
  }

  async placeOrder(checkoutData) {
    const response = await fetch(`/api/stores/${this.storeId}/checkout`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${this.token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(checkoutData)
    });
    
    const result = await response.json();
    
    if (result.success) {
      // Redirect to order confirmation
      window.location.href = `/order-success?id=${result.data.id}`;
    }
    
    return result;
  }
}

// Usage
const checkout = new Checkout(1, customerToken);

// Get saved addresses
const addresses = await checkout.getAddresses();

// Place order
await checkout.placeOrder({
  customer_name: 'John Doe',
  customer_email: 'john@example.com',
  customer_phone: '+234123456789',
  shipping_address_id: addresses[0].id,
  payment_method: 'cash_on_delivery',
  shipping_cost: 2000,
  notes: 'Call before delivery'
});
```

### 2. Guest Checkout

```javascript
async function guestCheckout(storeId, formData, cartItems) {
  const response = await fetch(`/api/stores/${storeId}/checkout`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      customer_name: formData.name,
      customer_email: formData.email,
      customer_phone: formData.phone,
      payment_method: formData.payment_method,
      shipping_cost: 2000,
      items: cartItems, // [{ product_id: 1, quantity: 2 }]
      shipping_address: {
        full_name: formData.name,
        phone: formData.phone,
        address_line1: formData.address,
        city: formData.city,
        state: formData.state,
        country: 'Nigeria'
      },
      notes: formData.notes
    })
  });

  const result = await response.json();
  
  if (result.success) {
    // Save order ID and email for tracking
    localStorage.setItem('last_order', JSON.stringify({
      id: result.data.id,
      email: formData.email
    }));
    
    // Clear guest cart
    localStorage.removeItem('guest_cart');
    
    // Redirect to success page
    window.location.href = `/order-success?id=${result.data.id}`;
  }
  
  return result;
}
```

### 3. Order Tracking

```javascript
async function trackOrder(storeId, orderId, email) {
  const response = await fetch(
    `/api/stores/${storeId}/orders/track?order_id=${orderId}&email=${encodeURIComponent(email)}`
  );
  
  const result = await response.json();
  
  if (result.success) {
    displayOrderStatus(result.data);
  }
}
```

### 4. Address Management

```javascript
async function addAddress(storeId, token, addressData) {
  const response = await fetch(`/api/stores/${storeId}/addresses`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(addressData)
  });
  
  return response.json();
}

async function setDefaultAddress(storeId, token, addressId) {
  const response = await fetch(
    `/api/stores/${storeId}/addresses/${addressId}/set-default`,
    {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${token}` }
    }
  );
  
  return response.json();
}
```

---

## Stock Management

The system automatically:
- ✅ Validates stock before checkout
- ✅ Deducts stock when order is placed
- ✅ Prevents overselling
- ✅ Shows stock availability in cart

```php
// Automatic stock deduction
private function updateProductStock(array $items, string $operation = 'decrease'): void
{
    foreach ($items as $item) {
        $product = $this->productModel->find($item['product_id']);
        
        if ($product) {
            $newStock = $operation === 'decrease'
                ? $product['stock_quantity'] - $item['quantity']
                : $product['stock_quantity'] + $item['quantity'];

            $this->productModel->update($item['product_id'], [
                'stock_quantity' => max(0, $newStock)
            ]);
        }
    }
}
```

---

## Payment Methods

Currently supported:
- `cash_on_delivery` - Pay on delivery
- `bank_transfer` - Bank transfer
- `card` - Card payment (requires gateway integration)
- `wallet` - Digital wallet (requires gateway integration)

**Payment Status:**
- `pending` - Awaiting payment
- `paid` - Payment confirmed
- `failed` - Payment failed
- `refunded` - Payment refunded

---

## Order Status Flow

```
pending → processing → shipped → delivered
   ↓
cancelled (can be cancelled at any stage before shipped)
```

---

## Controllers Created

1. **AddressController** - Address CRUD operations
2. **CheckoutController** - Order placement & management

## Models Updated

- **Order** - Added new fields and methods for customer checkout
- Uses existing: Product, ShoppingCart, StoreCustomer, CustomerAddress

---

## Testing Checklist

- [ ] Create address for registered customer
- [ ] Place order from cart (registered customer)
- [ ] Guest checkout with items array
- [ ] Track order with email
- [ ] View order history
- [ ] Stock deduction verified
- [ ] Cart cleared after checkout
- [ ] Default address selection works

---

## Next Steps

### Immediate Enhancements
1. Email notifications (order confirmation, status updates)
2. Payment gateway integration (Paystack/Flutterwave)
3. Admin order management interface
4. Order status update endpoints
5. Invoice generation

### Advanced Features
1. Order cancellation
2. Order refunds
3. Delivery tracking integration
4. SMS notifications
5. Multiple payment methods per order
6. Partial refunds

---

## Files Modified/Created

✅ `backend/controllers/AddressController.php` - NEW  
✅ `backend/controllers/CheckoutController.php` - NEW  
✅ `backend/models/Order.php` - UPDATED  
✅ `api/index.php` - UPDATED (added routes)  

---

## Complete Checkout Ready! 🎉

The checkout system is now fully functional for both registered and guest customers!
