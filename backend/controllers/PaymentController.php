<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Store;
use App\Models\Order;
use App\Services\PaystackService;

/**
 * Payment Controller
 * Handles payment operations for multi-tenant stores
 */
class PaymentController extends Controller
{
    private PaystackService $paystackService;
    private Store $storeModel;
    private Order $orderModel;

    public function __construct()
    {
        $this->paystackService = new PaystackService();
        $this->storeModel = new Store();
        $this->orderModel = new Order();
    }

    /**
     * Get store's payment configuration
     * GET /api/stores/{store_id}/payment/config
     * Returns public key and payment status
     */
    public function getConfig(int $storeId): void
    {
        $store = $this->storeModel->find($storeId);

        if (!$store) {
            $this->error('Store not found', 404);
        }

        // Return all payment method configurations
        $config = [
            'paystack' => [
                'enabled' => (bool) ($store['payment_enabled'] ?? false),
                'public_key' => $store['paystack_public_key'] ?? null,
            ],
            'bank_transfer' => [
                'enabled' => (bool) ($store['bank_transfer_enabled'] ?? false),
                'bank_name' => $store['bank_name'] ?? null,
                'account_number' => $store['account_number'] ?? null,
                'account_name' => $store['account_name'] ?? null,
            ],
            'cod' => [
                'enabled' => (bool) ($store['cod_enabled'] ?? true), // Default enabled
            ],
            // Legacy support for existing integrations
            'payment_enabled' => (bool) ($store['payment_enabled'] ?? false),
            'public_key' => $store['paystack_public_key'] ?? null,
            'gateway' => 'paystack'
        ];

        $this->success($config);
    }

    /**
     * Initialize payment for an order
     * POST /api/payment/initialize
     * 
     * @OA\Post(
     *     path="/api/payment/initialize",
     *     tags={"Payment"},
     *     summary="Initialize Paystack payment",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"order_id"},
     *             @OA\Property(property="order_id", type="integer", example=123),
     *             @OA\Property(property="callback_url", type="string", example="https://store.com/payment/callback")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment initialized",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="authorization_url", type="string"),
     *                 @OA\Property(property="access_code", type="string"),
     *                 @OA\Property(property="reference", type="string")
     *             )
     *         )
     *     )
     * )
     */
    public function initialize(): void
    {
        $data = $this->input();

        // Validate
        if (empty($data['order_id'])) {
            $this->error('Order ID is required', 400);
        }

        // Get order
        $order = $this->orderModel->withItems($data['order_id']);
        if (!$order) {
            $this->error('Order not found', 404);
        }

        // Verify ownership
        $authUser = $this->request['auth_user'] ?? null;
        if ($authUser && $authUser['role'] === 'customer' && $order['customer_id'] != $authUser['id']) {
            $this->error('Unauthorized', 403);
        }

        // Check if already paid
        if ($order['payment_status'] === 'paid') {
            $this->error('Order already paid', 400);
        }

        // Get store
        $store = $this->storeModel->find($order['store_id']);
        if (!$store) {
            $this->error('Store not found', 404);
        }

        // Check if payment is enabled for this store
        if (!$store['payment_enabled']) {
            $this->error('Payment not enabled for this store', 400);
        }

        // Initialize Paystack with store's keys
        $this->paystackService->setKeys($store);

        if (!$this->paystackService->isConfigured()) {
            $this->error('Payment gateway not configured for this store', 500);
        }

        try {
            // Generate or use existing reference
            $reference = $order['payment_reference'] ?? $this->paystackService->generateReference();

            // Prepare payment data
            $paymentData = [
                'email' => $order['customer_email'],
                'amount' => (float) $order['total_amount'],
                'reference' => $reference,
                'callback_url' => $data['callback_url'] ?? null,
                'metadata' => [
                    'order_id' => $order['id'],
                    'store_id' => $order['store_id'],
                    'customer_name' => $order['customer_name']
                ]
            ];

            // Initialize payment with Paystack
            $response = $this->paystackService->initializePayment($paymentData);

            // Save payment reference to order
            $this->orderModel->update($order['id'], [
                'payment_reference' => $reference,
                'payment_gateway' => 'paystack'
            ]);

            $this->success($response);
        } catch (\Exception $e) {
            $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Verify payment
     * POST /api/payment/verify
     * 
     * @OA\Post(
     *     path="/api/payment/verify",
     *     tags={"Payment"},
     *     summary="Verify Paystack payment",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"reference"},
     *             @OA\Property(property="reference", type="string", example="PS_1234567890_abcdef")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment verified",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="string", example="success"),
     *                 @OA\Property(property="amount", type="number"),
     *                 @OA\Property(property="order_id", type="integer")
     *             )
     *         )
     *     )
     * )
     */
    public function verify(): void
    {
        $data = $this->input();

        // Validate
        if (empty($data['reference'])) {
            $this->error('Payment reference is required', 400);
        }

        // Find order by payment reference
        $order = $this->orderModel->findByPaymentReference($data['reference']);
        if (!$order) {
            $this->error('Order not found', 404);
        }

        // Verify ownership
        $authUser = $this->request['auth_user'] ?? null;
        if ($authUser && $authUser['role'] === 'customer' && $order['customer_id'] != $authUser['id']) {
            $this->error('Unauthorized', 403);
        }

        // Get store
        $store = $this->storeModel->find($order['store_id']);
        if (!$store) {
            $this->error('Store not found', 404);
        }

        // Initialize Paystack with store's keys
        $this->paystackService->setKeys($store);

        try {
            // Verify payment with Paystack
            $transaction = $this->paystackService->verifyPayment($data['reference']);

            // Check if payment was successful
            if ($transaction['status'] === 'success') {
                // Update order payment status
                $this->orderModel->update($order['id'], [
                    'payment_status' => 'paid',
                    'payment_verified_at' => date('Y-m-d H:i:s'),
                    'status' => 'processing' // Move to processing after payment
                ]);

                $this->success([
                    'status' => 'success',
                    'amount' => PaystackService::formatAmount($transaction['amount']),
                    'order_id' => $order['id'],
                    'message' => 'Payment verified successfully'
                ]);
            } else {
                $this->error('Payment verification failed', 400);
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Handle Paystack webhook
     * POST /api/payment/webhook/paystack
     * 
     * Public endpoint - no authentication required
     */
    public function webhook(): void
    {
        // Get raw POST body
        $payload = file_get_contents('php://input');

        // Get signature from header
        $signature = $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ?? '';

        if (empty($signature)) {
            $this->error('No signature provided', 400);
        }

        // Parse payload
        $event = json_decode($payload, true);

        if (!$event || !isset($event['data']['reference'])) {
            $this->error('Invalid payload', 400);
        }

        // Find order by reference
        $reference = $event['data']['reference'];
        $order = $this->orderModel->findByPaymentReference($reference);

        if (!$order) {
            $this->error('Order not found', 404);
        }

        // Get store to verify signature
        $store = $this->storeModel->find($order['store_id']);
        if (!$store) {
            $this->error('Store not found', 404);
        }

        // Initialize Paystack and verify signature
        $this->paystackService->setKeys($store);

        if (!$this->paystackService->verifyWebhookSignature($payload, $signature)) {
            $this->error('Invalid signature', 401);
        }

        // Process event based on type
        $eventType = $event['event'] ?? '';

        try {
            switch ($eventType) {
                case 'charge.success':
                    // Payment successful
                    if ($event['data']['status'] === 'success') {
                        $this->orderModel->update($order['id'], [
                            'payment_status' => 'paid',
                            'payment_verified_at' => date('Y-m-d H:i:s'),
                            'status' => 'processing'
                        ]);
                    }
                    break;

                case 'charge.failed':
                    // Payment failed
                    $this->orderModel->update($order['id'], [
                        'payment_status' => 'failed'
                    ]);
                    break;

                default:
                    // Log other events but don't process
                    break;
            }

            // Return 200 OK to Paystack
            http_response_code(200);
            echo json_encode(['status' => 'success']);
            exit;
        } catch (\Exception $e) {
            $this->error($e->getMessage(), 500);
        }
    }
}
