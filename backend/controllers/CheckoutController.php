<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShoppingCart;
use App\Models\StoreCustomer;
use App\Models\CustomerAddress;
use App\Models\Store;
use App\Services\CustomerJWTService;
use App\Services\NotificationService;

/**
 * Checkout Controller
 * Handles checkout process and order creation from cart
 */
class CheckoutController extends Controller
{
    private Order $orderModel;
    private Product $productModel;
    private ShoppingCart $cartModel;
    private StoreCustomer $customerModel;
    private CustomerAddress $addressModel;
    private Store $storeModel;
    private NotificationService $notificationService;

    public function __construct()
    {
        $this->orderModel = new Order();
        $this->productModel = new Product();
        $this->cartModel = new ShoppingCart();
        $this->customerModel = new StoreCustomer();
        $this->addressModel = new CustomerAddress();
        $this->storeModel = new Store();
        $this->notificationService = new NotificationService();
    }

    /**
     * Get customer orders
     * GET /api/stores/{store_id}/orders
     * 
     * @OA\Get(
     *     path="/api/stores/{store_id}/orders",
     *     tags={"Customer Orders"},
     *     summary="Get customer orders",
     *     description="Retrieve all orders for the authenticated customer",
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
     *         description="Orders retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="total_amount", type="number", format="float", example=25000.00),
     *                     @OA\Property(property="status", type="string", example="processing"),
     *                     @OA\Property(property="payment_status", type="string", example="paid"),
     *                     @OA\Property(property="created_at", type="string", format="datetime")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=403, description="Invalid store", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function index(int $storeId): void
    {
        $customerPayload = CustomerJWTService::getCustomerFromRequest();

        if (!$customerPayload) {
            $this->error('Unauthorized', 401);
        }

        if ($customerPayload['store_id'] != $storeId) {
            $this->error('Invalid store', 403);
        }

        $orders = $this->orderModel->getByCustomer($customerPayload['id']);

        $this->success($orders);
    }

    /**
     * Get single order details
     * GET /api/stores/{store_id}/orders/{id}
     * 
     * @OA\Get(
     *     path="/api/stores/{store_id}/orders/{id}",
     *     tags={"Customer Orders"},
     *     summary="Get order details",
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
     *         name="id",
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
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="total_amount", type="number", format="float", example=25000.00),
     *                 @OA\Property(property="status", type="string", example="processing"),
     *                 @OA\Property(property="items", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="shipping_address", type="object"),
     *                 @OA\Property(property="billing_address", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Access denied or invalid store", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=404, description="Order not found", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function show(int $storeId, int $id): void
    {
        $customerPayload = CustomerJWTService::getCustomerFromRequest();

        // Allow access with token or allow guest with order ID (for order tracking)
        $order = $this->orderModel->getFullDetails($id);

        if (!$order) {
            $this->error('Order not found', 404);
        }

        // Verify store matches
        if ($order['store_id'] != $storeId) {
            $this->error('Invalid store', 403);
        }

        // If authenticated, verify ownership
        if ($customerPayload && $order['customer_id'] != $customerPayload['id']) {
            $this->error('Access denied', 403);
        }

        $this->success($order);
    }

    /**
     * Create order from cart (Checkout)
     * POST /api/stores/{store_id}/checkout
     * 
     * @OA\Post(
     *     path="/api/stores/{store_id}/checkout",
     *     tags={"Checkout"},
     *     summary="Checkout and create order",
     *     description="Create an order from cart (registered customers) or from provided items (guest checkout). Supports both registered and guest customers.",
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
     *             required={"customer_email", "customer_name", "customer_phone", "payment_method"},
     *             @OA\Property(property="customer_email", type="string", format="email", example="customer@example.com"),
     *             @OA\Property(property="customer_name", type="string", example="John Doe"),
     *             @OA\Property(property="customer_phone", type="string", example="+2348012345678"),
     *             @OA\Property(property="payment_method", type="string", enum={"card", "bank_transfer", "cash", "paystack"}, example="paystack"),
     *             @OA\Property(property="shipping_cost", type="number", format="float", example=2000.00),
     *             @OA\Property(property="notes", type="string", example="Please deliver between 9am-5pm"),
     *             @OA\Property(property="shipping_address_id", type="integer", example=1, description="Use existing address (registered customers)"),
     *             @OA\Property(
     *                 property="shipping_address",
     *                 type="object",
     *                 description="New shipping address (if shipping_address_id not provided)",
     *                 @OA\Property(property="address_line1", type="string", example="123 Main Street"),
     *                 @OA\Property(property="city", type="string", example="Lagos"),
     *                 @OA\Property(property="state", type="string", example="Lagos State"),
     *                 @OA\Property(property="postal_code", type="string", example="100001")
     *             ),
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 description="Required for guest checkout",
     *                 @OA\Items(
     *                     @OA\Property(property="product_id", type="integer", example=1),
     *                     @OA\Property(property="quantity", type="integer", example=2)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order placed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Order placed successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Cart empty, insufficient stock, or invalid data", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=403, description="Invalid store", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=404, description="Product not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Failed to create order", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function checkout(int $storeId): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        // Get customer (can be guest or registered)
        $customerPayload = CustomerJWTService::getCustomerFromRequest();
        $customerId = null;
        $isGuest = true;

        if ($customerPayload) {
            // Registered customer
            if ($customerPayload['store_id'] != $storeId) {
                $this->error('Invalid store', 403);
            }
            $customerId = $customerPayload['id'];
            $isGuest = $customerPayload['is_guest'] ?? false;
        }

        // Validate required fields
        $errors = $this->validate($data, [
            'customer_email' => 'required|email',
            'customer_name' => 'required',
            'customer_phone' => 'required',
            'payment_method' => 'required'
        ]);

        if (!empty($errors)) {
            $this->error('Validation failed', 422, $errors);
        }

        // If registered customer with cart
        if ($customerId && !$isGuest) {
            $this->checkoutFromCart($storeId, $customerId, $data);
            return;
        }

        // Guest checkout or guest customer checkout
        $this->guestCheckout($storeId, $data);
    }

    /**
     * Checkout from saved cart (for registered customers)
     */
    private function checkoutFromCart(int $storeId, int $customerId, array $data): void
    {
        // Get cart items
        $cartItems = $this->cartModel->getCartItems($customerId);

        if (empty($cartItems)) {
            $this->error('Cart is empty', 400);
        }

        // Validate cart
        $issues = $this->cartModel->validateCart($customerId);
        if (!empty($issues)) {
            $this->error('Cart has issues. Please review your cart.', 400, ['issues' => $issues]);
        }

        // Calculate totals
        $subtotal = 0;
        $orderItems = [];

        foreach ($cartItems as $item) {
            $subtotal += $item['subtotal'];
            $orderItems[] = [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['product_price']
            ];
        }

        $shippingCost = $data['shipping_cost'] ?? ($subtotal >= 10000 ? 0 : 1500);
        $totalAmount = $subtotal + $shippingCost;

        // Get or create shipping address
        $shippingAddressId = null;
        if (!empty($data['shipping_address_id'])) {
            $shippingAddressId = $data['shipping_address_id'];
        } elseif (!empty($data['shipping_address'])) {
            // Build address array from individual fields
            $addressData = [
                'address_line1' => $data['shipping_address'],
                'city' => $data['shipping_city'] ?? '',
                'state' => $data['shipping_state'] ?? '',
                'postal_code' => $data['shipping_postal_code'] ?? '',
                'country' => $data['shipping_country'] ?? 'Nigeria'
            ];
            $shippingAddressId = $this->createAddressFromData(
                $customerId,
                $addressData,
                'shipping'
            );
        }

        // Get or create billing address
        $billingAddressId = $data['billing_address_id'] ?? $shippingAddressId;

        // Create order
        $orderData = [
            'store_id' => $storeId,
            'customer_id' => $customerId,
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'],
            'shipping_address_id' => $shippingAddressId,
            'billing_address_id' => $billingAddressId,
            'shipping_address' => $data['shipping_address'] ?? null,
            'shipping_city' => $data['shipping_city'] ?? null,
            'shipping_state' => $data['shipping_state'] ?? null,
            'shipping_postal_code' => $data['shipping_postal_code'] ?? null,
            'shipping_country' => $data['shipping_country'] ?? 'Nigeria',
            'total_amount' => $totalAmount,
            'shipping_cost' => $shippingCost,
            'payment_method' => $data['payment_method'],
            'payment_status' => 'pending',
            'order_notes' => $data['notes'] ?? null,
            'status' => 'pending'
        ];

        $orderId = $this->orderModel->createWithItems($orderData, $orderItems);

        if (!$orderId) {
            $this->error('Failed to create order', 500);
        }

        // Update product stock
        $this->updateProductStock($orderItems, 'decrease');

        // Clear cart
        $this->cartModel->clearCart($customerId);

        // Get full order details
        $order = $this->orderModel->getFullDetails($orderId);

        // Send notification to store owner
        $store = $this->storeModel->find($storeId);
        if ($store && $store['client_id']) {
            $this->notificationService->notifyOrderPlaced(
                $store['client_id'],
                $orderId,
                $orderData
            );
        }

        $this->success($order, 'Order placed successfully', 201);
    }

    /**
     * Guest checkout (or quick checkout without using cart)
     */
    private function guestCheckout(int $storeId, array $data): void
    {
        // Validate items are provided
        if (empty($data['items']) || !is_array($data['items'])) {
            $this->error('Order items are required', 400);
        }

        // Create or get guest customer
        $customerId = null;

        // Check if email exists as guest
        $existingCustomer = $this->customerModel->findByEmailAndStore(
            $data['customer_email'],
            $storeId
        );

        if ($existingCustomer) {
            $customerId = $existingCustomer['id'];
        } else {
            // Create guest customer
            $customerId = $this->customerModel->createGuest($storeId, [
                'email' => $data['customer_email'],
                'first_name' => $this->extractFirstName($data['customer_name']),
                'last_name' => $this->extractLastName($data['customer_name']),
                'phone' => $data['customer_phone']
            ]);
        }

        if (!$customerId) {
            $this->error('Failed to create customer', 500);
        }

        // Validate and prepare order items
        $orderItems = [];
        $subtotal = 0;

        foreach ($data['items'] as $item) {
            if (empty($item['product_id']) || empty($item['quantity'])) {
                $this->error('Invalid item data', 400);
            }

            $product = $this->productModel->find($item['product_id']);

            if (!$product || $product['store_id'] != $storeId) {
                $this->error("Product {$item['product_id']} not found", 404);
            }

            if ($product['status'] !== 'active') {
                $this->error("Product '{$product['name']}' is not available", 400);
            }

            if ($product['stock_quantity'] < $item['quantity']) {
                $this->error("Insufficient stock for '{$product['name']}'", 400);
            }

            $itemTotal = $product['price'] * $item['quantity'];
            $subtotal += $itemTotal;

            $orderItems[] = [
                'product_id' => $product['id'],
                'quantity' => $item['quantity'],
                'price' => $product['price']
            ];
        }

        $shippingCost = $data['shipping_cost'] ?? ($subtotal >= 10000 ? 0 : 1500);
        $totalAmount = $subtotal + $shippingCost;

        // Create shipping address if provided
        $shippingAddressId = null;
        if (!empty($data['shipping_address'])) {
            // Build address array from individual fields
            $addressData = [
                'address_line1' => $data['shipping_address'],
                'city' => $data['shipping_city'] ?? '',
                'state' => $data['shipping_state'] ?? '',
                'postal_code' => $data['shipping_postal_code'] ?? '',
                'country' => $data['shipping_country'] ?? 'Nigeria'
            ];
            $shippingAddressId = $this->createAddressFromData(
                $customerId,
                $addressData,
                'shipping'
            );
        }

        // Create order
        $orderData = [
            'store_id' => $storeId,
            'customer_id' => $customerId,
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'],
            'shipping_address_id' => $shippingAddressId,
            'billing_address_id' => $shippingAddressId,
            'shipping_address' => $data['shipping_address'] ?? null,
            'shipping_city' => $data['shipping_city'] ?? null,
            'shipping_state' => $data['shipping_state'] ?? null,
            'shipping_postal_code' => $data['shipping_postal_code'] ?? null,
            'shipping_country' => $data['shipping_country'] ?? 'Nigeria',
            'total_amount' => $totalAmount,
            'shipping_cost' => $shippingCost,
            'payment_method' => $data['payment_method'],
            'payment_status' => 'pending',
            'order_notes' => $data['notes'] ?? null,
            'status' => 'pending'
        ];

        $orderId = $this->orderModel->createWithItems($orderData, $orderItems);

        if (!$orderId) {
            $this->error('Failed to create order', 500);
        }

        // Update product stock
        $this->updateProductStock($orderItems, 'decrease');

        // Get full order details
        $order = $this->orderModel->getFullDetails($orderId);

        // Send notification to store owner
        $store = $this->storeModel->find($storeId);
        if ($store && $store['client_id']) {
            $this->notificationService->notifyOrderPlaced(
                $store['client_id'],
                $orderId,
                $orderData
            );
        }

        $this->success($order, 'Order placed successfully', 201);
    }

    /**
     * Create address from data array
     */
    private function createAddressFromData(int $customerId, array $addressData, string $type): ?int
    {
        $addressData['customer_id'] = $customerId;
        $addressData['address_type'] = $type;
        $addressData['country'] = $addressData['country'] ?? 'Nigeria';

        return $this->addressModel->createAddress($addressData);
    }

    /**
     * Update product stock quantities
     */
    private function updateProductStock(array $items, string $operation = 'decrease'): void
    {
        foreach ($items as $item) {
            $product = $this->productModel->find($item['product_id']);

            if ($product) {
                $newStock = $operation === 'decrease'
                    ? $product['stock_quantity'] - $item['quantity']
                    : $product['stock_quantity'] + $item['quantity'];

                $this->productModel->update($item['product_id'], [
                    'stock_quantity' => max(0, $newStock)
                ]);
            }
        }
    }

    /**
     * Extract first name from full name
     */
    private function extractFirstName(string $fullName): string
    {
        $parts = explode(' ', trim($fullName), 2);
        return $parts[0];
    }

    /**
     * Extract last name from full name
     */
    private function extractLastName(string $fullName): string
    {
        $parts = explode(' ', trim($fullName), 2);
        return $parts[1] ?? '';
    }

    /**
     * Track order by ID and email (for guests)
     * GET /api/stores/{store_id}/orders/track?order_id={id}&email={email}
     * 
     * @OA\Get(
     *     path="/api/stores/{store_id}/orders/track",
     *     tags={"Customer Orders"},
     *     summary="Track order (guest)",
     *     description="Track order status using order ID and email. No authentication required - useful for guest customers.",
     *     @OA\Parameter(
     *         name="store_id",
     *         in="path",
     *         description="Store ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="order_id",
     *         in="query",
     *         description="Order ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Customer email",
     *         required=true,
     *         @OA\Schema(type="string", format="email")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Order ID and email are required", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=403, description="Invalid email or store", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=404, description="Order not found", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function track(int $storeId): void
    {
        $orderId = $_GET['order_id'] ?? null;
        $email = $_GET['email'] ?? null;

        if (!$orderId || !$email) {
            $this->error('Order ID and email are required', 400);
        }

        $order = $this->orderModel->getFullDetails($orderId);

        if (!$order) {
            $this->error('Order not found', 404);
        }

        // Verify email matches
        if (strtolower($order['customer_email']) !== strtolower($email)) {
            $this->error('Invalid email for this order', 403);
        }

        // Verify store matches
        if ($order['store_id'] != $storeId) {
            $this->error('Invalid store', 403);
        }

        $this->success($order);
    }
}
