-- Notifications System Migration
-- Created: 2026-02-04
-- Description: Adds comprehensive notification system with in-app and email support

-- Main notifications table
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'References client_id, admin_id, or customer_id',
  `user_type` enum('admin','client','customer') NOT NULL DEFAULT 'client',
  `type` varchar(50) NOT NULL COMMENT 'order, product, system, store, payment, customer',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `data` json DEFAULT NULL COMMENT 'Additional context data',
  `action_url` varchar(500) DEFAULT NULL COMMENT 'Link for notification action',
  `priority` enum('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`, `user_type`),
  KEY `idx_is_read` (`is_read`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_type` (`type`),
  KEY `idx_priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notification preferences table
CREATE TABLE IF NOT EXISTS `notification_preferences` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_type` enum('admin','client','customer') NOT NULL DEFAULT 'client',
  `notification_type` varchar(50) NOT NULL COMMENT 'order, product, system, store, payment, customer',
  `in_app_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `email_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `sms_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_type` (`user_id`, `user_type`, `notification_type`),
  KEY `idx_user` (`user_id`, `user_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Email queue table (for async email sending)
CREATE TABLE IF NOT EXISTS `email_queue` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `to_email` varchar(255) NOT NULL,
  `to_name` varchar(255) DEFAULT NULL,
  `from_email` varchar(255) DEFAULT NULL,
  `from_name` varchar(255) DEFAULT NULL,
  `subject` varchar(500) NOT NULL,
  `body_html` text NOT NULL,
  `body_text` text DEFAULT NULL,
  `notification_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Related notification ID',
  `priority` enum('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
  `status` enum('pending','sent','failed','cancelled') NOT NULL DEFAULT 'pending',
  `attempts` int(11) NOT NULL DEFAULT 0,
  `max_attempts` int(11) NOT NULL DEFAULT 3,
  `last_error` text DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `scheduled_at` timestamp NULL DEFAULT NULL COMMENT 'For delayed sending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_scheduled_at` (`scheduled_at`),
  KEY `idx_priority` (`priority`),
  KEY `idx_notification_id` (`notification_id`),
  CONSTRAINT `fk_email_notification` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default notification preferences for existing users
INSERT INTO `notification_preferences` (user_id, user_type, notification_type, in_app_enabled, email_enabled)
SELECT id, 'client', 'order', 1, 1 FROM clients
UNION ALL
SELECT id, 'client', 'product', 1, 1 FROM clients
UNION ALL
SELECT id, 'client', 'system', 1, 1 FROM clients
UNION ALL
SELECT id, 'client', 'store', 1, 1 FROM clients
UNION ALL
SELECT id, 'client', 'payment', 1, 1 FROM clients;

-- Add indexes for better query performance
CREATE INDEX idx_unread_notifications ON notifications(user_id, user_type, is_read, created_at DESC);
CREATE INDEX idx_pending_emails ON email_queue(status, scheduled_at, priority);
