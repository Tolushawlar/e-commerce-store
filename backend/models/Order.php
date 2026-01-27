<?php

namespace App\Models;

use App\Core\Model;

/**
 * Order Model
 */
class Order extends Model
{
    protected string $table = 'orders';

    protected array $fillable = [
        'store_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'total_amount',
        'status'
    ];

    /**
     * Get orders by store
     */
    public function getByStore(int $storeId, array $filters = []): array
    {
        $query = "SELECT * FROM {$this->table} WHERE store_id = ?";
        $params = [$storeId];

        if (isset($filters['status'])) {
            $query .= " AND status = ?";
            $params[] = $filters['status'];
        }

        if (isset($filters['from_date'])) {
            $query .= " AND created_at >= ?";
            $params[] = $filters['from_date'];
        }

        if (isset($filters['to_date'])) {
            $query .= " AND created_at <= ?";
            $params[] = $filters['to_date'];
        }

        $query .= " ORDER BY created_at DESC";

        if (isset($filters['limit'])) {
            $query .= " LIMIT ?";
            $params[] = (int)$filters['limit'];
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Get order with items
     */
    public function withItems(int $id): ?array
    {
        $order = $this->find($id);

        if (!$order) {
            return null;
        }

        $stmt = $this->db->prepare("
            SELECT oi.*, p.name as product_name, p.image_url as product_image
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$id]);
        $order['items'] = $stmt->fetchAll();

        return $order;
    }

    /**
     * Count orders by store
     */
    public function countByStore(int $storeId, array $filters = []): int
    {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE store_id = ?";
        $params = [$storeId];

        if (isset($filters['status'])) {
            $query .= " AND status = ?";
            $params[] = $filters['status'];
        }

        if (isset($filters['from_date'])) {
            $query .= " AND created_at >= ?";
            $params[] = $filters['from_date'];
        }

        if (isset($filters['to_date'])) {
            $query .= " AND created_at <= ?";
            $params[] = $filters['to_date'];
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return (int)$stmt->fetch()['count'];
    }

    /**
     * Get order statistics for store
     */
    public function getStoreStats(int $storeId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_orders,
                SUM(total_amount) as total_revenue,
                AVG(total_amount) as average_order_value,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_orders,
                COUNT(CASE WHEN status = 'delivered' THEN 1 END) as delivered_orders
            FROM {$this->table}
            WHERE store_id = ?
        ");

        $stmt->execute([$storeId]);
        return $stmt->fetch();
    }

    /**
     * Update order status
     */
    public function updateStatus(int $id, string $status): bool
    {
        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        if (!in_array($status, $validStatuses)) {
            return false;
        }

        return $this->update($id, ['status' => $status]);
    }
}
