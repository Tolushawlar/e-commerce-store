/**
 * Customer Authentication Service for Generated Stores
 * Handles customer login, registration, and session management
 */

if (typeof API_BASE_URL === "undefined") {
  var API_BASE_URL = window.location.origin;
}

const CustomerAuth = {
  /**
   * Get the current store ID
   */
  getStoreId() {
    return window.storeConfig?.store_id || window.storeConfig?.storeId || null;
  },

  /**
   * Get auth token
   */
  getToken() {
    return localStorage.getItem("customer_token");
  },

  /**
   * Get customer data
   */
  getCustomer() {
    try {
      const customer = localStorage.getItem("customer_data");
      return customer ? JSON.parse(customer) : null;
    } catch (error) {
      console.error("Error reading customer data:", error);
      return null;
    }
  },

  /**
   * Check if authenticated
   */
  isAuthenticated() {
    return this.validateToken();
  },

  /**
   * Validate token (check if exists and not expired)
   */
  validateToken() {
    const token = this.getToken();
    if (!token) return false;

    try {
      // Decode JWT to check expiration
      const payload = JSON.parse(atob(token.split('.')[1]));
      const isExpired = payload.exp * 1000 < Date.now();

      if (isExpired) {
        console.warn('Token expired, logging out');
        this.logout(false);  // Don't redirect in validation
        return false;
      }
      return true;
    } catch (error) {
      console.error('Error validating token:', error);
      // If token is malformed, clear it
      localStorage.removeItem('customer_token');
      localStorage.removeItem('customer_data');
      return false;
    }
  },

  /**
   * Login customer
   */
  async login(email, password) {
    try {
      const storeId = this.getStoreId();
      if (!storeId) {
        throw new Error("Store ID not found");
      }

      const response = await fetch(
        `${API_BASE_URL}/api/stores/${storeId}/customers/login`,
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ email, password }),
        },
      );

      const data = await response.json();

      if (!response.ok || !data.success) {
        throw new Error(data.message || "Login failed");
      }

      // Store token and customer data
      localStorage.setItem("customer_token", data.data.token);
      localStorage.setItem("customer_data", JSON.stringify(data.data.customer));

      // Sync guest cart to authenticated cart
      await this.syncGuestCart(storeId, data.data.token);

      return {
        success: true,
        customer: data.data.customer,
        token: data.data.token,
      };
    } catch (error) {
      console.error("Login error:", error);
      return {
        success: false,
        error: error.message || "Failed to login",
      };
    }
  },

  /**
   * Register new customer
   */
  async register(formData) {
    try {
      const storeId = this.getStoreId();
      if (!storeId) {
        throw new Error("Store ID not found");
      }

      const response = await fetch(
        `${API_BASE_URL}/api/stores/${storeId}/customers/register`,
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(formData),
        },
      );

      const data = await response.json();
      
      // Log validation errors if present
      if (data.errors) {
        console.error('Validation errors:', data.errors);
      }

      if (!response.ok || !data.success) {
        // Include validation errors in the error message if available
        const errorMessage = data.errors 
          ? `${data.message}: ${Object.values(data.errors).flat().join(', ')}`
          : (data.message || "Registration failed");
        throw new Error(errorMessage);
      }

      // Store token and customer data
      localStorage.setItem("customer_token", data.data.token);
      localStorage.setItem("customer_data", JSON.stringify(data.data.customer));

      // Sync guest cart to authenticated cart
      await this.syncGuestCart(storeId, data.data.token);

      return {
        success: true,
        customer: data.data.customer,
        token: data.data.token,
      };
    } catch (error) {
      console.error("Registration error:", error);
      return {
        success: false,
        error: error.message || "Failed to register",
      };
    }
  },

  /**
   * Logout customer
   */
  logout(redirect = true) {
    localStorage.removeItem("customer_token");
    localStorage.removeItem("customer_data");
    
    if (redirect) {
      // Save current page for return after login
      const returnUrl = encodeURIComponent(window.location.pathname + window.location.search);
      window.location.href = `login.html?redirect=${returnUrl}`;
    }
  },

  /**
   * Sync guest cart (localStorage) to authenticated cart (API)
   * Called after successful login/register
   */
  async syncGuestCart(storeId, token) {
    try {
      // Get guest cart from localStorage
      const cartKey = `cart_${storeId}`;
      const guestCartJson = localStorage.getItem(cartKey);
      
      if (!guestCartJson) {
        console.log('No guest cart to sync');
        return;
      }

      const guestCart = JSON.parse(guestCartJson);
      
      if (!Array.isArray(guestCart) || guestCart.length === 0) {
        console.log('Guest cart is empty');
        return;
      }

      // Transform localStorage cart format to API format
      // Handle both old (productId) and new (product_id) structures
      const items = guestCart.map(item => ({
        product_id: item.product_id || item.productId,
        quantity: item.quantity
      }));

      console.log('Syncing guest cart to authenticated cart:', items);

      // Send cart to API for syncing
      const response = await fetch(
        `${API_BASE_URL}/api/stores/${storeId}/cart/sync`,
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Authorization": `Bearer ${token}`
          },
          body: JSON.stringify({ items }),
        }
      );

      const data = await response.json();

      if (response.ok && data.success) {
        console.log('Cart synced successfully');
        // Clear guest cart from localStorage after successful sync
        localStorage.removeItem(cartKey);
        // Trigger cart update event for badge
        window.dispatchEvent(new Event('cartUpdated'));
      } else {
        console.warn('Failed to sync cart:', data.message || 'Unknown error');
      }
    } catch (error) {
      console.error("Error syncing guest cart:", error);
      // Don't throw error - cart sync failure shouldn't prevent login
    }
  },
};

// Make available globally
if (typeof window !== "undefined") {
  window.CustomerAuth = CustomerAuth;
}
