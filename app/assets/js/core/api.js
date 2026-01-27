/**
 * API Client - Handles all API communication
 * Base URL points to the backend API server
 */
class APIClient {
  constructor(baseURL = "http://localhost:8000") {
    this.baseURL = baseURL;
    this.isRefreshing = false;
    this.failedQueue = [];
  }

  processQueue(error, token = null) {
    this.failedQueue.forEach((prom) => {
      if (error) {
        prom.reject(error);
      } else {
        prom.resolve(token);
      }
    });

    this.failedQueue = [];
  }

  /**
   * Make HTTP request
   */
  async request(endpoint, options = {}, retryCount = 0) {
    const url = `${this.baseURL}${endpoint}`;

    const config = {
      headers: {
        "Content-Type": "application/json",
        ...options.headers,
      },
      ...options,
    };

    // Add Authorization header if token exists
    const token = localStorage.getItem("auth_token");
    if (token) {
      config.headers["Authorization"] = `Bearer ${token}`;
    }

    if (config.body && typeof config.body === "object") {
      config.body = JSON.stringify(config.body);
    }

    try {
      const response = await fetch(url, config);
      const data = await response.json();

      // Handle token expiration (401 Unauthorized)
      if (response.status === 401 && !endpoint.includes("/auth/refresh")) {
        // Try to refresh token first
        const refreshToken = localStorage.getItem("refresh_token");

        if (refreshToken && retryCount === 0) {
          // If already refreshing, queue this request
          if (this.isRefreshing) {
            return new Promise((resolve, reject) => {
              this.failedQueue.push({ resolve, reject });
            })
              .then(() => {
                return this.request(endpoint, options, retryCount + 1);
              })
              .catch((err) => {
                throw err;
              });
          }

          this.isRefreshing = true;

          try {
            // Attempt to refresh token
            const refreshResponse = await fetch(
              `${this.baseURL}/api/auth/refresh`,
              {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ refresh_token: refreshToken }),
              },
            );

            const refreshData = await refreshResponse.json();

            if (refreshResponse.ok && refreshData.success) {
              // Save new token
              localStorage.setItem("auth_token", refreshData.data.token);
              this.processQueue(null, refreshData.data.token);
              this.isRefreshing = false;

              // Retry original request with new token
              return this.request(endpoint, options, retryCount + 1);
            } else {
              throw new Error("Token refresh failed");
            }
          } catch (refreshError) {
            this.processQueue(refreshError, null);
            this.isRefreshing = false;

            // Clear all auth data
            localStorage.removeItem("auth_token");
            localStorage.removeItem("auth_user");
            localStorage.removeItem("refresh_token");

            // Redirect to login
            const loginUrl = window.location.pathname.includes("/admin/")
              ? "/auth/login.php"
              : "/auth/login.php";

            window.location.href = `${loginUrl}?expired=1&message=${encodeURIComponent("Session expired. Please login again.")}`;
            throw new Error("Session expired");
          }
        } else {
          // No refresh token or already retried, redirect to login
          localStorage.removeItem("auth_token");
          localStorage.removeItem("auth_user");
          localStorage.removeItem("refresh_token");

          const loginUrl = window.location.pathname.includes("/admin/")
            ? "/auth/login.php"
            : "/auth/login.php";

          window.location.href = `${loginUrl}?expired=1&message=${encodeURIComponent(data.message || "Session expired. Please login again.")}`;
          throw new Error(data.message || "Session expired");
        }
      }

      if (!response.ok) {
        throw new Error(data.message || "Request failed");
      }

      return data;
    } catch (error) {
      if (error.message.includes("Failed to fetch")) {
        throw new Error("Unable to connect to server");
      }
      throw error;
    }
  }

  /**
   * GET request
   */
  async get(endpoint, params = {}) {
    const queryString = new URLSearchParams(params).toString();
    const url = queryString ? `${endpoint}?${queryString}` : endpoint;
    return this.request(url, { method: "GET" });
  }

  /**
   * POST request
   */
  async post(endpoint, data = {}) {
    return this.request(endpoint, {
      method: "POST",
      body: data,
    });
  }

  /**
   * PUT request
   */
  async put(endpoint, data = {}) {
    return this.request(endpoint, {
      method: "PUT",
      body: data,
    });
  }

  /**
   * DELETE request
   */
  async delete(endpoint) {
    return this.request(endpoint, {
      method: "DELETE",
    });
  }
}

// Create global api instance
const api = new APIClient();
