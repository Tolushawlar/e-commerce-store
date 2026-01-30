<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Template Model
 */
class Template extends Model
{
    protected string $table = 'store_templates';

    protected array $fillable = [
        'name',
        'description',
        'preview_image',
        'html_template',
        'css_template'
    ];

    /**
     * Find template by ID with all template data
     * 
     * @param int $id Template ID
     * @return array|null Template data including html_template and css_template
     */
    public function findWithTemplateData(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, name, description, preview_image, html_template, css_template, created_at
            FROM {$this->table}
            WHERE id = ?
        ");
        $stmt->execute([$id]);

        $template = $stmt->fetch(PDO::FETCH_ASSOC);
        return $template ?: null;
    }

    /**
     * Get default template (ID = 1)
     * 
     * @return array|null Default template data
     */
    public function getDefault(): ?array
    {
        return $this->findWithTemplateData(1);
    }
}
