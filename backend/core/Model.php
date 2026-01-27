<?php

namespace App\Core;

use App\Config\Database;
use PDO;

/**
 * Base Model Class
 * All models extend this class for database operations
 */
abstract class Model
{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $hidden = [];

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Find record by ID
     */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();

        return $result ? $this->filterHidden($result) : null;
    }

    /**
     * Get all records
     */
    public function all(array $conditions = [], int $limit = 100, int $offset = 0): array
    {
        $query = "SELECT * FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $query .= " WHERE " . $this->buildWhereClause($conditions, $params);
        }

        $query .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return array_map([$this, 'filterHidden'], $stmt->fetchAll());
    }

    /**
     * Create new record
     */
    public function create(array $data): ?int
    {
        $data = $this->filterFillable($data);

        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');

        $query = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $stmt = $this->db->prepare($query);
        $success = $stmt->execute(array_values($data));

        return $success ? (int)$this->db->lastInsertId() : null;
    }

    /**
     * Update record
     */
    public function update(int $id, array $data): bool
    {
        $data = $this->filterFillable($data);

        $setClauses = [];
        foreach (array_keys($data) as $column) {
            $setClauses[] = "{$column} = ?";
        }

        $query = sprintf(
            "UPDATE %s SET %s WHERE %s = ?",
            $this->table,
            implode(', ', $setClauses),
            $this->primaryKey
        );

        $params = array_values($data);
        $params[] = $id;

        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    /**
     * Delete record
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Count records
     */
    public function count(array $conditions = []): int
    {
        $query = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $query .= " WHERE " . $this->buildWhereClause($conditions, $params);
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return (int)$stmt->fetch()['count'];
    }

    /**
     * Execute custom query
     */
    protected function query(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Build WHERE clause from conditions
     */
    protected function buildWhereClause(array $conditions, array &$params): string
    {
        $clauses = [];
        foreach ($conditions as $column => $value) {
            $clauses[] = "{$column} = ?";
            $params[] = $value;
        }
        return implode(' AND ', $clauses);
    }

    /**
     * Filter only fillable fields
     */
    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * Remove hidden fields from result
     */
    protected function filterHidden(array $data): array
    {
        if (empty($this->hidden)) {
            return $data;
        }

        return array_diff_key($data, array_flip($this->hidden));
    }
}
