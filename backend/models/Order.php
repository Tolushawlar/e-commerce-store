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
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',
        'shipping_address_id',
        'billing_address_id',
        'total_amount',
        'shipping_cost',
        'payment_method',
        'payment_status',
        'payment_reference',
        'payment_gateway',
        'payment_verified_at',
        'order_notes',
        'tracking_number',
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

        if (isset($filters['payment_status'])) {
            $query .= " AND payment_status = ?";
            $params[] = $filters['payment_status'];
        }

        if (isset($filters['from_date'])) {
            $query .= " AND DATE(created_at) >= ?";
            $params[] = $filters['from_date'];
        }

        if (isset($filters['to_date'])) {
            $query .= " AND DATE(created_at) <= ?";
            $params[] = $filters['to_date'];
        }

        // Search by customer email, name, or order ID
        if (isset($filters['search'])) {
            $query .= " AND (customer_email LIKE ? OR customer_name LIKE ? OR id = ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $filters['search']; // Exact ID match
        }

        $query .= " ORDER BY created_at DESC";

        if (isset($filters['limit'])) {
            $query .= " LIMIT ?";
            $params[] = (int)$filters['limit'];

            if (isset($filters['offset'])) {
                $query .= " OFFSET ?";
                $params[] = (int)$filters['offset'];
            }
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
            SELECT oi.*, p.name as product_name
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$id]);
        $order['items'] = $stmt->fetchAll();

        // Fetch images for each product
        foreach ($order['items'] as &$item) {
            $stmt = $this->db->prepare("
                SELECT * FROM product_images 
                WHERE product_id = ? 
                ORDER BY is_primary DESC, id ASC
            ");
            $stmt->execute([$item['product_id']]);
            $item['images'] = $stmt->fetchAll();
        }

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

        if (isset($filters['payment_status'])) {
            $query .= " AND payment_status = ?";
            $params[] = $filters['payment_status'];
        }

        if (isset($filters['from_date'])) {
            $query .= " AND DATE(created_at) >= ?";
            $params[] = $filters['from_date'];
        }

        if (isset($filters['to_date'])) {
            $query .= " AND DATE(created_at) <= ?";
            $params[] = $filters['to_date'];
        }

        if (isset($filters['search'])) {
            $query .= " AND (customer_email LIKE ? OR customer_name LIKE ? OR id = ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $filters['search'];
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return (int)$stmt->fetch()['count'];
    }

    /**
     * Get orders by customer
     */
    public function getByCustomer(int $customerId, array $filters = []): array
    {
        $query = "SELECT * FROM {$this->table} WHERE customer_id = ?";
        $params = [$customerId];

        if (!empty($filters['status'])) {
            $query .= " AND status = ?";
            $params[] = $filters['status'];
        }

        $query .= " ORDER BY created_at DESC";

        if (!empty($filters['limit'])) {
            $query .= " LIMIT ?";
            $params[] = (int)$filters['limit'];
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Get order with full details (items, addresses)
     */
    public function getFullDetails(int $id): ?array
    {
        $order = $this->find($id);

        if (!$order) {
            return null;
        }

        // Get order items with product details
        $stmt = $this->db->prepare("
            SELECT 
                oi.*,
                p.name as product_name,
                p.description as product_description
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$id]);
        $order['items'] = $stmt->fetchAll();

        // Fetch images for each product
        foreach ($order['items'] as &$item) {
            $stmt = $this->db->prepare("
                SELECT * FROM product_images 
                WHERE product_id = ? 
                ORDER BY is_primary DESC, id ASC
            ");
            $stmt->execute([$item['product_id']]);
            $item['images'] = $stmt->fetchAll();
        }

        // Get shipping address if exists
        if ($order['shipping_address_id']) {
            $stmt = $this->db->prepare("SELECT * FROM customer_addresses WHERE id = ?");
            $stmt->execute([$order['shipping_address_id']]);
            $order['shipping_address'] = $stmt->fetch() ?: null;
        }

        // Get billing address if exists
        if ($order['billing_address_id']) {
            $stmt = $this->db->prepare("SELECT * FROM customer_addresses WHERE id = ?");
            $stmt->execute([$order['billing_address_id']]);
            $order['billing_address'] = $stmt->fetch() ?: null;
        }

        return $order;
    }

    /**
     * Create order with items
     */
    public function createWithItems(array $orderData, array $items): ?int
    {
        try {
            $this->db->beginTransaction();

            // Create order
            $orderId = $this->create($orderData);

            if (!$orderId) {
                throw new \Exception('Failed to create order');
            }

            // Create order items
            $stmt = $this->db->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price)
                VALUES (?, ?, ?, ?)
            ");

            foreach ($items as $item) {
                $stmt->execute([
                    $orderId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['unit_price'] ?? $item['price'] ?? 0
                ]);
            }

            $this->db->commit();
            return $orderId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return null;
        }
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
                COUNT(CASE WHEN status = 'processing' THEN 1 END) as processing_orders,
                COUNT(CASE WHEN status = 'shipped' THEN 1 END) as shipped_orders,
                COUNT(CASE WHEN status = 'delivered' THEN 1 END) as delivered_orders,
                COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_orders
            FROM {$this->table}
            WHERE store_id = ?
        ");

        $stmt->execute([$storeId]);
        return $stmt->fetch();
    }


    /**
     * Update payment status
     */
    public function updatePaymentStatus(int $id, string $paymentStatus): bool
    {
        $validStatuses = ['pending', 'paid', 'failed', 'refunded'];

        if (!in_array($paymentStatus, $validStatuses)) {
            return false;
        }

        return $this->update($id, ['payment_status' => $paymentStatus]);
    }

    /**
     * Get daily statistics for date range
     */
    public function getDailyStats(int $storeId, string $fromDate, string $toDate): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as order_count,
                SUM(total_amount) as revenue,
                COUNT(CASE WHEN status = 'delivered' THEN 1 END) as delivered_count,
                COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_count
            FROM {$this->table}
            WHERE store_id = ?
                AND DATE(created_at) BETWEEN ? AND ?
            GROUP BY DATE(created_at)
            ORDER BY date DESC
        ");

        $stmt->execute([$storeId, $fromDate, $toDate]);
        return $stmt->fetchAll();
    }

    /**
     * Get revenue by payment method
     */
    public function getRevenueByPaymentMethod(int $storeId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                payment_method,
                COUNT(*) as order_count,
                SUM(total_amount) as total_revenue
            FROM {$this->table}
            WHERE store_id = ?
                AND payment_status = 'paid'
            GROUP BY payment_method
        ");

        $stmt->execute([$storeId]);
        return $stmt->fetchAll();
    }

    /**
     * Get top customers by order value
     */
    public function getTopCustomers(int $storeId, int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                customer_id,
                customer_name,
                customer_email,
                COUNT(*) as order_count,
                SUM(total_amount) as total_spent,
                AVG(total_amount) as avg_order_value
            FROM {$this->table}
            WHERE store_id = ?
                AND customer_id IS NOT NULL
            GROUP BY customer_id, customer_name, customer_email
            ORDER BY total_spent DESC
            LIMIT ?
        ");

        $stmt->execute([$storeId, $limit]);
        return $stmt->fetchAll();
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

    /**
     * Check if user has access to store
     */
    public function hasUserAccessToStore(int $userId, int $storeId): bool
    {
        // Check if user is client owner of this store
        $stmt = $this->db->prepare("
            SELECT id FROM stores WHERE id = ? AND client_id = ?
        ");
        $stmt->execute([$storeId, $userId]);

        return $stmt->fetch() !== false;
    }

    /**
     * Get order items for stock restoration
     */
    public function getOrderItems(int $orderId): array
    {
        $stmt = $this->db->prepare("
            SELECT product_id, quantity 
            FROM order_items 
            WHERE order_id = ?
        ");
        $stmt->execute([$orderId]);

        return $stmt->fetchAll();
    }

    /**
     * Find order by payment reference
     */
    public function findByPaymentReference(string $reference): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE payment_reference = ?
        ");
        $stmt->execute([$reference]);
        $result = $stmt->fetch();

        return $result ?: null;
    }
}
