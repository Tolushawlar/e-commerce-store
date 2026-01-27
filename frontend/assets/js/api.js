/**
 * API Client - Handles all API communication
 */
class APIClient {
  constructor(baseURL = "http://localhost:8000") {
    this.baseURL = baseURL;
  }

  async request(endpoint, options = {}) {
    const url = `${this.baseURL}${endpoint}`;

    const config = {
      headers: {
        "Content-Type": "application/json",
        ...options.headers,
      },
      ...options,
    };

    if (config.body && typeof config.body === "object") {
      config.body = JSON.stringify(config.body);
    }

    try {
      const response = await fetch(url, config);
      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || "Request failed");
      }

      return data;
    } catch (error) {
      console.error("API Error:", error);
      throw error;
    }
  }

  // GET request
  async get(endpoint, params = {}) {
    const queryString = new URLSearchParams(params).toString();
    const url = queryString ? `${endpoint}?${queryString}` : endpoint;

    return this.request(url, {
      method: "GET",
    });
  }

  // POST request
  async post(endpoint, data = {}) {
    return this.request(endpoint, {
      method: "POST",
      body: data,
    });
  }

  // PUT request
  async put(endpoint, data = {}) {
    return this.request(endpoint, {
      method: "PUT",
      body: data,
    });
  }

  // DELETE request
  async delete(endpoint) {
    return this.request(endpoint, {
      method: "DELETE",
    });
  }
}

// Create global API instance
const api = new APIClient();

/**
 * Client API Methods
 */
const clientAPI = {
  getAll: (params) => api.get("/api/clients", params),
  getById: (id) => api.get(`/api/clients/${id}`),
  create: (data) => api.post("/api/clients", data),
  update: (id, data) => api.put(`/api/clients/${id}`, data),
  delete: (id) => api.delete(`/api/clients/${id}`),
};

/**
 * Store API Methods
 */
const storeAPI = {
  getAll: (params) => api.get("/api/stores", params),
  getById: (id, includeCustomization = false) => {
    const params = includeCustomization ? { include: "customization" } : {};
    return api.get(`/api/stores/${id}`, params);
  },
  create: (data) => api.post("/api/stores", data),
  update: (id, data) => api.put(`/api/stores/${id}`, data),
  delete: (id) => api.delete(`/api/stores/${id}`),
  generate: (id) => api.post(`/api/stores/${id}/generate`),
};

/**
 * Product API Methods
 */
const productAPI = {
  getAll: (params) => api.get("/api/products", params),
  getById: (id) => api.get(`/api/products/${id}`),
  getByStore: (storeId, filters = {}) =>
    api.get("/api/products", { store_id: storeId, ...filters }),
  create: (data) => api.post("/api/products", data),
  update: (id, data) => api.put(`/api/products/${id}`, data),
  delete: (id) => api.delete(`/api/products/${id}`),
  getLowStock: (storeId, threshold = 10) =>
    api.get("/api/products/low-stock", { store_id: storeId, threshold }),
};

/**
 * Order API Methods
 */
const orderAPI = {
  getAll: (params) => api.get("/api/orders", params),
  getById: (id) => api.get(`/api/orders/${id}`),
  getByStore: (storeId, filters = {}) =>
    api.get("/api/orders", { store_id: storeId, ...filters }),
  create: (data) => api.post("/api/orders", data),
  updateStatus: (id, status) => api.put(`/api/orders/${id}/status`, { status }),
  getStats: (storeId) => api.get("/api/orders/stats", { store_id: storeId }),
};

/**
 * UI Helper Functions
 */
const UI = {
  showLoading(element) {
    element.innerHTML = `
            <div class="flex items-center justify-center p-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
            </div>
        `;
  },

  showError(element, message) {
    element.innerHTML = `
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <p class="font-semibold">Error</p>
                <p>${message}</p>
            </div>
        `;
  },

  showSuccess(message) {
    // You can implement a toast notification here
    alert(message);
  },

  formatCurrency(amount) {
    return new Intl.NumberFormat("en-NG", {
      style: "currency",
      currency: "NGN",
    }).format(amount);
  },

  formatDate(dateString) {
    return new Date(dateString).toLocaleDateString("en-US", {
      year: "numeric",
      month: "short",
      day: "numeric",
    });
  },
};
