<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Order;

/**
 * Order Controller
 */
class OrderController extends Controller
{
    private Order $orderModel;

    public function __construct()
    {
        $this->orderModel = new Order();
    }

    /**
     * @OA\Get(
     *     path="/api/orders",
     *     tags={"Orders"},
     *     summary="Get all orders",
     *     description="Retrieve list of orders for a specific store with filtering options",
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
     *         description="Filter by order status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"pending", "processing", "shipped", "delivered", "cancelled"})
     *     ),
     *     @OA\Parameter(
     *         name="from_date",
     *         in="query",
     *         description="Filter orders from this date (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="to_date",
     *         in="query",
     *         description="Filter orders until this date (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Maximum number of orders to return",
     *         required=false,
     *         @OA\Schema(type="integer", default=50)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of orders",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="orders", type="array", @OA\Items(ref="#/components/schemas/Order"))
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
            'status' => $this->query('status'),
            'from_date' => $this->query('from_date'),
            'to_date' => $this->query('to_date'),
            'limit' => $limit
        ];

        $orders = $this->orderModel->getByStore((int)$storeId, $filters);
        $total = $this->orderModel->countByStore((int)$storeId, $filters);

        $this->success([
            'orders' => $orders,
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
     *     path="/api/orders/{id}",
     *     tags={"Orders"},
     *     summary="Get single order",
     *     description="Retrieve detailed order information including order items",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Order ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order details",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Order")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Order not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Server error", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function show(string $id): void
    {
        $order = $this->orderModel->withItems((int)$id);

        if (!$order) {
            $this->error('Order not found', 404);
        }

        $this->success($order);
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     tags={"Orders"},
     *     summary="Create new order",
     *     description="Create a new order for a store",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"store_id", "customer_name", "customer_email", "total_amount"},
     *             @OA\Property(property="store_id", type="integer", example=1),
     *             @OA\Property(property="customer_name", type="string", minLength=2, maxLength=100, example="John Doe"),
     *             @OA\Property(property="customer_email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="customer_phone", type="string", nullable=true, example="+1234567890"),
     *             @OA\Property(property="shipping_address", type="string", nullable=true, example="123 Main St, City, Country"),
     *             @OA\Property(property="total_amount", type="number", format="float", example=199.99),
     *             @OA\Property(property="payment_method", type="string", nullable=true, example="credit_card"),
     *             @OA\Property(property="notes", type="string", nullable=true, example="Please deliver before 5 PM"),
     *             @OA\Property(property="status", type="string", enum={"pending", "processing", "shipped", "delivered", "cancelled"}, example="pending", default="pending")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Order created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Order")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Failed to create order", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function store(): void
    {
        $data = $this->input();

        // Validation
        $errors = $this->validate($data, [
            'store_id' => 'required|numeric',
            'customer_name' => 'required|min:2|max:100',
            'customer_email' => 'required|email',
            'total_amount' => 'required|numeric'
        ]);

        if (!empty($errors)) {
            $this->error('Validation failed', 422, $errors);
        }

        $data['status'] = $data['status'] ?? 'pending';

        $orderId = $this->orderModel->create($data);

        if ($orderId) {
            $order = $this->orderModel->find($orderId);
            $this->success($order, 'Order created successfully', 201);
        } else {
            $this->error('Failed to create order', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/orders/{id}/status",
     *     tags={"Orders"},
     *     summary="Update order status",
     *     description="Update the status of an existing order",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
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
     *             @OA\Property(property="data", ref="#/components/schemas/Order")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Order not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=400, description="Invalid status or status is required", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Server error", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function updateStatus(string $id): void
    {
        $orderId = (int)$id;

        if (!$this->orderModel->find($orderId)) {
            $this->error('Order not found', 404);
        }

        $status = $this->input('status');

        if (!$status) {
            $this->error('Status is required', 400);
        }

        if ($this->orderModel->updateStatus($orderId, $status)) {
            $order = $this->orderModel->find($orderId);
            $this->success($order, 'Order status updated successfully');
        } else {
            $this->error('Invalid status or failed to update', 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/orders/stats",
     *     tags={"Orders"},
     *     summary="Get store statistics",
     *     description="Retrieve order statistics and analytics for a specific store",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="store_id",
     *         in="query",
     *         description="Store ID (required)",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Store statistics",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="total_orders", type="integer", example=150),
     *                 @OA\Property(property="total_revenue", type="number", format="float", example=15999.99),
     *                 @OA\Property(property="pending_orders", type="integer", example=10),
     *                 @OA\Property(property="processing_orders", type="integer", example=5),
     *                 @OA\Property(property="shipped_orders", type="integer", example=8),
     *                 @OA\Property(property="delivered_orders", type="integer", example=120),
     *                 @OA\Property(property="cancelled_orders", type="integer", example=7),
     *                 @OA\Property(property="average_order_value", type="number", format="float", example=106.67)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Store ID is required", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Server error", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function stats(): void
    {
        $storeId = $this->query('store_id');

        if (!$storeId) {
            $this->error('Store ID is required', 400);
        }

        $stats = $this->orderModel->getStoreStats((int)$storeId);

        $this->success($stats);
    }
}
