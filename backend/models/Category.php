<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Category Model
 * 
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     title="Category",
 *     required={"id", "store_id", "name", "slug"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="store_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Electronics"),
 *     @OA\Property(property="slug", type="string", example="electronics"),
 *     @OA\Property(property="description", type="string", example="Electronic devices and accessories"),
 *     @OA\Property(property="icon", type="string", example="devices"),
 *     @OA\Property(property="color", type="string", example="#064E3B"),
 *     @OA\Property(property="parent_id", type="integer", nullable=true, example=null),
 *     @OA\Property(property="display_order", type="integer", example=0),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Category extends Model
{
    protected string $table = 'categories';

    protected array $fillable = [
        'store_id',
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'parent_id',
        'display_order',
        'status'
    ];

    /**
     * Get categories by store
     */
    public function getByStore(?int $storeId = null, array $filters = []): array
    {
        $query = "SELECT c.*, 
                  (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count,
                  parent.name as parent_name,
                  s.store_name
                  FROM {$this->table} c
                  LEFT JOIN {$this->table} parent ON c.parent_id = parent.id
                  LEFT JOIN stores s ON c.store_id = s.id";
        $params = [];

        if ($storeId) {
            $query .= " WHERE c.store_id = ?";
            $params[] = $storeId;
        } else {
            $query .= " WHERE 1=1";
        }

        if (!empty($filters['status'])) {
            $query .= " AND c.status = ?";
            $params[] = $filters['status'];
        }

        if (isset($filters['parent_id']) && $filters['parent_id'] !== '') {
            if ($filters['parent_id'] === 'null' || $filters['parent_id'] === null) {
                $query .= " AND c.parent_id IS NULL";
            } else {
                $query .= " AND c.parent_id = ?";
                $params[] = $filters['parent_id'];
            }
        }

        if (!empty($filters['search'])) {
            $query .= " AND (c.name LIKE ? OR c.description LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $query .= " ORDER BY c.display_order ASC, c.name ASC";

        if (isset($filters['limit'])) {
            $query .= " LIMIT ?";
            $params[] = (int)$filters['limit'];
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Get category with subcategories
     */
    public function withSubcategories(int $id): ?array
    {
        $category = $this->find($id);

        if (!$category) {
            return null;
        }

        // Get subcategories
        $stmt = $this->db->prepare("
            SELECT c.*, 
            (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count
            FROM {$this->table} c
            WHERE c.parent_id = ?
            ORDER BY c.display_order ASC, c.name ASC
        ");
        $stmt->execute([$id]);
        $category['subcategories'] = $stmt->fetchAll();

        // Get product count
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM products WHERE category_id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        $category['product_count'] = $result['count'] ?? 0;

        return $category;
    }

    /**
     * Get category by slug
     */
    public function findBySlug(int $storeId, string $slug): ?array
    {
        $stmt = $this->db->prepare("
            SELECT c.*,
            (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count
            FROM {$this->table} c
            WHERE c.store_id = ? AND c.slug = ?
        ");
        $stmt->execute([$storeId, $slug]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * Check if slug exists for store (excluding specific ID)
     */
    public function slugExists(int $storeId, string $slug, ?int $excludeId = null): bool
    {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE store_id = ? AND slug = ?";
        $params = [$storeId, $slug];

        if ($excludeId) {
            $query .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch();

        return ($result['count'] ?? 0) > 0;
    }

    /**
     * Get category tree (hierarchical structure)
     */
    public function getTree(?int $storeId = null, ?string $status = null): array
    {
        $query = "SELECT c.*,
                  (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count,
                  s.store_name
                  FROM {$this->table} c
                  LEFT JOIN stores s ON c.store_id = s.id";
        $params = [];

        if ($storeId) {
            $query .= " WHERE c.store_id = ?";
            $params[] = $storeId;

            if ($status) {
                $query .= " AND c.status = ?";
                $params[] = $status;
            }
        } else {
            if ($status) {
                $query .= " WHERE c.status = ?";
                $params[] = $status;
            }
        }

        $query .= " ORDER BY c.display_order ASC, c.name ASC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $categories = $stmt->fetchAll();

        // Build tree structure
        return $this->buildTree($categories);
    }

    /**
     * Build hierarchical tree from flat array
     */
    private function buildTree(array $categories, ?int $parentId = null): array
    {
        $branch = [];

        foreach ($categories as $category) {
            if ($category['parent_id'] == $parentId) {
                $children = $this->buildTree($categories, $category['id']);
                if ($children) {
                    $category['children'] = $children;
                }
                $branch[] = $category;
            }
        }

        return $branch;
    }

    /**
     * Update display order
     */
    public function updateOrder(int $id, int $order): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET display_order = ? 
            WHERE id = ?
        ");

        return $stmt->execute([$order, $id]);
    }

    /**
     * Get popular categories (by product count)
     */
    public function getPopular(int $storeId, int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, COUNT(p.id) as product_count
            FROM {$this->table} c
            LEFT JOIN products p ON c.id = p.category_id
            WHERE c.store_id = ? AND c.status = 'active'
            GROUP BY c.id
            ORDER BY product_count DESC, c.name ASC
            LIMIT ?
        ");

        $stmt->execute([$storeId, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Delete category and handle products
     */
    public function deleteWithProducts(int $id): bool
    {
        try {
            $this->db->beginTransaction();

            // Update products to remove category reference
            $stmt = $this->db->prepare("
                UPDATE products 
                SET category_id = NULL 
                WHERE category_id = ?
            ");
            $stmt->execute([$id]);

            // Update child categories to remove parent reference
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET parent_id = NULL 
                WHERE parent_id = ?
            ");
            $stmt->execute([$id]);

            // Delete the category
            $result = $this->delete($id);

            $this->db->commit();
            return $result;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
