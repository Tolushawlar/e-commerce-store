/**
 * Admin Order Service
 * Handles all admin order management operations
 */
class AdminOrderService {
  constructor(apiClient) {
    this.api = apiClient;
  }

  /**
   * Get all orders for a store with filters
   */
  async getOrders(storeId, filters = {}) {
    const params = new URLSearchParams();

    if (filters.status) params.append("status", filters.status);
    if (filters.payment_status)
      params.append("payment_status", filters.payment_status);
    if (filters.from_date) params.append("from_date", filters.from_date);
    if (filters.to_date) params.append("to_date", filters.to_date);
    if (filters.search) params.append("search", filters.search);
    if (filters.page) params.append("page", filters.page);
    if (filters.limit) params.append("limit", filters.limit);

    const queryString = params.toString();
    const endpoint = `/api/stores/${storeId}/admin/orders${queryString ? "?" + queryString : ""}`;

    return this.api.get(endpoint);
  }

  /**
   * Get single order details
   */
  async getOrder(storeId, orderId) {
    return this.api.get(`/api/stores/${storeId}/admin/orders/${orderId}`);
  }

  /**
   * Update order status
   */
  async updateStatus(storeId, orderId, status) {
    return this.api.put(
      `/api/stores/${storeId}/admin/orders/${orderId}/status`,
      {
        status,
      },
    );
  }

  /**
   * Update payment status
   */
  async updatePaymentStatus(storeId, orderId, paymentStatus) {
    return this.api.put(
      `/api/stores/${storeId}/admin/orders/${orderId}/payment-status`,
      {
        payment_status: paymentStatus,
      },
    );
  }

  /**
   * Add tracking number
   */
  async addTracking(storeId, orderId, trackingNumber) {
    return this.api.put(
      `/api/stores/${storeId}/admin/orders/${orderId}/tracking`,
      {
        tracking_number: trackingNumber,
      },
    );
  }

  /**
   * Get order statistics
   */
  async getStats(storeId, fromDate = null, toDate = null) {
    const params = {};
    if (fromDate) params.from_date = fromDate;
    if (toDate) params.to_date = toDate;

    const queryString = new URLSearchParams(params).toString();
    const endpoint = `/api/stores/${storeId}/admin/orders/stats${queryString ? "?" + queryString : ""}`;

    return this.api.get(endpoint);
  }

  /**
   * Bulk update order statuses
   */
  async bulkUpdate(storeId, orderIds, status) {
    return this.api.post(`/api/stores/${storeId}/admin/orders/bulk-update`, {
      order_ids: orderIds,
      status,
    });
  }

  /**
   * Helper: Format currency
   */
  formatCurrency(amount) {
    return new Intl.NumberFormat("en-NG", {
      style: "currency",
      currency: "NGN",
    }).format(amount);
  }

  /**
   * Helper: Format date
   */
  formatDate(dateString) {
    return new Date(dateString).toLocaleDateString("en-US", {
      year: "numeric",
      month: "short",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    });
  }

  /**
   * Helper: Get status badge class
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
   * Helper: Get payment status badge class
   */
  getPaymentStatusBadgeClass(status) {
    const classes = {
      pending: "bg-yellow-100 text-yellow-800",
      paid: "bg-green-100 text-green-800",
      failed: "bg-red-100 text-red-800",
      refunded: "bg-gray-100 text-gray-800",
    };
    return classes[status] || "bg-gray-100 text-gray-800";
  }
}

// Export for use
if (typeof module !== "undefined" && module.exports) {
  module.exports = AdminOrderService;
}
