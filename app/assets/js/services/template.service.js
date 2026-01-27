/**
 * Template Service
 * Handles all template-related API calls
 */

const templateService = {
  /**
   * Get all templates with optional pagination
   * @param {Object} params - Query parameters (page, limit)
   * @returns {Promise}
   */
  async getAll(params = {}) {
    const queryParams = new URLSearchParams();

    if (params.page) queryParams.append("page", params.page);
    if (params.limit) queryParams.append("limit", params.limit);

    const queryString = queryParams.toString();
    const url = `/api/templates${queryString ? "?" + queryString : ""}`;

    return await api.request(url, {
      method: "GET",
    });
  },

  /**
   * Get template by ID
   * @param {number} id - Template ID
   * @returns {Promise}
   */
  async getById(id) {
    return await api.request(`/api/templates/${id}`, {
      method: "GET",
    });
  },

  /**
   * Create new template
   * @param {Object} data - Template data
   * @returns {Promise}
   */
  async create(data) {
    return await api.request("/api/templates", {
      method: "POST",
      body: JSON.stringify(data),
    });
  },

  /**
   * Update existing template
   * @param {number} id - Template ID
   * @param {Object} data - Updated template data
   * @returns {Promise}
   */
  async update(id, data) {
    return await api.request(`/api/templates/${id}`, {
      method: "PUT",
      body: JSON.stringify(data),
    });
  },

  /**
   * Delete template
   * @param {number} id - Template ID
   * @returns {Promise}
   */
  async delete(id) {
    return await api.request(`/api/templates/${id}`, {
      method: "DELETE",
    });
  },
};
