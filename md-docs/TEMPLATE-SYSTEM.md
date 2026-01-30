# Template System Implementation

## Overview

The e-commerce platform now has a **fully functional template system** that allows different stores to use different design templates. Templates are stored in the database and use placeholder-based rendering.

## Architecture

### Components

1. **Template Model** (`backend/models/Template.php`)
   - Manages template database operations
   - Methods:
     - `findWithTemplateData(int $id)` - Get template by ID with html_template
     - `getDefault()` - Get default template (ID = 1)

2. **StoreGeneratorService** (`backend/services/StoreGeneratorService.php`)
   - Loads templates from database
   - Replaces placeholders with store-specific data
   - Generates static HTML files for each store

3. **Database Table** (`store_templates`)
   - `id` - Template ID
   - `name` - Template name
   - `description` - Template description
   - `preview_image` - Preview image URL
   - `html_template` - HTML template with placeholders
   - `css_template` - Additional CSS (optional)

## How It Works

### 1. Store Creation Flow

```
User creates store → Selects template → Saves template_id
     ↓
StoreController.store() → StoreGeneratorService.generate()
     ↓
Load template from DB → Replace placeholders → Save HTML file
```

### 2. Template Rendering

**Template placeholders** are replaced with actual store data:

| Placeholder | Replaced With | Example |
|------------|---------------|---------|
| `{{store_name}}` | Store name | "TechMart" |
| `{{store_description}}` | Store description | "Your one-stop tech shop" |
| `{{primary_color}}` | Primary brand color | "#064E3B" |
| `{{accent_color}}` | Accent brand color | "#BEF264" |
| `{{logo_url}}` | Logo URL | "https://..." |
| `{{store_id}}` | Store ID | 1 |

### 3. Template Variations

5 default templates are available:

1. **CampMart Style** (ID: 1) - Green (#064E3B) - Modern campus commerce design
2. **Minimal Clean** (ID: 2) - Gray (#1F2937) - Minimalist professional design
3. **Bold Modern** (ID: 3) - Red (#DC2626) - Vibrant modern brand design
4. **Classic Ecommerce** (ID: 4) - Blue (#1E40AF) - Traditional ecommerce layout
5. **Premium Luxury** (ID: 5) - Dark (#0F172A) - Elegant luxury product design

## Implementation Details

### Code Changes

#### Template Model
```php
// New method to load template with HTML content
public function findWithTemplateData(int $id): ?array
{
    $stmt = $this->db->prepare("
        SELECT id, name, description, preview_image, html_template, css_template, created_at
        FROM {$this->table}
        WHERE id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}
```

#### StoreGeneratorService
```php
// Load template from database
$templateId = $store['template_id'] ?? 1;
$template = $this->templateModel->findWithTemplateData($templateId);

// Generate HTML with placeholder replacement
$html = $this->generateHTML($store, $template);
```

#### Placeholder Replacement
```php
private function replacePlaceholders(string $template, array $store): string
{
    $placeholders = [
        '{{store_name}}' => $store['store_name'] ?? 'My Store',
        '{{store_description}}' => $store['description'] ?? 'Welcome to our online store',
        '{{primary_color}}' => $store['primary_color'] ?? '#064E3B',
        '{{accent_color}}' => $store['accent_color'] ?? '#BEF264',
        // ... more placeholders
    ];
    
    return str_replace(array_keys($placeholders), array_values($placeholders), $template);
}
```

## Database Setup

### Populate Templates

Run the population script to load HTML templates:

```bash
php backend/database/populate_templates.php
```

This script:
- Reads `store-templates/campmart-style.html`
- Inserts HTML into `html_template` column for all 5 templates
- Creates variations with different default colors

### Manual Template Creation

To create a new template via admin UI:

1. Go to Admin → Templates
2. Click "Add Template"
3. Fill in:
   - **Name**: Template display name
   - **Description**: Template description
   - **HTML Template**: Full HTML with `{{placeholders}}`
   - **CSS Template**: Additional custom CSS (optional)

## Testing

### Test Different Templates

1. **Create Store with Template 1** (CampMart - Green):
   - Template ID: 1
   - Expected: Green primary color (#064E3B)

2. **Create Store with Template 3** (Bold Modern - Red):
   - Template ID: 3
   - Expected: Red primary color (#DC2626)

3. **Verify Customization**:
   - Each store should apply its own `primary_color` and `accent_color`
   - Template provides base HTML structure
   - Store customization colors override template defaults

### Validation

Check generated stores:
- Navigate to `/api/stores/store-{id}/index.html`
- Verify store name appears correctly
- Verify colors match store settings (not template defaults)
- Verify different templates produce visually different layouts

## Adding New Placeholders

To add new placeholder support:

1. **Add to `replacePlaceholders()` method**:
   ```php
   '{{new_placeholder}}' => $store['new_field'] ?? 'default_value',
   ```

2. **Add to template HTML**:
   ```html
   <div>{{new_placeholder}}</div>
   ```

3. **Update documentation** with new placeholder

## Best Practices

### Template Design

1. **Use Tailwind CSS** for styling (already loaded in templates)
2. **Include responsive design** (mobile-first approach)
3. **Use semantic HTML** for accessibility
4. **Add placeholder comments** for clarity:
   ```html
   <!-- Store Name: {{store_name}} -->
   ```

### Placeholder Naming

- Use lowercase with underscores
- Wrap in double curly braces: `{{placeholder}}`
- Keep names descriptive and concise
- Document all placeholders

### Template Testing

Before saving a template:
1. Test with different store names (short, long)
2. Test with different color combinations
3. Verify all placeholders are replaced
4. Check mobile responsiveness
5. Validate HTML syntax

## Troubleshooting

### Template Not Applied

**Problem**: Store uses default template instead of selected one

**Solution**:
- Check `stores.template_id` in database
- Verify template exists: `SELECT * FROM store_templates WHERE id = ?`
- Check template has `html_template` content
- Review StoreGeneratorService logs

### Placeholders Not Replaced

**Problem**: Seeing `{{store_name}}` in generated HTML

**Solution**:
- Verify placeholder spelling matches exactly
- Check `replacePlaceholders()` includes the placeholder
- Ensure store data contains the field
- Check for typos in template HTML

### Style Not Applied

**Problem**: Colors or styles not showing correctly

**Solution**:
- Verify Tailwind CSS is loaded
- Check browser console for errors
- Verify color hex codes are valid
- Clear browser cache

## Future Enhancements

Potential improvements:

1. **Visual Template Editor**
   - Drag-and-drop interface
   - Live preview with sample data
   - WYSIWYG editing

2. **Template Marketplace**
   - Community-contributed templates
   - Premium template store
   - Template ratings and reviews

3. **Advanced Placeholders**
   - Conditional rendering: `{{#if show_cart}}...{{/if}}`
   - Loops: `{{#each products}}...{{/each}}`
   - Filters: `{{store_name|uppercase}}`

4. **Component System**
   - Reusable header/footer components
   - Product card variations
   - Hero section templates

5. **A/B Testing**
   - Test multiple templates for same store
   - Analytics integration
   - Conversion tracking

## Files Changed

- ✅ `backend/models/Template.php` - Added findWithTemplateData()
- ✅ `backend/services/StoreGeneratorService.php` - Template loading & rendering
- ✅ `backend/database/populate_templates.php` - Template population script
- ✅ `store-templates/campmart-style.html` - Base template with placeholders

## Conclusion

The template system is now fully operational. Stores will use their selected template from the database, with placeholders replaced by actual store data. Each template can have a unique design, and stores can customize colors while maintaining the template's structure.
