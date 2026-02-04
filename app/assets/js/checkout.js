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
   * Fetch customer profile from API
   */
  async fetchCustomerProfile() {
    try {
      const storeId = this.getStoreId();
      const token = this.getAuthToken();

      if (!token || !storeId) {
        return null;
      }

      const response = await fetch(
        `${API_BASE_URL}/api/stores/${storeId}/customers/me`,
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        },
      );

      if (!response.ok) {
        return null;
      }

      const result = await response.json();
      return result.data || result;
    } catch (error) {
      console.error("Error fetching customer profile:", error);
      return null;
    }
  },

  /**
   * Auto-fill form with customer data
   */
  autoFillForm(profile) {
    if (!profile) return false;

    let filled = false;

    // Fill contact information
    if (profile.first_name) {
      const firstNameInput = document.getElementById("firstName");
      if (firstNameInput && !firstNameInput.value) {
        firstNameInput.value = profile.first_name;
        filled = true;
      }
    }

    if (profile.last_name) {
      const lastNameInput = document.getElementById("lastName");
      if (lastNameInput && !lastNameInput.value) {
        lastNameInput.value = profile.last_name;
        filled = true;
      }
    }

    if (profile.email) {
      const emailInput = document.getElementById("email");
      if (emailInput && !emailInput.value) {
        emailInput.value = profile.email;
        filled = true;
      }
    }

    if (profile.phone) {
      const phoneInput = document.getElementById("phone");
      if (phoneInput && !phoneInput.value) {
        phoneInput.value = profile.phone;
        filled = true;
      }
    }

    // Fill address information from first address or profile fields
    const address =
      profile.addresses && profile.addresses.length > 0
        ? profile.addresses[0]
        : profile;

    if (address.street_address || address.address) {
      const addressInput = document.getElementById("address");
      if (addressInput && !addressInput.value) {
        addressInput.value = address.street_address || address.address;
        filled = true;
      }
    }

    if (address.city) {
      const cityInput = document.getElementById("city");
      if (cityInput && !cityInput.value) {
        cityInput.value = address.city;
        filled = true;
      }
    }

    if (address.state) {
      const stateInput = document.getElementById("state");
      if (stateInput && !stateInput.value) {
        stateInput.value = address.state;
        filled = true;
      }
    }

    if (address.postal_code) {
      const postalCodeInput = document.getElementById("postalCode");
      if (postalCodeInput && !postalCodeInput.value) {
        postalCodeInput.value = address.postal_code;
        filled = true;
      }
    }

    return filled;
  },

  /**
   * Check if profile needs to be saved
   */
  shouldShowSaveOption(profile) {
    // Show save option if customer is authenticated but missing address details
    if (!profile) return false;

    const hasAddress = profile.addresses && profile.addresses.length > 0;
    const hasAddressFields = profile.street_address || profile.address;

    return !hasAddress && !hasAddressFields;
  },

  /**
   * Update customer profile with shipping details
   */
  async saveCustomerDetails(profileData) {
    try {
      const storeId = this.getStoreId();
      const token = this.getAuthToken();

      if (!token || !storeId) {
        return { success: false, error: "Not authenticated" };
      }

      const response = await fetch(
        `${API_BASE_URL}/api/stores/${storeId}/customers/me`,
        {
          method: "PUT",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
          body: JSON.stringify(profileData),
        },
      );

      const result = await response.json();
      return {
        success: response.ok && result.success,
        data: result.data,
      };
    } catch (error) {
      console.error("Error saving customer details:", error);
      return { success: false, error: error.message };
    }
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
        order_notes: orderData.notes || "",
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

  /**
   * Get Paystack configuration for store
   */
  async getPaystackConfig() {
    try {
      const storeId = this.getStoreId();
      if (!storeId) {
        throw new Error("Store ID not found");
      }

      const response = await fetch(
        `${API_BASE_URL}/api/stores/${storeId}/payment/config`,
      );

      if (!response.ok) {
        throw new Error("Failed to load payment configuration");
      }

      const data = await response.json();
      return {
        success: true,
        publicKey: data.data.public_key,
        enabled: data.data.payment_enabled,
      };
    } catch (error) {
      console.error("Error loading Paystack config:", error);
      return {
        success: false,
        error: error.message,
      };
    }
  },

  /**
   * Initialize Paystack payment
   */
  async initializePayment(orderId, amount, email) {
    try {
      const token = this.getAuthToken();
      if (!token) {
        throw new Error("Authentication required");
      }

      const response = await fetch(`${API_BASE_URL}/api/payment/initialize`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({
          order_id: orderId,
          amount: amount,
          email: email,
        }),
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || "Failed to initialize payment");
      }

      return {
        success: true,
        authorizationUrl: data.data.authorization_url,
        reference: data.data.reference,
      };
    } catch (error) {
      console.error("Error initializing payment:", error);
      return {
        success: false,
        error: error.message,
      };
    }
  },

  /**
   * Verify Paystack payment
   */
  async verifyPayment(reference) {
    try {
      const token = this.getAuthToken();
      if (!token) {
        throw new Error("Authentication required");
      }

      const response = await fetch(`${API_BASE_URL}/api/payment/verify`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({
          reference: reference,
        }),
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || "Payment verification failed");
      }

      return {
        success: true,
        order_id: data.data.order_id,
        payment: data.data,
      };
    } catch (error) {
      console.error("Error verifying payment:", error);
      return {
        success: false,
        error: error.message,
      };
    }
  },

  /**
   * Check if payment gateway is configured
   */
  async isPaymentEnabled() {
    try {
      const config = await this.getPaystackConfig();
      return config.success && config.enabled && config.publicKey;
    } catch (error) {
      return false;
    }
  },

  /**
   * Process payment with Paystack Popup
   */
  async processPayment(
    orderId,
    amount,
    email,
    customerName,
    onSuccess,
    onClose,
  ) {
    try {
      // Check if Paystack is loaded
      if (typeof PaystackPop === "undefined") {
        console.warn("Paystack library not loaded");
        return {
          success: false,
          error: "Paystack library not loaded",
        };
      }

      // Get Paystack configuration
      const config = await this.getPaystackConfig();
      if (!config.success || !config.enabled) {
        console.warn("Payment gateway not configured");
        return {
          success: false,
          error: "Payment gateway not configured",
        };
      }

      // Initialize payment
      const init = await this.initializePayment(orderId, amount, email);
      if (!init.success) {
        throw new Error(init.error);
      }

      // Open Paystack popup
      const nameParts = customerName
        ? customerName.trim().split(" ")
        : ["", ""];
      const firstName = nameParts[0] || "";
      const lastName = nameParts.slice(1).join(" ") || nameParts[0] || "";

      const handler = PaystackPop.setup({
        key: config.publicKey,
        email: email,
        amount: Math.round(amount * 100), // Convert to kobo
        ref: init.reference,
        first_name: firstName,
        last_name: lastName,
        onClose: function () {
          if (typeof onClose === "function") {
            onClose();
          }
        },
        callback: function (response) {
          // Verify payment on backend (handle async inside)
          CheckoutService.verifyPayment(response.reference)
            .then(function (verification) {
              if (verification.success) {
                // Payment verified successfully
                if (typeof onSuccess === "function") {
                  onSuccess({ id: verification.order_id });
                }
              } else {
                alert(
                  "Payment verification failed. Please contact support with reference: " +
                    response.reference,
                );
              }
            })
            .catch(function (error) {
              console.error("Verification error:", error);
              alert(
                "Payment verification error. Please contact support with reference: " +
                  response.reference,
              );
            });
        },
      });

      handler.openIframe();

      return {
        success: true,
        reference: init.reference,
      };
    } catch (error) {
      console.error("Error processing payment:", error);
      return {
        success: false,
        error: error.message,
      };
    }
  },
};
