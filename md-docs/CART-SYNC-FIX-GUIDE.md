# Cart Sync & Authentication Issues - Fix Guide

## Issues Identified

### 1. Missing JavaScript Dependencies in Templates

**Problem**: Templates reference JavaScript files that don't exist in the same directory:

- `cart.js` ❌
- `checkout.js` ❌
- `customer-auth.js` ❌  
- `product-detail.js` ❌

**Actual Location**: `app/assets/js/store/` and `app/assets/js/services/`

**Impact**:

- Cart functionality broken
- Authentication fails
- Product pages can't add to cart
- Cart badge doesn't update

### 2. Missing `store_id` Configuration

**Problem**: Most templates don't set `window.storeConfig` before using cart/auth services

**Example from bold-modern-product.html**:

```html
<!-- ❌ NO storeConfig defined -->
<script src="cart.js"></script>
<script src="product-detail.js"></script>
```

**Impact**:

- `CustomerAuth.getStoreId()` returns `null`
- localStorage uses wrong keys: `cart_null`, `cart_undefined`
- Different pages use different localStorage keys
- Cart appears empty despite badge showing counts

### 3. Cart Badge Not Initialized

**Problem**: Cart badge HTML exists but no code to update on page load

**Current State**:

```html
<span id="cart-badge" class="...hidden">0</span>
<!-- NO initialization script! -->
```

**Impact**:

- Badge shows wrong count or "0"
- Inconsistent across pages
- Old items counted but not visible

### 4. Token Expiration Handling

**Problem**: No token refresh mechanism, expired tokens not cleared

**Impact**:

- "Token expired" alerts
- "Unauthorized" errors in checkout
- Can't place orders despite being "logged in"

---

## Solution 1: Fix JavaScript File Paths

### Files to Update

All store template HTML files that reference these scripts

### Changes Required

**❌ Before**:

```html
<script src="cart.js"></script>
<script src="checkout.js"></script>
<script src="customer-auth.js"></script>
<script src="product-detail.js"></script>
```

**✅ After**:

```html
<!-- Core API Client -->
<script src="/app/assets/js/core/api.js"></script>

<!-- Services -->
<script src="/app/assets/js/services/cart.service.js"></script>

<!-- Store Scripts -->
<script src="/app/assets/js/store/customer-auth.js"></script>
<script src="/app/assets/js/store/product-detail.js"></script>
```

---

## Solution 2: Add Store Configuration

### Add to ALL Templates (Before other scripts)

```html
<script>
    // CRITICAL: Define store config BEFORE loading other scripts
    window.API_BASE_URL = window.location.origin;
    window.storeConfig = {
        store_id: {{ store_id }},
        storeName: '{{ store_name }}',
        apiUrl: window.location.origin + '/api'
    };
</script>

<!-- Then load dependencies -->
<script src="/app/assets/js/core/api.js"></script>
<script src="/app/assets/js/services/cart.service.js"></script>
<script src="/app/assets/js/store/customer-auth.js"></script>
```

---

## Solution 3: Initialize Cart Badge on All Pages

### Add to Product Pages, Home Pages, etc

```html
<script>
    // Initialize cart badge on page load
    document.addEventListener('DOMContentLoaded', function() {
        const storeId = window.storeConfig.store_id;
        const cartKey = `cart_${storeId}`;
        
        // Get cart count from localStorage
        function updateCartBadge() {
            try {
                const cart = JSON.parse(localStorage.getItem(cartKey) || '[]');
                const count = cart.reduce((sum, item) => sum + parseInt(item.quantity || 0), 0);
                
                const badge = document.getElementById('cart-badge');
                if (badge) {
                    badge.textContent = count;
                    badge.classList.toggle('hidden', count === 0);
                }
            } catch (error) {
                console.error('Error updating cart badge:', error);
            }
        }
        
        updateCartBadge();
        
        // Listen for cart updates
        window.addEventListener('storage', updateCartBadge);
        window.addEventListener('cartUpdated', updateCartBadge);
    });
</script>
```

---

## Solution 4: Fix Token Expiration Handling

### Update customer-auth.js

Add token validation and auto-logout:

```javascript
// Add to CustomerAuth object
validateToken: function() {
    const token = this.getToken();
    if (!token) return false;
    
    try {
        // Decode JWT to check expiration
        const payload = JSON.parse(atob(token.split('.')[1]));
        const isExpired = payload.exp * 1000 < Date.now();
        
        if (isExpired) {
            this.logout();
            return false;
        }
        return true;
    } catch (error) {
        console.error('Error validating token:', error);
        this.logout();
        return false;
    }
},

// Update isAuthenticated
isAuthenticated() {
    return this.validateToken();
},
```

### Make API Calls Handle 401 Errors

In cart.service.js and API client, add interceptor:

```javascript
async makeRequest(url, options = {}) {
    const response = await fetch(url, options);
    
    // Handle unauthorized
    if (response.status === 401) {
        // Token expired, clear auth and redirect
        localStorage.removeItem('customer_token');
        localStorage.removeItem('customer_data');
        
        // Redirect to login with return URL
        const returnUrl = encodeURIComponent(window.location.pathname);
        window.location.href = `login.html?redirect=${returnUrl}`;
        return;
    }
    
    return response;
}
```

---

## Solution 5: Cart Sync on Page Load

### Fix Cart Display Issues

Update cart page to properly load from localStorage:

```javascript
async function loadCart() {
    const storeId = window.storeConfig.store_id;
    const isAuth = CustomerAuth.isAuthenticated();
    
    let cartItems = [];
    
    if (isAuth) {
        // Try to fetch from API
        try {
            const response = await CartService.getCart(storeId);
            if (response.success) {
                cartItems = response.data.items || [];
            }
        } catch (error) {
            console.warn('Failed to load cart from API, using local:', error);
            cartItems = getLocalCart(storeId);
        }
    } else {
        // Load from localStorage
        cartItems = getLocalCart(storeId);
    }
    
    displayCart(cartItems);
}

function getLocalCart(storeId) {
    const cartKey = `cart_${storeId}`;
    return JSON.parse(localStorage.getItem(cartKey) || '[]');
}
```

---

## Solution 6: Unified localStorage Key Strategy

### Enforce Consistent Keys

```javascript
// In cart.service.js
getCartKey(storeId) {
    if (!storeId || storeId === 'null' || storeId === 'undefined') {
        console.error('Invalid store ID:', storeId);
        throw new Error('Store ID is required for cart operations');
    }
    return `cart_${storeId}`;
}
```

---

## Implementation Priority

### Phase 1: Critical Fixes (Do First)

1. ✅ Add `window.storeConfig` to all templates
2. ✅ Fix JavaScript file paths
3. ✅ Add cart badge initialization

### Phase 2: Cart Functionality

4. ✅ Fix localStorage key consistency
2. ✅ Add cart sync on page load
3. ✅ Test add-to-cart flow

### Phase 3: Authentication

7. ✅ Add token validation
2. ✅ Handle 401 errors gracefully
3. ✅ Add token refresh (optional enhancement)

---

## Testing Checklist

### Cart Functionality

- [ ] Visit product page → Cart badge shows correct count
- [ ] Add product → Badge updates immediately
- [ ] Navigate to cart → All items visible
- [ ] Cart count consistent across all pages
- [ ] Refresh page → Cart persists
- [ ] Add from different pages → Items accumulate

### Authentication

- [ ] Login → No "token expired" errors
- [ ] Place order while logged in → Success
- [ ] Token expires → Auto logout, redirect to login
- [ ] Login → Redirects back to intended page

### Multi-Store

- [ ] Visit Store A → Add items
- [ ] Visit Store B → Different cart
- [ ] Return to Store A → Original cart intact

---

## Example: Complete Product Page Template

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ product_name }} - {{ store_name }}</title>
</head>
<body>
    <!-- Header with cart badge -->
    <a href="cart.html">
        <span id="cart-badge" class="hidden">0</span>
    </a>
    
    <!-- Product content -->
    
    <!-- 1. Configure store FIRST -->
    <script>
        window.API_BASE_URL = window.location.origin;
        window.storeConfig = {
            store_id: {{ store_id }},
            storeName: '{{ store_name }}',
            apiUrl: window.location.origin + '/api'
        };
    </script>
    
    <!-- 2. Load dependencies in correct order -->
    <script src="/app/assets/js/core/api.js"></script>
    <script src="/app/assets/js/services/cart.service.js"></script>
    <script src="/app/assets/js/store/customer-auth.js"></script>
    <script src="/app/assets/js/store/product-detail.js"></script>
    
    <!-- 3. Initialize cart badge -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const storeId = window.storeConfig.store_id;
            const cartKey = `cart_${storeId}`;
            
            function updateCartBadge() {
                try {
                    const cart = JSON.parse(localStorage.getItem(cartKey) || '[]');
                    const count = cart.reduce((sum, item) => sum + parseInt(item.quantity || 0), 0);
                    
                    const badge = document.getElementById('cart-badge');
                    if (badge) {
                        badge.textContent = count;
                        badge.classList.toggle('hidden', count === 0);
                    }
                } catch (error) {
                    console.error('Error updating cart badge:', error);
                }
            }
            
            updateCartBadge();
            window.addEventListener('cartUpdated', updateCartBadge);
        });
    </script>
</body>
</html>
```

---

## Files That Need Updates

### Templates Directory (`store-templates/`)

All `.html` files need the fixes:

- bold-modern-*.html (8 files)
- premium-luxury-*.html (8 files)
- classic-ecommerce-*.html (8 files)  
- minimal-clean-*.html (8 files)
- campmart-style-*.html (8 files)
- cart.html, checkout.html, etc.

### JavaScript Files (`app/assets/js/`)

- `store/customer-auth.js` - Add token validation
- `services/cart.service.js` - Add error handling
- `core/api.js` - Add 401 interceptor

---

## Quick Fix Script

You can run this script to see localStorage issues:

```javascript
// Run in browser console
console.log('=== Cart Debug Info ===');
console.log('Store ID:', window.storeConfig?.store_id);
console.log('All localStorage keys:', Object.keys(localStorage));
console.log('Cart keys:', Object.keys(localStorage).filter(k => k.startsWith('cart_')));

// Show all cart data
Object.keys(localStorage)
    .filter(k => k.startsWith('cart_'))
    .forEach(key => {
        const data = JSON.parse(localStorage.getItem(key));
        console.log(`${key}:`, data);
    });
```

This will reveal if you have `cart_null`, `cart_undefined`, or multiple cart keys.
