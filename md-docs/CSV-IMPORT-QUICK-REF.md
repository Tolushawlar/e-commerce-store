# CSV Bulk Import - Quick Reference

## API Endpoints

### Import CSV
```http
POST /api/products/import-csv
Authorization: Bearer {token}
Content-Type: multipart/form-data

csv_file: [file]
store_id: [integer]
```

### Download Template
```http
GET /api/products/csv-template
Authorization: Bearer {token}
```

---

## CSV Format

### Required Columns
- `name` - Product name (2-200 chars)
- `price` - Price in Naira (> 0)
- `stock_quantity` - Stock count (>= 0)

### Optional Columns
- `sku` - Unique identifier
- `description` - Product description
- `category_name` - Auto-created if new
- `weight` - Weight in kg
- `status` - `active` or `inactive`

### Example CSV
```csv
name,sku,description,price,stock_quantity,category_name,weight,status
iPhone 13,IP13-128,Latest model,450000,25,Electronics,0.2,active
Earbuds,EB-001,Wireless,15000,100,Accessories,0.05,active
```

---

## Validation Rules

| Field | Rule | Error Message |
|-------|------|---------------|
| name | 2-200 chars | "Product name is required and must be at least 2 characters" |
| price | > 0 | "Price must be greater than 0" |
| stock_quantity | >= 0 | "Stock quantity cannot be negative" |
| sku | Unique | "SKU already exists in this store" |
| status | active/inactive | "Status must be either active or inactive" |
| weight | Numeric | "Weight must be a number" |

---

## Response Format

### Success (200 OK)
```json
{
  "success": true,
  "message": "Successfully imported 10 products",
  "data": {
    "success_count": 10,
    "total_rows": 10,
    "failed_count": 0,
    "errors": []
  }
}
```

### Partial Success
```json
{
  "success": true,
  "message": "Successfully imported 8 products. 2 rows failed",
  "data": {
    "success_count": 8,
    "total_rows": 10,
    "failed_count": 2,
    "errors": [
      {
        "row": 3,
        "error": "Price must be greater than 0",
        "data": { "name": "Product", "price": "-100" }
      }
    ]
  }
}
```

---

## File Constraints

- **Format:** CSV only
- **Max Size:** 5MB
- **Encoding:** UTF-8
- **Headers:** Required
- **Empty Rows:** Skipped

---

## Features

✅ Auto-category creation
✅ Duplicate SKU detection  
✅ Row-by-row validation  
✅ Partial success handling  
✅ Detailed error reporting  
✅ Transaction safety  

---

## Common Issues

### File Upload Fails
- Check file size (< 5MB)
- Verify file is CSV format
- Ensure `store_id` is provided

### All Rows Fail
- Check required columns exist
- Verify header row matches format
- Check data types (price/stock must be numeric)

### Some Rows Fail
- Review error messages in response
- Check for duplicate SKUs
- Validate price/stock values

---

## Testing

### Quick Test
```bash
php test_csv_import.php
```

### With Sample Data
```bash
# Use sample_products_import.csv
# Contains 10 sample products
```

---

## Code References

- **Model Methods:** `backend/models/Product.php`
  - `findCategoryByName()`
  - `createCategory()`
  - `bulkInsert()`
  - `skuExists()`

- **Controller:** `backend/controllers/ProductController.php`
  - `importCSV()` - Main import handler
  - `csvTemplate()` - Template download

- **Routes:** `api/index.php` (lines 92-96)
