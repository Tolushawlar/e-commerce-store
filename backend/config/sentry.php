<?php

/**
 * Sentry Configuration
 * Error monitoring and logging configuration
 */

return [
    'dsn' => getenv('SENTRY_DSN') ?: '',

    // Environment (production, staging, development)
    'environment' => config('app.env', 'development'),

    // Release version for tracking
    'release' => config('app.version', '2.0.0'),

    // Sample rate for transactions (0.0 to 1.0)
    // 1.0 = capture 100% of transactions
    'traces_sample_rate' => config('app.env') === 'production' ? 0.2 : 1.0,

    // Send default PII (Personally Identifiable Information)
    'send_default_pii' => false,

    // Attach stack traces to messages
    'attach_stacktrace' => true,

    // Maximum breadcrumbs
    'max_breadcrumbs' => 50,

    // Before send callback - filter sensitive data
    'before_send' => function (\Sentry\Event $event): ?\Sentry\Event {
        // Sentry automatically filters sensitive data when send_default_pii is false
        // You can add custom filtering logic here if needed
        // Return null to prevent sending the event, or return $event to send it
        return $event;
    },

    // Ignore specific exceptions
    'ignore_exceptions' => [
        // Add exception classes to ignore, e.g., validation errors in development
    ],

    // Context tags
    'tags' => [
        'platform' => 'ecommerce-platform',
    ],
];
