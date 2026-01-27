/**
 * Authentication Service
 * Handles user authentication and session management
 */

class AuthService {
  constructor() {
    this.token = this.getToken();
    this.user = this.getUser();
  }

  /**
   * Admin Login
   * Endpoint: POST /api/auth/admin/login
   */
  async adminLogin(email, password) {
    try {
      const response = await api.post("/api/auth/admin/login", {
        email,
        password,
      });

      if (response.success) {
        this.saveAuth(
          response.data.token,
          response.data.user,
          response.data.refresh_token,
        );
        return response;
      }

      throw new Error(response.message);
    } catch (error) {
      throw error;
    }
  }

  /**
   * Client Login
   * Endpoint: POST /api/auth/client/login
   */
  async clientLogin(email, password) {
    try {
      const response = await api.post("/api/auth/client/login", {
        email,
        password,
      });

      if (response.success) {
        this.saveAuth(
          response.data.token,
          response.data.user,
          response.data.refresh_token,
        );
        return response;
      }

      throw new Error(response.message);
    } catch (error) {
      throw error;
    }
  }

  /**
   * Client Registration
   * Endpoint: POST /api/auth/register
   */
  async register(data) {
    try {
      const response = await api.post("/api/auth/register", data);

      if (response.success) {
        return response;
      }

      throw new Error(response.message);
    } catch (error) {
      throw error;
    }
  }

  /**
   * Logout
   * Endpoint: POST /api/auth/logout
   */
  async logout() {
    try {
      await api.post("/api/auth/logout");
    } catch (error) {
      console.error("Logout error:", error);
    } finally {
      this.clearAuth();
      window.location.href = "/auth/login.php";
    }
  }

  /**
   * Get Current User
   * Endpoint: GET /api/auth/me
   */
  async getCurrentUser() {
    try {
      const response = await api.get("/api/auth/me");

      if (response.success) {
        this.user = response.data;
        localStorage.setItem("auth_user", JSON.stringify(response.data));
        return response.data;
      }

      throw new Error(response.message);
    } catch (error) {
      throw error;
    }
  }

  /**
   * Refresh Token
   * Endpoint: POST /api/auth/refresh
   */
  async refreshToken() {
    try {
      const refreshToken = localStorage.getItem("refresh_token");
      if (!refreshToken) {
        throw new Error("No refresh token available");
      }

      const response = await api.post("/api/auth/refresh", {
        refresh_token: refreshToken,
      });

      if (response.success) {
        this.token = response.data.token;
        localStorage.setItem("auth_token", response.data.token);
        return response.data.token;
      }

      throw new Error(response.message);
    } catch (error) {
      this.clearAuth();
      throw error;
    }
  }

  /**
   * Request Password Reset
   * Endpoint: POST /api/auth/password/reset-request
   */
  async requestPasswordReset(email) {
    try {
      const response = await api.post("/api/auth/password/reset-request", {
        email,
      });
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Reset Password
   * Endpoint: POST /api/auth/password/reset
   */
  async resetPassword(token, password) {
    try {
      const response = await api.post("/api/auth/password/reset", {
        token,
        password,
      });
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Change Password
   * Endpoint: PUT /api/auth/password/change
   */
  async changePassword(currentPassword, newPassword) {
    try {
      const response = await api.put("/api/auth/password/change", {
        current_password: currentPassword,
        new_password: newPassword,
      });
      return response;
    } catch (error) {
      throw error;
    }
  }

  /**
   * Save authentication data
   */
  saveAuth(token, user, refreshToken = null) {
    this.token = token;
    this.user = user;
    localStorage.setItem("auth_token", token);
    localStorage.setItem("auth_user", JSON.stringify(user));
    if (refreshToken) {
      localStorage.setItem("refresh_token", refreshToken);
    }
  }

  /**
   * Clear authentication data
   */
  clearAuth() {
    this.token = null;
    this.user = null;
    localStorage.removeItem("auth_token");
    localStorage.removeItem("auth_user");
    localStorage.removeItem("refresh_token");
  }

  /**
   * Get stored token
   */
  getToken() {
    return localStorage.getItem("auth_token");
  }

  /**
   * Get stored user
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
   * Require authentication (redirect if not authenticated)
   */
  requireAuth() {
    if (!this.isAuthenticated()) {
      window.location.href = "/auth/login.php";
      return false;
    }
    return true;
  }

  /**
   * Require admin role (redirect if not admin)
   */
  requireAdmin() {
    if (!this.requireAuth()) return false;

    if (!this.isAdmin()) {
      window.location.href = "/client/dashboard.php";
      return false;
    }
    return true;
  }

  /**
   * Require client role (redirect if not client)
   */
  requireClient() {
    if (!this.requireAuth()) return false;

    if (!this.isClient()) {
      window.location.href = "/admin/dashboard.php";
      return false;
    }
    return true;
  }
}

// Create global auth instance
const auth = new AuthService();
