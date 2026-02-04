<?php

namespace App\Models;

use App\Core\Model;

/**
 * Client Model
 */
class Client extends Model
{
    protected string $table = 'clients';

    protected array $fillable = [
        'name',
        'email',
        'password',
        'company_name',
        'phone',
        'subscription_plan',
        'status'
    ];

    protected array $hidden = ['password'];

    /**
     * Get client by email
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * Get email and name by ID
     */
    public function getEmailAndName(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT email, CONCAT(name, '') as name FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * Get client with stores
     */
    public function withStores(int $id): ?array
    {
        $client = $this->find($id);

        if (!$client) {
            return null;
        }

        $stmt = $this->db->prepare("SELECT * FROM stores WHERE client_id = ?");
        $stmt->execute([$id]);
        $client['stores'] = $stmt->fetchAll();

        return $client;
    }

    /**
     * Get clients with statistics
     */
    public function withStats(array $conditions = [], int $limit = 20, int $offset = 0, ?string $search = null): array
    {
        $query = "
            SELECT c.*, 
                   COUNT(DISTINCT s.id) as store_count,
                   COUNT(DISTINCT o.id) as order_count
            FROM clients c
            LEFT JOIN stores s ON c.id = s.client_id
            LEFT JOIN orders o ON s.id = o.store_id
        ";

        $params = [];
        $whereClauses = [];

        if (!empty($conditions)) {
            $whereClauses[] = $this->buildWhereClause($conditions, $params, 'c');
        }

        if ($search) {
            $whereClauses[] = "(c.name LIKE ? OR c.email LIKE ? OR c.company_name LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($whereClauses)) {
            $query .= " WHERE " . implode(' AND ', $whereClauses);
        }

        $query .= " GROUP BY c.id ORDER BY c.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return array_map([$this, 'filterHidden'], $stmt->fetchAll());
    }

    /**
     * Count clients with search support
     */
    public function count(array $conditions = [], ?string $search = null): int
    {
        $query = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];
        $whereClauses = [];

        if (!empty($conditions)) {
            $whereClauses[] = $this->buildWhereClause($conditions, $params);
        }

        if ($search) {
            $whereClauses[] = "(name LIKE ? OR email LIKE ? OR company_name LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($whereClauses)) {
            $query .= " WHERE " . implode(' AND ', $whereClauses);
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return (int)$stmt->fetch()['count'];
    }

    /**
     * Build WHERE clause with table prefix
     */
    protected function buildWhereClause(array $conditions, array &$params, string $tableAlias = ''): string
    {
        $clauses = [];
        $prefix = $tableAlias ? "{$tableAlias}." : '';

        foreach ($conditions as $column => $value) {
            $clauses[] = "{$prefix}{$column} = ?";
            $params[] = $value;
        }

        return implode(' AND ', $clauses);
    }
}
