class NotificationService {
  constructor() {
    this.baseUrl = "/api";
  }

  /**
   * Get all notifications
   */
  async getNotifications(filters = {}) {
    const params = new URLSearchParams();

    if (filters.type) params.append("type", filters.type);
    if (filters.is_read !== undefined)
      params.append("is_read", filters.is_read);
    if (filters.priority) params.append("priority", filters.priority);
    if (filters.page) params.append("page", filters.page);
    if (filters.limit) params.append("limit", filters.limit);

    const queryString = params.toString();
    const url = `${this.baseUrl}/notifications${queryString ? "?" + queryString : ""}`;

    return await api.get(url);
  }

  /**
   * Get unread notification count
   */
  async getUnreadCount() {
    return await api.get(`${this.baseUrl}/notifications/unread-count`);
  }

  /**
   * Get recent notifications (last 24 hours)
   */
  async getRecent(limit = 10) {
    return await api.get(`${this.baseUrl}/notifications/recent?limit=${limit}`);
  }

  /**
   * Get notification statistics
   */
  async getStats() {
    return await api.get(`${this.baseUrl}/notifications/stats`);
  }

  /**
   * Mark notification as read
   */
  async markAsRead(notificationId) {
    return await api.put(
      `${this.baseUrl}/notifications/${notificationId}/read`,
    );
  }

  /**
   * Mark all notifications as read
   */
  async markAllAsRead() {
    return await api.put(`${this.baseUrl}/notifications/mark-all-read`);
  }

  /**
   * Delete notification
   */
  async deleteNotification(notificationId) {
    return await api.delete(`${this.baseUrl}/notifications/${notificationId}`);
  }

  /**
   * Get notification preferences
   */
  async getPreferences() {
    return await api.get(`${this.baseUrl}/notification-preferences`);
  }

  /**
   * Update notification preferences
   */
  async updatePreference(notificationType, settings) {
    return await api.put(`${this.baseUrl}/notification-preferences`, {
      notification_type: notificationType,
      ...settings,
    });
  }

  /**
   * Listen for real-time notifications (polling)
   */
  startPolling(callback, interval = 30000) {
    this.pollingInterval = setInterval(async () => {
      try {
        const result = await this.getUnreadCount();
        if (result.success) {
          callback(result.data.count);
        }
      } catch (error) {
        console.error("Polling error:", error);
      }
    }, interval);
  }

  /**
   * Stop polling
   */
  stopPolling() {
    if (this.pollingInterval) {
      clearInterval(this.pollingInterval);
      this.pollingInterval = null;
    }
  }
}

// Export singleton instance
const notificationService = new NotificationService();
