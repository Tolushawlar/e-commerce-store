<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ShoppingCart;
use App\Models\Product;
use App\Services\CustomerJWTService;

/**
 * Cart Controller
 * Handles shopping cart operations for store customers
 */
class CartController extends Controller
{
    private ShoppingCart $cartModel;
    private Product $productModel;

    public function __construct()
    {
        $this->cartModel = new ShoppingCart();
        $this->productModel = new Product();
    }

    /**
     * Get cart items for customer
     * GET /api/stores/{store_id}/cart
     * 
     * @OA\Get(
     *     path="/api/stores/{store_id}/cart",
     *     tags={"Shopping Cart"},
     *     summary="Get shopping cart",
     *     description="Get customer's shopping cart with items, totals, and validation issues. Returns empty cart for unauthenticated users.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="store_id",
     *         in="path",
     *         description="Store ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="items", type="array", @OA\Items(type="object")),
     *                 @OA\Property(
     *                     property="totals",
     *                     type="object",
     *                     @OA\Property(property="item_count", type="integer", example=3),
     *                     @OA\Property(property="total_items", type="integer", example=5),
     *                     @OA\Property(property="total_amount", type="number", format="float", example=45000.00)
     *                 ),
     *                 @OA\Property(property="issues", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Invalid store", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function index(int $storeId): void
    {
        $customerPayload = CustomerJWTService::getCustomerFromRequest();

        if (!$customerPayload) {
            // Return empty cart for non-authenticated users
            $this->success([
                'items' => [],
                'totals' => [
                    'item_count' => 0,
                    'total_items' => 0,
                    'total_amount' => 0
                ]
            ]);
            return;
        }

        // Verify store matches
        if ($customerPayload['store_id'] != $storeId) {
            $this->error('Invalid store', 403);
        }

        $customerId = $customerPayload['customer_id'];

        // Get cart items with product details
        $items = $this->cartModel->getCartItems($customerId);

        // Get totals
        $totals = $this->cartModel->getCartTotal($customerId);

        // Validate cart (check stock availability)
        $issues = $this->cartModel->validateCart($customerId);

        $this->success([
            'items' => $items,
            'totals' => $totals,
            'issues' => $issues
        ]);
    }

    /**
     * Add item to cart
     * POST /api/stores/{store_id}/cart
     * 
     * @OA\Post(
     *     path="/api/stores/{store_id}/cart",
     *     tags={"Shopping Cart"},
     *     summary="Add item to cart",
     *     description="Add a product to the shopping cart. Requires authentication. Validates stock availability.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="store_id",
     *         in="path",
     *         description="Store ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id", "quantity"},
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="quantity", type="integer", minimum=1, example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item added to cart",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Item added to cart"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="items", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="totals", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Product not available or insufficient stock", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Please login to add items", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=403, description="Invalid store", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=404, description="Product not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function addItem(int $storeId): void
    {
        $customerPayload = CustomerJWTService::getCustomerFromRequest();

        if (!$customerPayload) {
            $this->error('Please login or register to add items to cart', 401);
        }

        if ($customerPayload['store_id'] != $storeId) {
            $this->error('Invalid store', 403);
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $errors = $this->validate($data, [
            'product_id' => 'required',
            'quantity' => 'required'
        ]);

        if (!empty($errors)) {
            $this->error('Validation failed', 422, $errors);
        }

        $productId = (int)$data['product_id'];
        $quantity = (int)$data['quantity'];

        if ($quantity < 1) {
            $this->error('Quantity must be at least 1', 400);
        }

        // Verify product exists and belongs to this store
        $product = $this->productModel->find($productId);

        if (!$product) {
            $this->error('Product not found', 404);
        }

        if ($product['store_id'] != $storeId) {
            $this->error('Product does not belong to this store', 400);
        }

        if ($product['status'] !== 'active') {
            $this->error('Product is not available', 400);
        }

        // Check stock
        if ($product['stock_quantity'] < $quantity) {
            $this->error("Only {$product['stock_quantity']} items available", 400);
        }

        // Add to cart
        $success = $this->cartModel->addItem(
            $customerPayload['customer_id'],
            $productId,
            $quantity
        );

        if (!$success) {
            $this->error('Failed to add item to cart', 500);
        }

        // Get updated cart
        $items = $this->cartModel->getCartItems($customerPayload['customer_id']);
        $totals = $this->cartModel->getCartTotal($customerPayload['customer_id']);

        $this->success([
            'items' => $items,
            'totals' => $totals
        ], 'Item added to cart');
    }

    /**
     * Update cart item quantity
     * PUT /api/stores/{store_id}/cart/{item_id}
     * 
     * @OA\Put(
     *     path="/api/stores/{store_id}/cart/{item_id}",
     *     tags={"Shopping Cart"},
     *     summary="Update cart item quantity",
     *     description="Update quantity of a cart item. Set quantity to 0 to remove item.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="store_id",
     *         in="path",
     *         description="Store ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="item_id",
     *         in="path",
     *         description="Cart item ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"quantity"},
     *             @OA\Property(property="quantity", type="integer", minimum=0, example=3)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Cart updated"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid quantity or insufficient stock", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=404, description="Cart item not found", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function updateQuantity(int $storeId, int $itemId): void
    {
        $customerPayload = CustomerJWTService::getCustomerFromRequest();

        if (!$customerPayload) {
            $this->error('Unauthorized', 401);
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['quantity'])) {
            $this->error('Quantity is required', 400);
        }

        $quantity = (int)$data['quantity'];

        if ($quantity < 0) {
            $this->error('Invalid quantity', 400);
        }

        // Get cart item
        $item = $this->cartModel->find($itemId);

        if (!$item || $item['customer_id'] != $customerPayload['customer_id']) {
            $this->error('Cart item not found', 404);
        }

        // If quantity is 0, remove item
        if ($quantity === 0) {
            $success = $this->cartModel->delete($itemId);
            $message = 'Item removed from cart';
        } else {
            // Check product stock
            $product = $this->productModel->find($item['product_id']);

            if ($product['stock_quantity'] < $quantity) {
                $this->error("Only {$product['stock_quantity']} items available", 400);
            }

            $success = $this->cartModel->updateQuantity($itemId, $quantity);
            $message = 'Cart updated';
        }

        if (!$success) {
            $this->error('Failed to update cart', 500);
        }

        // Get updated cart
        $items = $this->cartModel->getCartItems($customerPayload['customer_id']);
        $totals = $this->cartModel->getCartTotal($customerPayload['customer_id']);

        $this->success([
            'items' => $items,
            'totals' => $totals
        ], $message);
    }

    /**
     * Remove item from cart
     * DELETE /api/stores/{store_id}/cart/{item_id}
     * 
     * @OA\Delete(
     *     path="/api/stores/{store_id}/cart/{item_id}",
     *     tags={"Shopping Cart"},
     *     summary="Remove item from cart",
     *     description="Remove a specific item from the shopping cart",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="store_id",
     *         in="path",
     *         description="Store ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="item_id",
     *         in="path",
     *         description="Cart item ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item removed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Item removed from cart"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=404, description="Cart item not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Failed to remove item", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function removeItem(int $storeId, int $itemId): void
    {
        $customerPayload = CustomerJWTService::getCustomerFromRequest();

        if (!$customerPayload) {
            $this->error('Unauthorized', 401);
        }

        // Get cart item
        $item = $this->cartModel->find($itemId);

        if (!$item || $item['customer_id'] != $customerPayload['customer_id']) {
            $this->error('Cart item not found', 404);
        }

        $success = $this->cartModel->delete($itemId);

        if (!$success) {
            $this->error('Failed to remove item', 500);
        }

        // Get updated cart
        $items = $this->cartModel->getCartItems($customerPayload['customer_id']);
        $totals = $this->cartModel->getCartTotal($customerPayload['customer_id']);

        $this->success([
            'items' => $items,
            'totals' => $totals
        ], 'Item removed from cart');
    }

    /**
     * Clear entire cart
     * DELETE /api/stores/{store_id}/cart
     * 
     * @OA\Delete(
     *     path="/api/stores/{store_id}/cart",
     *     tags={"Shopping Cart"},
     *     summary="Clear entire cart",
     *     description="Remove all items from the shopping cart",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="store_id",
     *         in="path",
     *         description="Store ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart cleared successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Cart cleared"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="items", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="totals", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Failed to clear cart", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function clearCart(int $storeId): void
    {
        $customerPayload = CustomerJWTService::getCustomerFromRequest();

        if (!$customerPayload) {
            $this->error('Unauthorized', 401);
        }

        $success = $this->cartModel->clearCart($customerPayload['customer_id']);

        if (!$success) {
            $this->error('Failed to clear cart', 500);
        }

        $this->success([
            'items' => [],
            'totals' => [
                'item_count' => 0,
                'total_items' => 0,
                'total_amount' => 0
            ]
        ], 'Cart cleared');
    }

    /**
     * Sync session cart with database (when user logs in)
     * POST /api/stores/{store_id}/cart/sync
     * 
     * @OA\Post(
     *     path="/api/stores/{store_id}/cart/sync",
     *     tags={"Shopping Cart"},
     *     summary="Sync session cart with database",
     *     description="Merge session cart items with database cart when user logs in",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="store_id",
     *         in="path",
     *         description="Store ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"items"},
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="product_id", type="integer", example=1),
     *                     @OA\Property(property="quantity", type="integer", example=2)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart synced successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Cart synced successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid cart data", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Failed to sync cart", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function syncCart(int $storeId): void
    {
        $customerPayload = CustomerJWTService::getCustomerFromRequest();

        if (!$customerPayload) {
            $this->error('Unauthorized', 401);
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['items']) || !is_array($data['items'])) {
            $this->error('Invalid cart data', 400);
        }

        $success = $this->cartModel->syncWithSession(
            $customerPayload['customer_id'],
            $data['items']
        );

        if (!$success) {
            $this->error('Failed to sync cart', 500);
        }

        // Get updated cart
        $items = $this->cartModel->getCartItems($customerPayload['customer_id']);
        $totals = $this->cartModel->getCartTotal($customerPayload['customer_id']);

        $this->success([
            'items' => $items,
            'totals' => $totals
        ], 'Cart synced successfully');
    }
}
