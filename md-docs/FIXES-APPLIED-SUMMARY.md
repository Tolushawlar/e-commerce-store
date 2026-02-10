# Cart Sync & Authentication Fixes - Summary

## What I Fixed

### 1. ✅ Fixed: bold-modern-product.html

**Changes:**

- Added `window.storeConfig` with `store_id` **before** loading scripts
- Fixed script paths from `cart.js` → `/app/assets/js/services/cart.service.js`
- Added cart badge initialization on page load
- Added event listeners for automatic badge updates

**Result:** Cart badge now shows correct count, updates when items added

### 2. ✅ Fixed: bold-modern.html (Homepage)

**Changes:**

- Moved `storeConfig` definition **before** script loading
- Added both `store_id` and `storeId` for compatibility
- Fixed script paths to correct locations
- Added cart badge initialization
- Removed references to non-existent `profile-header.js` and `store.js`

**Result:** Cart badge consistent across homepage and product pages

### 3. ✅ Fixed: customer-auth.js (Token Expiration)

**Added:**

- `validateToken()` method - Checks if JWT is expired
- Auto-logout on expired token
- `isAuthenticated()` now validates expiration, not just existence
- Logout with optional redirect to return after login

**Result:** No more "token expired" or "unauthorized" errors during checkout

### 4. ✅ Fixed: cart.service.js (Event Dispatching)

**Added:**

- `cartUpdated` custom event when cart changes
- Other pages can now listen for cart updates

**Result:** Real-time badge updates across tabs/pages

### 5. ✅ Created: cart-debug.js (Diagnostic Tool)

**Purpose:** Console script to identify cart sync issues

**Usage:**

```javascript
// Copy/paste entire cart-debug.js into browser console
// Or load it: <script src="/app/assets/js/utils/cart-debug.js"></script>
```

**Output:**

- Shows store configuration status
- Lists all cart localStorage keys
- Identifies problem keys like `cart_null`
- Shows authentication status
- Provides quick fix commands

---

## What Still Needs Fixing

### Templates to Update (Same fixes as bold-modern)

#### Priority 1: Core Templates

- [ ] bold-modern-cart.html
- [ ] bold-modern-checkout.html  
- [ ] bold-modern-login.html
- [ ] bold-modern-orders.html
- [ ] bold-modern-profile.html

#### Priority 2: Other Themes (40+ files)

- [ ] premium-luxury-*.html (8 files)
- [ ] classic-ecommerce-*.html (8 files)
- [ ] minimal-clean-*.html (8 files)
- [ ] campmart-style-*.html (8 files)
- [ ] cart.html, checkout.html, login.html (generic)

**For Each File:**

1. Add `window.storeConfig` before scripts
2. Fix script paths
3. Add cart badge initialization
4. Remove non-existent script references

---

## Testing Your Fixes

### 1. Test Cart Badge

```bash
# Open browser console, run diagnostic
<script src="/app/assets/js/utils/cart-debug.js"></script>

# Should show:
✅ storeConfig exists
✅ store_id: 1 (or your store ID)
✅ Expected cart key exists
```

### 2. Test Add to Cart

1. Visit product page
2. Check console - should see `store_id` logged
3. Add product to cart
4. Badge should update immediately (no refresh needed)
5. Visit cart page - item should be there

### 3. Test Cart Persistence

1. Add items to cart
2. Refresh page
3. Badge should show same count
4. Visit cart - items still there

### 4. Test Cross-Page Consistency

1. Add items on product page - note badge count
2. Go to homepage - badge should show same count
3. Go to cart - should see all items

### 5. Test Authentication

1. Login as customer
2. Make API call (add to cart as authenticated user)
3. Wait for token to expire (~1 hour typically)
4. Try to checkout
5. Should auto-logout and redirect to login
6. After login, should return to checkout

---

## Quick Fixes for Existing Issues

### Clean Invalid Cart Data

If you already have cart data under wrong keys:

```javascript
// Run in console
localStorage.removeItem('cart_null');
localStorage.removeItem('cart_undefined');

// Or clean ALL carts and start fresh
Object.keys(localStorage)
    .filter(k => k.startsWith('cart_'))
    .forEach(k => localStorage.removeItem(k));
```

### Fix Expired Token

```javascript
// Run in console
localStorage.removeItem('customer_token');
localStorage.removeItem('customer_data');
// Then refresh page
```

### Manually Set Store Config (Temporary)

If a page is missing storeConfig:

```javascript
// Run in console before using cart
window.storeConfig = {
    store_id: 1,  // Replace with your actual store ID
    storeName: 'Your Store',
    apiUrl: window.location.origin + '/api'
};
```

---

## How to Apply Fixes to Remaining Templates

### Template Pattern (Copy this for each file)

**Before `</body>` tag, add:**

```html
    <!-- Store Configuration (MUST be first) -->
    <script>
        window.API_BASE_URL = window.location.origin;
        window.storeConfig = {
            store_id: {{ store_id }},
            storeName: '{{ store_name }}',
            apiUrl: window.location.origin + '/api'
        };
    </script>
    
    <!-- Dependencies -->
    <script src="/app/assets/js/core/api.js"></script>
    <script src="/app/assets/js/services/cart.service.js"></script>
    <script src="/app/assets/js/store/customer-auth.js"></script>
    
    <!-- Cart Badge Initialization -->
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
            window.addEventListener('storage', updateCartBadge);
            window.addEventListener('cartUpdated', updateCartBadge);
        });
    </script>
</body>
</html>
```

**Replace these old patterns:**

```html
<!-- ❌ REMOVE -->
<script src="cart.js"></script>
<script src="checkout.js"></script>
<script src="customer-auth.js"></script>
<script src="product-detail.js"></script>
<script src="store.js"></script>
<script src="profile-header.js"></script>
```

---

## Automated Fix Script

Want to update all templates at once? Create a script:

```php
<?php
// update-templates.php
$templateDir = __DIR__ . '/store-templates';
$templates = glob($templateDir . '/*.html');

foreach ($templates as $template) {
    $content = file_get_contents($template);
    
    // Remove old script tags
    $content = str_replace('<script src="cart.js"></script>', '', $content);
    $content = str_replace('<script src="checkout.js"></script>', '', $content);
    $content = str_replace('<script src="customer-auth.js"></script>', '', $content);
    $content = str_replace('<script src="product-detail.js"></script>', '', $content);
    $content = str_replace('<script src="store.js"></script>', '', $content);
    $content = str_replace('<script src="profile-header.js"></script>', '', $content);
    
    // Add new configuration before </body>
    $newScripts = <<<'HTML'
    <!-- Store Configuration -->
    <script>
        window.API_BASE_URL = window.location.origin;
        window.storeConfig = {
            store_id: {{ store_id }},
            storeName: '{{ store_name }}',
            apiUrl: window.location.origin + '/api'
        };
    </script>
    
    <!-- Dependencies -->
    <script src="/app/assets/js/core/api.js"></script>
    <script src="/app/assets/js/services/cart.service.js"></script>
    <script src="/app/assets/js/store/customer-auth.js"></script>
    
    <!-- Cart Badge Init -->
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
            window.addEventListener('storage', updateCartBadge);
            window.addEventListener('cartUpdated', updateCartBadge);
        });
    </script>
</body>
HTML;
    
    $content = str_replace('</body>', $newScripts, $content);
    
    file_put_contents($template, $content);
    echo "Updated: " . basename($template) . "\n";
}

echo "All templates updated!\n";
```

---

## Verification Checklist

After applying fixes, verify:

- [ ] Console shows `window.storeConfig.store_id = <number>`
- [ ] No `cart_null` or `cart_undefined` in localStorage
- [ ] Cart badge updates when adding products
- [ ] Cart page shows all items
- [ ] Badge count same across all pages
- [ ] Refresh preserves cart
- [ ] Checkout works without "unauthorized" errors
- [ ] Expired token triggers auto-logout
- [ ] Login redirects back to intended page

---

## Support

If issues persist after fixes:

1. Run cart-debug.js and share output
2. Check browser console for errors
3. Verify template placeholders ({{ store_id }}) are being replaced
4. Check that files exist at new paths:
   - /app/assets/js/core/api.js
   - /app/assets/js/services/cart.service.js
   - /app/assets/js/store/customer-auth.js

For more details, see:

- CART-SYNC-FIX-GUIDE.md (comprehensive documentation)
- cart-debug.js (diagnostic tool)
