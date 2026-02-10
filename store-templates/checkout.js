/**
 * Checkout Service - Static utility methods for checkout functionality
 */
const CheckoutService = {
  /**
   * Validate contact information
   * @param {Object} contact - Contact data to validate
   * @returns {Object} Validation result with isValid and errors
   */
  validateContact(contact) {
    const errors = {};

    if (!contact.firstName || contact.firstName.trim() === '') {
      errors.firstName = 'First name is required';
    }

    if (!contact.lastName || contact.lastName.trim() === '') {
      errors.lastName = 'Last name is required';
    }

    if (!contact.email || contact.email.trim() === '') {
      errors.email = 'Email is required';
    } else if (!this.isValidEmail(contact.email)) {
      errors.email = 'Please enter a valid email address';
    }

    if (!contact.phone || contact.phone.trim() === '') {
      errors.phone = 'Phone number is required';
    } else if (!this.isValidPhone(contact.phone)) {
      errors.phone = 'Please enter a valid phone number';
    }

    return {
      isValid: Object.keys(errors).length === 0,
      errors
    };
  },

  /**
   * Validate shipping address
   * @param {Object} shipping - Shipping data to validate
   * @returns {Object} Validation result with isValid and errors
   */
  validateShipping(shipping) {
    const errors = {};

    if (!shipping.address || shipping.address.trim() === '') {
      errors.address = 'Street address is required';
    }

    if (!shipping.city || shipping.city.trim() === '') {
      errors.city = 'City is required';
    }

    if (!shipping.state || shipping.state.trim() === '') {
      errors.state = 'State is required';
    }

    if (!shipping.postalCode || shipping.postalCode.trim() === '') {
      errors.postalCode = 'Postal code is required';
    }

    return {
      isValid: Object.keys(errors).length === 0,
      errors
    };
  },

  /**
   * Validate email format
   * @param {string} email - Email to validate
   * @returns {boolean} Is valid email
   */
  isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  },

  /**
   * Validate phone number format
   * @param {string} phone - Phone to validate
   * @returns {boolean} Is valid phone
   */
  isValidPhone(phone) {
    // Remove all non-digit characters
    const cleaned = phone.replace(/\D/g, '');
    // Check if it's at least 10 digits
    return cleaned.length >= 10;
  },

  /**
   * Get current customer data from localStorage
   * @returns {Object|null} Customer data
   */
  getCustomer() {
    if (typeof CustomerAuth !== 'undefined') {
      return CustomerAuth.getCustomer();
    }
    return null;
  },

  /**
   * Check if payment gateway is enabled
   * @returns {Promise<boolean>} Whether payment is enabled
   */
  async isPaymentEnabled() {
    try {
      const storeId = window.storeConfig.store_id;
      const apiBase = window.storeConfig.apiUrl || window.location.origin + '/api';
      const response = await fetch(`${apiBase}/stores/${storeId}/payment/config`);
      const data = await response.json();
      
      if (data.success && data.data) {
        // Check if any payment method is enabled
        return (data.data.paystack && data.data.paystack.enabled) ||
               (data.data.bank_transfer && data.data.bank_transfer.enabled) ||
               (data.data.cod && data.data.cod.enabled);
      }
      return false;
    } catch (error) {
      console.error('Error checking payment status:', error);
      return false;
    }
  },

  /**
   * Place an order
   * @param {Object} orderData - Order data
   * @returns {Promise<Object>} Order result
   */
  async placeOrder(orderData) {
    try {
      const storeId = window.storeConfig.store_id;
      const apiBase = window.storeConfig.apiUrl || window.location.origin + '/api';
      
      // Get authentication token if available
      const token = typeof CustomerAuth !== 'undefined' ? CustomerAuth.getToken() : null;
      
      // Prepare order payload
      const payload = {
        customer_name: `${orderData.contact.firstName} ${orderData.contact.lastName}`,
        customer_email: orderData.contact.email,
        customer_phone: orderData.contact.phone,
        shipping_address: orderData.shipping.address,
        shipping_city: orderData.shipping.city,
        shipping_state: orderData.shipping.state,
        shipping_postal_code: orderData.shipping.postalCode,
        shipping_country: 'Nigeria',
        payment_method: orderData.payment.method,
        order_notes: orderData.notes || null,
        items: orderData.items.map(item => ({
          product_id: item.product_id,
          quantity: item.quantity,
          unit_price: item.product.price
        }))
      };

      const headers = {
        'Content-Type': 'application/json'
      };

      if (token) {
        headers['Authorization'] = `Bearer ${token}`;
      }

      const response = await fetch(`${apiBase}/stores/${storeId}/checkout`, {
        method: 'POST',
        headers: headers,
        body: JSON.stringify(payload)
      });

      const result = await response.json();

      if (response.ok && result.success) {
        return {
          success: true,
          order: result.data || result.order
        };
      } else {
        return {
          success: false,
          error: result.message || 'Failed to place order'
        };
      }
    } catch (error) {
      console.error('Error placing order:', error);
      return {
        success: false,
        error: error.message || 'An error occurred while placing the order'
      };
    }
  },

  /**
   * Get order details by ID
   * @param {number} orderId - Order ID
   * @returns {Promise<Object>} Order result
   */
  async getOrder(orderId) {
    try {
      const storeId = window.storeConfig.store_id;
      const apiBase = window.storeConfig.apiUrl || window.location.origin + '/api';
      
      // Get authentication token if available
      const token = typeof CustomerAuth !== 'undefined' ? CustomerAuth.getToken() : null;
      
      const headers = {
        'Content-Type': 'application/json'
      };

      if (token) {
        headers['Authorization'] = `Bearer ${token}`;
      }

      const response = await fetch(`${apiBase}/stores/${storeId}/orders/${orderId}`, {
        method: 'GET',
        headers: headers
      });

      const result = await response.json();

      if (response.ok && result.success) {
        return {
          success: true,
          order: result.data || result.order
        };
      } else {
        return {
          success: false,
          error: result.message || 'Failed to fetch order'
        };
      }
    } catch (error) {
      console.error('Error fetching order:', error);
      return {
        success: false,
        error: error.message || 'An error occurred while fetching the order'
      };
    }
  },

  /**
   * Process payment with Paystack
   * @param {number} orderId - Order ID
   * @param {number} amount - Amount to charge (in Naira)
   * @param {string} email - Customer email
   * @param {string} customerName - Customer name
   * @param {Function} onSuccess - Success callback
   * @param {Function} onClose - Close callback
   * @returns {Promise<Object>} Payment result
   */
  async processPayment(orderId, amount, email, customerName, onSuccess, onClose) {
    try {
      const apiBase = window.storeConfig.apiUrl || window.location.origin + '/api';
      const storeId = window.storeConfig.store_id;
      const token = typeof CustomerAuth !== 'undefined' ? CustomerAuth.getToken() : null;
      
      if (!token) {
        return {
          success: false,
          error: 'Authentication required for payment'
        };
      }

      // Get payment config for public key
      const configResponse = await fetch(`${apiBase}/stores/${storeId}/payment/config`);
      const configResult = await configResponse.json();

      if (!configResponse.ok || !configResult.success || !configResult.data.public_key) {
        return {
          success: false,
          error: 'Failed to load payment configuration'
        };
      }

      const publicKey = configResult.data.public_key;

      // Initialize payment on backend
      const response = await fetch(`${apiBase}/payment/initialize`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({
          order_id: orderId,
          amount: amount,
          email: email,
          metadata: {
            customer_name: customerName,
            order_id: orderId
          }
        })
      });

      const result = await response.json();

      if (!response.ok || !result.success) {
        return {
          success: false,
          error: result.message || 'Failed to initialize payment'
        };
      }

      // Check if Paystack library is loaded
      if (typeof PaystackPop === 'undefined') {
        return {
          success: false,
          error: 'Payment system not loaded. Please refresh the page and try again.'
        };
      }

      // Open Paystack popup
      const handler = PaystackPop.setup({
        key: publicKey,
        email: email,
        amount: Math.round(amount * 100), // Convert to kobo
        currency: 'NGN',
        ref: result.data.reference,
        metadata: {
          custom_fields: [
            {
              display_name: 'Order ID',
              variable_name: 'order_id',
              value: orderId
            },
            {
              display_name: 'Customer Name',
              variable_name: 'customer_name',
              value: customerName
            }
          ]
        },
        callback: function(response) {
          // Verify payment on backend (use .then() instead of async/await)
          fetch(`${apiBase}/payment/verify`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({
              reference: response.reference,
              order_id: orderId
            })
          })
          .then(res => res.json())
          .then(verifyResult => {
            if (verifyResult.success) {
              // Clear cart
              if (typeof cartService !== 'undefined') {
                cartService.clearCart(window.storeConfig.store_id).catch(err => {
                  console.error('Error clearing cart:', err);
                });
              }
              localStorage.removeItem(`cart_${window.storeConfig.store_id}`);
              
              // Call success callback
              if (onSuccess) {
                onSuccess(verifyResult.data.order || { id: orderId });
              }
            } else {
              alert('Payment verification failed: ' + (verifyResult.message || 'Unknown error'));
            }
          })
          .catch(error => {
            console.error('Payment verification error:', error);
            alert('Payment verification failed. Please contact support with reference: ' + response.reference);
          });
        },
        onClose: function() {
          if (onClose) {
            onClose();
          }
        }
      });

      handler.openIframe();

      return {
        success: true,
        reference: result.data.reference
      };
    } catch (error) {
      console.error('Payment processing error:', error);
      return {
        success: false,
        error: error.message || 'Payment processing failed'
      };
    }
  },

  /**
   * Save customer details to profile
   * @param {Object} profileData - Profile data to save
   * @returns {Promise<Object>} Save result
   */
  async saveCustomerDetails(profileData) {
    try {
      const storeId = window.storeConfig.store_id;
      const apiBase = window.storeConfig.apiUrl || window.location.origin + '/api';
      const token = typeof CustomerAuth !== 'undefined' ? CustomerAuth.getToken() : null;
      
      if (!token) {
        return {
          success: false,
          error: 'Authentication required'
        };
      }

      const response = await fetch(`${apiBase}/stores/${storeId}/customers/me`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify(profileData)
      });

      const result = await response.json();

      return {
        success: response.ok && result.success,
        data: result.data,
        error: result.message
      };
    } catch (error) {
      console.error('Error saving customer details:', error);
      return {
        success: false,
        error: error.message || 'Failed to save customer details'
      };
    }
  }
};
