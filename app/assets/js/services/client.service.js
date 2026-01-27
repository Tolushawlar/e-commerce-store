/**
 * Client Service
 * Handles client management operations (Admin only)
 */

class ClientService {
  /**
   * Get all clients (paginated)
   * Endpoint: GET /api/clients
   */
  async getAll(params = {}) {
    try {
      const response = await api.get("/api/clients", params);
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Get single client
   * Endpoint: GET /api/clients/{id}
   */
  async getById(id) {
    try {
      const response = await api.get(`/api/clients/${id}`);
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Create new client
   * Endpoint: POST /api/clients
   */
  async create(data) {
    try {
      const response = await api.post("/api/clients", data);
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Update client
   * Endpoint: PUT /api/clients/{id}
   */
  async update(id, data) {
    try {
      const response = await api.put(`/api/clients/${id}`, data);
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Delete client
   * Endpoint: DELETE /api/clients/{id}
   */
  async delete(id) {
    try {
      const response = await api.delete(`/api/clients/${id}`);
      return response;
    } catch (error) {
      throw error;
    }
  }
}

// Create global instance
const clientService = new ClientService();
