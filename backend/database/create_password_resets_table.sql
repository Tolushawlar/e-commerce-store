-- Create password_resets table for managing password reset tokens
CREATE TABLE
IF NOT EXISTS password_resets
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    user_type ENUM
('client', 'admin') NOT NULL DEFAULT 'client',
    token VARCHAR
(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token
(token),
    INDEX idx_user
(user_id, user_type),
    INDEX idx_expires
(expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
