# Category System - Quick Start Guide

## 🚀 Quick Setup (5 Minutes)

### Step 1: Run Database Migration
```bash
mysql -u your_username -p your_database < backend/database/add_categories_table.sql
```

### Step 2: Verify Installation
Check that these files exist:
- ✅ `backend/models/Category.php`
- ✅ `backend/controllers/CategoryController.php`
- ✅ `app/assets/js/services/category.service.js`
- ✅ `app/admin/categories.php`

### Step 3: Test API
Visit: `http://your-domain/api/docs`
Look for "Categories" section in Swagger UI

### Step 4: Access Admin Panel
Visit: `http://your-domain/admin/categories.php`

---

## 💡 Common Use Cases

### Creating a Category
```javascript
await categoryService.create({
  store_id: 1,
  name: "Electronics",
  description: "Gadgets and devices"
});
```

### Listing Categories in Product Form
```html
<select id="category_id">
  <option value="">Select Category</option>
</select>

<script>
async function loadCategories(storeId) {
  const { data } = await categoryService.getAll({ 
    store_id: storeId, 
    status: 'active' 
  });
  
  const select = document.getElementById('category_id');
  data.categories.forEach(cat => {
    select.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
  });
}
</script>
```

### Filtering Products by Category
```javascript
const products = await productService.getAll({
  store_id: 1,
  category_id: 5  // Electronics
});
```

### Creating Subcategories
```javascript
// 1. Create parent
const parent = await categoryService.create({
  store_id: 1,
  name: "Electronics"
});

// 2. Create child
await categoryService.create({
  store_id: 1,
  name: "Smartphones",
  parent_id: parent.data.id
});
```

---

## 🔗 Key Files

| File | Purpose |
|------|---------|
| `backend/database/add_categories_table.sql` | Database schema |
| `backend/models/Category.php` | Data operations |
| `backend/controllers/CategoryController.php` | API logic |
| `app/assets/js/services/category.service.js` | Frontend API client |
| `app/admin/categories.php` | Admin interface |
| `md-docs/CATEGORY-SYSTEM.md` | Full documentation |

---

## 📞 API Quick Reference

```javascript
// Get all categories
categoryService.getAll({ store_id: 1 })

// Get tree structure
categoryService.getTree(storeId)

// Get single category
categoryService.getById(categoryId)

// Create category
categoryService.create(data)

// Update category
categoryService.update(categoryId, data)

// Delete category
categoryService.delete(categoryId)

// Get popular categories
categoryService.getPopular(storeId, limit)
```

---

## ⚠️ Important Notes

1. **Always specify store_id** when fetching categories
2. **Slugs are auto-generated** if not provided
3. **Deleting categories** unlinks products (doesn't delete them)
4. **Parent categories** must be in the same store
5. **Circular relationships** are prevented automatically

---

## 🐛 Troubleshooting

### "Store ID is required" error
```javascript
// ❌ Wrong
categoryService.getAll()

// ✅ Correct
categoryService.getAll({ store_id: 1 })
```

### Category not appearing in dropdown
```javascript
// Check status filter
categoryService.getAll({ 
  store_id: 1, 
  status: 'active'  // Make sure category is active
})
```

### Slug already exists
```javascript
// Each store can have unique slugs
// "electronics" can exist in Store 1 and Store 2
// But not twice in Store 1
```

---

## 📚 Need More Help?

- Full Documentation: `md-docs/CATEGORY-SYSTEM.md`
- Implementation Summary: `CATEGORY-IMPLEMENTATION-SUMMARY.md`
- API Docs: `http://your-domain/api/docs`
- Example Admin Page: `app/admin/categories.php`
