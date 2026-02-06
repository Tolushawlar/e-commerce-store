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

  /**
   * Import products from CSV file
   * Endpoint: POST /api/products/import-csv
   */
  async importCSV(file, storeId) {
    try {
      const formData = new FormData();
      formData.append("csv_file", file);
      formData.append("store_id", storeId);

      const token = auth.getToken();
      const response = await fetch(`${api.baseURL}/api/products/import-csv`, {
        method: "POST",
        headers: {
          Authorization: `Bearer ${token}`,
        },
        body: formData,
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || "CSV import failed");
      }

      return data;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Download CSV template
   * Endpoint: GET /api/products/csv-template
   */
  async downloadTemplate() {
    try {
      const token = auth.getToken();
      const response = await fetch(`${api.baseURL}/api/products/csv-template`, {
        method: "GET",
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      if (!response.ok) {
        throw new Error("Failed to download template");
      }

      // Create blob from response
      const blob = await response.blob();

      // Create download link
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = "product_import_template.csv";
      document.body.appendChild(a);
      a.click();
      window.URL.revokeObjectURL(url);
      document.body.removeChild(a);

      return true;
    } catch (error) {
      throw error;
    }
  }
}

// Create global instance
const productService = new ProductService();
