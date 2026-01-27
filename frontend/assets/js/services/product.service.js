/**
 * Product Service
 * Handles product management operations
 */

class ProductService {
  /**
   * Get all products (paginated)
   * Endpoint: GET /api/products
   */
  async getAll(params = {}) {
    try {
      const response = await api.get("/api/products", params);
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Get single product
   * Endpoint: GET /api/products/{id}
   */
  async getById(id) {
    try {
      const response = await api.get(`/api/products/${id}`);
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Create new product
   * Endpoint: POST /api/products
   */
  async create(data) {
    try {
      const response = await api.post("/api/products", data);
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Update product
   * Endpoint: PUT /api/products/{id}
   */
  async update(id, data) {
    try {
      const response = await api.put(`/api/products/${id}`, data);
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Delete product
   * Endpoint: DELETE /api/products/{id}
   */
  async delete(id) {
    try {
      const response = await api.delete(`/api/products/${id}`);
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Get low stock products
   * Endpoint: GET /api/products/low-stock
   */
  async getLowStock(params = {}) {
    try {
      const response = await api.get("/api/products/low-stock", params);
      return response;
    } catch (error) {
      throw error;
    }
  }
}

// Create global instance
const productService = new ProductService();
