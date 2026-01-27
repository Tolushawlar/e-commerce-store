/**
 * Order Service
 * Handles order management operations
 */

class OrderService {
  /**
   * Get all orders (paginated)
   * Endpoint: GET /api/orders
   */
  async getAll(params = {}) {
    try {
      const response = await api.get("/api/orders", params);
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Get single order
   * Endpoint: GET /api/orders/{id}
   */
  async getById(id) {
    try {
      const response = await api.get(`/api/orders/${id}`);
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Create new order
   * Endpoint: POST /api/orders
   */
  async create(data) {
    try {
      const response = await api.post("/api/orders", data);
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Update order status
   * Endpoint: PUT /api/orders/{id}/status
   */
  async updateStatus(id, status) {
    try {
      const response = await api.put(`/api/orders/${id}/status`, { status });
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Get order statistics
   * Endpoint: GET /api/orders/stats
   */
  async getStats(params = {}) {
    try {
      const response = await api.get("/api/orders/stats", params);
      return response;
    } catch (error) {
      throw error;
    }
  }
}

// Create global instance
const orderService = new OrderService();
