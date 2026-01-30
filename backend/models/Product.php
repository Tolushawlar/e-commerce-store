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
        $query = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                  FROM {$this->table} p
                  LEFT JOIN categories c ON p.category_id = c.id
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

        $query .= " ORDER BY p.created_at DESC";

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
}
