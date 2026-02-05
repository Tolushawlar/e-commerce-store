<?php

namespace App\Models;

use App\Core\Model;

/**
 * EmailQueue Model
 * Manages email queue for async email sending
 */
class EmailQueue extends Model
{
    protected string $table = 'email_queue';

    protected array $fillable = [
        'notification_id',
        'recipient_email',
        'recipient_name',
        'subject',
        'body',
        'template',
        'template_data',
        'priority',
        'status',
        'attempts',
        'max_attempts',
        'last_error',
        'scheduled_at',
        'sent_at'
    ];

    /**
     * Get pending emails
     */
    public function getPending(int $limit = 50): array
    {
        $query = "SELECT * FROM {$this->table} 
                  WHERE status = 'pending' 
                  AND (scheduled_at IS NULL OR scheduled_at <= NOW())
                  AND attempts < max_attempts
                  ORDER BY priority DESC, created_at ASC
                  LIMIT ?";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$limit]);

        return $stmt->fetchAll();
    }

    /**
     * Mark email as sent
     */
    public function markAsSent(int $id): bool
    {
        $query = "UPDATE {$this->table} 
                  SET status = 'sent', sent_at = NOW() 
                  WHERE id = ?";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }

    /**
     * Mark email as failed
     */
    public function markAsFailed(int $id, string $error): bool
    {
        $query = "UPDATE {$this->table} 
                  SET status = 'failed', 
                      attempts = attempts + 1,
                      last_error = ?
                  WHERE id = ?";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([$error, $id]);
    }

    /**
     * Retry failed email
     */
    public function retry(int $id): bool
    {
        $query = "UPDATE {$this->table} 
                  SET status = 'pending',
                      last_error = NULL
                  WHERE id = ? AND attempts < max_attempts";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }

    /**
     * Increment attempt count
     */
    public function incrementAttempts(int $id, ?string $error = null): bool
    {
        $query = "UPDATE {$this->table} 
                  SET attempts = attempts + 1,
                      last_error = ?
                  WHERE id = ?";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([$error, $id]);
    }

    /**
     * Get failed emails
     */
    public function getFailed(int $limit = 50): array
    {
        $query = "SELECT * FROM {$this->table} 
                  WHERE status = 'failed' 
                  OR (status = 'pending' AND attempts >= max_attempts)
                  ORDER BY created_at DESC
                  LIMIT ?";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$limit]);

        return $stmt->fetchAll();
    }

    /**
     * Delete old sent emails
     */
    public function deleteOldSent(int $daysOld = 30): int
    {
        $query = "DELETE FROM {$this->table} 
                  WHERE status = 'sent' 
                  AND sent_at < DATE_SUB(NOW(), INTERVAL ? DAY)";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$daysOld]);

        return $stmt->rowCount();
    }

    /**
     * Get queue stats
     */
    public function getStats(): array
    {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                    SUM(CASE WHEN status = 'pending' AND attempts >= max_attempts THEN 1 ELSE 0 END) as exhausted
                  FROM {$this->table}";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetch() ?: [];
    }

    /**
     * Schedule email for later
     */
    public function schedule(array $data, string $scheduledAt): ?int
    {
        $data['scheduled_at'] = $scheduledAt;
        $data['status'] = 'pending';

        return $this->create($data);
    }

    /**
     * Get emails for a notification
     */
    public function getByNotification(int $notificationId): array
    {
        $query = "SELECT * FROM {$this->table} 
                  WHERE notification_id = ?
                  ORDER BY created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$notificationId]);

        return $stmt->fetchAll();
    }
}
