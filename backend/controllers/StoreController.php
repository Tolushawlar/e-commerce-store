<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Store;
use App\Services\StoreGeneratorService;

/**
 * Store Controller
 */
class StoreController extends Controller
{
    private Store $storeModel;
    private StoreGeneratorService $generator;

    public function __construct()
    {
        $this->storeModel = new Store();
        $this->generator = new StoreGeneratorService();
    }

    /**
     * @OA\Get(
     *     path="/api/stores",
     *     tags={"Stores"},
     *     summary="Get all stores",
     *     description="Retrieve paginated list of stores with filtering options",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=20)
     *     ),
     *     @OA\Parameter(
     *         name="client_id",
     *         in="query",
     *         description="Filter by client ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"active", "inactive", "maintenance"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of stores",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="stores", type="array", @OA\Items(ref="#/components/schemas/Store")),
     *                 @OA\Property(property="pagination", type="object",
     *                     @OA\Property(property="page", type="integer", example=1),
     *                     @OA\Property(property="limit", type="integer", example=20),
     *                     @OA\Property(property="total", type="integer", example=50),
     *                     @OA\Property(property="pages", type="integer", example=3)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Server error", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function index(): void
    {
        $page = (int)$this->query('page', 1);
        $limit = (int)$this->query('limit', 20);
        $offset = ($page - 1) * $limit;

        $clientId = $this->query('client_id');
        $status = $this->query('status');

        $conditions = [];
        if ($clientId) $conditions['client_id'] = $clientId;
        if ($status) $conditions['status'] = $status;

        $stores = $this->storeModel->allWithClients($conditions, $limit, $offset);
        $total = $this->storeModel->count($conditions);

        $this->success([
            'stores' => $stores,
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
     *     path="/api/stores/{id}",
     *     tags={"Stores"},
     *     summary="Get single store",
     *     description="Retrieve store details with optional customization data",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Store ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="include",
     *         in="query",
     *         description="Include related data (use 'customization' to include customization details)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"customization"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Store details",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Store")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Store not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Server error", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function show(string $id): void
    {
        $includeCustomization = $this->query('include') === 'customization';

        $store = $includeCustomization
            ? $this->storeModel->withCustomization((int)$id)
            : $this->storeModel->withClient((int)$id);

        if (!$store) {
            $this->error('Store not found', 404);
        }

        $this->success($store);
    }

    /**
     * @OA\Post(
     *     path="/api/stores",
     *     tags={"Stores"},
     *     summary="Create new store",
     *     description="Create a new online store for a client",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"client_id", "store_name", "store_slug"},
     *             @OA\Property(property="client_id", type="integer", example=1),
     *             @OA\Property(property="store_name", type="string", minLength=2, maxLength=100, example="My Awesome Store"),
     *             @OA\Property(property="store_slug", type="string", minLength=2, maxLength=100, example="my-awesome-store", description="Lowercase letters, numbers, and hyphens only"),
     *             @OA\Property(property="template_id", type="integer", example=1, default=1),
     *             @OA\Property(property="primary_color", type="string", example="#064E3B", default="#064E3B"),
     *             @OA\Property(property="accent_color", type="string", example="#BEF264", default="#BEF264"),
     *             @OA\Property(property="product_grid_columns", type="integer", example=4, default=4),
     *             @OA\Property(property="font_family", type="string", example="Plus Jakarta Sans", default="Plus Jakarta Sans"),
     *             @OA\Property(property="button_style", type="string", example="rounded", default="rounded"),
     *             @OA\Property(property="show_search", type="boolean", example=true, default=true),
     *             @OA\Property(property="show_cart", type="boolean", example=true, default=true),
     *             @OA\Property(property="show_wishlist", type="boolean", example=false, default=false),
     *             @OA\Property(property="status", type="string", enum={"active", "inactive", "maintenance"}, example="active", default="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Store created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Store created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Store")
     *         )
     *     ),
     *     @OA\Response(response=409, description="Store slug already exists", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Failed to create store", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function store(): void
    {
        $data = $this->input();

        // Validation
        $errors = $this->validate($data, [
            'client_id' => 'required|numeric',
            'store_name' => 'required|min:2|max:100',
            'store_slug' => 'required|min:2|max:100'
        ]);

        if (!empty($errors)) {
            $this->error('Validation failed', 422, $errors);
        }

        // Validate slug format (lowercase, hyphens only)
        if (!preg_match('/^[a-z0-9-]+$/', $data['store_slug'])) {
            $this->error('Store slug must contain only lowercase letters, numbers, and hyphens', 422);
        }

        // Check slug availability
        if (!$this->storeModel->isSlugAvailable($data['store_slug'])) {
            $this->error('Store slug already exists', 409);
        }

        // Set defaults
        $data['template_id'] = $data['template_id'] ?? 1;
        $data['primary_color'] = $data['primary_color'] ?? '#064E3B';
        $data['accent_color'] = $data['accent_color'] ?? '#BEF264';
        $data['product_grid_columns'] = $data['product_grid_columns'] ?? 4;
        $data['font_family'] = $data['font_family'] ?? 'Plus Jakarta Sans';
        $data['button_style'] = $data['button_style'] ?? 'rounded';
        $data['show_search'] = $data['show_search'] ?? true;
        $data['show_cart'] = $data['show_cart'] ?? true;
        $data['show_wishlist'] = $data['show_wishlist'] ?? false;
        $data['status'] = $data['status'] ?? 'active';

        $storeId = $this->storeModel->create($data);

        if ($storeId) {
            $store = $this->storeModel->find($storeId);
            $this->success($store, 'Store created successfully', 201);
        } else {
            $this->error('Failed to create store', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/stores/{id}",
     *     tags={"Stores"},
     *     summary="Update store",
     *     description="Update store information and customization",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Store ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="store_name", type="string", minLength=2, maxLength=100, example="My Updated Store"),
     *             @OA\Property(property="store_slug", type="string", minLength=2, maxLength=100, example="my-updated-store", description="Lowercase letters, numbers, and hyphens only"),
     *             @OA\Property(property="template_id", type="integer", example=1),
     *             @OA\Property(property="primary_color", type="string", example="#064E3B"),
     *             @OA\Property(property="accent_color", type="string", example="#BEF264"),
     *             @OA\Property(property="product_grid_columns", type="integer", example=4),
     *             @OA\Property(property="font_family", type="string", example="Plus Jakarta Sans"),
     *             @OA\Property(property="button_style", type="string", example="rounded"),
     *             @OA\Property(property="show_search", type="boolean", example=true),
     *             @OA\Property(property="show_cart", type="boolean", example=true),
     *             @OA\Property(property="show_wishlist", type="boolean", example=false),
     *             @OA\Property(property="status", type="string", enum={"active", "inactive", "maintenance"}, example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Store updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Store updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Store")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Store not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=409, description="Store slug already exists", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Failed to update store", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function update(string $id): void
    {
        $storeId = (int)$id;

        if (!$this->storeModel->find($storeId)) {
            $this->error('Store not found', 404);
        }

        $data = $this->input();

        // Validation
        $errors = $this->validate($data, [
            'store_name' => 'min:2|max:100',
            'store_slug' => 'min:2|max:100'
        ]);

        if (!empty($errors)) {
            $this->error('Validation failed', 422, $errors);
        }

        // Check slug availability if being updated
        if (isset($data['store_slug'])) {
            if (!preg_match('/^[a-z0-9-]+$/', $data['store_slug'])) {
                $this->error('Store slug must contain only lowercase letters, numbers, and hyphens', 422);
            }

            if (!$this->storeModel->isSlugAvailable($data['store_slug'], $storeId)) {
                $this->error('Store slug already exists', 409);
            }
        }

        if ($this->storeModel->update($storeId, $data)) {
            $store = $this->storeModel->find($storeId);
            $this->success($store, 'Store updated successfully');
        } else {
            $this->error('Failed to update store', 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/stores/{id}",
     *     tags={"Stores"},
     *     summary="Delete store",
     *     description="Delete a store and all associated data (products, orders, etc.)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Store ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Store deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Store deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Store not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Failed to delete store", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function destroy(string $id): void
    {
        $storeId = (int)$id;

        if (!$this->storeModel->find($storeId)) {
            $this->error('Store not found', 404);
        }

        if ($this->storeModel->delete($storeId)) {
            $this->success(null, 'Store deleted successfully');
        } else {
            $this->error('Failed to delete store', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/stores/{id}/generate",
     *     tags={"Stores"},
     *     summary="Generate store files",
     *     description="Generate static HTML/CSS files for the store based on template and customization",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Store ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Store generated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Store generated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="store_url", type="string", example="https://example.com/stores/my-store"),
     *                 @OA\Property(property="files_generated", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Store not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Failed to generate store", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function generate(string $id): void
    {
        $storeId = (int)$id;
        $store = $this->storeModel->withCustomization($storeId);

        if (!$store) {
            $this->error('Store not found', 404);
        }

        try {
            $result = $this->generator->generate($store);
            $this->success($result, 'Store generated successfully');
        } catch (\Exception $e) {
            $this->error($e->getMessage(), 500);
        }
    }
}
