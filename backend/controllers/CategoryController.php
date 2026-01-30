<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Helpers\Response;
use App\Helpers\Validator;

/**
 * Category Controller
 * Handles category management operations
 */
class CategoryController extends Controller
{
    private Category $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
    }

    /**
     * @OA\Get(
     *     path="/api/categories",
     *     tags={"Categories"},
     *     summary="Get all categories",
     *     description="Retrieve list of categories for a specific store with filtering options",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="store_id",
     *         in="query",
     *         description="Store ID (required)",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"active", "inactive"})
     *     ),
     *     @OA\Parameter(
     *         name="parent_id",
     *         in="query",
     *         description="Filter by parent category ID (use 'null' for top-level categories)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by category name or description",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="tree",
     *         in="query",
     *         description="Get hierarchical tree structure",
     *         required=false,
     *         @OA\Schema(type="boolean", default=false)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Maximum number of categories to return",
     *         required=false,
     *         @OA\Schema(type="integer", default=100)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of categories",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="categories", type="array", @OA\Items(ref="#/components/schemas/Category"))
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
        try {
            $storeId = $this->query('store_id');

            $tree = $this->query('tree') === 'true' || $this->query('tree') === '1';

            if ($tree) {
                $status = $this->query('status');
                $categories = $this->categoryModel->getTree($storeId ? (int)$storeId : null, $status);
            } else {
                $filters = [
                    'status' => $this->query('status'),
                    'parent_id' => $this->query('parent_id'),
                    'search' => $this->query('search'),
                    'limit' => $this->query('limit', 100)
                ];

                $categories = $this->categoryModel->getByStore($storeId ? (int)$storeId : null, $filters);
            }

            Response::success([
                'categories' => $categories,
                'count' => count($categories)
            ]);
        } catch (\Exception $e) {
            \Sentry\captureException($e);
            Response::error('Failed to fetch categories: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{id}",
     *     tags={"Categories"},
     *     summary="Get category by ID",
     *     description="Retrieve a specific category with subcategories and product count",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Category ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category details",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Category")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Category not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Server error", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function show(int $id): void
    {
        try {
            $category = $this->categoryModel->withSubcategories($id);

            if (!$category) {
                Response::error('Category not found', 404);
            }

            Response::success($category);
        } catch (\Exception $e) {
            \Sentry\captureException($e);
            Response::error('Failed to fetch category: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/categories",
     *     tags={"Categories"},
     *     summary="Create a new category",
     *     description="Create a new category for a store (Admin only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"store_id", "name"},
     *             @OA\Property(property="store_id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Electronics"),
     *             @OA\Property(property="slug", type="string", example="electronics"),
     *             @OA\Property(property="description", type="string", example="Electronic devices and accessories"),
     *             @OA\Property(property="icon", type="string", example="devices"),
     *             @OA\Property(property="color", type="string", example="#064E3B"),
     *             @OA\Property(property="parent_id", type="integer", nullable=true, example=null),
     *             @OA\Property(property="display_order", type="integer", example=0),
     *             @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="category", ref="#/components/schemas/Category")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Validation error", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Server error", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function store(): void
    {
        try {
            $data = $this->input();

            // Validation
            $errors = $this->validate($data, [
                'store_id' => 'required|numeric',
                'name' => 'required|max:100',
                'slug' => 'max:100',
                'icon' => 'max:100',
                'color' => 'max:7'
            ]);

            if (!empty($errors)) {
                $this->error('Validation failed', 400, $errors);
            }

            // Auto-generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = $this->generateSlug($data['name']);
            }

            // Check if slug already exists for this store
            if ($this->categoryModel->slugExists((int)$data['store_id'], $data['slug'])) {
                Response::error('A category with this slug already exists for this store', 400);
            }

            // Verify parent category exists if parent_id is provided
            if (!empty($data['parent_id'])) {
                $parent = $this->categoryModel->find((int)$data['parent_id']);
                if (!$parent || $parent['store_id'] != $data['store_id']) {
                    Response::error('Invalid parent category', 400);
                }
            }

            $categoryId = $this->categoryModel->create($data);

            if ($categoryId) {
                $category = $this->categoryModel->find($categoryId);
                Response::success([
                    'id' => $categoryId,
                    'category' => $category
                ], 'Category created successfully', 201);
            } else {
                Response::error('Failed to create category', 500);
            }
        } catch (\Exception $e) {
            \Sentry\captureException($e);
            Response::error('Failed to create category: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/categories/{id}",
     *     tags={"Categories"},
     *     summary="Update a category",
     *     description="Update an existing category (Admin only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Category ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Electronics"),
     *             @OA\Property(property="slug", type="string", example="electronics"),
     *             @OA\Property(property="description", type="string", example="Electronic devices and accessories"),
     *             @OA\Property(property="icon", type="string", example="devices"),
     *             @OA\Property(property="color", type="string", example="#064E3B"),
     *             @OA\Property(property="parent_id", type="integer", nullable=true, example=null),
     *             @OA\Property(property="display_order", type="integer", example=0),
     *             @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Category")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Validation error", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=404, description="Category not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Server error", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function update(int $id): void
    {
        try {
            $category = $this->categoryModel->find($id);

            if (!$category) {
                Response::error('Category not found', 404);
            }

            $data = $this->input();

            // Validation
            $errors = $this->validate($data, [
                'name' => 'max:100',
                'slug' => 'max:100',
                'icon' => 'max:100',
                'color' => 'max:7'
            ]);

            if (!empty($errors)) {
                $this->error('Validation failed', 400, $errors);
            }

            // Check if slug already exists for this store (excluding current category)
            if (!empty($data['slug'])) {
                if ($this->categoryModel->slugExists((int)$category['store_id'], $data['slug'], $id)) {
                    Response::error('A category with this slug already exists for this store', 400);
                }
            }

            // Prevent category from being its own parent
            if (!empty($data['parent_id']) && $data['parent_id'] == $id) {
                Response::error('Category cannot be its own parent', 400);
            }

            // Verify parent category exists if parent_id is provided
            if (!empty($data['parent_id'])) {
                $parent = $this->categoryModel->find((int)$data['parent_id']);
                if (!$parent || $parent['store_id'] != $category['store_id']) {
                    Response::error('Invalid parent category', 400);
                }
            }

            $success = $this->categoryModel->update($id, $data);

            if ($success) {
                $updatedCategory = $this->categoryModel->find($id);
                Response::success($updatedCategory, 'Category updated successfully');
            } else {
                Response::error('Failed to update category', 500);
            }
        } catch (\Exception $e) {
            \Sentry\captureException($e);
            Response::error('Failed to update category: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/categories/{id}",
     *     tags={"Categories"},
     *     summary="Delete a category",
     *     description="Delete a category and unlink associated products (Admin only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Category ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Category not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Server error", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function destroy(int $id): void
    {
        try {
            $category = $this->categoryModel->find($id);

            if (!$category) {
                Response::error('Category not found', 404);
            }

            $success = $this->categoryModel->deleteWithProducts($id);

            if ($success) {
                Response::success(null, 'Category deleted successfully');
            } else {
                Response::error('Failed to delete category', 500);
            }
        } catch (\Exception $e) {
            \Sentry\captureException($e);
            Response::error('Failed to delete category: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/categories/slug/{slug}",
     *     tags={"Categories"},
     *     summary="Get category by slug",
     *     description="Retrieve a category by its slug for a specific store",
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Category slug",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="store_id",
     *         in="query",
     *         description="Store ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category details",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Category")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Category not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Server error", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function getBySlug(string $slug): void
    {
        try {
            $storeId = $this->query('store_id');

            if (!$storeId) {
                Response::error('Store ID is required', 400);
            }

            $category = $this->categoryModel->findBySlug((int)$storeId, $slug);

            if (!$category) {
                Response::error('Category not found', 404);
            }

            Response::success($category);
        } catch (\Exception $e) {
            \Sentry\captureException($e);
            Response::error('Failed to fetch category: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/categories/popular",
     *     tags={"Categories"},
     *     summary="Get popular categories",
     *     description="Get categories with the most products",
     *     @OA\Parameter(
     *         name="store_id",
     *         in="query",
     *         description="Store ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Maximum number of categories to return",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of popular categories",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="categories", type="array", @OA\Items(ref="#/components/schemas/Category"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=500, description="Server error", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function popular(): void
    {
        try {
            $storeId = $this->query('store_id');

            if (!$storeId) {
                Response::error('Store ID is required', 400);
            }

            $limit = $this->query('limit', 10);
            $categories = $this->categoryModel->getPopular((int)$storeId, (int)$limit);

            Response::success(['categories' => $categories]);
        } catch (\Exception $e) {
            \Sentry\captureException($e);
            Response::error('Failed to fetch popular categories: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Generate URL-friendly slug from name
     */
    private function generateSlug(string $name): string
    {
        $slug = strtolower($name);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');

        return $slug;
    }
}
