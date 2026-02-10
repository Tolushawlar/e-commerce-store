<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * ShoppingCart Model
 * Manages persistent shopping cart for registered customers
 */
class ShoppingCart extends Model
{
    protected string $table = 'shopping_carts';
    protected array $fillable = [
        'customer_id',
        'product_id',
        'quantity'
    ];

    /**
     * Get cart items for a customer with product details
     */
    public function getCartItems(int $customerId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                c.*,
                p.name as product_name,
                p.description as product_description,
                p.price as product_price,
                p.stock_quantity,
                p.status as product_status,
                (c.quantity * p.price) as subtotal,
                (
                    SELECT image_url 
                    FROM product_images 
                    WHERE product_id = p.id 
                    ORDER BY is_primary DESC, id ASC 
                    LIMIT 1
                ) as product_image
            FROM {$this->table} c
            INNER JOIN products p ON c.product_id = p.id
            WHERE c.customer_id = ?
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$customerId]);
        return $stmt->fetchAll();
    }

    /**
     * Add item to cart (or update quantity if exists)
     */
    public function addItem(int $customerId, int $productId, int $quantity = 1): bool
    {
        error_log("[ShoppingCart::addItem] Called with customerId=$customerId, productId=$productId, quantity=$quantity");
        
        // Check if item already in cart
        $existing = $this->findCartItem($customerId, $productId);
        error_log("[ShoppingCart::addItem] Existing item: " . json_encode($existing));

        if ($existing) {
            // Update quantity
            $newQuantity = $existing['quantity'] + $quantity;
            error_log("[ShoppingCart::addItem] Updating existing item to quantity=$newQuantity");
            $result = $this->updateQuantity($existing['id'], $newQuantity);
            error_log("[ShoppingCart::addItem] Update result: " . ($result ? 'true' : 'false'));
            return $result;
        } else {
            // Add new item
            $data = [
                'customer_id' => $customerId,
                'product_id' => $productId,
                'quantity' => $quantity
            ];
            error_log("[ShoppingCart::addItem] Creating new item with data: " . json_encode($data));
            $result = $this->create($data);
            error_log("[ShoppingCart::addItem] Create result (lastInsertId): " . json_encode($result));
            return $result !== null;
        }
    }

    /**
     * Find cart item
     */
    public function findCartItem(int $customerId, int $productId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE customer_id = ? AND product_id = ?
        ");
        $stmt->execute([$customerId, $productId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity(int $cartItemId, int $quantity): bool
    {
        if ($quantity <= 0) {
            return $this->delete($cartItemId);
        }

        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET quantity = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        return $stmt->execute([$quantity, $cartItemId]);
    }

    /**
     * Remove item from cart
     */
    public function removeItem(int $customerId, int $productId): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM {$this->table} 
            WHERE customer_id = ? AND product_id = ?
        ");
        return $stmt->execute([$customerId, $productId]);
    }

    /**
     * Clear entire cart
     */
    public function clearCart(int $customerId): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM {$this->table} 
            WHERE customer_id = ?
        ");
        return $stmt->execute([$customerId]);
    }

    /**
     * Get cart total
     */
    public function getCartTotal(int $customerId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as item_count,
                SUM(c.quantity) as total_items,
                SUM(c.quantity * p.price) as total_amount
            FROM {$this->table} c
            INNER JOIN products p ON c.product_id = p.id
            WHERE c.customer_id = ?
        ");
        $stmt->execute([$customerId]);
        $result = $stmt->fetch();

        return [
            'item_count' => (int)($result['item_count'] ?? 0),
            'total_items' => (int)($result['total_items'] ?? 0),
            'total_amount' => (float)($result['total_amount'] ?? 0)
        ];
    }

    /**
     * Get cart item count
     */
    public function getItemCount(int $customerId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM {$this->table} 
            WHERE customer_id = ?
        ");
        $stmt->execute([$customerId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    /**
     * Validate cart items (check stock, active status)
     */
    public function validateCart(int $customerId): array
    {
        $items = $this->getCartItems($customerId);
        $issues = [];

        foreach ($items as $item) {
            // Check if product is inactive
            if ($item['product_status'] !== 'active') {
                $issues[] = [
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'issue' => 'Product is no longer available',
                    'type' => 'unavailable'
                ];
                continue;
            }

            // Check stock
            if ($item['stock_quantity'] < $item['quantity']) {
                $issues[] = [
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'issue' => "Only {$item['stock_quantity']} items available",
                    'type' => 'insufficient_stock',
                    'available' => $item['stock_quantity']
                ];
            }
        }

        return $issues;
    }

    /**
     * Sync cart with session cart (when user logs in)
     */
    public function syncWithSession(int $customerId, array $sessionCart): bool
    {
        try {
            foreach ($sessionCart as $item) {
                $this->addItem(
                    $customerId,
                    $item['product_id'],
                    $item['quantity']
                );
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Remove out-of-stock items from cart
     */
    public function removeOutOfStock(int $customerId): int
    {
        $stmt = $this->db->prepare("
            DELETE c FROM {$this->table} c
            INNER JOIN products p ON c.product_id = p.id
            WHERE c.customer_id = ? 
            AND (p.stock_quantity < c.quantity OR p.status != 'active')
        ");
        $stmt->execute([$customerId]);
        return $stmt->rowCount();
    }
}
