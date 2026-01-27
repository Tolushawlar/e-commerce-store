<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;

/**
 * Product Controller
 */
class ProductController extends Controller
{
    private Product $productModel;

    public function __construct()
    {
        $this->productModel = new Product();
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
     *         description="Filter by category",
     *         required=false,
     *         @OA\Schema(type="string")
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

        $filters = [
            'category' => $this->query('category'),
            'status' => $this->query('status'),
            'search' => $this->query('search'),
            'limit' => $limit
        ];

        $products = $this->productModel->getByStore((int)$storeId, $filters);
        $total = $this->productModel->countByStore((int)$storeId, $filters);

        $this->success([
            'products' => $products,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
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

        $productId = $this->productModel->create($data);

        if ($productId) {
            $product = $this->productModel->find($productId);
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

        if ($this->productModel->update($productId, $data)) {
            $product = $this->productModel->find($productId);
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
}
