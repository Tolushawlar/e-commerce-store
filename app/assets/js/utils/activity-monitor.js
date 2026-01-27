/**
 * Activity Monitor
 * Tracks user activity and automatically refreshes tokens to keep session alive
 */
class ActivityMonitor {
  constructor() {
    this.lastActivity = Date.now();
    this.refreshInterval = 12 * 60 * 1000; // Refresh every 12 minutes (before 15min expiration)
    this.inactivityTimeout = 30 * 60 * 1000; // 30 minutes of inactivity
    this.warningTime = 2 * 60 * 1000; // Show warning 2 minutes before expiration
    this.activityTimer = null;
    this.refreshTimer = null;
    this.warningTimer = null;
    this.isActive = false;
    this.tokenExpiresAt = null;
  }

  /**
   * Start monitoring user activity
   */
  start() {
    if (this.isActive) return;

    this.isActive = true;
    this.lastActivity = Date.now();
    this.setupTokenExpiration();

    // Listen for user activity
    const events = [
      "mousedown",
      "mousemove",
      "keypress",
      "scroll",
      "touchstart",
      "click",
    ];

    events.forEach((event) => {
      document.addEventListener(event, this.handleActivity.bind(this), {
        passive: true,
      });
    });

    // Start refresh timer
    this.startRefreshTimer();

    console.log("Activity monitor started");
  }

  /**
   * Stop monitoring
   */
  stop() {
    if (!this.isActive) return;

    this.isActive = false;
    const events = [
      "mousedown",
      "mousemove",
      "keypress",
      "scroll",
      "touchstart",
      "click",
    ];

    events.forEach((event) => {
      document.removeEventListener(event, this.handleActivity.bind(this));
    });

    if (this.activityTimer) clearTimeout(this.activityTimer);
    if (this.refreshTimer) clearTimeout(this.refreshTimer);
    if (this.warningTimer) clearTimeout(this.warningTimer);

    console.log("Activity monitor stopped");
  }

  /**
   * Handle user activity
   */
  handleActivity() {
    this.lastActivity = Date.now();

    // Clear inactivity timer
    if (this.activityTimer) {
      clearTimeout(this.activityTimer);
    }

    // Set new inactivity timer
    this.activityTimer = setTimeout(() => {
      this.handleInactivity();
    }, this.inactivityTimeout);
  }

  /**
   * Handle user inactivity
   */
  handleInactivity() {
    console.log("User inactive for 30 minutes");

    // Show inactivity warning
    if (typeof utils !== "undefined" && utils.confirm) {
      utils.confirm(
        "You have been inactive. Do you want to stay logged in?",
        () => {
          // User wants to stay - refresh token
          this.refreshToken();
        },
        () => {
          // User wants to logout or no response
          if (typeof auth !== "undefined") {
            auth.logout();
          }
        },
      );
    }
  }

  /**
   * Start automatic token refresh timer
   */
  startRefreshTimer() {
    if (this.refreshTimer) clearTimeout(this.refreshTimer);

    this.refreshTimer = setTimeout(async () => {
      await this.refreshToken();
      this.startRefreshTimer(); // Schedule next refresh
    }, this.refreshInterval);
  }

  /**
   * Set token expiration and warning timer
   */
  setupTokenExpiration() {
    // Access tokens expire in 15 minutes (900 seconds)
    this.tokenExpiresAt = Date.now() + 15 * 60 * 1000;

    // Show warning 2 minutes before expiration (at 13 minutes)
    if (this.warningTimer) clearTimeout(this.warningTimer);

    this.warningTimer = setTimeout(
      () => {
        this.showExpirationWarning();
      },
      13 * 60 * 1000,
    );
  }

  /**
   * Show expiration warning
   */
  showExpirationWarning() {
    const timeLeft = Math.floor((this.tokenExpiresAt - Date.now()) / 1000 / 60);

    if (timeLeft <= 0) return;

    // Create warning notification
    const warningDiv = document.createElement("div");
    warningDiv.id = "token-expiration-warning";
    warningDiv.className =
      "fixed top-4 right-4 z-50 bg-amber-50 border border-amber-200 rounded-xl p-4 shadow-lg max-w-sm";
    warningDiv.innerHTML = `
      <div class="flex items-start gap-3">
        <span class="material-symbols-outlined text-amber-600 text-2xl">schedule</span>
        <div class="flex-1">
          <h4 class="font-bold text-amber-900 mb-1">Session Expiring Soon</h4>
          <p class="text-sm text-amber-800 mb-3">Your session will expire in ${timeLeft} minute${timeLeft > 1 ? "s" : ""}. Do you want to stay logged in?</p>
          <div class="flex gap-2">
            <button onclick="activityMonitor.extendSession()" 
              class="px-4 py-2 bg-amber-600 text-white rounded-lg text-sm font-semibold hover:bg-amber-700">
              Stay Logged In
            </button>
            <button onclick="activityMonitor.dismissWarning()" 
              class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-300">
              Dismiss
            </button>
          </div>
        </div>
        <button onclick="activityMonitor.dismissWarning()" class="text-amber-600 hover:text-amber-800">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>
    `;

    document.body.appendChild(warningDiv);
  }

  /**
   * Extend session by refreshing token
   */
  async extendSession() {
    this.dismissWarning();
    await this.refreshToken();
  }

  /**
   * Dismiss expiration warning
   */
  dismissWarning() {
    const warning = document.getElementById("token-expiration-warning");
    if (warning) {
      warning.remove();
    }
  }

  /**
   * Refresh authentication token
   */
  async refreshToken() {
    try {
      if (typeof auth !== "undefined" && auth.refreshToken) {
        await auth.refreshToken();
        this.setupTokenExpiration(); // Reset expiration timers
        console.log("Token refreshed successfully");

        if (typeof utils !== "undefined" && utils.toast) {
          utils.toast("Session extended", "success");
        }
      }
    } catch (error) {
      console.error("Token refresh failed:", error);

      if (typeof utils !== "undefined" && utils.toast) {
        utils.toast("Session refresh failed", "error");
      }
    }
  }

  /**
   * Get time until token expiration (in seconds)
   */
  getTimeUntilExpiration() {
    if (!this.tokenExpiresAt) return 0;
    return Math.max(0, Math.floor((this.tokenExpiresAt - Date.now()) / 1000));
  }

  /**
   * Check if user is currently active
   */
  isUserActive() {
    const timeSinceActivity = Date.now() - this.lastActivity;
    return timeSinceActivity < 60000; // Active if activity within last minute
  }
}

// Create global instance
const activityMonitor = new ActivityMonitor();
