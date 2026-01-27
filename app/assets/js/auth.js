/**
 * Authentication Module
 * Handles user authentication and token management
 */

class AuthService {
  constructor() {
    this.token = this.getToken();
    this.user = this.getUser();
  }

  /**
   * Login as super admin
   */
  async adminLogin(email, password) {
    try {
      const response = await api.post("/api/auth/admin/login", {
        email,
        password,
      });

      if (response.success) {
        this.saveAuth(response.data.token, response.data.user);
        return response;
      }

      throw new Error(response.message);
    } catch (error) {
      throw error;
    }
  }

  /**
   * Login as client
   */
  async clientLogin(email, password) {
    try {
      const response = await api.post("/api/auth/client/login", {
        email,
        password,
      });

      if (response.success) {
        this.saveAuth(response.data.token, response.data.user);
        return response;
      }

      throw new Error(response.message);
    } catch (error) {
      throw error;
    }
  }

  /**
   * Register new client
   */
  async clientRegister(data) {
    try {
      const response = await api.post("/api/auth/client/register", data);

      if (response.success) {
        this.saveAuth(response.data.token, response.data.user);
        return response;
      }

      throw new Error(response.message);
    } catch (error) {
      throw error;
    }
  }

  /**
   * Verify current token
   */
  async verify() {
    try {
      const response = await api.get("/api/auth/verify");

      if (response.success) {
        this.user = response.data.user;
        this.saveUser(response.data.user);
        return response;
      }

      throw new Error(response.message);
    } catch (error) {
      this.logout();
      throw error;
    }
  }

  /**
   * Refresh token
   */
  async refresh() {
    try {
      const response = await api.post("/api/auth/refresh");

      if (response.success) {
        this.token = response.data.token;
        this.saveToken(response.data.token);
        return response;
      }

      throw new Error(response.message);
    } catch (error) {
      this.logout();
      throw error;
    }
  }

  /**
   * Change password
   */
  async changePassword(currentPassword, newPassword, confirmPassword) {
    try {
      const response = await api.post("/api/auth/change-password", {
        current_password: currentPassword,
        new_password: newPassword,
        confirm_password: confirmPassword,
      });

      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Logout
   */
  logout() {
    localStorage.removeItem("auth_token");
    localStorage.removeItem("auth_user");
    this.token = null;
    this.user = null;

    // Redirect to login
    window.location.href = "/frontend/login.php";
  }

  /**
   * Save authentication data
   */
  saveAuth(token, user) {
    this.token = token;
    this.user = user;
    this.saveToken(token);
    this.saveUser(user);
  }

  /**
   * Save token to localStorage
   */
  saveToken(token) {
    localStorage.setItem("auth_token", token);
  }

  /**
   * Save user to localStorage
   */
  saveUser(user) {
    localStorage.setItem("auth_user", JSON.stringify(user));
  }

  /**
   * Get token from localStorage
   */
  getToken() {
    return localStorage.getItem("auth_token");
  }

  /**
   * Get user from localStorage
   */
  getUser() {
    const user = localStorage.getItem("auth_user");
    return user ? JSON.parse(user) : null;
  }

  /**
   * Check if user is authenticated
   */
  isAuthenticated() {
    return !!this.token;
  }

  /**
   * Check if user is admin
   */
  isAdmin() {
    return this.user && this.user.role === "admin";
  }

  /**
   * Check if user is client
   */
  isClient() {
    return this.user && this.user.role === "client";
  }

  /**
   * Get authorization header
   */
  getAuthHeader() {
    return this.token ? { Authorization: `Bearer ${this.token}` } : {};
  }
}

// Create global auth instance
const auth = new AuthService();

// Update API client to include auth token
class AuthenticatedAPIClient extends APIClient {
  async request(endpoint, options = {}) {
    // Add auth header if token exists
    if (auth.token) {
      options.headers = {
        ...options.headers,
        Authorization: `Bearer ${auth.token}`,
      };
    }

    try {
      return await super.request(endpoint, options);
    } catch (error) {
      // If unauthorized, logout
      if (
        error.message.includes("token") ||
        error.message.includes("Unauthorized")
      ) {
        auth.logout();
      }
      throw error;
    }
  }
}

// Replace global API instance with authenticated version
const api = new AuthenticatedAPIClient();

/**
 * Auto-verify token on page load
 */
document.addEventListener("DOMContentLoaded", async () => {
  if (auth.isAuthenticated()) {
    try {
      await auth.verify();
    } catch (error) {
      console.error("Token verification failed:", error);
    }
  }
});

/**
 * Auto-refresh token before expiration
 */
setInterval(async () => {
  if (auth.isAuthenticated()) {
    try {
      await auth.refresh();
    } catch (error) {
      console.error("Token refresh failed:", error);
    }
  }
}, 1800000); // Refresh every 30 minutes
