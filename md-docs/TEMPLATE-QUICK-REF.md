# Template System - Quick Reference

## 🎯 Quick Start

### Creating a Store with a Template

```php
POST /api/stores
{
    "client_id": 1,
    "store_name": "My Store",
    "template_id": 1,  // Choose 1-5
    "primary_color": "#064E3B",
    "accent_color": "#BEF264",
    "description": "My store description"
}
```

### Available Templates

| ID | Name | Color Theme |
|----|------|-------------|
| 1 | CampMart Style | Green & Lime |
| 2 | Minimal Clean | Gray & Light Gray |
| 3 | Bold Modern | Red & Yellow |
| 4 | Classic Ecommerce | Blue & Light Blue |
| 5 | Premium Luxury | Dark Navy & Gold |

### Supported Placeholders

- `{{store_name}}` - Store display name
- `{{store_description}}` - Store tagline/description  
- `{{primary_color}}` - Primary brand color (hex)
- `{{accent_color}}` - Accent brand color (hex)
- `{{logo_url}}` - Store logo URL
- `{{store_id}}` - Store ID number

## 📂 Key Files

- **Template Model**: `backend/models/Template.php`
- **Generator Service**: `backend/services/StoreGeneratorService.php`
- **Base Template**: `store-templates/campmart-style.html`
- **Population Script**: `backend/database/populate_templates.php`

## 🔧 Common Tasks

### Update Template HTML in Database

```bash
php backend/database/populate_templates.php
```

### Add New Placeholder

1. Update `replacePlaceholders()` in StoreGeneratorService:
```php
'{{new_field}}' => $store['new_field'] ?? 'default',
```

2. Add to template HTML:
```html
<div>{{new_field}}</div>
```

3. Re-run populate script

### Create New Template Variation

1. Go to Admin → Templates
2. Click "Add Template"
3. Paste HTML with placeholders
4. Set name and description
5. Save

## 🧪 Testing

### Test Template System

1. Create store with template ID 1 (green)
2. Create store with template ID 3 (red)  
3. Compare `/api/stores/store-1/` vs `/api/stores/store-2/`
4. Verify colors differ

### Verify Template Applied

```bash
# Check generated HTML
cat api/stores/store-1/index.html | grep "primary"

# Should show your store's primary_color, not template default
```

## 📋 Troubleshooting

**Templates not different?**
- Run `php backend/database/populate_templates.php`
- Check `store_templates.html_template` in database
- Verify template_id saved in stores table

**Placeholders showing in output?**
- Check spelling matches exactly  
- Verify field exists in stores table
- Review `replacePlaceholders()` method

**Colors not applying?**
- Check store has `primary_color` and `accent_color`
- Verify template uses `{{primary_color}}` placeholder
- Clear browser cache

## 📖 Full Documentation

- Complete Guide: `md-docs/TEMPLATE-SYSTEM.md`
- Implementation Summary: `md-docs/TEMPLATE-IMPLEMENTATION-SUMMARY.md`
