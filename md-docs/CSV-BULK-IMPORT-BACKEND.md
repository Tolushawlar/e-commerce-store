# CSV Bulk Product Import - Backend Implementation

## Overview
The CSV bulk product import feature allows store owners to import multiple products at once using a CSV file. This implementation follows the existing architecture patterns in the e-commerce store codebase.

## Backend Components

### 1. Product Model Methods (`backend/models/Product.php`)

#### `findCategoryByName(int $storeId, string $categoryName): ?array`
Finds a category by name for a specific store.
- **Parameters:**
  - `$storeId`: Store ID
  - `$categoryName`: Category name to search
- **Returns:** Category data array or null if not found
- **Use Case:** Check if category exists before creating a new one

#### `createCategory(int $storeId, string $categoryName): ?int`
Creates a new category for a store.
- **Parameters:**
  - `$storeId`: Store ID
  - `$categoryName`: Category name
- **Returns:** Category ID on success, null on failure
- **Features:** 
  - Auto-generates slug from name
  - Sets status to 'active' by default

#### `bulkInsert(array $products): array`
Performs batch insertion of products with transaction support.
- **Parameters:**
  - `$products`: Array of product data arrays
- **Returns:** Array with:
  - `success_count`: Number of successfully inserted products
  - `errors`: Array of error objects with row number, error message, and data
- **Features:**
  - Uses database transactions
  - Continues on individual row failures
  - Provides detailed error reporting

#### `skuExists(int $storeId, string $sku): bool`
Checks if a SKU already exists in a store.
- **Parameters:**
  - `$storeId`: Store ID
  - `$sku`: SKU to check
- **Returns:** True if SKU exists, false otherwise
- **Use Case:** Prevent duplicate SKUs during import

---

### 2. ProductController Methods (`backend/controllers/ProductController.php`)

#### `importCSV(): void`
Main endpoint for CSV file upload and processing.

**Endpoint:** `POST /api/products/import-csv`

**Authentication:** Required (AuthMiddleware)

**Request:**
- **Content-Type:** `multipart/form-data`
- **Parameters:**
  - `csv_file` (file, required): CSV file to import
  - `store_id` (integer, required): Target store ID

**CSV Format:**
```csv
name,sku,description,price,stock_quantity,category_name,weight,status
Product Name,PROD-001,Product description,25000,100,Electronics,0.5,active
```

**Required CSV Columns:**
- `name`: Product name (min 2, max 200 characters)
- `price`: Product price (must be > 0)
- `stock_quantity`: Stock quantity (must be >= 0)

**Optional CSV Columns:**
- `sku`: Stock Keeping Unit (unique per store, max 100 chars)
- `description`: Product description
- `category_name`: Category name (auto-created if doesn't exist)
- `weight`: Product weight in kg
- `status`: Product status (`active` or `inactive`, default: `active`)

**Validation Rules:**
1. File must be CSV format (validated by MIME type and extension)
2. File size must not exceed 5MB
3. Required columns must be present in header row
4. Each row must have valid data:
   - Name: 2-200 characters
   - Price: Positive number
   - Stock: Non-negative integer
   - SKU: Unique within store (if provided)
   - Weight: Numeric (if provided)
   - Status: 'active' or 'inactive' (if provided)

**Processing Flow:**
1. Validate file upload (size, type)
2. Parse CSV headers
3. Validate required headers exist
4. Process each row:
   - Validate data
   - Check/create category if `category_name` provided
   - Check for duplicate SKU
   - Add to valid products array or errors array
5. Bulk insert valid products using transaction
6. Return detailed report with success count and errors

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Successfully imported 45 products. 5 rows failed",
  "data": {
    "success_count": 45,
    "total_rows": 50,
    "failed_count": 5,
    "errors": [
      {
        "row": 5,
        "error": "Price must be greater than 0",
        "data": {
          "name": "Invalid Product",
          "price": "-100",
          "stock_quantity": "10"
        }
      }
    ]
  }
}
```

**Error Responses:**
- `400`: Missing file, invalid file type, or missing store_id
- `422`: Validation errors (missing required columns, no valid products)
- `500`: Server error during processing

**Features:**
- Execution time increased to 5 minutes for large files
- Memory limit increased to 256MB
- Auto-category creation
- Transaction rollback on critical errors
- Detailed row-by-row error reporting
- Continues processing valid rows even if some fail

---

#### `csvTemplate(): void`
Downloads a sample CSV template for product import.

**Endpoint:** `GET /api/products/csv-template`

**Authentication:** Required (AuthMiddleware)

**Query Parameters:**
- `store_id` (integer, optional): Include existing categories (future enhancement)

**Response:**
- **Content-Type:** `text/csv; charset=utf-8`
- **File Name:** `product_import_template.csv`

**Template Contents:**
```csv
name,sku,description,price,stock_quantity,category_name,weight,status
Sample Product 1,PROD-001,This is a sample product description,25000,100,Electronics,0.5,active
Sample Product 2,PROD-002,Another sample product,15000,50,Accessories,0.2,active
```

---

### 3. API Routes (`api/index.php`)

```php
// CSV Import Routes (must come before /{id} routes)
$router->get('/api/products/csv-template', [ProductController::class, 'csvTemplate'])
    ->middleware([AuthMiddleware::class, 'handle']);
$router->post('/api/products/import-csv', [ProductController::class, 'importCSV'])
    ->middleware([AuthMiddleware::class, 'handle']);
```

**Important:** These routes are placed before `/api/products/{id}` to prevent route matching conflicts.

---

## Security Features

1. **File Validation:**
   - MIME type checking
   - File extension validation
   - File size limits (5MB max)

2. **Authentication:**
   - All endpoints require valid JWT token
   - Store ownership verification (future enhancement)

3. **Data Sanitization:**
   - All input data trimmed
   - SQL injection prevention via prepared statements
   - Category slug generation sanitizes input

4. **Transaction Safety:**
   - Database transactions for bulk inserts
   - Rollback on critical failures
   - Partial success handling (commits successful inserts)

---

## Error Handling

### Validation Errors
Each row is validated independently. Errors are collected and returned with:
- Row number (1-based, accounting for header)
- Error message
- Original row data for debugging

### Common Error Messages
- "Product name is required and must be at least 2 characters"
- "Price is required and must be a number"
- "Price must be greater than 0"
- "Stock quantity cannot be negative"
- "SKU already exists in this store"
- "Status must be either active or inactive"

### File Upload Errors
- "No CSV file uploaded"
- "File size exceeds 5MB limit"
- "Invalid file type. Please upload a CSV file"
- "CSV file is empty"
- "Missing required columns: name, price, stock_quantity"

---

## Performance Considerations

1. **Execution Time:**
   - Set to 300 seconds (5 minutes)
   - Allows processing of large CSV files

2. **Memory Limit:**
   - Increased to 256MB
   - Handles files with thousands of rows

3. **Batch Processing:**
   - Products inserted in single transaction
   - Reduces database round trips

4. **Error Collection:**
   - All validation errors collected before database operations
   - Prevents unnecessary database calls

---

## Testing

### Test Script
Run `test_csv_import.php` to verify implementation:
```bash
php test_csv_import.php
```

**Tests performed:**
- Model methods exist
- Controller methods exist
- CSV template generation
- Validation logic
- Route configuration

### Sample CSV File
Use `sample_products_import.csv` for testing with real data.

---

## Usage Examples

### 1. Download Template
```javascript
// Frontend JavaScript
const response = await fetch('/api/products/csv-template', {
  headers: {
    'Authorization': `Bearer ${token}`
  }
});
const blob = await response.blob();
const url = window.URL.createObjectURL(blob);
const a = document.createElement('a');
a.href = url;
a.download = 'product_import_template.csv';
a.click();
```

### 2. Upload CSV
```javascript
// Frontend JavaScript
const formData = new FormData();
formData.append('csv_file', fileInput.files[0]);
formData.append('store_id', selectedStoreId);

const response = await fetch('/api/products/import-csv', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`
  },
  body: formData
});

const result = await response.json();
console.log(`Imported ${result.data.success_count} products`);
if (result.data.errors.length > 0) {
  console.log('Failed rows:', result.data.errors);
}
```

---

## Future Enhancements

1. **Update Existing Products:**
   - Add `update_if_exists` parameter
   - Match products by SKU
   - Update instead of insert if SKU exists

2. **Image URL Support:**
   - Add `image_url` column
   - Validate and download images during import

3. **Async Processing:**
   - Queue large imports (100+ products)
   - Send email notification when complete
   - Real-time progress updates via WebSocket

4. **Import History:**
   - Track all CSV imports
   - Store import logs with timestamps
   - Allow re-download of error reports

5. **Category Mapping:**
   - Include existing categories in template
   - Provide category suggestions
   - Bulk category management

---

## Maintenance Notes

### Code Location
- **Model:** `backend/models/Product.php` (lines 251-394)
- **Controller:** `backend/controllers/ProductController.php` (lines 440-730)
- **Routes:** `api/index.php` (lines 92-96)

### Dependencies
- PHP 7.4+
- PDO extension
- finfo extension (for MIME type checking)

### Configuration
Edit these values in `importCSV()` method:
```php
set_time_limit(300);           // Execution time (seconds)
ini_set('memory_limit', '256M'); // Memory limit
$maxFileSize = 5 * 1024 * 1024;  // Max file size (bytes)
```

---

## Changelog

### Version 1.0.0 (Initial Implementation)
- CSV file upload and validation
- Row-by-row validation with detailed errors
- Auto-category creation from category_name
- Duplicate SKU detection
- Transaction-based bulk insert
- CSV template download endpoint
- Comprehensive error reporting
