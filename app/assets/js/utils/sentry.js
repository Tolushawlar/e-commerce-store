/**
 * Sentry Error Monitoring - Frontend
 * Initialize and configure Sentry for client-side error tracking
 */

// Sentry configuration
const SENTRY_CONFIG = {
  dsn: "", // Will be set from environment or config
  environment: "development", // production, staging, development
  release: "2.0.0",
  tracesSampleRate: 1.0,
  replaysSessionSampleRate: 0.1,
  replaysOnErrorSampleRate: 1.0,
};

/**
 * Initialize Sentry
 */
function initSentry() {
  // Check if Sentry SDK is loaded
  if (typeof Sentry === "undefined") {
    console.warn("Sentry SDK not loaded. Error monitoring disabled.");
    return;
  }

  // Only initialize if DSN is configured
  if (!SENTRY_CONFIG.dsn) {
    console.info("Sentry DSN not configured. Skipping initialization.");
    return;
  }

  try {
    Sentry.init({
      dsn: SENTRY_CONFIG.dsn,
      environment: SENTRY_CONFIG.environment,
      release: SENTRY_CONFIG.release,
      integrations: [
        new Sentry.BrowserTracing(),
        new Sentry.Replay({
          maskAllText: true,
          blockAllMedia: true,
        }),
      ],
      tracesSampleRate: SENTRY_CONFIG.tracesSampleRate,
      replaysSessionSampleRate: SENTRY_CONFIG.replaysSessionSampleRate,
      replaysOnErrorSampleRate: SENTRY_CONFIG.replaysOnErrorSampleRate,

      // Filter sensitive data
      beforeSend(event, hint) {
        // Remove sensitive data from request
        if (event.request && event.request.data) {
          const data = event.request.data;
          if (typeof data === "object") {
            delete data.password;
            delete data.password_confirmation;
            delete data.old_password;
          }
        }

        // Remove authorization headers
        if (event.request && event.request.headers) {
          delete event.request.headers.Authorization;
          delete event.request.headers.Cookie;
        }

        return event;
      },

      // Ignore certain errors
      ignoreErrors: [
        // Browser extension errors
        "top.GLOBALS",
        "originalCreateNotification",
        "canvas.contentDocument",
        "MyApp_RemoveAllHighlights",
        "atomicFindClose",
        // Network errors (handled separately)
        "NetworkError",
        "Network request failed",
        // Script loading errors
        "ChunkLoadError",
      ],
    });

    console.info("Sentry initialized successfully");
  } catch (error) {
    console.error("Failed to initialize Sentry:", error);
  }
}

/**
 * Set user context
 */
function setSentryUser(userId, email, username) {
  if (typeof Sentry !== "undefined") {
    Sentry.setUser({
      id: userId,
      email: email,
      username: username,
    });
  }
}

/**
 * Clear user context (on logout)
 */
function clearSentryUser() {
  if (typeof Sentry !== "undefined") {
    Sentry.setUser(null);
  }
}

/**
 * Log custom error to Sentry
 */
function logError(message, context = {}) {
  if (typeof Sentry !== "undefined") {
    Sentry.captureMessage(message, {
      level: "error",
      contexts: { additional_info: context },
    });
  } else {
    console.error(message, context);
  }
}

/**
 * Log warning to Sentry
 */
function logWarning(message, context = {}) {
  if (typeof Sentry !== "undefined") {
    Sentry.captureMessage(message, {
      level: "warning",
      contexts: { additional_info: context },
    });
  } else {
    console.warn(message, context);
  }
}

/**
 * Log info to Sentry
 */
function logInfo(message, context = {}) {
  if (typeof Sentry !== "undefined") {
    Sentry.captureMessage(message, {
      level: "info",
      contexts: { additional_info: context },
    });
  } else {
    console.info(message, context);
  }
}

/**
 * Add breadcrumb (trail of events)
 */
function addBreadcrumb(message, data = {}, category = "default") {
  if (typeof Sentry !== "undefined") {
    Sentry.addBreadcrumb({
      message: message,
      category: category,
      level: "info",
      data: data,
    });
  }
}

/**
 * Capture exception
 */
function captureException(error, context = {}) {
  if (typeof Sentry !== "undefined") {
    Sentry.captureException(error, {
      contexts: { additional_info: context },
    });
  } else {
    console.error(error, context);
  }
}

/**
 * Set custom tag
 */
function setTag(key, value) {
  if (typeof Sentry !== "undefined") {
    Sentry.setTag(key, value);
  }
}

/**
 * Start performance transaction
 */
function startTransaction(name, op = "pageload") {
  if (typeof Sentry !== "undefined") {
    return Sentry.startTransaction({ name, op });
  }
  return null;
}

// Auto-initialize on page load
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initSentry);
} else {
  initSentry();
}

// Export functions
window.SentryHelper = {
  init: initSentry,
  setUser: setSentryUser,
  clearUser: clearSentryUser,
  logError,
  logWarning,
  logInfo,
  addBreadcrumb,
  captureException,
  setTag,
  startTransaction,
};
