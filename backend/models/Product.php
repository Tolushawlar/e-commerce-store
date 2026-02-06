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
        'category_id',
        'image_url',
        'stock_quantity',
        'status'
    ];

    /**
     * Get products by store
     */
    public function getByStore(int $storeId, array $filters = []): array
    {
        $query = "SELECT p.*, c.name as category_name, c.slug as category_slug,
                  COALESCE(SUM(oi.quantity), 0) as sales_count
                  FROM {$this->table} p
                  LEFT JOIN categories c ON p.category_id = c.id
                  LEFT JOIN order_items oi ON p.id = oi.product_id
                  LEFT JOIN orders o ON oi.order_id = o.id AND o.status != 'cancelled'
                  WHERE p.store_id = ?";
        $params = [$storeId];

        // Support both old 'category' string and new 'category_id' filters
        if (isset($filters['category'])) {
            $query .= " AND p.category = ?";
            $params[] = $filters['category'];
        }

        if (isset($filters['category_id'])) {
            $query .= " AND p.category_id = ?";
            $params[] = $filters['category_id'];
        }

        if (isset($filters['status'])) {
            $query .= " AND p.status = ?";
            $params[] = $filters['status'];
        }

        if (isset($filters['search'])) {
            $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $query .= " GROUP BY p.id, p.store_id, p.category_id, p.name, p.description, p.price, p.category, p.stock_quantity, p.status, p.created_at, c.name, c.slug";
        $query .= " ORDER BY p.created_at DESC";

        if (isset($filters['limit'])) {
            $query .= " LIMIT ?";
            $params[] = (int)$filters['limit'];

            // Add OFFSET for pagination
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
     * Get product with images
     */
    public function withImages(int $id): ?array
    {
        $product = $this->find($id);

        if (!$product) {
            return null;
        }

        // Get category info if category_id is set
        if (!empty($product['category_id'])) {
            $stmt = $this->db->prepare("
                SELECT name, slug FROM categories WHERE id = ?
            ");
            $stmt->execute([$product['category_id']]);
            $category = $stmt->fetch();
            if ($category) {
                $product['category_name'] = $category['name'];
                $product['category_slug'] = $category['slug'];
            }
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

        // Support both old 'category' string and new 'category_id' filters
        if (isset($filters['category'])) {
            $query .= " AND category = ?";
            $params[] = $filters['category'];
        }

        if (isset($filters['category_id'])) {
            $query .= " AND category_id = ?";
            $params[] = $filters['category_id'];
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

    /**
     * Add product images
     * @param int $productId Product ID
     * @param array $imageUrls Array of image URLs
     * @param int $primaryIndex Index of the primary image (default: 0)
     * @return bool
     */
    public function addImages(int $productId, array $imageUrls, int $primaryIndex = 0): bool
    {
        if (empty($imageUrls)) {
            return true;
        }

        try {
            $this->db->beginTransaction();

            // Delete existing images
            $stmt = $this->db->prepare("DELETE FROM product_images WHERE product_id = ?");
            $stmt->execute([$productId]);

            // Insert new images
            $stmt = $this->db->prepare("
                INSERT INTO product_images (product_id, image_url, is_primary) 
                VALUES (?, ?, ?)
            ");

            foreach ($imageUrls as $index => $url) {
                $isPrimary = ($index === $primaryIndex) ? 1 : 0;
                $stmt->execute([$productId, $url, $isPrimary]);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Get product images
     * @param int $productId Product ID
     * @return array
     */
    public function getImages(int $productId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM product_images 
            WHERE product_id = ? 
            ORDER BY is_primary DESC, id ASC
        ");
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    /**
     * Delete product images
     * @param int $productId Product ID
     * @return bool
     */
    public function deleteImages(int $productId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM product_images WHERE product_id = ?");
        return $stmt->execute([$productId]);
    }

    /**
     * Restore stock for order items (used when order is cancelled)
     */
    public function restoreStockForItems(array $items): bool
    {
        try {
            foreach ($items as $item) {
                $stmt = $this->db->prepare("
                    UPDATE products 
                    SET stock_quantity = stock_quantity + ? 
                    WHERE id = ?
                ");
                $stmt->execute([$item['quantity'], $item['product_id']]);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Find category by name for a specific store
     * @param int $storeId Store ID
     * @param string $categoryName Category name
     * @return array|null Category data or null if not found
     */
    public function findCategoryByName(int $storeId, string $categoryName): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM categories 
            WHERE store_id = ? AND name = ? AND status = 'active'
            LIMIT 1
        ");
        $stmt->execute([$storeId, $categoryName]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * Create category for store
     * @param int $storeId Store ID
     * @param string $categoryName Category name
     * @return int|null Category ID or null on failure
     */
    public function createCategory(int $storeId, string $categoryName): ?int
    {
        try {
            // Generate slug from name
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $categoryName)));

            $stmt = $this->db->prepare("
                INSERT INTO categories (store_id, name, slug, status, created_at, updated_at)
                VALUES (?, ?, ?, 'active', NOW(), NOW())
            ");

            if ($stmt->execute([$storeId, $categoryName, $slug])) {
                return (int)$this->db->lastInsertId();
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Bulk insert products
     * @param array $products Array of product data
     * @return array Result with success count and errors
     */
    public function bulkInsert(array $products): array
    {
        $successCount = 0;
        $errors = [];

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} 
                (store_id, name, description, price, category_id, sku, stock_quantity, weight, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");

            foreach ($products as $index => $product) {
                try {
                    $stmt->execute([
                        $product['store_id'],
                        $product['name'],
                        $product['description'] ?? null,
                        $product['price'],
                        $product['category_id'] ?? null,
                        $product['sku'] ?? null,
                        $product['stock_quantity'] ?? 0,
                        $product['weight'] ?? null,
                        $product['status'] ?? 'active'
                    ]);
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = [
                        'row' => $index + 2, // +2 for header row and 0-based index
                        'error' => $e->getMessage(),
                        'data' => $product
                    ];
                }
            }

            // If there are errors but some succeeded, commit what worked
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }

        return [
            'success_count' => $successCount,
            'errors' => $errors
        ];
    }

    /**
     * Check if SKU exists for a store
     * @param int $storeId Store ID
     * @param string $sku SKU to check
     * @return bool True if SKU exists
     */
    public function skuExists(int $storeId, string $sku): bool
    {
        if (empty($sku)) {
            return false;
        }

        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM {$this->table} 
            WHERE store_id = ? AND sku = ?
        ");
        $stmt->execute([$storeId, $sku]);
        $result = $stmt->fetch();

        return ($result['count'] ?? 0) > 0;
    }

    /**
     * Get statistics for a store
     * @param int $storeId Store ID
     * @param array $filters Optional filters (same as getByStore)
     * @return array Statistics including total, active, low stock, out of stock, and total value
     */
    public function getStoreStats(int $storeId, array $filters = []): array
    {
        $query = "SELECT 
            COUNT(*) as total_products,
            SUM(CASE WHEN status = 'active' AND stock_quantity > 0 THEN 1 ELSE 0 END) as active_products,
            SUM(CASE WHEN stock_quantity < 10 AND stock_quantity > 0 THEN 1 ELSE 0 END) as low_stock,
            SUM(CASE WHEN stock_quantity = 0 THEN 1 ELSE 0 END) as out_of_stock,
            SUM(price * stock_quantity) as total_value
            FROM {$this->table}
            WHERE store_id = ?";
        $params = [$storeId];

        // Apply same filters as getByStore
        if (isset($filters['category'])) {
            $query .= " AND category = ?";
            $params[] = $filters['category'];
        }

        if (isset($filters['category_id'])) {
            $query .= " AND category_id = ?";
            $params[] = $filters['category_id'];
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
        $stats = $stmt->fetch();

        return [
            'total_products' => (int)($stats['total_products'] ?? 0),
            'active_products' => (int)($stats['active_products'] ?? 0),
            'low_stock' => (int)($stats['low_stock'] ?? 0),
            'out_of_stock' => (int)($stats['out_of_stock'] ?? 0),
            'total_value' => (float)($stats['total_value'] ?? 0)
        ];
    }
}
