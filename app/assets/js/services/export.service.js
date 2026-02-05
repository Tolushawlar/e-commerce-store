/**
 * ExportService
 * Handles data export functionality
 */
class ExportService {
  constructor(apiClient) {
    this.api = apiClient || new APIClient();
  }

  /**
   * Export orders to CSV
   */
  async exportOrders(storeId, filters = {}) {
    try {
      // Build query params
      const params = new URLSearchParams();

      if (filters.status) params.append("status", filters.status);
      if (filters.payment_status)
        params.append("payment_status", filters.payment_status);
      if (filters.from_date) params.append("from_date", filters.from_date);
      if (filters.to_date) params.append("to_date", filters.to_date);

      const queryString = params.toString();
      const url = `/api/stores/${storeId}/orders/export${queryString ? "?" + queryString : ""}`;

      // Trigger download
      this.downloadFile(
        url,
        `orders_export_${new Date().toISOString().split("T")[0]}.csv`,
      );

      return { success: true };
    } catch (error) {
      console.error("Export orders failed:", error);
      throw error;
    }
  }

  /**
   * Export products to CSV
   */
  async exportProducts(storeId, filters = {}) {
    try {
      // Build query params
      const params = new URLSearchParams();

      if (filters.category_id)
        params.append("category_id", filters.category_id);
      if (filters.status) params.append("status", filters.status);
      if (filters.search) params.append("search", filters.search);

      const queryString = params.toString();
      const url = `/api/stores/${storeId}/products/export${queryString ? "?" + queryString : ""}`;

      // Trigger download
      this.downloadFile(
        url,
        `products_export_${new Date().toISOString().split("T")[0]}.csv`,
      );

      return { success: true };
    } catch (error) {
      console.error("Export products failed:", error);
      throw error;
    }
  }

  /**
   * Export customers to CSV
   */
  async exportCustomers(storeId) {
    try {
      const url = `/api/stores/${storeId}/customers/export`;

      // Trigger download
      this.downloadFile(
        url,
        `customers_export_${new Date().toISOString().split("T")[0]}.csv`,
      );

      return { success: true };
    } catch (error) {
      console.error("Export customers failed:", error);
      throw error;
    }
  }

  /**
   * Download file from URL
   */
  downloadFile(url, filename) {
    // Get auth token
    const token = localStorage.getItem("auth_token");

    // Build full URL with API base
    const fullUrl = this.api.baseURL + url;

    // Create a temporary link and trigger download
    fetch(fullUrl, {
      method: "GET",
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "text/csv",
      },
    })
      .then((response) => {
        // Check content type to ensure it's actually a CSV
        const contentType = response.headers.get("content-type");

        if (!response.ok) {
          // If response is not ok, try to get error message
          return response
            .json()
            .then((data) => {
              throw new Error(data.message || "Export failed");
            })
            .catch(() => {
              throw new Error(`Export failed with status ${response.status}`);
            });
        }

        // Verify it's actually a CSV file
        if (
          contentType &&
          !contentType.includes("text/csv") &&
          !contentType.includes("application/csv")
        ) {
          console.error("Unexpected content type:", contentType);
          throw new Error(
            "Server returned unexpected content type. Expected CSV.",
          );
        }

        return response.blob();
      })
      .then((blob) => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.style.display = "none";
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
      })
      .catch((error) => {
        console.error("Download failed:", error);
        throw error;
      });
  }
}

// Make available globally
if (typeof window !== "undefined") {
  window.ExportService = ExportService;
}
