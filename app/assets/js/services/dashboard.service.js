/**
 * Dashboard Service
 * Handles all dashboard-related API calls
 */

const DashboardService = {
  baseUrl: "http://localhost:8000",

  /**
   * Get auth token
   */
  getToken() {
    return localStorage.getItem("auth_token");
  },

  /**
   * Get dashboard statistics
   */
  async getStats(storeId, period = 30) {
    const response = await fetch(
      `${this.baseUrl}/api/stores/${storeId}/dashboard/stats?period=${period}`,
      {
        headers: {
          Authorization: `Bearer ${this.getToken()}`,
          "Content-Type": "application/json",
        },
      },
    );
    return await response.json();
  },

  /**
   * Get revenue chart data
   */
  async getRevenueChart(storeId, period = 30) {
    const response = await fetch(
      `${this.baseUrl}/api/stores/${storeId}/dashboard/revenue-chart?period=${period}`,
      {
        headers: {
          Authorization: `Bearer ${this.getToken()}`,
          "Content-Type": "application/json",
        },
      },
    );
    return await response.json();
  },

  /**
   * Get order status distribution
   */
  async getOrderStatus(storeId, period = 30) {
    const response = await fetch(
      `${this.baseUrl}/api/stores/${storeId}/dashboard/order-status?period=${period}`,
      {
        headers: {
          Authorization: `Bearer ${this.getToken()}`,
          "Content-Type": "application/json",
        },
      },
    );
    return await response.json();
  },

  /**
   * Get top products
   */
  async getTopProducts(storeId, limit = 5, period = 30) {
    const response = await fetch(
      `${this.baseUrl}/api/stores/${storeId}/dashboard/top-products?limit=${limit}&period=${period}`,
      {
        headers: {
          Authorization: `Bearer ${this.getToken()}`,
          "Content-Type": "application/json",
        },
      },
    );
    return await response.json();
  },

  /**
   * Get traffic sources
   */
  async getTrafficSources(storeId, period = 30) {
    const response = await fetch(
      `${this.baseUrl}/api/stores/${storeId}/dashboard/traffic-sources?period=${period}`,
      {
        headers: {
          Authorization: `Bearer ${this.getToken()}`,
          "Content-Type": "application/json",
        },
      },
    );
    return await response.json();
  },

  /**
   * Get recent activities
   */
  async getActivities(storeId, limit = 10) {
    const response = await fetch(
      `${this.baseUrl}/api/stores/${storeId}/dashboard/activities?limit=${limit}`,
      {
        headers: {
          Authorization: `Bearer ${this.getToken()}`,
          "Content-Type": "application/json",
        },
      },
    );
    return await response.json();
  },
};

// Make it globally available
window.DashboardService = DashboardService;
