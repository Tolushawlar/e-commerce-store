-- ============================================================================
-- Token Security Tables
-- Implements token blacklist and device tracking for JWT security
-- Run this migration to add enhanced JWT token security
-- ============================================================================

-- Token Blacklist Table
-- Store revoked/invalidated tokens
CREATE TABLE
IF NOT EXISTS `token_blacklist`
(
  `id` INT
(11) NOT NULL AUTO_INCREMENT,
  `token_jti` VARCHAR
(255) NOT NULL COMMENT 'JWT ID (unique token identifier)',
  `user_id` INT
(11) NOT NULL,
  `user_type` ENUM
('admin', 'client', 'customer') NOT NULL,
  `reason` VARCHAR
(255) DEFAULT NULL COMMENT 'Reason for revocation',
  `expires_at` DATETIME NOT NULL COMMENT 'When the token naturally expires',
  `revoked_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY
(`id`),
  UNIQUE KEY `idx_token_jti`
(`token_jti`),
  KEY `idx_user_id`
(`user_id`),
  KEY `idx_expires_at`
(`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Token Device Fingerprints Table
-- Track devices/browsers that have valid tokens
CREATE TABLE
IF NOT EXISTS `token_devices`
(
  `id` INT
(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT
(11) NOT NULL,
  `user_type` ENUM
('admin', 'client', 'customer') NOT NULL,
  `fingerprint` VARCHAR
(64) NOT NULL COMMENT 'Hashed device fingerprint',
  `ip_address` VARCHAR
(45) NOT NULL,
  `user_agent` TEXT,
  `last_used_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON
UPDATE CURRENT_TIMESTAMP,
  `is_trusted
` TINYINT
(1) DEFAULT 1 COMMENT '1 = trusted, 0 = blocked/untrusted',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY
(`id`),
  KEY `idx_user_fingerprint`
(`user_id`, `fingerprint`),
  KEY `idx_user_type`
(`user_type`),
  KEY `idx_is_trusted`
(`is_trusted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Security Events Table
-- Log suspicious activities for analysis
CREATE TABLE
IF NOT EXISTS `security_events`
(
  `id` INT
(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT
(11) DEFAULT NULL,
  `user_type` ENUM
('admin', 'client', 'customer') DEFAULT NULL,
  `event_type` VARCHAR
(50) NOT NULL COMMENT 'ip_change, token_reuse, suspicious_activity, new_device, etc.',
  `severity` ENUM
('low', 'medium', 'high', 'critical') DEFAULT 'medium',
  `ip_address` VARCHAR
(45),
  `user_agent` TEXT,
  `details` JSON COMMENT 'Additional event details',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY
(`id`),
  KEY `idx_user_id`
(`user_id`),
  KEY `idx_event_type`
(`event_type`),
  KEY `idx_severity`
(`severity`),
  KEY `idx_created_at`
(`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- Cleanup Procedures (Optional - Run periodically via cron)
-- ============================================================================

-- Clean expired blacklisted tokens (older than 30 days)
-- DELETE FROM token_blacklist WHERE expires_at < DATE_SUB(NOW(), INTERVAL 30 DAY);

-- Clean old security events (older than 90 days)
-- DELETE FROM security_events WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);

-- Clean inactive device fingerprints (not used in 90 days)
-- DELETE FROM token_devices WHERE last_used_at < DATE_SUB(NOW(), INTERVAL 90 DAY) AND is_trusted = 1;
