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
    return !!this.getToken();
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
      localStorage.setItem("customer_token", data.token);
      localStorage.setItem("customer_data", JSON.stringify(data.customer));

      return {
        success: true,
        customer: data.customer,
        token: data.token,
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

      if (!response.ok || !data.success) {
        throw new Error(data.message || "Registration failed");
      }

      // Store token and customer data
      localStorage.setItem("customer_token", data.token);
      localStorage.setItem("customer_data", JSON.stringify(data.customer));

      return {
        success: true,
        customer: data.customer,
        token: data.token,
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
  logout() {
    localStorage.removeItem("customer_token");
    localStorage.removeItem("customer_data");
    window.location.href = "index.html";
  },
};

// Make available globally
if (typeof window !== "undefined") {
  window.CustomerAuth = CustomerAuth;
}
