/**
 * Store Service
 * Handles store management operations
 */

class StoreService {
  /**
   * Get all stores (paginated)
   * Endpoint: GET /api/stores
   */
  async getAll(params = {}) {
    try {
      const response = await api.get("/api/stores", params);
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Get single store
   * Endpoint: GET /api/stores/{id}
   */
  async getById(id) {
    try {
      const response = await api.get(`/api/stores/${id}`);
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Create new store
   * Endpoint: POST /api/stores
   */
  async create(data) {
    try {
      const response = await api.post("/api/stores", data);
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Update store
   * Endpoint: PUT /api/stores/{id}
   */
  async update(id, data) {
    try {
      const response = await api.put(`/api/stores/${id}`, data);
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Delete store
   * Endpoint: DELETE /api/stores/{id}
   */
  async delete(id) {
    try {
      const response = await api.delete(`/api/stores/${id}`);
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Generate store files
   * Endpoint: POST /api/stores/{id}/generate
   */
  async generate(id) {
    try {
      const response = await api.post(`/api/stores/${id}/generate`);
      return response;
    } catch (error) {
      throw error;
    }
  }
}

// Create global instance
const storeService = new StoreService();
