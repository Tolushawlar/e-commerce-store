/**
 * Toast Notification System
 * Displays temporary notification messages
 */
class ToastNotification {
  constructor() {
    this.container = this.createContainer();
    this.maxToasts = 5;
    this.defaultDuration = 5000;
  }

  /**
   * Create toast container
   */
  createContainer() {
    let container = document.getElementById("toast-container");
    if (!container) {
      container = document.createElement("div");
      container.id = "toast-container";
      container.className = "fixed top-4 right-4 z-50 space-y-3 max-w-md";
      document.body.appendChild(container);
    }
    return container;
  }

  /**
   * Show toast notification
   */
  show(message, type = "info", duration = null) {
    const toast = this.createToast(message, type);
    this.container.insertBefore(toast, this.container.firstChild);

    // Remove oldest toast if exceeding max
    const toasts = this.container.querySelectorAll(".toast");
    if (toasts.length > this.maxToasts) {
      toasts[toasts.length - 1].remove();
    }

    // Auto remove
    setTimeout(() => {
      this.removeToast(toast);
    }, duration || this.defaultDuration);

    // Animate in
    setTimeout(() => {
      toast.classList.remove("translate-x-full", "opacity-0");
    }, 10);
  }

  /**
   * Create toast element
   */
  createToast(message, type) {
    const toast = document.createElement("div");
    toast.className =
      "toast transform translate-x-full opacity-0 transition-all duration-300 ease-out";

    const config = this.getToastConfig(type);

    toast.innerHTML = `
            <div class="flex items-start gap-3 p-4 rounded-lg shadow-lg border ${config.bgClass} ${config.borderClass}">
                <div class="flex-shrink-0">
                    <span class="material-symbols-outlined ${config.iconClass}">
                        ${config.icon}
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium ${config.textClass}">
                        ${this.escapeHtml(message)}
                    </p>
                </div>
                <button onclick="this.closest('.toast').remove()" 
                        class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>
        `;

    return toast;
  }

  /**
   * Get toast configuration by type
   */
  getToastConfig(type) {
    const configs = {
      success: {
        icon: "check_circle",
        iconClass: "text-green-500",
        bgClass: "bg-white dark:bg-gray-800",
        borderClass: "border-green-500",
        textClass: "text-gray-900 dark:text-white",
      },
      error: {
        icon: "error",
        iconClass: "text-red-500",
        bgClass: "bg-white dark:bg-gray-800",
        borderClass: "border-red-500",
        textClass: "text-gray-900 dark:text-white",
      },
      warning: {
        icon: "warning",
        iconClass: "text-yellow-500",
        bgClass: "bg-white dark:bg-gray-800",
        borderClass: "border-yellow-500",
        textClass: "text-gray-900 dark:text-white",
      },
      info: {
        icon: "info",
        iconClass: "text-blue-500",
        bgClass: "bg-white dark:bg-gray-800",
        borderClass: "border-blue-500",
        textClass: "text-gray-900 dark:text-white",
      },
      notification: {
        icon: "notifications",
        iconClass: "text-purple-500",
        bgClass: "bg-white dark:bg-gray-800",
        borderClass: "border-purple-500",
        textClass: "text-gray-900 dark:text-white",
      },
    };

    return configs[type] || configs.info;
  }

  /**
   * Remove toast with animation
   */
  removeToast(toast) {
    toast.classList.add("translate-x-full", "opacity-0");
    setTimeout(() => {
      toast.remove();
    }, 300);
  }

  /**
   * Escape HTML to prevent XSS
   */
  escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
  }

  /**
   * Show notification-specific toast
   */
  notification(notification) {
    const message = `<strong>${notification.title}</strong><br>${notification.message}`;
    const duration = notification.priority === "urgent" ? 10000 : 5000;

    const toast = this.createNotificationToast(notification);
    this.container.insertBefore(toast, this.container.firstChild);

    // Auto remove
    setTimeout(() => {
      this.removeToast(toast);
    }, duration);

    // Animate in
    setTimeout(() => {
      toast.classList.remove("translate-x-full", "opacity-0");
    }, 10);
  }

  /**
   * Create notification toast with action button
   */
  createNotificationToast(notification) {
    const toast = document.createElement("div");
    toast.className =
      "toast transform translate-x-full opacity-0 transition-all duration-300 ease-out";

    const priorityColors = {
      urgent: "border-red-500",
      high: "border-orange-500",
      normal: "border-blue-500",
      low: "border-gray-500",
    };

    const borderClass =
      priorityColors[notification.priority] || "border-blue-500";

    toast.innerHTML = `
            <div class="flex items-start gap-3 p-4 rounded-lg shadow-lg border ${borderClass} bg-white dark:bg-gray-800 min-w-[320px] max-w-md">
                <div class="flex-shrink-0">
                    <span class="material-symbols-outlined text-purple-500">
                        notifications_active
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white mb-1">
                        ${this.escapeHtml(notification.title)}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        ${this.escapeHtml(notification.message)}
                    </p>
                    ${
                      notification.action_url
                        ? `
                        <a href="${notification.action_url}" 
                           class="inline-block mt-2 text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium"
                           onclick="notificationService.markAsRead(${notification.id})">
                            View Details →
                        </a>
                    `
                        : ""
                    }
                </div>
                <button onclick="this.closest('.toast').remove(); notificationService.markAsRead(${notification.id})" 
                        class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>
        `;

    return toast;
  }

  /**
   * Convenience methods
   */
  success(message, duration = null) {
    this.show(message, "success", duration);
  }

  error(message, duration = null) {
    this.show(message, "error", duration);
  }

  warning(message, duration = null) {
    this.show(message, "warning", duration);
  }

  info(message, duration = null) {
    this.show(message, "info", duration);
  }
}

// Export singleton instance
const toast = new ToastNotification();
