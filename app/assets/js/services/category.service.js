/**
 * Category Service
 * Handles category management operations
 */

class CategoryService {
  /**
   * Get all categories
   * Endpoint: GET /api/categories
   */
  async getAll(params = {}) {
    try {
      const response = await api.get("/api/categories", params);
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Get categories as tree structure
   * Endpoint: GET /api/categories?tree=true
   */
  async getTree(storeId, status = null) {
    try {
      const params = { store_id: storeId, tree: true };
      if (status) params.status = status;
      const response = await api.get("/api/categories", params);
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Get single category by ID
   * Endpoint: GET /api/categories/{id}
   */
  async getById(id) {
    try {
      const response = await api.get(`/api/categories/${id}`);
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Get category by slug
   * Endpoint: GET /api/categories/slug/{slug}
   */
  async getBySlug(slug, storeId) {
    try {
      const response = await api.get(`/api/categories/slug/${slug}`, {
        store_id: storeId,
      });
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Get popular categories
   * Endpoint: GET /api/categories/popular
   */
  async getPopular(storeId, limit = 10) {
    try {
      const response = await api.get("/api/categories/popular", {
        store_id: storeId,
        limit: limit,
      });
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Create new category
   * Endpoint: POST /api/categories
   */
  async create(data) {
    try {
      const response = await api.post("/api/categories", data);
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Update category
   * Endpoint: PUT /api/categories/{id}
   */
  async update(id, data) {
    try {
      const response = await api.put(`/api/categories/${id}`, data);
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Delete category
   * Endpoint: DELETE /api/categories/{id}
   */
  async delete(id) {
    try {
      const response = await api.delete(`/api/categories/${id}`);
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Get categories for a specific store with optional filters
   */
  async getByStore(storeId, filters = {}) {
    try {
      const params = { store_id: storeId, ...filters };
      const response = await api.get("/api/categories", params);
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Get only top-level categories (no parent)
   */
  async getTopLevel(storeId, status = "active") {
    try {
      const params = {
        store_id: storeId,
        parent_id: "null",
        status: status,
      };
      const response = await api.get("/api/categories", params);
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Get subcategories of a parent category
   */
  async getSubcategories(parentId) {
    try {
      const category = await this.getById(parentId);
      return {
        success: true,
        data: {
          categories: category.data.subcategories || [],
        },
      };
    } catch (error) {
      throw error;
    }
  }
}

// Create global instance
const categoryService = new CategoryService();
