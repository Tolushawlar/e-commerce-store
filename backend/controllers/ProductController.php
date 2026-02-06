<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Store;
use App\Services\NotificationService;
use App\Services\ExportService;

/**
 * Product Controller
 */
class ProductController extends Controller
{
    private Product $productModel;
    private NotificationService $notificationService;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->notificationService = new NotificationService();
    }

    /**
     * @OA\Get(
     *     path="/api/products",
     *     tags={"Products"},
     *     summary="Get all products",
     *     description="Retrieve list of products for a specific store with filtering options",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="store_id",
     *         in="query",
     *         description="Store ID (required)",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter by category (legacy string-based)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter by category ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"active", "inactive", "out_of_stock"})
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by product name or description",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Maximum number of products to return",
     *         required=false,
     *         @OA\Schema(type="integer", default=50)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of products",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="products", type="array", @OA\Items(ref="#/components/schemas/Product"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Store ID is required", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Server error", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function index(): void
    {
        $storeId = $this->query('store_id');

        if (!$storeId) {
            $this->error('Store ID is required', 400);
        }

        $page = (int)$this->query('page', 1);
        $limit = (int)$this->query('limit', 50);
        $offset = ($page - 1) * $limit;

        $filters = [
            'category' => $this->query('category'),
            'category_id' => $this->query('category_id'),
            'status' => $this->query('status'),
            'search' => $this->query('search'),
            'limit' => $limit,
            'offset' => $offset
        ];

        $products = $this->productModel->getByStore((int)$storeId, $filters);
        $total = $this->productModel->countByStore((int)$storeId, $filters);
        $stats = $this->productModel->getStoreStats((int)$storeId, $filters);

        // Add images to each product
        foreach ($products as &$product) {
            $product['images'] = $this->productModel->getImages($product['id']);
        }

        $this->success([
            'products' => $products,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ],
            'stats' => $stats
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     summary="Get single product",
     *     description="Retrieve detailed product information including images",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product details",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Product not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Server error", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function show(string $id): void
    {
        $product = $this->productModel->withImages((int)$id);

        if (!$product) {
            $this->error('Product not found', 404);
        }

        $this->success($product);
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     tags={"Products"},
     *     summary="Create new product",
     *     description="Add a new product to a store",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"store_id", "name", "price"},
     *             @OA\Property(property="store_id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", minLength=2, maxLength=200, example="Premium Leather Wallet"),
     *             @OA\Property(property="description", type="string", nullable=true, example="High-quality genuine leather wallet"),
     *             @OA\Property(property="price", type="number", format="float", example=49.99),
     *             @OA\Property(property="category", type="string", nullable=true, example="Accessories"),
     *             @OA\Property(property="stock_quantity", type="integer", example=100, default=0),
     *             @OA\Property(property="sku", type="string", nullable=true, example="WALLET-001"),
     *             @OA\Property(property="image_url", type="string", nullable=true, example="https://example.com/images/wallet.jpg"),
     *             @OA\Property(property="status", type="string", enum={"active", "inactive", "out_of_stock"}, example="active", default="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Failed to create product", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function store(): void
    {
        $data = $this->input();

        // Validation
        $errors = $this->validate($data, [
            'store_id' => 'required|numeric',
            'name' => 'required|min:2|max:200',
            'price' => 'required|numeric',
            'stock_quantity' => 'numeric'
        ]);

        if (!empty($errors)) {
            $this->error('Validation failed', 422, $errors);
        }

        $data['status'] = $data['status'] ?? 'active';
        $data['stock_quantity'] = $data['stock_quantity'] ?? 0;

        // Extract images data (don't save in products table)
        $images = [];
        if (!empty($data['images']) && is_array($data['images'])) {
            $images = $data['images'];
            unset($data['images']);
        }
        // Remove image_url if it exists (legacy support)
        unset($data['image_url']);

        $productId = $this->productModel->create($data);

        if ($productId) {
            // Save images to product_images table
            if (!empty($images)) {
                $this->productModel->addImages($productId, $images);
            }

            $product = $this->productModel->withImages($productId);
            $this->success($product, 'Product created successfully', 201);
        } else {
            $this->error('Failed to create product', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     summary="Update product",
     *     description="Update product information",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", minLength=2, maxLength=200, example="Premium Leather Wallet - Updated"),
     *             @OA\Property(property="description", type="string", nullable=true, example="Updated description"),
     *             @OA\Property(property="price", type="number", format="float", example=59.99),
     *             @OA\Property(property="category", type="string", nullable=true, example="Accessories"),
     *             @OA\Property(property="stock_quantity", type="integer", example=150),
     *             @OA\Property(property="sku", type="string", nullable=true, example="WALLET-001-V2"),
     *             @OA\Property(property="image_url", type="string", nullable=true, example="https://example.com/images/wallet-new.jpg"),
     *             @OA\Property(property="status", type="string", enum={"active", "inactive", "out_of_stock"}, example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Product not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Failed to update product", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function update(string $id): void
    {
        $productId = (int)$id;

        if (!$this->productModel->find($productId)) {
            $this->error('Product not found', 404);
        }

        $data = $this->input();

        // Validation
        $errors = $this->validate($data, [
            'name' => 'min:2|max:200',
            'price' => 'numeric',
            'stock_quantity' => 'numeric'
        ]);

        if (!empty($errors)) {
            $this->error('Validation failed', 422, $errors);
        }

        // Extract images data (don't save in products table)
        $images = null;
        if (isset($data['images']) && is_array($data['images'])) {
            $images = $data['images'];
            unset($data['images']);
        }
        // Remove image_url if it exists (legacy support)
        unset($data['image_url']);

        if ($this->productModel->update($productId, $data)) {
            // Update images if provided
            if ($images !== null) {
                $this->productModel->addImages($productId, $images);
            }

            $product = $this->productModel->withImages($productId);

            // Check for low stock and send notification
            if (isset($data['stock_quantity'])) {
                try {
                    $storeModel = new Store();
                    $store = $storeModel->find($product['store_id']);

                    if ($store && isset($store['client_id'])) {
                        $lowStockThreshold = 10; // Can be made configurable
                        $stockQuantity = (int)$data['stock_quantity'];

                        if ($stockQuantity <= $lowStockThreshold && $stockQuantity > 0) {
                            $this->notificationService->send(
                                (int)$store['client_id'],
                                'client',
                                'product',
                                'Low Stock Alert',
                                "Product '{$product['name']}' is running low on stock. Only {$stockQuantity} units remaining.",
                                null,
                                "/client/products.php?id={$productId}",
                                'high'
                            );
                        } elseif ($stockQuantity <= 0) {
                            $this->notificationService->send(
                                (int)$store['client_id'],
                                'client',
                                'product',
                                'Out of Stock Alert',
                                "Product '{$product['name']}' is now out of stock!",
                                null,
                                "/client/products.php?id={$productId}",
                                'urgent'
                            );
                        }
                    }
                } catch (\Exception $e) {
                    // Log error but don't fail the update
                    error_log("Failed to send low stock notification: " . $e->getMessage());
                }
            }

            $this->success($product, 'Product updated successfully');
        } else {
            $this->error('Failed to update product', 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     summary="Delete product",
     *     description="Delete a product from the store",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Product not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Failed to delete product", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function destroy(string $id): void
    {
        $productId = (int)$id;

        if (!$this->productModel->find($productId)) {
            $this->error('Product not found', 404);
        }

        if ($this->productModel->delete($productId)) {
            $this->success(null, 'Product deleted successfully');
        } else {
            $this->error('Failed to delete product', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/products/low-stock",
     *     tags={"Products"},
     *     summary="Get low stock products",
     *     description="Retrieve products that are running low on stock for inventory management",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="store_id",
     *         in="query",
     *         description="Store ID (required)",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="threshold",
     *         in="query",
     *         description="Stock quantity threshold for low stock alert",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of low stock products",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="products", type="array", @OA\Items(ref="#/components/schemas/Product"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Store ID is required", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Server error", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function lowStock(): void
    {
        $storeId = $this->query('store_id');

        if (!$storeId) {
            $this->error('Store ID is required', 400);
        }

        $threshold = (int)$this->query('threshold', 10);
        $products = $this->productModel->getLowStock((int)$storeId, $threshold);

        $this->success(['products' => $products]);
    }

    /**
     * @OA\Post(
     *     path="/api/products/import-csv",
     *     tags={"Products"},
     *     summary="Import products from CSV file",
     *     description="Bulk import products from a CSV file with validation and error reporting",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"csv_file", "store_id"},
     *                 @OA\Property(
     *                     property="csv_file",
     *                     type="string",
     *                     format="binary",
     *                     description="CSV file containing product data"
     *                 ),
     *                 @OA\Property(
     *                     property="store_id",
     *                     type="integer",
     *                     description="Store ID to import products into",
     *                     example=1
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="CSV import completed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Successfully imported 45 products"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="success_count", type="integer", example=45),
     *                 @OA\Property(property="total_rows", type="integer", example=50),
     *                 @OA\Property(property="failed_count", type="integer", example=5),
     *                 @OA\Property(
     *                     property="errors",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="row", type="integer", example=5),
     *                         @OA\Property(property="error", type="string", example="Price must be a positive number"),
     *                         @OA\Property(property="data", type="object")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Server error", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function importCSV(): void
    {
        // Increase execution time for large imports
        set_time_limit(300); // 5 minutes
        ini_set('max_execution_time', '300');
        ini_set('memory_limit', '256M');

        try {
            // Validate store_id
            $storeId = $_POST['store_id'] ?? null;
            if (!$storeId) {
                $this->error('Store ID is required', 400);
            }

            // Check if file was uploaded
            if (!isset($_FILES['csv_file'])) {
                $this->error('No CSV file uploaded', 400);
            }

            $file = $_FILES['csv_file'];

            // Validate file
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $this->error('File upload failed', 400);
            }

            // Check file size (max 5MB)
            $maxFileSize = 5 * 1024 * 1024; // 5MB in bytes
            if ($file['size'] > $maxFileSize) {
                $this->error('File size exceeds 5MB limit', 400);
            }

            // Check file type
            $allowedMimes = ['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            // Also check file extension
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($mimeType, $allowedMimes) && $fileExtension !== 'csv') {
                $this->error('Invalid file type. Please upload a CSV file', 400);
            }

            // Parse CSV file
            $handle = fopen($file['tmp_name'], 'r');
            if ($handle === false) {
                $this->error('Failed to read CSV file', 500);
            }

            // Read header row
            $headers = fgetcsv($handle);
            if ($headers === false) {
                fclose($handle);
                $this->error('CSV file is empty', 400);
            }

            // Normalize headers (lowercase, trim)
            $headers = array_map(function ($header) {
                return strtolower(trim($header));
            }, $headers);

            // Required headers
            $requiredHeaders = ['name', 'price', 'stock_quantity'];
            $missingHeaders = array_diff($requiredHeaders, $headers);
            if (!empty($missingHeaders)) {
                fclose($handle);
                $this->error('Missing required columns: ' . implode(', ', $missingHeaders), 422);
            }

            // Parse rows and validate
            $validProducts = [];
            $errors = [];
            $rowNumber = 1; // Start at 1 (header is row 0)

            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Combine headers with row data
                $rowData = array_combine($headers, $row);

                // Validate row
                $validationErrors = $this->validateCSVRow($rowData, $rowNumber, (int)$storeId);
                if (!empty($validationErrors)) {
                    $errors[] = [
                        'row' => $rowNumber,
                        'error' => implode(', ', $validationErrors),
                        'data' => $rowData
                    ];
                    continue;
                }

                // Process category if provided
                $categoryId = null;
                if (!empty($rowData['category_name'])) {
                    $category = $this->productModel->findCategoryByName((int)$storeId, trim($rowData['category_name']));
                    if ($category) {
                        $categoryId = $category['id'];
                    } else {
                        // Auto-create category
                        $categoryId = $this->productModel->createCategory((int)$storeId, trim($rowData['category_name']));
                    }
                }

                // Prepare product data
                $validProducts[] = [
                    'store_id' => (int)$storeId,
                    'name' => trim($rowData['name']),
                    'description' => !empty($rowData['description']) ? trim($rowData['description']) : null,
                    'price' => (float)$rowData['price'],
                    'stock_quantity' => (int)($rowData['stock_quantity'] ?? 0),
                    'category_id' => $categoryId,
                    'sku' => !empty($rowData['sku']) ? trim($rowData['sku']) : null,
                    'weight' => !empty($rowData['weight']) ? (float)$rowData['weight'] : null,
                    'status' => !empty($rowData['status']) && in_array(strtolower($rowData['status']), ['active', 'inactive'])
                        ? strtolower($rowData['status'])
                        : 'active'
                ];
            }

            fclose($handle);

            // Check if we have any valid products to import
            if (empty($validProducts)) {
                $this->error('No valid products found in CSV file', 422, ['errors' => $errors]);
            }

            // Bulk insert products
            $result = $this->productModel->bulkInsert($validProducts);

            // Merge validation errors with insertion errors
            $allErrors = array_merge($errors, $result['errors']);

            $totalRows = count($validProducts) + count($errors);
            $message = $result['success_count'] > 0
                ? "Successfully imported {$result['success_count']} products"
                : "Failed to import products";

            if (!empty($allErrors)) {
                $message .= ". {count($allErrors)} rows failed";
            }

            $this->success([
                'success_count' => $result['success_count'],
                'total_rows' => $totalRows,
                'failed_count' => count($allErrors),
                'errors' => $allErrors
            ], $message);
        } catch (\Exception $e) {
            error_log("CSV Import Error: " . $e->getMessage());
            $this->error('Failed to import CSV: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Validate a single CSV row
     * @param array $rowData Row data
     * @param int $rowNumber Row number for error reporting
     * @param int $storeId Store ID
     * @return array Array of validation errors
     */
    private function validateCSVRow(array $rowData, int $rowNumber, int $storeId): array
    {
        $errors = [];

        // Validate name
        if (empty($rowData['name']) || strlen(trim($rowData['name'])) < 2) {
            $errors[] = 'Product name is required and must be at least 2 characters';
        } elseif (strlen(trim($rowData['name'])) > 200) {
            $errors[] = 'Product name must not exceed 200 characters';
        }

        // Validate price
        if (empty($rowData['price']) || !is_numeric($rowData['price'])) {
            $errors[] = 'Price is required and must be a number';
        } elseif ((float)$rowData['price'] <= 0) {
            $errors[] = 'Price must be greater than 0';
        }

        // Validate stock_quantity
        if (!isset($rowData['stock_quantity']) || $rowData['stock_quantity'] === '') {
            $errors[] = 'Stock quantity is required';
        } elseif (!is_numeric($rowData['stock_quantity'])) {
            $errors[] = 'Stock quantity must be a number';
        } elseif ((int)$rowData['stock_quantity'] < 0) {
            $errors[] = 'Stock quantity cannot be negative';
        }

        // Validate SKU if provided (check for duplicates)
        if (!empty($rowData['sku'])) {
            if (strlen($rowData['sku']) > 100) {
                $errors[] = 'SKU must not exceed 100 characters';
            } elseif ($this->productModel->skuExists($storeId, trim($rowData['sku']))) {
                $errors[] = 'SKU already exists in this store';
            }
        }

        // Validate weight if provided
        if (!empty($rowData['weight']) && !is_numeric($rowData['weight'])) {
            $errors[] = 'Weight must be a number';
        }

        // Validate status if provided
        if (!empty($rowData['status']) && !in_array(strtolower($rowData['status']), ['active', 'inactive'])) {
            $errors[] = 'Status must be either active or inactive';
        }

        return $errors;
    }

    /**
     * @OA\Get(
     *     path="/api/products/csv-template",
     *     tags={"Products"},
     *     summary="Download CSV template",
     *     description="Download a sample CSV template for bulk product import",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="store_id",
     *         in="query",
     *         description="Store ID to include existing categories in template",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="CSV template file",
     *         @OA\MediaType(
     *             mediaType="text/csv",
     *             @OA\Schema(type="string")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function csvTemplate(): void
    {
        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="product_import_template.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Write CSV headers
        fputcsv($output, ['name', 'sku', 'description', 'price', 'stock_quantity', 'category_name', 'weight', 'status']);

        // Write sample data
        fputcsv($output, [
            'Sample Product 1',
            'PROD-001',
            'This is a sample product description',
            '25000',
            '100',
            'Electronics',
            '0.5',
            'active'
        ]);

        fputcsv($output, [
            'Sample Product 2',
            'PROD-002',
            'Another sample product',
            '15000',
            '50',
            'Accessories',
            '0.2',
            'active'
        ]);

        fclose($output);
        exit;
    }

    /**
     * @OA\Get(
     *     path="/api/stores/{storeId}/products/export",
     *     tags={"Products"},
     *     summary="Export products data",
     *     description="Export store products to CSV format",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="storeId",
     *         in="path",
     *         description="Store ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter by category ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="CSV file download"),
     *     @OA\Response(response=400, description="Bad request", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function exportProducts(int $storeId): void
    {
        // Get filters from query params
        $filters = [
            'category_id' => $this->query('category_id'),
            'status' => $this->query('status'),
            'search' => $this->query('search'),
            'limit' => 10000  // High limit to get all products for export
        ];

        // Remove empty filters (except limit)
        $filters = array_filter($filters, function($value, $key) {
            return $value !== null && $value !== '' || $key === 'limit';
        }, ARRAY_FILTER_USE_BOTH);

        // Get all products using the correct method
        $products = $this->productModel->getByStore($storeId, $filters);

        // Export to CSV
        $exportService = new ExportService();
        $exportService->exportProducts($products);
    }
}
