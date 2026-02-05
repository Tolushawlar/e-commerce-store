<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Notification Model
 * Handles in-app notifications for admin, clients, and customers
 */
class Notification extends Model
{
    protected string $table = 'notifications';

    protected array $fillable = [
        'user_id',
        'user_type',
        'type',
        'title',
        'message',
        'data',
        'action_url',
        'priority',
        'is_read',
        'read_at'
    ];

    /**
     * Get notifications for a specific user
     */
    public function getByUser(int $userId, string $userType, array $filters = []): array
    {
        $query = "SELECT * FROM {$this->table} WHERE user_id = ? AND user_type = ?";
        $params = [$userId, $userType];

        // Filter by read status
        if (isset($filters['is_read'])) {
            $query .= " AND is_read = ?";
            $params[] = $filters['is_read'];
        }

        // Filter by type
        if (isset($filters['type'])) {
            $query .= " AND type = ?";
            $params[] = $filters['type'];
        }

        // Filter by priority
        if (isset($filters['priority'])) {
            $query .= " AND priority = ?";
            $params[] = $filters['priority'];
        }

        // Pagination
        $limit = $filters['limit'] ?? 50;
        $offset = $filters['offset'] ?? 0;

        $query .= " ORDER BY priority DESC, created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Get unread count for a user
     */
    public function getUnreadCount(int $userId, string $userType): int
    {
        $query = "SELECT COUNT(*) as count FROM {$this->table} 
                  WHERE user_id = ? AND user_type = ? AND is_read = 0";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId, $userType]);
        $result = $stmt->fetch();

        return (int)($result['count'] ?? 0);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId): bool
    {
        $query = "UPDATE {$this->table} 
                  SET is_read = 1, read_at = NOW() 
                  WHERE id = ?";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([$notificationId]);
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead(int $userId, string $userType): bool
    {
        $query = "UPDATE {$this->table} 
                  SET is_read = 1, read_at = NOW() 
                  WHERE user_id = ? AND user_type = ? AND is_read = 0";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([$userId, $userType]);
    }

    /**
     * Delete old read notifications (cleanup)
     */
    public function deleteOldRead(int $daysOld = 30): int
    {
        $query = "DELETE FROM {$this->table} 
                  WHERE is_read = 1 
                  AND read_at < DATE_SUB(NOW(), INTERVAL ? DAY)";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$daysOld]);

        return $stmt->rowCount();
    }

    /**
     * Get recent notifications (last 24 hours)
     */
    public function getRecent(int $userId, string $userType, int $limit = 10): array
    {
        $query = "SELECT * FROM {$this->table} 
                  WHERE user_id = ? AND user_type = ? 
                  AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                  ORDER BY created_at DESC 
                  LIMIT ?";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId, $userType, $limit]);

        return $stmt->fetchAll();
    }

    /**
     * Delete notification
     */
    public function deleteNotification(int $notificationId, int $userId, string $userType): bool
    {
        $query = "DELETE FROM {$this->table} 
                  WHERE id = ? AND user_id = ? AND user_type = ?";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([$notificationId, $userId, $userType]);
    }

    /**
     * Get notification statistics
     */
    public function getStats(int $userId, string $userType): array
    {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread,
                    SUM(CASE WHEN is_read = 1 THEN 1 ELSE 0 END) as read,
                    SUM(CASE WHEN priority = 'urgent' AND is_read = 0 THEN 1 ELSE 0 END) as urgent_unread
                  FROM {$this->table}
                  WHERE user_id = ? AND user_type = ?";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId, $userType]);

        return $stmt->fetch() ?: [];
    }
}
