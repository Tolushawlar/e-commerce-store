/**
 * Checkout Service for Generated Stores
 * Handles checkout process and order placement
 */

if (typeof API_BASE_URL === "undefined") {
  var API_BASE_URL = window.location.origin;
}

const CheckoutService = {
  /**
   * Get the current store ID
   */
  getStoreId() {
    return window.storeConfig?.store_id || window.storeConfig?.storeId || null;
  },

  /**
   * Get customer auth token
   */
  getAuthToken() {
    return localStorage.getItem("customer_token");
  },

  /**
   * Get authenticated customer
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
   * Check if customer is authenticated
   */
  isAuthenticated() {
    return !!this.getAuthToken();
  },

  /**
   * Get checkout state from localStorage
   */
  getCheckoutState() {
    try {
      const state = localStorage.getItem("checkout_state");
      return state ? JSON.parse(state) : { step: 1 };
    } catch (error) {
      console.error("Error reading checkout state:", error);
      return { step: 1 };
    }
  },

  /**
   * Save checkout state
   */
  saveCheckoutState(state) {
    try {
      localStorage.setItem("checkout_state", JSON.stringify(state));
      return true;
    } catch (error) {
      console.error("Error saving checkout state:", error);
      return false;
    }
  },

  /**
   * Clear checkout state
   */
  clearCheckoutState() {
    localStorage.removeItem("checkout_state");
  },

  /**
   * Validate contact information
   */
  validateContact(data) {
    const errors = {};

    if (!data.email || !this.isValidEmail(data.email)) {
      errors.email = "Please enter a valid email address";
    }

    if (!data.phone || data.phone.length < 10) {
      errors.phone = "Please enter a valid phone number";
    }

    if (!data.firstName || data.firstName.trim().length < 2) {
      errors.firstName = "First name is required";
    }

    if (!data.lastName || data.lastName.trim().length < 2) {
      errors.lastName = "Last name is required";
    }

    return {
      isValid: Object.keys(errors).length === 0,
      errors,
    };
  },

  /**
   * Validate shipping address
   */
  validateShipping(data) {
    const errors = {};

    if (!data.address || data.address.trim().length < 5) {
      errors.address = "Please enter a valid street address";
    }

    if (!data.city || data.city.trim().length < 2) {
      errors.city = "City is required";
    }

    if (!data.state || data.state.trim().length < 2) {
      errors.state = "State is required";
    }

    if (!data.postalCode || data.postalCode.trim().length < 5) {
      errors.postalCode = "Postal code is required";
    }

    return {
      isValid: Object.keys(errors).length === 0,
      errors,
    };
  },

  /**
   * Validate email format
   */
  isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  },

  /**
   * Calculate shipping cost
   */
  calculateShipping(state, subtotal) {
    // Simple shipping calculation - can be made more complex
    if (subtotal >= 50000) {
      return 0; // Free shipping over ₦50,000
    }

    // State-based shipping (example)
    const shippingRates = {
      Lagos: 1500,
      Abuja: 2000,
      "Port Harcourt": 2500,
      Kano: 3000,
      Ibadan: 1800,
    };

    return shippingRates[state] || 2500; // Default ₦2,500
  },

  /**
   * Place order
   */
  async placeOrder(orderData) {
    try {
      const storeId = this.getStoreId();

      if (!storeId) {
        throw new Error("Store ID not found");
      }

      // Prepare order payload
      const payload = {
        store_id: storeId,
        customer_email: orderData.contact.email,
        customer_phone: orderData.contact.phone,
        customer_name: `${orderData.contact.firstName} ${orderData.contact.lastName}`,
        shipping_address: orderData.shipping.address,
        shipping_city: orderData.shipping.city,
        shipping_state: orderData.shipping.state,
        shipping_postal_code: orderData.shipping.postalCode,
        shipping_country: orderData.shipping.country || "Nigeria",
        payment_method: orderData.payment.method,
        subtotal: orderData.totals.subtotal,
        shipping_cost: orderData.totals.shipping,
        tax_amount: orderData.totals.tax,
        total_amount: orderData.totals.total,
        items: orderData.items.map((item) => ({
          product_id: item.productId,
          quantity: item.quantity,
          unit_price: item.product.price,
          total_price: item.product.price * item.quantity,
        })),
        notes: orderData.notes || "",
      };

      // Submit order to API with authentication
      const token = this.getAuthToken();
      if (!token) {
        throw new Error("Please login to place an order");
      }

      const response = await fetch(`${API_BASE_URL}/api/orders`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(payload),
      });

      const data = await response.json();

      if (!response.ok || !data.success) {
        throw new Error(data.message || "Failed to place order");
      }

      // Clear cart and checkout state on success
      if (window.CartService) {
        window.CartService.clearCart();
      }
      this.clearCheckoutState();

      return {
        success: true,
        order: data.data,
      };
    } catch (error) {
      console.error("Error placing order:", error);
      return {
        success: false,
        error: error.message || "Failed to place order. Please try again.",
      };
    }
  },

  /**
   * Get order by ID
   */
  async getOrder(orderId) {
    try {
      const token = this.getAuthToken();
      const headers = {
        "Content-Type": "application/json",
      };

      if (token) {
        headers["Authorization"] = `Bearer ${token}`;
      }

      const response = await fetch(`${API_BASE_URL}/api/orders/${orderId}`, {
        headers: headers,
      });
      const data = await response.json();

      if (!response.ok || !data.success) {
        throw new Error("Order not found");
      }

      return {
        success: true,
        order: data.data,
      };
    } catch (error) {
      console.error("Error fetching order:", error);
      return {
        success: false,
        error: error.message,
      };
    }
  },

  /**
   * Track order by email and order number
   */
  async trackOrder(orderNumber, email) {
    try {
      const response = await fetch(
        `${API_BASE_URL}/api/orders/track?order_number=${orderNumber}&email=${email}`,
      );
      const data = await response.json();

      if (!response.ok || !data.success) {
        throw new Error("Order not found");
      }

      return {
        success: true,
        order: data.data,
      };
    } catch (error) {
      console.error("Error tracking order:", error);
      return {
        success: false,
        error:
          error.message ||
          "Order not found. Please check your order number and email.",
      };
    }
  },

  /**
   * Format currency
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

  /**
   * Get order status label
   */
  getStatusLabel(status) {
    const labels = {
      pending: "Order Pending",
      processing: "Processing",
      shipped: "Shipped",
      delivered: "Delivered",
      cancelled: "Cancelled",
    };
    return labels[status] || status;
  },

  /**
   * Get order status color
   */
  getStatusColor(status) {
    const colors = {
      pending: "text-yellow-600 bg-yellow-50",
      processing: "text-blue-600 bg-blue-50",
      shipped: "text-purple-600 bg-purple-50",
      delivered: "text-green-600 bg-green-50",
      cancelled: "text-red-600 bg-red-50",
    };
    return colors[status] || "text-gray-600 bg-gray-50";
  },
};

// Make available globally
window.CheckoutService = CheckoutService;
