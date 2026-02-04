<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * StoreCustomer Model
 * Handles store-specific customer accounts (registered and guest)
 */
class StoreCustomer extends Model
{
    protected string $table = 'store_customers';
    protected array $fillable = [
        'store_id',
        'email',
        'password_hash',
        'first_name',
        'last_name',
        'phone',
        'is_guest',
        'email_verified',
        'status',
        'last_login_at'
    ];
    protected array $hidden = ['password_hash'];

    /**
     * Find customer by email and store
     */
    public function findByEmailAndStore(string $email, int $storeId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE email = ? AND store_id = ?
        ");
        $stmt->execute([$email, $storeId]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * Create guest customer
     */
    public function createGuest(int $storeId, array $data): ?int
    {
        $guestData = [
            'store_id' => $storeId,
            'email' => $data['email'],
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'phone' => $data['phone'] ?? null,
            'is_guest' => true,
            'status' => 'active'
        ];

        return $this->create($guestData);
    }

    /**
     * Create registered customer with password
     */
    public function createRegistered(int $storeId, array $data): ?int
    {
        // Hash password
        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);

        $customerData = [
            'store_id' => $storeId,
            'email' => $data['email'],
            'password_hash' => $passwordHash,
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'phone' => $data['phone'] ?? null,
            'is_guest' => false,
            'status' => 'active'
        ];

        return $this->create($customerData);
    }

    /**
     * Verify customer password
     */
    public function verifyPassword(array $customer, string $password): bool
    {
        // Guest customers have no password
        if ($customer['is_guest'] || empty($customer['password_hash'])) {
            return false;
        }

        return password_verify($password, $customer['password_hash']);
    }

    /**
     * Update last login time
     */
    public function updateLastLogin(int $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET last_login_at = NOW() 
            WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }

    /**
     * Get email and name by ID
     */
    public function getEmailAndName(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT email, CONCAT(first_name, ' ', last_name) as name FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * Get customer with addresses
     */
    public function findWithAddresses(int $id): ?array
    {
        $customer = $this->find($id);
        if (!$customer) {
            return null;
        }

        // Get addresses
        $stmt = $this->db->prepare("
            SELECT * FROM customer_addresses 
            WHERE customer_id = ?
            ORDER BY is_default DESC, created_at DESC
        ");
        $stmt->execute([$id]);
        $customer['addresses'] = $stmt->fetchAll();

        return $customer;
    }

    /**
     * Get all customers for a store (paginated)
     */
    public function getByStore(int $storeId, array $filters = []): array
    {
        $query = "SELECT * FROM {$this->table} WHERE store_id = ?";
        $params = [$storeId];

        // Filter by guest status
        if (isset($filters['is_guest'])) {
            $query .= " AND is_guest = ?";
            $params[] = $filters['is_guest'] ? 1 : 0;
        }

        // Filter by status
        if (!empty($filters['status'])) {
            $query .= " AND status = ?";
            $params[] = $filters['status'];
        }

        // Search by email or name
        if (!empty($filters['search'])) {
            $query .= " AND (email LIKE ? OR first_name LIKE ? OR last_name LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        // Sorting
        $query .= " ORDER BY created_at DESC";

        // Pagination
        $limit = $filters['limit'] ?? 50;
        $offset = $filters['offset'] ?? 0;
        $query .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return array_map([$this, 'filterHidden'], $stmt->fetchAll());
    }

    /**
     * Get customer order count
     */
    public function getOrderCount(int $customerId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM orders 
            WHERE customer_id = ?
        ");
        $stmt->execute([$customerId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    /**
     * Get customer total spent
     */
    public function getTotalSpent(int $customerId): float
    {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(total_amount), 0) as total 
            FROM orders 
            WHERE customer_id = ? AND payment_status = 'paid'
        ");
        $stmt->execute([$customerId]);
        $result = $stmt->fetch();
        return (float)($result['total'] ?? 0);
    }

    /**
     * Convert guest to registered customer
     */
    public function convertGuestToRegistered(int $id, string $password): bool
    {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET password_hash = ?, is_guest = false 
            WHERE id = ? AND is_guest = true
        ");
        return $stmt->execute([$passwordHash, $id]);
    }

    /**
     * Count customers by store
     */
    public function countByStore(int $storeId, bool $guestsOnly = false): int
    {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE store_id = ?";
        $params = [$storeId];

        if ($guestsOnly) {
            $query .= " AND is_guest = true";
        } else {
            $query .= " AND is_guest = false";
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
}
