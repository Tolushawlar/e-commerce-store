/**
 * Checkout Service
 * Handles all checkout-related API operations for customers
 */
class CheckoutService {
  constructor(apiClient) {
    this.apiClient = apiClient;
  }

  /**
   * Get checkout data (cart summary, addresses, etc.)
   * @param {number} storeId - The store ID
   * @returns {Promise} Checkout data
   */
  async getCheckoutData(storeId) {
    try {
      const response = await this.apiClient.get(
        `/api/stores/${storeId}/checkout`,
      );
      return response;
    } catch (error) {
      console.error("Error fetching checkout data:", error);
      throw error;
    }
  }

  /**
   * Place an order
   * @param {number} storeId - The store ID
   * @param {Object} orderData - Order details
   * @returns {Promise} Order confirmation
   */
  async placeOrder(storeId, orderData) {
    try {
      const response = await this.apiClient.post(
        `/api/stores/${storeId}/checkout`,
        orderData,
      );
      return response;
    } catch (error) {
      console.error("Error placing order:", error);
      throw error;
    }
  }

  /**
   * Get customer's addresses
   * @returns {Promise} List of addresses
   */
  async getAddresses() {
    try {
      const response = await this.apiClient.get("/api/customer/addresses");
      return response;
    } catch (error) {
      console.error("Error fetching addresses:", error);
      throw error;
    }
  }

  /**
   * Add new address
   * @param {Object} addressData - Address details
   * @returns {Promise} Created address
   */
  async addAddress(addressData) {
    try {
      const response = await this.apiClient.post(
        "/api/customer/addresses",
        addressData,
      );
      return response;
    } catch (error) {
      console.error("Error adding address:", error);
      throw error;
    }
  }

  /**
   * Update existing address
   * @param {number} addressId - Address ID
   * @param {Object} addressData - Updated address details
   * @returns {Promise} Updated address
   */
  async updateAddress(addressId, addressData) {
    try {
      const response = await this.apiClient.put(
        `/api/customer/addresses/${addressId}`,
        addressData,
      );
      return response;
    } catch (error) {
      console.error("Error updating address:", error);
      throw error;
    }
  }

  /**
   * Delete address
   * @param {number} addressId - Address ID
   * @returns {Promise} Success response
   */
  async deleteAddress(addressId) {
    try {
      const response = await this.apiClient.delete(
        `/api/customer/addresses/${addressId}`,
      );
      return response;
    } catch (error) {
      console.error("Error deleting address:", error);
      throw error;
    }
  }

  /**
   * Set default address
   * @param {number} addressId - Address ID
   * @returns {Promise} Success response
   */
  async setDefaultAddress(addressId) {
    try {
      const response = await this.apiClient.put(
        `/api/customer/addresses/${addressId}/default`,
      );
      return response;
    } catch (error) {
      console.error("Error setting default address:", error);
      throw error;
    }
  }

  /**
   * Track order
   * @param {number} storeId - The store ID
   * @param {string} orderId - Order ID or tracking number
   * @returns {Promise} Order tracking data
   */
  async trackOrder(storeId, orderId) {
    try {
      const response = await this.apiClient.get(
        `/api/stores/${storeId}/orders/${orderId}/track`,
      );
      return response;
    } catch (error) {
      console.error("Error tracking order:", error);
      throw error;
    }
  }

  /**
   * Get order details
   * @param {number} storeId - The store ID
   * @param {number} orderId - Order ID
   * @returns {Promise} Order details
   */
  async getOrder(storeId, orderId) {
    try {
      const response = await this.apiClient.get(
        `/api/stores/${storeId}/orders/${orderId}`,
      );
      return response;
    } catch (error) {
      console.error("Error fetching order:", error);
      throw error;
    }
  }

  /**
   * Validate checkout form data
   * @param {Object} formData - Form data to validate
   * @returns {Object} Validation result
   */
  validateCheckoutForm(formData) {
    const errors = {};

    // Validate shipping address
    if (!formData.shipping_address_id && !formData.shipping_address) {
      errors.shipping = "Shipping address is required";
    }

    if (formData.shipping_address) {
      if (!formData.shipping_address.address_line1) {
        errors.address_line1 = "Street address is required";
      }
      if (!formData.shipping_address.city) {
        errors.city = "City is required";
      }
      if (!formData.shipping_address.state) {
        errors.state = "State is required";
      }
      if (!formData.shipping_address.country) {
        errors.country = "Country is required";
      }
      if (!formData.shipping_address.postal_code) {
        errors.postal_code = "Postal code is required";
      }
    }

    // Validate contact info
    if (!formData.customer_name) {
      errors.customer_name = "Full name is required";
    }
    if (!formData.customer_email) {
      errors.customer_email = "Email is required";
    } else if (!this.isValidEmail(formData.customer_email)) {
      errors.customer_email = "Invalid email format";
    }
    if (!formData.customer_phone) {
      errors.customer_phone = "Phone number is required";
    }

    // Validate payment method
    if (!formData.payment_method) {
      errors.payment_method = "Payment method is required";
    }

    return {
      isValid: Object.keys(errors).length === 0,
      errors,
    };
  }

  /**
   * Validate email format
   * @param {string} email - Email to validate
   * @returns {boolean} Is valid email
   */
  isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  /**
   * Calculate shipping cost based on location and cart total
   * @param {Object} address - Shipping address
   * @param {number} cartTotal - Cart subtotal
   * @returns {number} Shipping cost
   */
  calculateShipping(address, cartTotal) {
    // Free shipping for orders above ₦10,000
    if (cartTotal >= 10000) {
      return 0;
    }

    // Base shipping cost
    let shipping = 1500;

    // Add extra for certain states (example logic)
    const remoteStates = [
      "Rivers",
      "Cross River",
      "Akwa Ibom",
      "Bayelsa",
      "Delta",
    ];
    if (address && remoteStates.includes(address.state)) {
      shipping += 500;
    }

    return shipping;
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
   * Format date
   * @param {string} dateString - Date string to format
   * @returns {string} Formatted date
   */
  formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", {
      year: "numeric",
      month: "long",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    });
  }

  /**
   * Get payment method display name
   * @param {string} method - Payment method code
   * @returns {string} Display name
   */
  getPaymentMethodName(method) {
    const methods = {
      card: "Credit/Debit Card",
      bank_transfer: "Bank Transfer",
      cash_on_delivery: "Cash on Delivery",
      paystack: "Paystack",
      flutterwave: "Flutterwave",
    };
    return methods[method] || method;
  }

  /**
   * Get order status badge class
   * @param {string} status - Order status
   * @returns {string} CSS class
   */
  getStatusBadgeClass(status) {
    const classes = {
      pending: "bg-yellow-100 text-yellow-800",
      processing: "bg-blue-100 text-blue-800",
      shipped: "bg-purple-100 text-purple-800",
      delivered: "bg-green-100 text-green-800",
      cancelled: "bg-red-100 text-red-800",
    };
    return classes[status] || "bg-gray-100 text-gray-800";
  }

  /**
   * Get order status icon
   * @param {string} status - Order status
   * @returns {string} Material icon name
   */
  getStatusIcon(status) {
    const icons = {
      pending: "pending",
      processing: "autorenew",
      shipped: "local_shipping",
      delivered: "check_circle",
      cancelled: "cancel",
    };
    return icons[status] || "info";
  }

  /**
   * Prepare order data for submission
   * @param {Object} checkoutData - Checkout form data
   * @param {Array} cartItems - Cart items
   * @returns {Object} Formatted order data
   */
  prepareOrderData(checkoutData, cartItems) {
    const orderData = {
      customer_name: checkoutData.customer_name,
      customer_email: checkoutData.customer_email,
      customer_phone: checkoutData.customer_phone,
      payment_method: checkoutData.payment_method,
      order_notes: checkoutData.order_notes || "",
      items: cartItems.map((item) => ({
        product_id: item.product_id,
        quantity: item.quantity,
        price: item.price,
      })),
    };

    // Add shipping address
    if (checkoutData.shipping_address_id) {
      orderData.shipping_address_id = checkoutData.shipping_address_id;
    } else if (checkoutData.shipping_address) {
      orderData.shipping_address = checkoutData.shipping_address;
    }

    // Add billing address if different
    if (checkoutData.billing_address_id) {
      orderData.billing_address_id = checkoutData.billing_address_id;
    } else if (checkoutData.billing_address) {
      orderData.billing_address = checkoutData.billing_address;
    }

    return orderData;
  }

  /**
   * Save checkout progress to localStorage
   * @param {number} storeId - The store ID
   * @param {Object} data - Checkout data to save
   */
  saveCheckoutProgress(storeId, data) {
    try {
      const key = `checkout_${storeId}`;
      localStorage.setItem(key, JSON.stringify(data));
    } catch (error) {
      console.error("Error saving checkout progress:", error);
    }
  }

  /**
   * Get saved checkout progress from localStorage
   * @param {number} storeId - The store ID
   * @returns {Object|null} Saved checkout data
   */
  getCheckoutProgress(storeId) {
    try {
      const key = `checkout_${storeId}`;
      const data = localStorage.getItem(key);
      return data ? JSON.parse(data) : null;
    } catch (error) {
      console.error("Error getting checkout progress:", error);
      return null;
    }
  }

  /**
   * Clear checkout progress from localStorage
   * @param {number} storeId - The store ID
   */
  clearCheckoutProgress(storeId) {
    try {
      const key = `checkout_${storeId}`;
      localStorage.removeItem(key);
    } catch (error) {
      console.error("Error clearing checkout progress:", error);
    }
  }
}

// Export for use in other scripts
if (typeof module !== "undefined" && module.exports) {
  module.exports = CheckoutService;
}
