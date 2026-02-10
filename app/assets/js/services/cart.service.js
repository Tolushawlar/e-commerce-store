/**
 * Cart Service
 * Handles all cart-related API operations for customers
 */
class CartService {
  
  /**
   * Validate and get store ID
   * @param {number} storeId - Optional store ID
   * @returns {number} Valid store ID
   * @throws {Error} If store ID cannot be determined
   */
  _validateStoreId(storeId = null) {
    if (storeId) return storeId;
    
    if (!window.storeConfig || !window.storeConfig.store_id) {
      throw new Error('Store configuration not found. Please ensure window.storeConfig is set.');
    }
    
    return window.storeConfig.store_id;
  }
  
  /**
   * Get API URL from config
   * @returns {string} API URL
   * @throws {Error} If config is missing
   */
  _getApiUrl() {
    if (!window.storeConfig || !window.storeConfig.apiUrl) {
      if (!window.API_BASE_URL) {
        throw new Error('API URL not configured. Please set window.storeConfig.apiUrl or window.API_BASE_URL');
      }
      return window.API_BASE_URL + '/api';
    }
    return window.storeConfig.apiUrl;
  }
  
  /**
   * Get cart items for a specific store
   * @param {number} storeId - The store ID
   * @returns {Promise} Cart data with items
   */
  async getCart(storeId) {
    try {
      const apiUrl = this._getApiUrl();
      const headers = {
        'Content-Type': 'application/json'
      };
      
      // Add authentication token if available
      const token = typeof CustomerAuth !== 'undefined' ? CustomerAuth.getToken() : null;
      console.log('[CartService] getCart - token:', token ? 'Present' : 'Missing');
      if (token) {
        headers['Authorization'] = `Bearer ${token}`;
      }
      
      const response = await fetch(
        `${apiUrl}/stores/${storeId}/cart`,
        { headers }
      );
      const result = await response.json();
      console.log('[CartService] getCart response:', result);
      return result;
    } catch (error) {
      console.error("Error fetching cart:", error);
      throw error;
    }
  }

  /**
   * Get cart items with full product details
   * Handles both authenticated (API) and guest (localStorage) users
   * @param {number} storeId - The store ID (optional, uses window.storeConfig if not provided)
   * @param {boolean} isAuthenticated - Whether user is authenticated (optional, auto-detected)
   * @returns {Promise<Array>} Array of cart items with product details
   */
  async getCartWithDetails(storeId = null, isAuthenticated = null) {
    try {
      // Auto-detect storeId from window.storeConfig if not provided
      storeId = this._validateStoreId(storeId);
      
      if (!storeId) {
        throw new Error("Store ID is required");
      }

      // Auto-detect authentication status if not provided
      if (isAuthenticated === null && typeof CustomerAuth !== 'undefined') {
        isAuthenticated = CustomerAuth.isAuthenticated();
      }

      console.log('[CartService] getCartWithDetails - storeId:', storeId, 'isAuthenticated:', isAuthenticated);

      if (isAuthenticated) {
        // Get cart from API for authenticated users
        console.log('[CartService] Fetching cart from API...');
        const response = await this.getCart(storeId);
        console.log('[CartService] API response:', response);
        
        if (!response.success || !response.data || !response.data.items) {
          console.log('[CartService] No cart items found in API response');
          return [];
        }

        // Transform API response to expected format
        return response.data.items.map(item => ({
          id: item.id,
          product_id: item.product_id,
          quantity: item.quantity,
          product: {
            id: item.product_id,
            name: item.product_name,
            description: item.product_description,
            price: parseFloat(item.product_price),
            image_url: item.product_image,
            stock_quantity: item.stock_quantity,
            category_name: item.category_name || '',
            images: item.product_image ? [{ image_url: item.product_image }] : []
          }
        }));
      } else {
        // Get cart from localStorage for guest users
        const localCart = this.getLocalCart(storeId);
        
        if (!localCart || localCart.length === 0) {
          return [];
        }

        // Fetch product details for each cart item
        const cartWithDetails = await Promise.all(
          localCart.map(async (cartItem) => {
            try {
              const response = await fetch(
                `${window.storeConfig.apiUrl}/products/${cartItem.product_id}`
              );
              
              if (!response.ok) {
                console.warn(`Failed to fetch product ${cartItem.product_id}`);
                return null;
              }

              const productData = await response.json();
              
              if (!productData.success || !productData.data) {
                return null;
              }

              const product = productData.data;
              
              return {
                product_id: cartItem.product_id,
                quantity: cartItem.quantity,
                product: {
                  id: product.id,
                  name: product.name,
                  description: product.description,
                  price: parseFloat(product.price),
                  image_url: product.image_url,
                  stock_quantity: product.stock_quantity,
                  category_name: product.category_name || '',
                  images: product.images || []
                }
              };
            } catch (error) {
              console.error(`Error fetching product ${cartItem.product_id}:`, error);
              return null;
            }
          })
        );

        // Filter out failed requests
        return cartWithDetails.filter(item => item !== null);
      }
    } catch (error) {
      console.error("Error fetching cart with details:", error);
      return [];
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
      const apiUrl = this._getApiUrl();
      const headers = {
        'Content-Type': 'application/json'
      };
      
      // Add authentication token if available
      const token = typeof CustomerAuth !== 'undefined' ? CustomerAuth.getToken() : null;
      if (token) {
        headers['Authorization'] = `Bearer ${token}`;
      }
      
      console.log('[CartService] addItem - storeId:', storeId, 'productId:', productId, 'quantity:', quantity);
      console.log('[CartService] addItem - token:', token ? 'Present' : 'None');
      
      const response = await fetch(
        `${apiUrl}/stores/${storeId}/cart`,
        {
          method: 'POST',
          headers,
          body: JSON.stringify({
            product_id: productId,
            quantity: quantity,
          }),
        }
      );
      
      const result = await response.json();
      console.log('[CartService] addItem - API response:', result);
      return result;
    } catch (error) {
      console.error("Error adding item to cart:", error);
      throw error;
    }
  }

  /**
   * Update cart item quantity (API method)
   * @param {number} storeId - The store ID
   * @param {number} itemId - The cart item ID
   * @param {number} quantity - New quantity
   * @returns {Promise} Updated cart data
   */
  async updateCartItemQuantity(storeId, itemId, quantity) {
    try {
      const headers = {
        'Content-Type': 'application/json'
      };
      
      // Add authentication token if available
      const token = typeof CustomerAuth !== 'undefined' ? CustomerAuth.getToken() : null;
      if (token) {
        headers['Authorization'] = `Bearer ${token}`;
      }
      
      const response = await fetch(
        `${window.storeConfig.apiUrl}/stores/${storeId}/cart/${itemId}`,
        {
          method: 'PUT',
          headers,
          body: JSON.stringify({ quantity }),
        }
      );
      return await response.json();
    } catch (error) {
      console.error("Error updating cart item:", error);
      throw error;
    }
  }

  /**
   * Remove item from cart (API method)
   * @param {number} storeId - The store ID
   * @param {number} itemId - The cart item ID
   * @returns {Promise} Updated cart data
   */
  async removeCartItem(storeId, itemId) {
    try {
      const headers = {
        'Content-Type': 'application/json'
      };
      
      // Add authentication token if available
      const token = typeof CustomerAuth !== 'undefined' ? CustomerAuth.getToken() : null;
      if (token) {
        headers['Authorization'] = `Bearer ${token}`;
      }
      
      const response = await fetch(
        `${window.storeConfig.apiUrl}/stores/${storeId}/cart/${itemId}`,
        {
          method: 'DELETE',
          headers,
        }
      );
      return await response.json();
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
      const headers = {
        'Content-Type': 'application/json'
      };
      
      // Add authentication token if available
      const token = typeof CustomerAuth !== 'undefined' ? CustomerAuth.getToken() : null;
      if (token) {
        headers['Authorization'] = `Bearer ${token}`;
      }
      
      const response = await fetch(
        `${window.storeConfig.apiUrl}/stores/${storeId}/cart`,
        {
          method: 'DELETE',
          headers,
        }
      );
      return await response.json();
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
      const headers = {
        'Content-Type': 'application/json'
      };
      
      // Add authentication token if available
      const token = typeof CustomerAuth !== 'undefined' ? CustomerAuth.getToken() : null;
      if (token) {
        headers['Authorization'] = `Bearer ${token}`;
      }
      
      const response = await fetch(
        `${window.storeConfig.apiUrl}/stores/${storeId}/cart/sync`,
        {
          method: 'POST',
          headers,
          body: JSON.stringify({ items }),
        }
      );
      return await response.json();
    } catch (error) {
      console.error("Error syncing cart:", error);
      throw error;
    }
  }

  /**
   * Calculate cart totals
   * @param {Array} items - Cart items (optional, will fetch if not provided)
   * @param {number} storeId - Store ID (optional, uses window.storeConfig if not provided)
   * @returns {Object|Promise<Object>} Totals object with subtotal, shipping, tax, total
   */
  async calculateTotals(items = null, storeId = null) {
    // If items not provided, fetch cart items
    if (!items) {
      storeId = this._validateStoreId(storeId);
      
      const cartItems = await this.getCartWithDetails(storeId);
      items = cartItems.map(item => ({
        price: item.product.price,
        quantity: item.quantity
      }));
    }

    const subtotal = items.reduce((sum, item) => {
      return sum + parseFloat(item.price) * parseInt(item.quantity);
    }, 0);

    // No shipping or tax fees - total equals subtotal
    const shipping = 0;
    const tax = 0;
    const total = subtotal;

    return {
      subtotal,
      shipping,
      tax,
      total,
      itemCount: items.reduce((sum, item) => sum + parseInt(item.quantity), 0),
    };
  }

  /**
   * Update quantity wrapper for templates
   * Auto-detects store ID and handles both guest and authenticated users
   * @param {number} productId - Product ID
   * @param {number} quantity - New quantity
   * @param {number} storeId - Store ID (optional)
   */
  async updateQuantity(productId, quantity, storeId = null) {
    storeId = this._validateStoreId(storeId);
    const isAuthenticated = typeof CustomerAuth !== 'undefined' && CustomerAuth.isAuthenticated();

    if (isAuthenticated) {
      // For authenticated users, we need the cart item ID, not product ID
      // Get the cart to find the item
      const cart = await this.getCart(storeId);
      
      if (!cart.success || !cart.data || !cart.data.items) {
        return { success: false, error: 'Failed to get cart' };
      }
      
      const cartItem = cart.data.items.find(item => item.product_id === productId);
      
      if (cartItem) {
        return await this.updateCartItemQuantity(storeId, cartItem.id, quantity);
      }
      
      return { success: false, error: 'Item not found in cart' };
    } else {
      // For guest users, update localStorage
      const cart = this.getLocalCart(storeId);
      const itemIndex = cart.findIndex(item => item.product_id === productId);
      
      if (itemIndex >= 0) {
        if (quantity <= 0) {
          cart.splice(itemIndex, 1);
        } else {
          cart[itemIndex].quantity = quantity;
        }
        this.saveLocalCart(storeId, cart);
        return { success: true };
      }
      
      return { success: false, error: 'Item not found in cart' };
    }
  }

  /**
   * Remove item wrapper for templates
   * Auto-detects store ID and handles both guest and authenticated users
   * @param {number} productId - Product ID
   * @param {number} storeId - Store ID (optional)
   */
  async removeItem(productId, storeId = null) {
    storeId = this._validateStoreId(storeId);
    const isAuthenticated = typeof CustomerAuth !== 'undefined' && CustomerAuth.isAuthenticated();

    if (isAuthenticated) {
      // For authenticated users, we need the cart item ID
      const cart = await this.getCart(storeId);
      
      if (!cart.success || !cart.data || !cart.data.items) {
        return { success: false, error: 'Failed to get cart' };
      }
      
      const cartItem = cart.data.items.find(item => item.product_id === productId);
      
      if (cartItem) {
        return await this.removeCartItem(storeId, cartItem.id);
      }
      
      return { success: false, error: 'Item not found in cart' };
    } else {
      // For guest users, remove from localStorage
      const cart = this.getLocalCart(storeId);
      const filteredCart = cart.filter(item => item.product_id !== productId);
      this.saveLocalCart(storeId, filteredCart);
      return { success: true };
    }
  }

  /**
   * Calculate cart totals (DEPRECATED - kept for backwards compatibility)
   * @param {Array} items - Cart items
   * @returns {Object} Totals object with subtotal, shipping, tax, total
   */
  _calculateTotalsSync(items) {
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
      // Dispatch custom event for other components
      window.dispatchEvent(new CustomEvent('cartUpdated', { 
        detail: { storeId, items, count: items.length } 
      }));    } catch (error) {
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
    
    // Support both class and ID selectors
    const badges = document.querySelectorAll(".cart-badge, #cart-badge");
    
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

// Create global CartService instance for browser use
if (typeof window !== 'undefined') {
  // Create a simple API client for browser use
  const simpleApiClient = {
    async get(endpoint) {
      const baseURL = window.API_BASE_URL || window.location.origin;
      const token = localStorage.getItem('customer_token');
      
      const response = await fetch(`${baseURL}${endpoint}`, {
        headers: {
          'Content-Type': 'application/json',
          ...(token && { 'Authorization': `Bearer ${token}` })
        }
      });
      
      return response.json();
    },
    
    async post(endpoint, data) {
      const baseURL = window.API_BASE_URL || window.location.origin;
      const token = localStorage.getItem('customer_token');
      
      const response = await fetch(`${baseURL}${endpoint}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          ...(token && { 'Authorization': `Bearer ${token}` })
        },
        body: JSON.stringify(data)
      });
      
      return response.json();
    },
    
    async put(endpoint, data) {
      const baseURL = window.API_BASE_URL || window.location.origin;
      const token = localStorage.getItem('customer_token');
      
      const response = await fetch(`${baseURL}${endpoint}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          ...(token && { 'Authorization': `Bearer ${token}` })
        },
        body: JSON.stringify(data)
      });
      
      return response.json();
    },
    
    async delete(endpoint) {
      const baseURL = window.API_BASE_URL || window.location.origin;
      const token = localStorage.getItem('customer_token');
      
      const response = await fetch(`${baseURL}${endpoint}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          ...(token && { 'Authorization': `Bearer ${token}` })
        }
      });
      
      return response.json();
    }
  };
  
  // Create singleton instance
  window.CartService = new CartService(simpleApiClient);
  console.log('[cart.js] CartService singleton created:', window.CartService);
  console.log('[cart.js] CartService.addItem:', window.CartService.addItem);
}

// Export for use in other scripts (Node.js/module environments)
if (typeof module !== 'undefined' && module.exports) {
  module.exports = CartService;
}
