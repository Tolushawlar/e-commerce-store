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
}
