/**
 * Cart Service
 * Handles all cart-related API operations for customers
 */
class CartService {
  constructor(apiClient) {
    this.apiClient = apiClient;
  }

  /**
   * Get cart items for a specific store
   * @param {number} storeId - The store ID
   * @returns {Promise} Cart data with items
   */
  async getCart(storeId) {
    try {
      const response = await this.apiClient.get(`/api/stores/${storeId}/cart`);
      return response;
    } catch (error) {
      console.error("Error fetching cart:", error);
      throw error;
    }
  }

  /**
   * Add item to cart
   * @param {number} storeId - The store ID
   * @param {number} productId - The product ID
   * @param {number} quantity - Quantity to add
   * @returns {Promise} Updated cart data
   */
  async addItem(storeId, productId, quantity = 1) {
    try {
      const response = await this.apiClient.post(
        `/api/stores/${storeId}/cart/items`,
        {
          product_id: productId,
          quantity: quantity,
        },
      );
      return response;
    } catch (error) {
      console.error("Error adding item to cart:", error);
      throw error;
    }
  }

  /**
   * Update cart item quantity
   * @param {number} storeId - The store ID
   * @param {number} itemId - The cart item ID
   * @param {number} quantity - New quantity
   * @returns {Promise} Updated cart data
   */
  async updateQuantity(storeId, itemId, quantity) {
    try {
      const response = await this.apiClient.put(
        `/api/stores/${storeId}/cart/items/${itemId}`,
        {
          quantity: quantity,
        },
      );
      return response;
    } catch (error) {
      console.error("Error updating cart item:", error);
      throw error;
    }
  }

  /**
   * Remove item from cart
   * @param {number} storeId - The store ID
   * @param {number} itemId - The cart item ID
   * @returns {Promise} Updated cart data
   */
  async removeItem(storeId, itemId) {
    try {
      const response = await this.apiClient.delete(
        `/api/stores/${storeId}/cart/items/${itemId}`,
      );
      return response;
    } catch (error) {
      console.error("Error removing cart item:", error);
      throw error;
    }
  }

  /**
   * Clear entire cart
   * @param {number} storeId - The store ID
   * @returns {Promise} Success response
   */
  async clearCart(storeId) {
    try {
      const response = await this.apiClient.delete(
        `/api/stores/${storeId}/cart`,
      );
      return response;
    } catch (error) {
      console.error("Error clearing cart:", error);
      throw error;
    }
  }

  /**
   * Sync cart for authenticated users
   * @param {number} storeId - The store ID
   * @param {Array} items - Cart items to sync
   * @returns {Promise} Updated cart data
   */
  async syncCart(storeId, items) {
    try {
      const response = await this.apiClient.post(
        `/api/stores/${storeId}/cart/sync`,
        {
          items: items,
        },
      );
      return response;
    } catch (error) {
      console.error("Error syncing cart:", error);
      throw error;
    }
  }

  /**
   * Calculate cart totals
   * @param {Array} items - Cart items
   * @returns {Object} Totals object with subtotal, shipping, tax, total
   */
  calculateTotals(items) {
    const subtotal = items.reduce((sum, item) => {
      return sum + parseFloat(item.price) * parseInt(item.quantity);
    }, 0);

    // Shipping calculation (can be customized based on business logic)
    const shipping = subtotal > 0 ? (subtotal >= 10000 ? 0 : 1500) : 0; // Free shipping above ₦10,000

    // Tax calculation (if applicable)
    const tax = 0; // Set to 0 for now, can be percentage of subtotal

    const total = subtotal + shipping + tax;

    return {
      subtotal,
      shipping,
      tax,
      total,
      itemCount: items.reduce((sum, item) => sum + parseInt(item.quantity), 0),
    };
  }

  /**
   * Get cart item count from localStorage for badge display
   * @param {number} storeId - The store ID
   * @returns {number} Total item count
   */
  getLocalCartCount(storeId) {
    try {
      const cartKey = `cart_${storeId}`;
      const cart = JSON.parse(localStorage.getItem(cartKey) || "[]");
      return cart.reduce((sum, item) => sum + parseInt(item.quantity || 0), 0);
    } catch (error) {
      console.error("Error getting local cart count:", error);
      return 0;
    }
  }

  /**
   * Save cart to localStorage (for guest users)
   * @param {number} storeId - The store ID
   * @param {Array} items - Cart items
   */
  saveLocalCart(storeId, items) {
    try {
      const cartKey = `cart_${storeId}`;
      localStorage.setItem(cartKey, JSON.stringify(items));
      this.updateCartBadge(storeId);
    } catch (error) {
      console.error("Error saving local cart:", error);
    }
  }

  /**
   * Get cart from localStorage (for guest users)
   * @param {number} storeId - The store ID
   * @returns {Array} Cart items
   */
  getLocalCart(storeId) {
    try {
      const cartKey = `cart_${storeId}`;
      return JSON.parse(localStorage.getItem(cartKey) || "[]");
    } catch (error) {
      console.error("Error getting local cart:", error);
      return [];
    }
  }

  /**
   * Update cart badge count in UI
   * @param {number} storeId - The store ID
   */
  updateCartBadge(storeId) {
    const count = this.getLocalCartCount(storeId);
    const badges = document.querySelectorAll(".cart-badge");
    badges.forEach((badge) => {
      badge.textContent = count;
      badge.classList.toggle("hidden", count === 0);
    });
  }

  /**
   * Format currency
   * @param {number} amount - Amount to format
   * @returns {string} Formatted currency string
   */
  formatCurrency(amount) {
    return new Intl.NumberFormat("en-NG", {
      style: "currency",
      currency: "NGN",
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    }).format(amount);
  }

  /**
   * Validate stock availability
   * @param {Object} item - Cart item
   * @param {number} requestedQuantity - Requested quantity
   * @returns {Object} Validation result
   */
  validateStock(item, requestedQuantity) {
    const available = parseInt(item.stock_quantity || 0);
    const isAvailable = available >= requestedQuantity;

    return {
      isValid: isAvailable,
      available: available,
      requested: requestedQuantity,
      message: isAvailable ? "In stock" : `Only ${available} available`,
    };
  }

  /**
   * Add product to cart (handles both local and API)
   * @param {number} storeId - The store ID
   * @param {Object} product - Product object
   * @param {number} quantity - Quantity to add
   * @param {boolean} isAuthenticated - Whether user is authenticated
   * @returns {Promise|Object} Result
   */
  async addToCart(storeId, product, quantity = 1, isAuthenticated = false) {
    // Validate stock
    const validation = this.validateStock(product, quantity);
    if (!validation.isValid) {
      throw new Error(validation.message);
    }

    if (isAuthenticated) {
      // Use API
      return await this.addItem(storeId, product.id, quantity);
    } else {
      // Use localStorage
      const cart = this.getLocalCart(storeId);
      const existingIndex = cart.findIndex(
        (item) => item.product_id === product.id,
      );

      if (existingIndex >= 0) {
        // Update existing item
        const newQuantity = cart[existingIndex].quantity + quantity;
        const stockValidation = this.validateStock(product, newQuantity);

        if (!stockValidation.isValid) {
          throw new Error(stockValidation.message);
        }

        cart[existingIndex].quantity = newQuantity;
      } else {
        // Add new item
        cart.push({
          product_id: product.id,
          product_name: product.name,
          price: product.price,
          quantity: quantity,
          image_url: product.image_url,
          stock_quantity: product.stock_quantity,
        });
      }

      this.saveLocalCart(storeId, cart);
      return { success: true, cart };
    }
  }
}

// Export for use in other scripts
if (typeof module !== "undefined" && module.exports) {
  module.exports = CartService;
}
