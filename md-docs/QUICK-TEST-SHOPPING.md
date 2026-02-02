# Quick Start: Testing Customer Shopping Features

## Prerequisites
- E-commerce backend running on `http://localhost:8000`
- At least one store created in the system
- Sample products added to the store

## Step-by-Step Test Guide

### 1. Create or Regenerate a Store

**Via API (using Postman or curl):**
```bash
# Create new store
curl -X POST http://localhost:8000/api/stores \
  -H "Content-Type: application/json" \
  -d '{
    "client_id": 1,
    "store_name": "Demo Shop",
    "store_slug": "demo-shop",
    "primary_color": "#064E3B",
    "accent_color": "#BEF264",
    "show_cart": true
  }'
```

**Or update existing store:**
```bash
curl -X PUT http://localhost:8000/api/stores/1 \
  -H "Content-Type: application/json" \
  -d '{"store_name": "Updated Shop"}'
```

This will automatically generate all shopping pages!

### 2. Access Your Generated Store

Open in browser:
```
http://localhost:8000/api/stores/store-1/index.html
```

### 3. Test the Shopping Flow

#### A. Browse Products (index.html)
- ✅ Products load from API
- ✅ Products display with images, prices
- ✅ Cart badge in header (should show "0")
- ✅ Click on a product card

#### B. View Product Details (product.html)
- ✅ Product details load
- ✅ Images display in carousel
- ✅ Stock status shows
- ✅ Click "Add to Cart" button
- ✅ See success toast notification
- ✅ Cart badge updates to "1"

#### C. View Shopping Cart (cart.html)
Click cart badge in header:
- ✅ Cart items display with product details
- ✅ Quantity controls work (+/-)
- ✅ Remove item works
- ✅ Subtotal, tax, shipping calculate correctly
- ✅ Click "Proceed to Checkout"

#### D. Checkout Process (checkout.html)
**Step 1: Contact Information**
- ✅ Fill out: First Name, Last Name, Email, Phone
- ✅ Validation works on required fields
- ✅ Click "Continue to Shipping"

**Step 2: Shipping Address**
- ✅ Fill out: Address, City, State, Postal Code
- ✅ Validation works
- ✅ Click "Continue to Payment"

**Step 3: Payment Method**
- ✅ Select payment method (card/transfer/COD)
- ✅ Optional: Add order notes
- ✅ Order summary shows on right sidebar
- ✅ Click "Place Order"

#### E. Order Confirmation (order-success.html)
- ✅ Success animation displays
- ✅ Order number shows
- ✅ Order items listed
- ✅ Shipping address displays
- ✅ Total amount correct
- ✅ Cart badge resets to "0"
- ✅ "Continue Shopping" returns to store

### 4. Verify Files Were Generated

Check the store directory:
```bash
ls api/stores/store-1/
```

Should contain:
```
cart.html
cart.js
checkout.html
checkout.js
config.json
index.html
order-success.html
product.html
product-detail.js
store.js
```

### 5. Test Edge Cases

#### Empty Cart
- Navigate to cart.html directly
- Should see "Your cart is empty" message
- "Browse Products" button works

#### Out of Stock Product
- Try adding product with 0 stock
- Should show "Unavailable" button
- Cannot add to cart

#### Maximum Stock
- Add max quantity to cart
- Try to increase quantity
- Should show "Maximum stock reached"

#### Validation Errors
- Try submitting checkout without required fields
- Should show red error messages
- Should not proceed to next step

### 6. Browser Console Checks

Open DevTools Console and verify:
```javascript
// Cart service is loaded
typeof CartService !== 'undefined'  // should be true

// Checkout service is loaded (on checkout.html)
typeof CheckoutService !== 'undefined'  // should be true

// Store config is set
console.log(window.storeConfig)  // should show store ID and settings

// Check cart contents
console.log(CartService.getCart())  // should show cart array

// Check cart count
console.log(CartService.getItemCount())  // should show number
```

### 7. LocalStorage Inspection

Open DevTools → Application → Local Storage → http://localhost:8000

Check for:
- `cart` - Array of cart items
- `checkout_state` - Checkout progress (during checkout)

### 8. API Endpoint Tests

Verify these endpoints work:

**Products:**
```
GET http://localhost:8000/api/products?store_id=1
```

**Single Product:**
```
GET http://localhost:8000/api/products/1
```

**Create Order:**
```
POST http://localhost:8000/api/orders
{
  "store_id": 1,
  "customer_email": "test@example.com",
  ...
}
```

## Common Issues & Fixes

### Issue: Cart badge not showing
**Fix:** Ensure `cart.js` is loaded in page:
```html
<script src="cart.js"></script>
```

### Issue: Products not loading
**Fix:** Check `storeConfig.store_id` is set:
```javascript
console.log(window.storeConfig.store_id)
```

### Issue: Checkout doesn't submit
**Fix:** Open Network tab and check for:
- 400/422 errors → validation issue
- 500 errors → server issue
- CORS errors → API configuration

### Issue: Order success page shows "loading"
**Fix:** Check URL has order ID parameter:
```
order-success.html?order=123
```

### Issue: Styles not applied
**Fix:** Verify Tailwind CDN loads:
```html
<script src="https://cdn.tailwindcss.com"></script>
```

## Quick Test Checklist

- [ ] Store generates successfully
- [ ] Products display on index.html
- [ ] Add to cart works
- [ ] Cart badge updates
- [ ] Cart page shows items
- [ ] Quantity controls work
- [ ] Checkout Step 1 validates
- [ ] Checkout Step 2 validates  
- [ ] Checkout Step 3 submits
- [ ] Order confirmation displays
- [ ] Cart clears after order
- [ ] All pages styled correctly
- [ ] Mobile responsive design works

## Demo Data

### Sample Product Add (via API)
```json
{
  "store_id": 1,
  "name": "Test Product",
  "description": "A sample product for testing",
  "price": 15000,
  "stock_quantity": 10,
  "category_id": 1,
  "status": "active",
  "images": ["https://via.placeholder.com/400"]
}
```

### Sample Order Data
```json
{
  "store_id": 1,
  "customer_name": "John Doe",
  "customer_email": "john@example.com",
  "customer_phone": "08012345678",
  "shipping_address": "123 Main St",
  "shipping_city": "Lagos",
  "shipping_state": "Lagos",
  "shipping_postal_code": "100001",
  "payment_method": "card",
  "items": [
    {
      "product_id": 1,
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

## Next Steps

After successful testing:
1. Add more products to your store
2. Customize store colors and branding
3. Test with different templates
4. Integrate payment gateway
5. Set up email notifications
6. Deploy to production

---

**Need Help?** Check [CUSTOMER-SHOPPING-INTEGRATION.md](./CUSTOMER-SHOPPING-INTEGRATION.md) for detailed documentation.
