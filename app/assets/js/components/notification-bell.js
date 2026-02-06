/**
 * Notification Bell Component
 * Shows notification dropdown with recent notifications
 */
class NotificationBell {
  constructor(containerId = "notification-bell") {
    this.container = document.getElementById(containerId);
    if (!this.container) {
      console.error(`Container ${containerId} not found`);
      return;
    }

    this.unreadCount = 0;
    this.notifications = [];
    this.isOpen = false;

    this.init();
  }

  /**
   * Initialize notification bell
   */
  init() {
    this.render();
    this.attachEventListeners();
    this.loadNotifications();
    this.startPolling();
  }

  /**
   * Render notification bell
   */
  render() {
    this.container.innerHTML = `
            <div class="relative">
                <!-- Bell Button -->
                <button id="notification-bell-btn" 
                        class="relative p-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <span class="material-symbols-outlined text-2xl">notifications</span>
                    <!-- Badge -->
                    <span id="notification-badge" 
                          class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center hidden">
                        0
                    </span>
                </button>

                <!-- Dropdown -->
                <div id="notification-dropdown" 
                     class="absolute right-0 mt-2 w-96 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 hidden z-50">
                    
                    <!-- Header -->
                    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Notifications
                        </h3>
                        <button id="mark-all-read-btn" 
                                class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium">
                            Mark all read
                        </button>
                    </div>

                    <!-- Notification List -->
                    <div id="notification-list" 
                         class="max-h-96 overflow-y-auto">
                        <!-- Notifications will be inserted here -->
                        <div class="flex flex-col items-center justify-center py-12 text-gray-500 dark:text-gray-400">
                            <span class="material-symbols-outlined text-5xl mb-2">notifications_off</span>
                            <p class="text-sm">No notifications yet</p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="p-3 border-t border-gray-200 dark:border-gray-700">
                        <a href="/client/notifications.php" 
                           class="block text-center text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium">
                            View all notifications
                        </a>
                    </div>
                </div>
            </div>
        `;
  }

  /**
   * Attach event listeners
   */
  attachEventListeners() {
    // Toggle dropdown
    const bellBtn = document.getElementById("notification-bell-btn");
    bellBtn?.addEventListener("click", (e) => {
      e.stopPropagation();
      this.toggleDropdown();
    });

    // Mark all as read
    const markAllBtn = document.getElementById("mark-all-read-btn");
    markAllBtn?.addEventListener("click", () => {
      this.markAllAsRead();
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", (e) => {
      const dropdown = document.getElementById("notification-dropdown");
      if (this.isOpen && !this.container.contains(e.target)) {
        this.closeDropdown();
      }
    });
  }

  /**
   * Toggle dropdown
   */
  toggleDropdown() {
    this.isOpen = !this.isOpen;
    const dropdown = document.getElementById("notification-dropdown");

    if (this.isOpen) {
      dropdown.classList.remove("hidden");
      this.loadNotifications();
    } else {
      dropdown.classList.add("hidden");
    }
  }

  /**
   * Close dropdown
   */
  closeDropdown() {
    this.isOpen = false;
    const dropdown = document.getElementById("notification-dropdown");
    dropdown?.classList.add("hidden");
  }

  /**
   * Load notifications
   */
  async loadNotifications() {
    try {
      // Load recent notifications
      const result = await notificationService.getRecent(10);

      if (result.success) {
        this.notifications = result.data;
        this.renderNotifications();
      }

      // Update unread count
      await this.updateUnreadCount();
    } catch (error) {
      console.error("Error loading notifications:", error);
    }
  }

  /**
   * Update unread count
   */
  async updateUnreadCount() {
    try {
      const result = await notificationService.getUnreadCount();

      if (result.success) {
        this.unreadCount = result.data.count;
        this.updateBadge();
      }
    } catch (error) {
      console.error("Error updating unread count:", error);
    }
  }

  /**
   * Update badge
   */
  updateBadge() {
    const badge = document.getElementById("notification-badge");
    if (!badge) return;

    if (this.unreadCount > 0) {
      badge.textContent = this.unreadCount > 99 ? "99+" : this.unreadCount;
      badge.classList.remove("hidden");
    } else {
      badge.classList.add("hidden");
    }
  }

  /**
   * Render notifications
   */
  renderNotifications() {
    const listContainer = document.getElementById("notification-list");
    if (!listContainer) return;

    if (this.notifications.length === 0) {
      listContainer.innerHTML = `
                <div class="flex flex-col items-center justify-center py-12 text-gray-500 dark:text-gray-400">
                    <span class="material-symbols-outlined text-5xl mb-2">notifications_off</span>
                    <p class="text-sm">No notifications yet</p>
                </div>
            `;
      return;
    }

    listContainer.innerHTML = this.notifications
      .map(
        (notification) => `
            <div class="notification-item p-4 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer ${notification.is_read ? "opacity-60" : ""}"
                 data-id="${notification.id}"
                 onclick="notificationBell.handleNotificationClick(${notification.id}, '${notification.action_url || ""}')">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        ${this.getNotificationIcon(notification.type, notification.priority)}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            ${this.escapeHtml(notification.title)}
                            ${!notification.is_read ? '<span class="inline-block w-2 h-2 bg-indigo-600 rounded-full ml-2"></span>' : ""}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            ${this.escapeHtml(notification.message)}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                            ${this.formatTime(notification.created_at)}
                        </p>
                    </div>
                    <button onclick="event.stopPropagation(); notificationBell.deleteNotification(${notification.id})"
                            class="flex-shrink-0 text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors">
                        <span class="material-symbols-outlined text-lg">delete</span>
                    </button>
                </div>
            </div>
        `,
      )
      .join("");
  }

  /**
   * Get notification icon
   */
  getNotificationIcon(type, priority) {
    const icons = {
      order: "shopping_cart",
      product: "inventory",
      system: "info",
      store: "storefront",
      payment: "payments",
      customer: "person",
    };

    const colors = {
      urgent: "text-red-500",
      high: "text-orange-500",
      normal: "text-blue-500",
      low: "text-gray-500",
    };

    const icon = icons[type] || "notifications";
    const color = colors[priority] || "text-blue-500";

    return `<span class="material-symbols-outlined ${color}">${icon}</span>`;
  }

  /**
   * Handle notification click
   */
  async handleNotificationClick(id, actionUrl) {
    // Mark as read
    await notificationService.markAsRead(id);

    // Update UI
    this.loadNotifications();

    // Navigate if action URL exists
    if (actionUrl) {
      window.location.href = actionUrl;
    }
  }

  /**
   * Delete notification
   */
  async deleteNotification(id) {
    try {
      const result = await notificationService.deleteNotification(id);

      if (result.success) {
        toast.success("Notification deleted");
        this.loadNotifications();
      } else {
        toast.error("Failed to delete notification");
      }
    } catch (error) {
      console.error("Error deleting notification:", error);
      toast.error("Error deleting notification");
    }
  }

  /**
   * Mark all as read
   */
  async markAllAsRead() {
    try {
      const result = await notificationService.markAllAsRead();

      if (result.success) {
        toast.success(result.message);
        this.loadNotifications();
      }
    } catch (error) {
      console.error("Error marking all as read:", error);
      toast.error("Failed to mark all as read");
    }
  }

  /**
   * Start polling for new notifications
   */
  startPolling() {
    notificationService.startPolling((count) => {
      this.unreadCount = count;
      this.updateBadge();
    }, 30000); // Poll every 30 seconds
  }

  /**
   * Format time
   */
  formatTime(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);

    if (diffMins < 1) return "Just now";
    if (diffMins < 60)
      return `${diffMins} minute${diffMins > 1 ? "s" : ""} ago`;
    if (diffHours < 24)
      return `${diffHours} hour${diffHours > 1 ? "s" : ""} ago`;
    if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? "s" : ""} ago`;

    return date.toLocaleDateString();
  }

  /**
   * Escape HTML
   */
  escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
  }
}

// Export for manual initialization
// Note: Initialize in header files with specific container ID
