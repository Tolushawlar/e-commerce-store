<?php

namespace App\Models;

use App\Core\Model;

/**
 * NotificationPreference Model
 * Manages user notification preferences
 */
class NotificationPreference extends Model
{
    protected string $table = 'notification_preferences';

    protected array $fillable = [
        'user_id',
        'user_type',
        'notification_type',
        'in_app_enabled',
        'email_enabled',
        'sms_enabled'
    ];

    /**
     * Get preferences for a user
     */
    public function getByUser(int $userId, string $userType): array
    {
        $query = "SELECT * FROM {$this->table} 
                  WHERE user_id = ? AND user_type = ?";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId, $userType]);

        $preferences = $stmt->fetchAll();

        // Convert to associative array keyed by notification_type
        $result = [];
        foreach ($preferences as $pref) {
            $result[$pref['notification_type']] = $pref;
        }

        return $result;
    }

    /**
     * Check if notification type is enabled for channel
     */
    public function isEnabled(int $userId, string $userType, string $notificationType, string $channel = 'in_app'): bool
    {
        $column = $channel . '_enabled';

        $query = "SELECT {$column} FROM {$this->table} 
                  WHERE user_id = ? AND user_type = ? AND notification_type = ?";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId, $userType, $notificationType]);
        $result = $stmt->fetch();

        return $result ? (bool)$result[$column] : true; // Default to enabled
    }

    /**
     * Update preference
     */
    public function updatePreference(int $userId, string $userType, string $notificationType, array $settings): bool
    {
        // Check if preference exists
        $existing = $this->db->prepare("SELECT id FROM {$this->table} 
                                        WHERE user_id = ? AND user_type = ? AND notification_type = ?");
        $existing->execute([$userId, $userType, $notificationType]);

        if ($existing->fetch()) {
            // Update existing
            $updates = [];
            $params = [];

            foreach ($settings as $key => $value) {
                if (in_array($key, $this->fillable)) {
                    $updates[] = "{$key} = ?";
                    $params[] = $value;
                }
            }

            $params[] = $userId;
            $params[] = $userType;
            $params[] = $notificationType;

            $query = "UPDATE {$this->table} SET " . implode(', ', $updates) . " 
                      WHERE user_id = ? AND user_type = ? AND notification_type = ?";

            $stmt = $this->db->prepare($query);
            return $stmt->execute($params);
        } else {
            // Create new
            $settings['user_id'] = $userId;
            $settings['user_type'] = $userType;
            $settings['notification_type'] = $notificationType;

            return $this->create($settings) !== null;
        }
    }

    /**
     * Initialize default preferences for a new user
     */
    public function initializeDefaults(int $userId, string $userType): bool
    {
        $types = ['order', 'product', 'system', 'store', 'payment', 'customer'];

        $query = "INSERT INTO {$this->table} (user_id, user_type, notification_type, in_app_enabled, email_enabled, sms_enabled) 
                  VALUES (?, ?, ?, 1, 1, 0)";

        $stmt = $this->db->prepare($query);

        foreach ($types as $type) {
            $stmt->execute([$userId, $userType, $type]);
        }

        return true;
    }
}
