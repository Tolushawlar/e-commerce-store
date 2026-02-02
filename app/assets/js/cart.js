/**
 * Shopping Cart Service for Generated Stores
 * Handles cart operations with localStorage and API integration
 */

if (typeof API_BASE_URL === "undefined") {
  var API_BASE_URL = window.location.origin;
}

const CartService = {
  /**
   * Get the current store ID from config
   */
  getStoreId() {
    return window.storeConfig?.store_id || window.storeConfig?.storeId || null;
  },

  /**
   * Get cart items from localStorage
   */
  getCart() {
    try {
      const storeId = this.getStoreId();
      const key = storeId ? `cart_${storeId}` : "cart";
      const cart = localStorage.getItem(key);
      return cart ? JSON.parse(cart) : [];
    } catch (error) {
      console.error("Error reading cart:", error);
      return [];
    }
  },

  /**
   * Save cart to localStorage
   */
  saveCart(cart) {
    try {
      const storeId = this.getStoreId();
      const key = storeId ? `cart_${storeId}` : "cart";
      localStorage.setItem(key, JSON.stringify(cart));
      this.updateCartBadge();
      return true;
    } catch (error) {
      console.error("Error saving cart:", error);
      return false;
    }
  },

  /**
   * Add item to cart
   */
  addItem(productId, quantity = 1) {
    const cart = this.getCart();
    const existingItem = cart.find((item) => item.productId === productId);

    if (existingItem) {
      existingItem.quantity += quantity;
    } else {
      cart.push({
        productId: productId,
        quantity: quantity,
        addedAt: new Date().toISOString(),
      });
    }

    this.saveCart(cart);
    return true;
  },

  /**
   * Update item quantity
   */
  updateQuantity(productId, quantity) {
    const cart = this.getCart();
    const item = cart.find((item) => item.productId === productId);

    if (item) {
      if (quantity <= 0) {
        return this.removeItem(productId);
      }
      item.quantity = quantity;
      this.saveCart(cart);
      return true;
    }
    return false;
  },

  /**
   * Remove item from cart
   */
  removeItem(productId) {
    let cart = this.getCart();
    cart = cart.filter((item) => item.productId !== productId);
    this.saveCart(cart);
    return true;
  },

  /**
   * Clear entire cart
   */
  clearCart() {
    const storeId = this.getStoreId();
    const key = storeId ? `cart_${storeId}` : "cart";
    localStorage.removeItem(key);
    this.updateCartBadge();
    return true;
  },

  /**
   * Get cart item count
   */
  getItemCount() {
    const cart = this.getCart();
    return cart.reduce((total, item) => total + item.quantity, 0);
  },

  /**
   * Get cart with full product details from API
   */
  async getCartWithDetails() {
    const cart = this.getCart();
    if (cart.length === 0) {
      return [];
    }

    const productIds = cart.map((item) => item.productId);
    const cartWithDetails = [];

    // Fetch product details for each item
    for (const item of cart) {
      try {
        const response = await fetch(
          `${API_BASE_URL}/api/products/${item.productId}`,
        );
        const data = await response.json();

        if (data.success && data.data) {
          cartWithDetails.push({
            ...item,
            product: data.data,
          });
        }
      } catch (error) {
        console.error(`Error fetching product ${item.productId}:`, error);
      }
    }

    return cartWithDetails;
  },

  /**
   * Calculate cart totals
   */
  async calculateTotals() {
    const cartItems = await this.getCartWithDetails();

    let subtotal = 0;
    let itemCount = 0;

    cartItems.forEach((item) => {
      const price = parseFloat(item.product.price);
      const quantity = parseInt(item.quantity);
      subtotal += price * quantity;
      itemCount += quantity;
    });

    const tax = subtotal * 0.075; // 7.5% VAT
    const shipping = subtotal > 0 ? 1500 : 0; // ₦1,500 flat shipping
    const total = subtotal + tax + shipping;

    return {
      subtotal,
      tax,
      shipping,
      total,
      itemCount,
    };
  },

  /**
   * Validate cart items (check stock availability)
   */
  async validateCart() {
    const cartItems = await this.getCartWithDetails();
    const issues = [];

    cartItems.forEach((item) => {
      if (!item.product) {
        issues.push({
          productId: item.productId,
          message: "Product not found",
        });
      } else if (item.product.stock_quantity < item.quantity) {
        issues.push({
          productId: item.productId,
          message: `Only ${item.product.stock_quantity} items available`,
          availableStock: item.product.stock_quantity,
        });
      } else if (item.product.status !== "active") {
        issues.push({
          productId: item.productId,
          message: "Product is no longer available",
        });
      }
    });

    return {
      isValid: issues.length === 0,
      issues,
    };
  },

  /**
   * Update cart badge in UI
   */
  updateCartBadge() {
    const badge = document.getElementById("cart-badge");
    const count = this.getItemCount();

    if (badge) {
      badge.textContent = count;
      if (count > 0) {
        badge.classList.remove("hidden");
      } else {
        badge.classList.add("hidden");
      }
    }
  },

  /**
   * Format currency (Nigerian Naira)
   */
  formatCurrency(amount) {
    return (
      "₦" +
      Number(amount).toLocaleString("en-NG", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })
    );
  },
};

// Initialize cart badge on page load
document.addEventListener("DOMContentLoaded", () => {
  CartService.updateCartBadge();
});

// Make available globally
window.CartService = CartService;
