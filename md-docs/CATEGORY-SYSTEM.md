# Category System Documentation

## Overview
The Category system provides a comprehensive solution for organizing products in the e-commerce platform. It supports hierarchical categories (parent-child relationships), filtering, and seamless integration with products.

## Database Schema

### Categories Table
```sql
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(100),
    color VARCHAR(7) DEFAULT '#064E3B',
    parent_id INT NULL,
    display_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    UNIQUE KEY unique_store_slug (store_id, slug)
);
```

### Products Table Update
```sql
ALTER TABLE products 
ADD COLUMN category_id INT NULL AFTER store_id,
ADD CONSTRAINT fk_products_category 
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL;
```

## Migration

Run the migration SQL file to set up the categories table:
```bash
mysql -u username -p database_name < backend/database/add_categories_table.sql
```

The migration script will:
1. Create the categories table
2. Add category_id column to products table
3. Migrate existing category strings to the new categories table
4. Update products to reference category_id

## API Endpoints

### Get All Categories
**GET** `/api/categories`

Query Parameters:
- `store_id` (required): Store ID
- `status` (optional): Filter by status (active/inactive)
- `parent_id` (optional): Filter by parent category ID (use 'null' for top-level)
- `search` (optional): Search by name or description
- `tree` (optional): Get hierarchical tree structure (true/false)
- `limit` (optional): Maximum number of results (default: 100)

```javascript
// Get all categories for a store
const response = await categoryService.getAll({ store_id: 1 });

// Get tree structure
const tree = await categoryService.getTree(1, 'active');

// Get top-level categories
const topLevel = await categoryService.getTopLevel(1);
```

### Get Category by ID
**GET** `/api/categories/{id}`

Returns category with subcategories and product count.

```javascript
const category = await categoryService.getById(1);
```

### Get Category by Slug
**GET** `/api/categories/slug/{slug}`

Query Parameters:
- `store_id` (required): Store ID

```javascript
const category = await categoryService.getBySlug('electronics', 1);
```

### Get Popular Categories
**GET** `/api/categories/popular`

Query Parameters:
- `store_id` (required): Store ID
- `limit` (optional): Maximum number of results (default: 10)

```javascript
const popular = await categoryService.getPopular(1, 5);
```

### Create Category
**POST** `/api/categories`

**Auth Required:** Yes

Request Body:
```json
{
  "store_id": 1,
  "name": "Electronics",
  "slug": "electronics",
  "description": "Electronic devices and accessories",
  "icon": "devices",
  "color": "#064E3B",
  "parent_id": null,
  "display_order": 0,
  "status": "active"
}
```

```javascript
const newCategory = await categoryService.create({
  store_id: 1,
  name: "Electronics",
  description: "Electronic devices and accessories"
});
```

### Update Category
**PUT** `/api/categories/{id}`

**Auth Required:** Yes

Request Body (all fields optional):
```json
{
  "name": "Updated Name",
  "description": "Updated description",
  "status": "inactive"
}
```

```javascript
const updated = await categoryService.update(1, {
  name: "Updated Electronics"
});
```

### Delete Category
**DELETE** `/api/categories/{id}`

**Auth Required:** Yes

Deletes the category and unlinks all associated products (sets category_id to NULL).

```javascript
await categoryService.delete(1);
```

## Frontend Integration

### Include the Service
```html
<script src="/assets/js/services/category.service.js"></script>
```

### Usage Examples

#### Display Categories Dropdown
```javascript
async function loadCategoryDropdown(storeId) {
  const response = await categoryService.getAll({ 
    store_id: storeId,
    status: 'active'
  });
  
  const select = document.getElementById('category_id');
  select.innerHTML = '<option value="">Select Category</option>';
  
  response.data.categories.forEach(cat => {
    const option = document.createElement('option');
    option.value = cat.id;
    option.textContent = cat.name;
    select.appendChild(option);
  });
}
```

#### Filter Products by Category
```javascript
async function filterProductsByCategory(storeId, categoryId) {
  const response = await productService.getAll({
    store_id: storeId,
    category_id: categoryId
  });
  
  displayProducts(response.data.products);
}
```

#### Display Category Tree
```javascript
async function displayCategoryTree(storeId) {
  const response = await categoryService.getTree(storeId);
  
  function renderTree(categories, parentElement) {
    const ul = document.createElement('ul');
    categories.forEach(cat => {
      const li = document.createElement('li');
      li.textContent = `${cat.name} (${cat.product_count} products)`;
      
      if (cat.children && cat.children.length > 0) {
        renderTree(cat.children, li);
      }
      
      ul.appendChild(li);
    });
    parentElement.appendChild(ul);
  }
  
  const container = document.getElementById('category-tree');
  container.innerHTML = '';
  renderTree(response.data.categories, container);
}
```

## Product Integration

### Creating Products with Categories
```javascript
const productData = {
  store_id: 1,
  name: "iPhone 15 Pro",
  description: "Latest iPhone model",
  price: 999.99,
  category_id: 5, // Electronics category
  stock_quantity: 50,
  status: "active"
};

await productService.create(productData);
```

### Filtering Products by Category
```javascript
// By category ID (recommended)
const products = await productService.getAll({
  store_id: 1,
  category_id: 5
});

// By category slug (legacy)
const productsLegacy = await productService.getAll({
  store_id: 1,
  category: "Electronics"
});
```

## Features

### Hierarchical Categories
Create nested category structures:
```javascript
// Create parent category
const parent = await categoryService.create({
  store_id: 1,
  name: "Electronics",
  slug: "electronics"
});

// Create child category
const child = await categoryService.create({
  store_id: 1,
  name: "Smartphones",
  slug: "smartphones",
  parent_id: parent.data.id
});
```

### Auto-slug Generation
If you don't provide a slug, it will be auto-generated from the name:
```javascript
// Name: "Mobile Phones & Accessories"
// Auto-generated slug: "mobile-phones-accessories"
```

### Product Count
Categories automatically track the number of products:
```javascript
const category = await categoryService.getById(1);
console.log(`${category.data.name} has ${category.data.product_count} products`);
```

### Ordering
Control category display order:
```javascript
await categoryService.update(1, { display_order: 1 });
await categoryService.update(2, { display_order: 2 });
```

## Error Handling

All operations include Sentry error reporting:
```javascript
try {
  await categoryService.create(data);
} catch (error) {
  // Error is automatically logged to Sentry
  console.error('Failed to create category:', error.message);
}
```

## Swagger Documentation

The Category API is fully documented with Swagger/OpenAPI annotations. Access the documentation at:
```
http://your-domain/api/docs
```

## Best Practices

1. **Use category_id instead of category string** for new implementations
2. **Keep category hierarchies shallow** (max 2-3 levels recommended)
3. **Use meaningful slugs** for SEO-friendly URLs
4. **Set proper display_order** for consistent category ordering
5. **Regularly review popular categories** to optimize product organization
6. **Handle deleted categories gracefully** - products are automatically unlinked

## Migration from Legacy String-based Categories

The system supports both the old `category` (string) field and new `category_id` (integer foreign key). This allows for backward compatibility during migration.

To complete migration:
1. Run the migration SQL
2. Update frontend to use category dropdowns
3. Verify all products are properly categorized
4. Optionally remove the old `category` column (commented out in migration script)

## Admin Interface Example

See `app/admin/categories.php` for a complete admin interface example with:
- Category listing with search and filters
- Create/Edit category forms
- Tree view display
- Product count indicators
- Bulk operations support
