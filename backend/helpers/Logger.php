<?php

namespace App\Helpers;

/**
 * Custom Logger Helper
 * Wrapper around Sentry for consistent logging
 */
class Logger
{
    /**
     * Log an info message
     */
    public static function info(string $message, array $context = []): void
    {
        self::log($message, 'info', $context);
    }

    /**
     * Log a warning message
     */
    public static function warning(string $message, array $context = []): void
    {
        self::log($message, 'warning', $context);
    }

    /**
     * Log an error message
     */
    public static function error(string $message, array $context = []): void
    {
        self::log($message, 'error', $context);
    }

    /**
     * Log a critical error
     */
    public static function critical(string $message, array $context = []): void
    {
        self::log($message, 'fatal', $context);
    }

    /**
     * Log an exception
     */
    public static function exception(\Throwable $exception, array $context = []): void
    {
        $isSentryAvailable = function_exists('\Sentry\captureException') && \Sentry\State\HubAdapter::getInstance()->getClient() !== null;

        if ($isSentryAvailable) {
            \Sentry\withScope(function (\Sentry\State\Scope $scope) use ($exception, $context) {
                if (!empty($context)) {
                    $scope->setContext('additional_info', $context);
                }
                error_log("Logging exception to Sentry: " . $exception);
                \Sentry\captureException($exception);
            });

            // Force Sentry to send events immediately before script terminates
            $client = \Sentry\State\HubAdapter::getInstance()->getClient();
            if ($client !== null) {
                $client->flush(2); // Wait up to 2 seconds for events to be sent
                error_log("Sentry events flushed");
            }
        } else {
            // Fallback to error_log if Sentry not initialized
            error_log("Sentry not initialized. Logging exception locally.");
            error_log(sprintf(
                "[Exception] %s: %s in %s:%d\nContext: %s\nStack Trace:\n%s",
                get_class($exception),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
                json_encode($context),
                $exception->getTraceAsString()
            ));
        }
    }

    /**
     * Log a message with custom severity
     */
    public static function log(string $message, string $severity, array $context = []): void
    {
        if (function_exists('\Sentry\captureMessage')) {
            \Sentry\withScope(function ($scope) use ($message, $severity, $context) {
                if (!empty($context)) {
                    $scope->setContext('additional_info', $context);
                }

                // Convert string severity to Sentry\Severity enum
                $sentrySeverity = match ($severity) {
                    'fatal' => \Sentry\Severity::fatal(),
                    'error' => \Sentry\Severity::error(),
                    'warning' => \Sentry\Severity::warning(),
                    'info' => \Sentry\Severity::info(),
                    'debug' => \Sentry\Severity::debug(),
                    default => \Sentry\Severity::info(),
                };

                \Sentry\captureMessage($message, $sentrySeverity);
            });
        } else {
            // Fallback to error_log if Sentry not initialized
            error_log(sprintf(
                "[%s] %s - Context: %s",
                strtoupper($severity),
                $message,
                json_encode($context)
            ));
        }
    }

    /**
     * Add user context to error reports
     */
    public static function setUser(?int $userId, ?string $email = null, ?string $username = null): void
    {
        if (function_exists('\Sentry\configureScope')) {
            \Sentry\configureScope(function ($scope) use ($userId, $email, $username) {
                $scope->setUser([
                    'id' => $userId,
                    'email' => $email,
                    'username' => $username,
                ]);
            });
        }
    }

    /**
     * Add custom tag
     */
    public static function setTag(string $key, string $value): void
    {
        if (function_exists('\Sentry\configureScope')) {
            \Sentry\configureScope(function ($scope) use ($key, $value) {
                $scope->setTag($key, $value);
            });
        }
    }

    /**
     * Add breadcrumb (trail of events leading to error)
     */
    public static function addBreadcrumb(string $message, array $data = [], string $category = 'default'): void
    {
        if (function_exists('\Sentry\addBreadcrumb')) {
            \Sentry\addBreadcrumb([
                'message' => $message,
                'category' => $category,
                'level' => 'info',
                'data' => $data,
            ]);
        }
    }

    /**
     * Start a transaction for performance monitoring
     */
    public static function startTransaction(string $name, string $op = 'http.request')
    {
        if (function_exists('\Sentry\startTransaction')) {
            $context = new \Sentry\Tracing\TransactionContext();
            $context->setName($name);
            $context->setOp($op);
            return \Sentry\startTransaction($context);
        }

        return null;
    }
}
