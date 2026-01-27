/**
 * Utility Functions
 */

const utils = {
  /**
   * Format date to readable string
   */
  formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", {
      year: "numeric",
      month: "short",
      day: "numeric",
    });
  },

  /**
   * Format date and time
   */
  formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString("en-US", {
      year: "numeric",
      month: "short",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    });
  },

  /**
   * Format currency
   */
  formatCurrency(amount) {
    return new Intl.NumberFormat("en-US", {
      style: "currency",
      currency: "USD",
    }).format(amount);
  },

  /**
   * Format number with commas
   */
  formatNumber(number) {
    return new Intl.NumberFormat("en-US").format(number);
  },

  /**
   * Truncate text
   */
  truncate(text, length = 50) {
    if (!text) return "";
    return text.length > length ? text.substring(0, length) + "..." : text;
  },

  /**
   * Get status badge class
   */
  getStatusClass(status) {
    const statusClasses = {
      active: "bg-green-100 text-green-800",
      inactive: "bg-gray-100 text-gray-800",
      suspended: "bg-red-100 text-red-800",
      pending: "bg-yellow-100 text-yellow-800",
      processing: "bg-blue-100 text-blue-800",
      completed: "bg-green-100 text-green-800",
      cancelled: "bg-red-100 text-red-800",
      out_of_stock: "bg-red-100 text-red-800",
      maintenance: "bg-orange-100 text-orange-800",
    };
    return statusClasses[status] || "bg-gray-100 text-gray-800";
  },

  /**
   * Debounce function
   */
  debounce(func, wait = 300) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  },

  /**
   * Show toast notification
   */
  toast(message, type = "success") {
    const colors = {
      success: "bg-green-500",
      error: "bg-red-500",
      warning: "bg-yellow-500",
      info: "bg-blue-500",
    };

    const toast = document.createElement("div");
    toast.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300`;
    toast.textContent = message;

    document.body.appendChild(toast);

    setTimeout(() => {
      toast.style.opacity = "0";
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  },

  /**
   * Confirm dialog
   */
  confirm(message, onConfirm, onCancel) {
    if (window.confirm(message)) {
      if (onConfirm) onConfirm();
    } else {
      if (onCancel) onCancel();
    }
  },

  /**
   * Copy to clipboard
   */
  async copyToClipboard(text) {
    try {
      await navigator.clipboard.writeText(text);
      this.toast("Copied to clipboard!");
    } catch (error) {
      this.toast("Failed to copy", "error");
    }
  },

  /**
   * Validate email
   */
  isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  },

  /**
   * Get query parameter
   */
  getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
  },

  /**
   * Set query parameter
   */
  setQueryParam(param, value) {
    const url = new URL(window.location);
    url.searchParams.set(param, value);
    window.history.pushState({}, "", url);
  },

  /**
   * Escape HTML to prevent XSS
   */
  escapeHtml(text) {
    if (!text) return "";
    const map = {
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#039;",
    };
    return text.replace(/[&<>"']/g, (m) => map[m]);
  },
};
