<?php

namespace App\Models;

use App\Core\Model;

/**
 * Super Admin Model
 */
class SuperAdmin extends Model
{
    protected string $table = 'super_admins';

    protected array $fillable = [
        'username',
        'email',
        'password'
    ];

    protected array $hidden = ['password'];

    /**
     * Get admin by email
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * Get admin by username
     */
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE username = ?");
        $stmt->execute([$username]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * Get email and name by ID
     */
    public function getEmailAndName(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT email, username as name FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();

        return $result ?: null;
    }
}
