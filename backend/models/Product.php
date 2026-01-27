<?php

namespace App\Models;

use App\Core\Model;

/**
 * Product Model
 */
class Product extends Model
{
    protected string $table = 'products';

    protected array $fillable = [
        'store_id',
        'name',
        'description',
        'price',
        'category',
        'image_url',
        'stock_quantity',
        'status'
    ];

    /**
     * Get products by store
     */
    public function getByStore(int $storeId, array $filters = []): array
    {
        $query = "SELECT * FROM {$this->table} WHERE store_id = ?";
        $params = [$storeId];

        if (isset($filters['category'])) {
            $query .= " AND category = ?";
            $params[] = $filters['category'];
        }

        if (isset($filters['status'])) {
            $query .= " AND status = ?";
            $params[] = $filters['status'];
        }

        if (isset($filters['search'])) {
            $query .= " AND (name LIKE ? OR description LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
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
     * Get product with images
     */
    public function withImages(int $id): ?array
    {
        $product = $this->find($id);

        if (!$product) {
            return null;
        }

        $stmt = $this->db->prepare("
            SELECT * FROM product_images 
            WHERE product_id = ? 
            ORDER BY is_primary DESC, id ASC
        ");
        $stmt->execute([$id]);
        $product['images'] = $stmt->fetchAll();

        return $product;
    }

    /**
     * Update stock quantity
     */
    public function updateStock(int $id, int $quantity): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET stock_quantity = stock_quantity + ? 
            WHERE id = ?
        ");

        return $stmt->execute([$quantity, $id]);
    }

    /**
     * Count products by store
     */
    public function countByStore(int $storeId, array $filters = []): int
    {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE store_id = ?";
        $params = [$storeId];

        if (isset($filters['category'])) {
            $query .= " AND category = ?";
            $params[] = $filters['category'];
        }

        if (isset($filters['status'])) {
            $query .= " AND status = ?";
            $params[] = $filters['status'];
        }

        if (isset($filters['search'])) {
            $query .= " AND (name LIKE ? OR description LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return (int)$stmt->fetch()['count'];
    }

    /**
     * Get low stock products
     */
    public function getLowStock(int $storeId, int $threshold = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE store_id = ? AND stock_quantity <= ? AND status = 'active'
            ORDER BY stock_quantity ASC
        ");

        $stmt->execute([$storeId, $threshold]);
        return $stmt->fetchAll();
    }
}
