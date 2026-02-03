<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * CustomerAddress Model
 * Manages shipping and billing addresses for customers
 */
class CustomerAddress extends Model
{
    protected string $table = 'customer_addresses';
    protected array $fillable = [
        'customer_id',
        'address_type',
        'full_name',
        'phone',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'is_default'
    ];

    /**
     * Get all addresses for a customer
     */
    public function getByCustomer(int $customerId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE customer_id = ?
            ORDER BY is_default DESC, created_at DESC
        ");
        $stmt->execute([$customerId]);
        return $stmt->fetchAll();
    }

    /**
     * Get default address for a customer
     */
    public function getDefault(int $customerId, string $type = 'shipping'): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE customer_id = ? 
            AND (address_type = ? OR address_type = 'both')
            AND is_default = true
            LIMIT 1
        ");
        $stmt->execute([$customerId, $type]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Set address as default
     */
    public function setAsDefault(int $id, int $customerId): bool
    {
        // First, unset all other defaults for this customer
        $this->db->prepare("
            UPDATE {$this->table} 
            SET is_default = false 
            WHERE customer_id = ?
        ")->execute([$customerId]);

        // Then set this one as default
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET is_default = true 
            WHERE id = ? AND customer_id = ?
        ");
        return $stmt->execute([$id, $customerId]);
    }

    /**
     * Create address (automatically set as default if first address)
     */
    public function createAddress(array $data): ?int
    {
        // Check if this is the first address
        $customerId = $data['customer_id'];
        $existingCount = $this->countByCustomer($customerId);

        // If first address, make it default
        if ($existingCount === 0) {
            $data['is_default'] = true;
        }

        return $this->create($data);
    }

    /**
     * Count addresses for customer
     */
    public function countByCustomer(int $customerId): int
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
     * Update address
     */
    public function updateAddress(int $id, int $customerId, array $data): bool
    {
        // Ensure customer owns this address
        $address = $this->find($id);
        if (!$address || $address['customer_id'] != $customerId) {
            return false;
        }

        return $this->update($id, $data);
    }

    /**
     * Delete address
     */
    public function deleteAddress(int $id, int $customerId): bool
    {
        // Ensure customer owns this address
        $address = $this->find($id);
        if (!$address || $address['customer_id'] != $customerId) {
            return false;
        }

        // If deleting default address, set another as default
        if ($address['is_default']) {
            $this->setNewDefault($customerId, $id);
        }

        return $this->delete($id);
    }

    /**
     * Set a new default address when current default is deleted
     */
    private function setNewDefault(int $customerId, int $excludeId): void
    {
        $stmt = $this->db->prepare("
            SELECT id FROM {$this->table} 
            WHERE customer_id = ? AND id != ?
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $stmt->execute([$customerId, $excludeId]);
        $newDefault = $stmt->fetch();

        if ($newDefault) {
            $this->setAsDefault($newDefault['id'], $customerId);
        }
    }

    /**
     * Format address as single string
     */
    public static function formatAddress(array $address): string
    {
        $parts = array_filter([
            $address['address_line1'] ?? '',
            $address['address_line2'] ?? '',
            $address['city'] ?? '',
            $address['state'] ?? '',
            $address['postal_code'] ?? '',
            $address['country'] ?? ''
        ]);

        return implode(', ', $parts);
    }
}
