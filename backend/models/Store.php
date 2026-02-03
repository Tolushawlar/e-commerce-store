<?php

namespace App\Models;

use App\Core\Model;

/**
 * Store Model
 */
class Store extends Model
{
    protected string $table = 'stores';

    protected array $fillable = [
        'client_id',
        'store_name',
        'store_slug',
        'domain',
        'template_id',
        'primary_color',
        'accent_color',
        'logo_url',
        'description',
        'tagline',
        'hero_background_url',
        'header_style',
        'product_grid_columns',
        'font_family',
        'button_style',
        'show_search',
        'show_cart',
        'show_wishlist',
        'footer_text',
        'social_facebook',
        'social_instagram',
        'social_twitter',
        'custom_css',
        'status',
        'group_by_category',
        'show_category_images',
        'paystack_public_key',
        'paystack_secret_key',
        'payment_enabled',
        'bank_transfer_enabled',
        'bank_name',
        'account_number',
        'account_name',
        'cod_enabled'
    ];

    /**
     * Get store by slug
     */
    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE store_slug = ?");
        $stmt->execute([$slug]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * Get store with client information
     */
    public function withClient(int $id): ?array
    {
        $query = "
            SELECT s.*, c.name as client_name, c.email as client_email, c.company_name
            FROM stores s
            JOIN clients c ON s.client_id = c.id
            WHERE s.id = ?
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * Get stores for a client
     */
    public function getByClient(int $clientId): array
    {
        return $this->all(['client_id' => $clientId]);
    }

    /**
     * Get store with sections
     */
    public function withSections(int $id): ?array
    {
        $store = $this->find($id);

        if (!$store) {
            return null;
        }

        $stmt = $this->db->prepare("
            SELECT * FROM store_sections 
            WHERE store_id = ? 
            ORDER BY sort_order ASC
        ");
        $stmt->execute([$id]);
        $store['sections'] = $stmt->fetchAll();

        return $store;
    }

    /**
     * Get store with navigation
     */
    public function withNavigation(int $id): ?array
    {
        $store = $this->find($id);

        if (!$store) {
            return null;
        }

        $stmt = $this->db->prepare("
            SELECT * FROM store_navigation 
            WHERE store_id = ? AND is_active = 1 
            ORDER BY sort_order ASC
        ");
        $stmt->execute([$id]);
        $store['navigation'] = $stmt->fetchAll();

        return $store;
    }

    /**
     * Get store with full customization data
     */
    public function withCustomization(int $id): ?array
    {
        $store = $this->withClient($id);

        if (!$store) {
            return null;
        }

        // Get sections
        $stmt = $this->db->prepare("
            SELECT * FROM store_sections 
            WHERE store_id = ? 
            ORDER BY sort_order ASC
        ");
        $stmt->execute([$id]);
        $store['sections'] = $stmt->fetchAll();

        // Get navigation
        $stmt = $this->db->prepare("
            SELECT * FROM store_navigation 
            WHERE store_id = ? AND is_active = 1 
            ORDER BY sort_order ASC
        ");
        $stmt->execute([$id]);
        $store['navigation'] = $stmt->fetchAll();

        return $store;
    }

    /**
     * Check if slug is available
     */
    public function isSlugAvailable(string $slug, ?int $excludeId = null): bool
    {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE store_slug = ?";
        $params = [$slug];

        if ($excludeId) {
            $query .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetch()['count'] == 0;
    }

    /**
     * Get all stores with client information
     */
    public function allWithClients(array $conditions = [], int $limit = 20, int $offset = 0): array
    {
        $query = "
            SELECT s.*, c.name as client_name, c.email as client_email, c.company_name
            FROM stores s
            JOIN clients c ON s.client_id = c.id
        ";

        $params = [];

        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $key => $value) {
                $where[] = "s.$key = ?";
                $params[] = $value;
            }
            $query .= " WHERE " . implode(' AND ', $where);
        }

        $query .= " ORDER BY s.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}
