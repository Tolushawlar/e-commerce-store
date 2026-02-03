# Customer System - Quick Setup Guide

## Prerequisites
- Database already set up (ecommerce_platform)
- API server running on port 8000
- At least one store created in the database

---

## Step 1: Run Database Migration

```bash
# Navigate to project root
cd "c:\Users\Dell\OneDrive\Documents\LivePetal Projects\e-commerce-store"

# Run migration (Windows - MySQL)
mysql -u root -p ecommerce_platform < backend\database\add_customer_system.sql

# Enter your MySQL password when prompted
```

**Expected Output:**
```
status: Customer system migration completed successfully!
store_customers_count: 0
customer_addresses_count: 0
shopping_carts_count: 0
```

---

## Step 2: Verify Database Tables

```bash
# Login to MySQL
mysql -u root -p ecommerce_platform

# Check tables
SHOW TABLES;
```

**You should see:**
- ✅ store_customers
- ✅ customer_addresses
- ✅ shopping_carts
- ✅ orders (with new columns)

```sql
# Verify store_customers structure
DESC store_customers;

# Verify orders has new columns
DESC orders;

# Exit MySQL
EXIT;
```

---

## Step 3: Start Development Servers

```bash
# Start both API and App servers
npm run dev

# Or start individually:
# npm run api  # Port 8000
# npm run app  # Port 3000
```

**Expected Output:**
```
[API] PHP Development Server started on http://localhost:8000
[APP] PHP Development Server started on http://localhost:3000
```

---

## Step 4: Test with HTML Test Page

Open in browser:
```
http://localhost:3000/test-customer-system.html
```

**Update STORE_ID:**
- Open `test-customer-system.html`
- Change line: `const STORE_ID = 1;` to your actual store ID

**Test Flow:**
1. ✅ Register a new customer
2. ✅ Login with credentials
3. ✅ View profile
4. ✅ Add product to cart (need existing product ID)
5. ✅ View cart

---

## Step 5: Test with cURL (Alternative)

### 1. Register Customer
```bash
curl -X POST http://localhost:8000/api/stores/1/customers/register ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"john@example.com\",\"password\":\"password123\",\"first_name\":\"John\",\"last_name\":\"Doe\"}"
```

**Copy the token from response!**

### 2. Login
```bash
curl -X POST http://localhost:8000/api/stores/1/customers/login ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"john@example.com\",\"password\":\"password123\"}"
```

### 3. Get Profile (replace YOUR_TOKEN)
```bash
curl http://localhost:8000/api/stores/1/customers/me ^
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 4. Add to Cart (replace YOUR_TOKEN and product_id)
```bash
curl -X POST http://localhost:8000/api/stores/1/cart ^
  -H "Authorization: Bearer YOUR_TOKEN" ^
  -H "Content-Type: application/json" ^
  -d "{\"product_id\":1,\"quantity\":2}"
```

### 5. View Cart
```bash
curl http://localhost:8000/api/stores/1/cart ^
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## Step 6: Verify Data in Database

```sql
-- Login to MySQL
mysql -u root -p ecommerce_platform

-- Check registered customers
SELECT id, email, first_name, last_name, is_guest, status 
FROM store_customers;

-- Check cart items
SELECT 
    c.id,
    c.customer_id,
    c.product_id,
    c.quantity,
    p.name as product_name,
    p.price
FROM shopping_carts c
JOIN products p ON c.product_id = p.id;

-- Check customer with addresses
SELECT 
    c.id,
    c.email,
    c.first_name,
    COUNT(a.id) as address_count,
    COUNT(sc.id) as cart_items
FROM store_customers c
LEFT JOIN customer_addresses a ON c.id = a.customer_id
LEFT JOIN shopping_carts sc ON c.id = sc.customer_id
GROUP BY c.id;
```

---

## Common Issues & Solutions

### Issue 1: "Table already exists"
**Solution:** Migration is idempotent. Safe to run multiple times.

### Issue 2: "Foreign key constraint fails"
**Solution:** 
```sql
-- Check if stores table has data
SELECT * FROM stores LIMIT 5;

-- Use existing store_id in API calls
```

### Issue 3: "Product not found" when adding to cart
**Solution:**
```sql
-- Find available products
SELECT id, name, price, stock_quantity, store_id, status 
FROM products 
WHERE status = 'active' AND stock_quantity > 0
LIMIT 5;

-- Use product_id from this query
```

### Issue 4: "Unauthorized" error
**Solution:**
- Check token is included: `Authorization: Bearer TOKEN`
- Token might be expired (7 days)
- Re-login to get new token

### Issue 5: CORS errors in browser
**Solution:** Already handled by existing `CorsMiddleware`

---

## API Endpoints Quick Reference

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/stores/{id}/customers/register` | No | Register customer |
| POST | `/api/stores/{id}/customers/login` | No | Login customer |
| GET | `/api/stores/{id}/customers/me` | Yes | Get profile |
| PUT | `/api/stores/{id}/customers/me` | Yes | Update profile |
| POST | `/api/stores/{id}/customers/change-password` | Yes | Change password |
| GET | `/api/stores/{id}/cart` | Yes | Get cart |
| POST | `/api/stores/{id}/cart` | Yes | Add to cart |
| PUT | `/api/stores/{id}/cart/{item_id}` | Yes | Update quantity |
| DELETE | `/api/stores/{id}/cart/{item_id}` | Yes | Remove item |

---

## Next: Build Checkout Flow

With customer system working, next steps are:

1. **Address Management Controller**
   - Add/edit/delete addresses
   - Set default address

2. **Checkout Controller**
   - Create order from cart
   - Validate stock
   - Calculate totals
   - Process payment

3. **Order Management**
   - View order history
   - Track orders
   - Update order status

---

## Files Created

- ✅ `backend/database/add_customer_system.sql`
- ✅ `backend/models/StoreCustomer.php`
- ✅ `backend/models/CustomerAddress.php`
- ✅ `backend/models/ShoppingCart.php`
- ✅ `backend/services/CustomerJWTService.php`
- ✅ `backend/controllers/CustomerController.php`
- ✅ `backend/controllers/CartController.php`
- ✅ `api/index.php` (updated with routes)
- ✅ `md-docs/CUSTOMER-SYSTEM.md`
- ✅ `md-docs/CUSTOMER-IMPLEMENTATION-SUMMARY.md`
- ✅ `test-customer-system.html`

---

## Success Checklist

- [ ] Migration ran successfully
- [ ] All 3 tables created
- [ ] Can register customer via API
- [ ] Can login and get token
- [ ] Can view profile with token
- [ ] Can add products to cart
- [ ] Can view cart items
- [ ] Can update cart quantities

**All checked?** ✅ Customer system is ready!

---

## Support

If you encounter issues:

1. Check API is running: `http://localhost:8000/api/health`
2. Check MySQL connection in `backend/config/config.php`
3. Check error logs in browser console / terminal
4. Verify store_id exists in database
5. Ensure products exist with `status='active'`

Happy coding! 🚀
