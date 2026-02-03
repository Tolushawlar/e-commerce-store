<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Order;
use App\Models\Product;

/**
 * Admin Order Controller
 * Handles order management for store owners/admins
 */
class AdminOrderController extends Controller
{
    private Order $orderModel;
    private Product $productModel;

    public function __construct()
    {
        $this->orderModel = new Order();
        $this->productModel = new Product();
    }

    /**
     * Get all orders for a store (admin view)
     * GET /api/stores/{store_id}/admin/orders
     * 
     * @OA\Get(
     *     path="/api/stores/{store_id}/admin/orders",
     *     tags={"Admin Orders"},
     *     summary="Get all orders for a store (admin/owner)",
     *     description="Retrieve paginated list of orders for a specific store with filtering and search capabilities. Requires admin or store owner authentication.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="store_id",
     *         in="path",
     *         description="Store ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by order status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"pending", "processing", "shipped", "delivered", "cancelled"})
     *     ),
     *     @OA\Parameter(
     *         name="payment_status",
     *         in="query",
     *         description="Filter by payment status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"pending", "paid", "failed", "refunded"})
     *     ),
     *     @OA\Parameter(
     *         name="from_date",
     *         in="query",
     *         description="Filter orders from date (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="to_date",
     *         in="query",
     *         description="Filter orders to date (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by customer email, name, or order ID",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
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
     *     @OA\Response(
     *         response=200,
     *         description="Orders retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="orders", type="array", @OA\Items(type="object")),
     *                 @OA\Property(
     *                     property="pagination",
     *                     type="object",
     *                     @OA\Property(property="total", type="integer", example=150),
     *                     @OA\Property(property="page", type="integer", example=1),
     *                     @OA\Property(property="limit", type="integer", example=20),
     *                     @OA\Property(property="pages", type="integer", example=8)
     *                 ),
     *                 @OA\Property(
     *                     property="stats",
     *                     type="object",
     *                     @OA\Property(property="total_orders", type="integer", example=150),
     *                     @OA\Property(property="total_revenue", type="number", format="float", example=45000.50)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=403, description="Access denied to this store", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function index(int $storeId): void
    {
        $userPayload = $_REQUEST['auth_user'] ?? null;

        if (!$userPayload) {
            $this->error('Unauthorized', 401);
        }

        // Verify user has access to this store
        if (!$this->verifyStoreAccess($userPayload['user_id'], $storeId)) {
            $this->error('Access denied to this store', 403);
        }

        // Get filters from query parameters
        $filters = [];

        if (isset($_GET['status'])) {
            $filters['status'] = $_GET['status'];
        }

        if (isset($_GET['payment_status'])) {
            $filters['payment_status'] = $_GET['payment_status'];
        }

        if (isset($_GET['from_date'])) {
            $filters['from_date'] = $_GET['from_date'];
        }

        if (isset($_GET['to_date'])) {
            $filters['to_date'] = $_GET['to_date'];
        }

        if (isset($_GET['search'])) {
            $filters['search'] = $_GET['search'];
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        $filters['limit'] = $limit;
        $filters['offset'] = ($page - 1) * $limit;

        // Get orders
        $orders = $this->orderModel->getByStore($storeId, $filters);
        $total = $this->orderModel->countByStore($storeId, $filters);

        // Get basic stats
        $stats = $this->orderModel->getStoreStats($storeId);

        $this->success([
            'orders' => $orders,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'pages' => ceil($total / $limit)
            ],
            'stats' => $stats
        ]);
    }

    /**
     * Get single order details (admin view)
     * GET /api/stores/{store_id}/admin/orders/{order_id}
     * 
     * @OA\Get(
     *     path="/api/stores/{store_id}/admin/orders/{order_id}",
     *     tags={"Admin Orders"},
     *     summary="Get order details (admin/owner)",
     *     description="Retrieve complete details of a specific order including items and addresses",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="store_id",
     *         in="path",
     *         description="Store ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="order_id",
     *         in="path",
     *         description="Order ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=123),
     *                 @OA\Property(property="store_id", type="integer", example=1),
     *                 @OA\Property(property="customer_name", type="string", example="John Doe"),
     *                 @OA\Property(property="customer_email", type="string", example="john@example.com"),
     *                 @OA\Property(property="total_amount", type="number", format="float", example=25000.00),
     *                 @OA\Property(property="status", type="string", example="processing"),
     *                 @OA\Property(property="payment_status", type="string", example="paid"),
     *                 @OA\Property(property="items", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="shipping_address", type="object"),
     *                 @OA\Property(property="billing_address", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=403, description="Access denied", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=404, description="Order not found", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function show(int $storeId, int $orderId): void
    {
        $userPayload = $_REQUEST['auth_user'] ?? null;

        if (!$userPayload) {
            $this->error('Unauthorized', 401);
        }

        if (!$this->verifyStoreAccess($userPayload['user_id'], $storeId)) {
            $this->error('Access denied to this store', 403);
        }

        $order = $this->orderModel->getFullDetails($orderId);

        if (!$order) {
            $this->error('Order not found', 404);
        }

        if ($order['store_id'] != $storeId) {
            $this->error('Order does not belong to this store', 403);
        }

        $this->success($order);
    }

    /**
     * Update order status
     * PUT /api/stores/{store_id}/admin/orders/{order_id}/status
     * 
     * @OA\Put(
     *     path="/api/stores/{store_id}/admin/orders/{order_id}/status",
     *     tags={"Admin Orders"},
     *     summary="Update order status (admin/owner)",
     *     description="Update the status of an order. Cannot update delivered or cancelled orders. Cancelling an order automatically restores product stock.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="store_id",
     *         in="path",
     *         description="Store ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="order_id",
     *         in="path",
     *         description="Order ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"pending", "processing", "shipped", "delivered", "cancelled"}, example="processing")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order status updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Order status updated successfully"),
     *             @OA\Property(property="data", type="object", @OA\Property(property="order", type="object"))
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid status or cannot update this order", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=403, description="Access denied", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=404, description="Order not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Failed to update order status", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function updateStatus(int $storeId, int $orderId): void
    {
        $userPayload = $_REQUEST['auth_user'] ?? null;

        if (!$userPayload) {
            $this->error('Unauthorized', 401);
        }

        if (!$this->verifyStoreAccess($userPayload['user_id'], $storeId)) {
            $this->error('Access denied to this store', 403);
        }

        // Get order
        $order = $this->orderModel->find($orderId);

        if (!$order) {
            $this->error('Order not found', 404);
        }

        if ($order['store_id'] != $storeId) {
            $this->error('Order does not belong to this store', 403);
        }

        // Get input
        $input = $this->input();

        if (!isset($input['status'])) {
            $this->error('Status is required', 400);
        }

        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        if (!in_array($input['status'], $validStatuses)) {
            $this->error('Invalid status. Must be one of: ' . implode(', ', $validStatuses), 400);
        }

        // Prevent status updates if order is already delivered or cancelled
        if (in_array($order['status'], ['delivered', 'cancelled'])) {
            $this->error('Cannot update status of ' . $order['status'] . ' orders', 400);
        }

        // If cancelling, restore stock
        if ($input['status'] === 'cancelled' && $order['status'] !== 'cancelled') {
            $this->restoreOrderStock($orderId);
        }

        $updated = $this->orderModel->updateStatus($orderId, $input['status']);

        if (!$updated) {
            $this->error('Failed to update order status', 500);
        }

        $this->success([
            'message' => 'Order status updated successfully',
            'order' => $this->orderModel->find($orderId)
        ]);
    }

    /**
     * Update payment status
     * PUT /api/stores/{store_id}/admin/orders/{order_id}/payment-status
     * 
     * @OA\Put(
     *     path="/api/stores/{store_id}/admin/orders/{order_id}/payment-status",
     *     tags={"Admin Orders"},
     *     summary="Update payment status (admin/owner)",
     *     description="Update the payment status of an order",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="store_id",
     *         in="path",
     *         description="Store ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="order_id",
     *         in="path",
     *         description="Order ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"payment_status"},
     *             @OA\Property(property="payment_status", type="string", enum={"pending", "paid", "failed", "refunded"}, example="paid")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment status updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Payment status updated successfully"),
     *             @OA\Property(property="data", type="object", @OA\Property(property="order", type="object"))
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid payment status", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=403, description="Access denied", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=404, description="Order not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Failed to update payment status", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function updatePaymentStatus(int $storeId, int $orderId): void
    {
        $userPayload = $_REQUEST['auth_user'] ?? null;

        if (!$userPayload) {
            $this->error('Unauthorized', 401);
        }

        if (!$this->verifyStoreAccess($userPayload['user_id'], $storeId)) {
            $this->error('Access denied to this store', 403);
        }

        // Get order
        $order = $this->orderModel->find($orderId);

        if (!$order) {
            $this->error('Order not found', 404);
        }

        if ($order['store_id'] != $storeId) {
            $this->error('Order does not belong to this store', 403);
        }

        // Get input
        $input = $this->input();

        if (!isset($input['payment_status'])) {
            $this->error('Payment status is required', 400);
        }

        $validPaymentStatuses = ['pending', 'paid', 'failed', 'refunded'];

        if (!in_array($input['payment_status'], $validPaymentStatuses)) {
            $this->error('Invalid payment status. Must be one of: ' . implode(', ', $validPaymentStatuses), 400);
        }

        $updated = $this->orderModel->updatePaymentStatus($orderId, $input['payment_status']);

        if (!$updated) {
            $this->error('Failed to update payment status', 500);
        }

        $this->success([
            'message' => 'Payment status updated successfully',
            'order' => $this->orderModel->find($orderId)
        ]);
    }

    /**
     * Add tracking number
     * PUT /api/stores/{store_id}/admin/orders/{order_id}/tracking
     * 
     * @OA\Put(
     *     path="/api/stores/{store_id}/admin/orders/{order_id}/tracking",
     *     tags={"Admin Orders"},
     *     summary="Add tracking number to order (admin/owner)",
     *     description="Add or update tracking number for an order. Automatically updates order status to 'shipped' if currently 'processing'.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="store_id",
     *         in="path",
     *         description="Store ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="order_id",
     *         in="path",
     *         description="Order ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"tracking_number"},
     *             @OA\Property(property="tracking_number", type="string", example="TRK123456789")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tracking number added successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Tracking number added successfully"),
     *             @OA\Property(property="data", type="object", @OA\Property(property="order", type="object"))
     *         )
     *     ),
     *     @OA\Response(response=400, description="Tracking number is required", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=403, description="Access denied", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=404, description="Order not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Failed to add tracking number", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function addTracking(int $storeId, int $orderId): void
    {
        $userPayload = $_REQUEST['auth_user'] ?? null;

        if (!$userPayload) {
            $this->error('Unauthorized', 401);
        }

        if (!$this->verifyStoreAccess($userPayload['user_id'], $storeId)) {
            $this->error('Access denied to this store', 403);
        }

        // Get order
        $order = $this->orderModel->find($orderId);

        if (!$order) {
            $this->error('Order not found', 404);
        }

        if ($order['store_id'] != $storeId) {
            $this->error('Order does not belong to this store', 403);
        }

        // Get input
        $input = $this->input();

        if (!isset($input['tracking_number']) || empty(trim($input['tracking_number']))) {
            $this->error('Tracking number is required', 400);
        }

        $updated = $this->orderModel->update($orderId, [
            'tracking_number' => trim($input['tracking_number'])
        ]);

        if (!$updated) {
            $this->error('Failed to add tracking number', 500);
        }

        // Auto-update status to shipped if still processing
        if ($order['status'] === 'processing') {
            $this->orderModel->updateStatus($orderId, 'shipped');
        }

        $this->success([
            'message' => 'Tracking number added successfully',
            'order' => $this->orderModel->find($orderId)
        ]);
    }

    /**
     * Get order statistics
     * GET /api/stores/{store_id}/admin/orders/stats
     * 
     * @OA\Get(
     *     path="/api/stores/{store_id}/admin/orders/stats",
     *     tags={"Admin Orders"},
     *     summary="Get order statistics (admin/owner)",
     *     description="Retrieve comprehensive order statistics including overview, recent orders, and daily stats for a date range",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="store_id",
     *         in="path",
     *         description="Store ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="from_date",
     *         in="query",
     *         description="Start date for stats (YYYY-MM-DD). Defaults to 30 days ago.",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="to_date",
     *         in="query",
     *         description="End date for stats (YYYY-MM-DD). Defaults to today.",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Statistics retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="overview",
     *                     type="object",
     *                     @OA\Property(property="total_orders", type="integer", example=150),
     *                     @OA\Property(property="total_revenue", type="number", format="float", example=450000.00),
     *                     @OA\Property(property="pending_orders", type="integer", example=10),
     *                     @OA\Property(property="completed_orders", type="integer", example=120)
     *                 ),
     *                 @OA\Property(property="recent_orders", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="daily_stats", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=403, description="Access denied", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function stats(int $storeId): void
    {
        $userPayload = $_REQUEST['auth_user'] ?? null;

        if (!$userPayload) {
            $this->error('Unauthorized', 401);
        }

        if (!$this->verifyStoreAccess($userPayload['user_id'], $storeId)) {
            $this->error('Access denied to this store', 403);
        }

        // Get date range from query parameters
        $fromDate = $_GET['from_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $toDate = $_GET['to_date'] ?? date('Y-m-d');

        $stats = $this->orderModel->getStoreStats($storeId);
        $recentOrders = $this->orderModel->getByStore($storeId, ['limit' => 10]);
        $dailyStats = $this->orderModel->getDailyStats($storeId, $fromDate, $toDate);

        $this->success([
            'overview' => $stats,
            'recent_orders' => $recentOrders,
            'daily_stats' => $dailyStats
        ]);
    }

    /**
     * Bulk update order statuses
     * POST /api/stores/{store_id}/admin/orders/bulk-update
     * 
     * @OA\Post(
     *     path="/api/stores/{store_id}/admin/orders/bulk-update",
     *     tags={"Admin Orders"},
     *     summary="Bulk update multiple orders (admin/owner)",
     *     description="Update the status of multiple orders at once. Delivered and cancelled orders will be skipped. Cancelling orders automatically restores product stock.",
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
     *             required={"order_ids", "status"},
     *             @OA\Property(
     *                 property="order_ids",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={1, 2, 3, 4}
     *             ),
     *             @OA\Property(property="status", type="string", enum={"pending", "processing", "shipped", "delivered", "cancelled"}, example="processing")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bulk update completed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Updated 3 orders"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="updated", type="integer", example=3),
     *                 @OA\Property(property="failed", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid input or status", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=403, description="Access denied", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function bulkUpdate(int $storeId): void
    {
        $userPayload = $_REQUEST['auth_user'] ?? null;

        if (!$userPayload) {
            $this->error('Unauthorized', 401);
        }

        if (!$this->verifyStoreAccess($userPayload['user_id'], $storeId)) {
            $this->error('Access denied to this store', 403);
        }

        $input = $this->input();

        if (!isset($input['order_ids']) || !is_array($input['order_ids'])) {
            $this->error('order_ids array is required', 400);
        }

        if (!isset($input['status'])) {
            $this->error('Status is required', 400);
        }

        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        if (!in_array($input['status'], $validStatuses)) {
            $this->error('Invalid status', 400);
        }

        $updated = 0;
        $failed = 0;

        foreach ($input['order_ids'] as $orderId) {
            $order = $this->orderModel->find($orderId);

            // Verify order belongs to this store
            if ($order && $order['store_id'] == $storeId) {
                // Don't update delivered or cancelled orders
                if (!in_array($order['status'], ['delivered', 'cancelled'])) {
                    if ($this->orderModel->updateStatus($orderId, $input['status'])) {
                        $updated++;

                        // If cancelling, restore stock
                        if ($input['status'] === 'cancelled') {
                            $this->restoreOrderStock($orderId);
                        }
                    } else {
                        $failed++;
                    }
                } else {
                    $failed++;
                }
            } else {
                $failed++;
            }
        }

        $this->success([
            'message' => "Updated {$updated} orders",
            'updated' => $updated,
            'failed' => $failed
        ]);
    }

    /**
     * Verify user has access to store
     */
    private function verifyStoreAccess(int $userId, int $storeId): bool
    {
        return $this->orderModel->hasUserAccessToStore($userId, $storeId);
    }

    /**
     * Restore stock when order is cancelled
     */
    private function restoreOrderStock(int $orderId): void
    {
        $items = $this->orderModel->getOrderItems($orderId);
        $this->productModel->restoreStockForItems($items);
    }
}
