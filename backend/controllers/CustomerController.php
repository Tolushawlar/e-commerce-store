<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\StoreCustomer;
use App\Models\CustomerAddress;
use App\Models\ShoppingCart;
use App\Services\CustomerJWTService;
use App\Helpers\Validator;

/**
 * Customer Controller
 * Handles customer registration, login, and profile management
 * Public-facing endpoints for store customers
 */
class CustomerController extends Controller
{
    private StoreCustomer $customerModel;
    private CustomerAddress $addressModel;
    private ShoppingCart $cartModel;

    public function __construct()
    {
        $this->customerModel = new StoreCustomer();
        $this->addressModel = new CustomerAddress();
        $this->cartModel = new ShoppingCart();
    }

    /**
     * Register new customer
     * POST /api/stores/{store_id}/customers/register
     * 
     * @OA\Post(
     *     path="/api/stores/{store_id}/customers/register",
     *     tags={"Customer Auth"},
     *     summary="Register new customer",
     *     description="Register a new customer for a specific store. If a guest account exists with the same email, it will be converted to a registered account.",
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
     *             required={"email", "password", "first_name", "last_name"},
     *             @OA\Property(property="email", type="string", format="email", example="customer@example.com"),
     *             @OA\Property(property="password", type="string", format="password", minLength=6, example="password123"),
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="phone", type="string", example="+2348012345678")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Registration successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Registration successful"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="customer", type="object"),
     *                 @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=409, description="Email already registered", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Failed to create account", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function register(int $storeId): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validate required fields
        $errors = $this->validate($data, [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'first_name' => 'required',
            'last_name' => 'required'
        ]);

        if (!empty($errors)) {
            $this->error('Validation failed', 422, $errors);
        }

        // Check if email already exists for this store
        $existing = $this->customerModel->findByEmailAndStore($data['email'], $storeId);
        if ($existing && !$existing['is_guest']) {
            $this->error('Email already registered for this store', 409);
        }

        // If guest exists with same email, convert to registered
        if ($existing && $existing['is_guest']) {
            $success = $this->customerModel->convertGuestToRegistered(
                $existing['id'],
                $data['password']
            );

            if (!$success) {
                $this->error('Failed to upgrade guest account', 500);
            }

            $customerId = $existing['id'];
        } else {
            // Create new customer
            $customerId = $this->customerModel->createRegistered($storeId, $data);
        }

        if (!$customerId) {
            $this->error('Failed to create customer account', 500);
        }

        // Get customer data
        $customer = $this->customerModel->find($customerId);

        // Generate JWT token
        $token = CustomerJWTService::generate($customer, $storeId);

        $this->success([
            'customer' => $customer,
            'token' => $token
        ], 'Registration successful', 201);
    }

    /**
     * Customer login
     * POST /api/stores/{store_id}/customers/login
     * 
     * @OA\Post(
     *     path="/api/stores/{store_id}/customers/login",
     *     tags={"Customer Auth"},
     *     summary="Customer login",
     *     description="Authenticate a customer and receive JWT token. Optionally sync session cart with database cart.",
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
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="customer@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(
     *                 property="session_cart",
     *                 type="array",
     *                 description="Optional session cart to sync",
     *                 @OA\Items(
     *                     @OA\Property(property="product_id", type="integer"),
     *                     @OA\Property(property="quantity", type="integer")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="customer", type="object"),
     *                 @OA\Property(property="token", type="string"),
     *                 @OA\Property(property="cart_count", type="integer", example=3)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Guest account - complete registration first", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Invalid credentials", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=403, description="Account is suspended/inactive", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function login(int $storeId): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validate
        $errors = $this->validate($data, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!empty($errors)) {
            $this->error('Validation failed', 422, $errors);
        }

        // Find customer
        $customer = $this->customerModel->findByEmailAndStore($data['email'], $storeId);

        if (!$customer) {
            $this->error('Invalid email or password', 401);
        }

        // Check if guest account
        if ($customer['is_guest']) {
            $this->error('This email is registered as guest. Please complete registration first.', 400);
        }

        // Verify password
        if (!$this->customerModel->verifyPassword($customer, $data['password'])) {
            $this->error('Invalid email or password', 401);
        }

        // Check account status
        if ($customer['status'] !== 'active') {
            $this->error('Account is ' . $customer['status'], 403);
        }

        // Update last login
        $this->customerModel->updateLastLogin($customer['id']);

        // Get customer with full data
        $customerData = $this->customerModel->findWithAddresses($customer['id']);

        // Generate token
        $token = CustomerJWTService::generate($customer, $storeId);

        // Sync session cart if provided
        if (!empty($data['session_cart'])) {
            $this->cartModel->syncWithSession($customer['id'], $data['session_cart']);
        }

        $this->success([
            'customer' => $customerData,
            'token' => $token,
            'cart_count' => $this->cartModel->getItemCount($customer['id'])
        ], 'Login successful');
    }

    /**
     * Get current customer profile
     * GET /api/stores/{store_id}/customers/me
     * 
     * @OA\Get(
     *     path="/api/stores/{store_id}/customers/me",
     *     tags={"Customer Profile"},
     *     summary="Get current customer profile",
     *     description="Get authenticated customer's profile including addresses, order count, total spent, and cart count",
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
     *         description="Profile retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="email", type="string", example="customer@example.com"),
     *                 @OA\Property(property="first_name", type="string", example="John"),
     *                 @OA\Property(property="last_name", type="string", example="Doe"),
     *                 @OA\Property(property="phone", type="string", example="+2348012345678"),
     *                 @OA\Property(property="order_count", type="integer", example=5),
     *                 @OA\Property(property="total_spent", type="number", format="float", example=125000.00),
     *                 @OA\Property(property="cart_count", type="integer", example=3),
     *                 @OA\Property(property="addresses", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=403, description="Invalid store", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=404, description="Customer not found", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function me(int $storeId): void
    {
        $customerPayload = CustomerJWTService::getCustomerFromRequest();

        if (!$customerPayload) {
            $this->error('Unauthorized', 401);
        }

        // Verify store matches
        if ($customerPayload['store_id'] != $storeId) {
            $this->error('Invalid store', 403);
        }

        $customer = $this->customerModel->findWithAddresses($customerPayload['customer_id']);

        if (!$customer) {
            $this->error('Customer not found', 404);
        }

        // Get additional data
        $customer['order_count'] = $this->customerModel->getOrderCount($customer['id']);
        $customer['total_spent'] = $this->customerModel->getTotalSpent($customer['id']);
        $customer['cart_count'] = $this->cartModel->getItemCount($customer['id']);

        $this->success($customer);
    }

    /**
     * Update customer profile
     * PUT /api/stores/{store_id}/customers/me
     * 
     * @OA\Put(
     *     path="/api/stores/{store_id}/customers/me",
     *     tags={"Customer Profile"},
     *     summary="Update customer profile",
     *     description="Update customer's profile information (first_name, last_name, phone)",
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
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="phone", type="string", example="+2348012345678")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Profile updated successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=400, description="No valid fields to update", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Failed to update profile", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function updateProfile(int $storeId): void
    {
        $customerPayload = CustomerJWTService::getCustomerFromRequest();

        if (!$customerPayload) {
            $this->error('Unauthorized', 401);
        }

        $data = json_decode(file_get_contents('php://input'), true);

        // Only allow updating certain fields
        $allowedFields = ['first_name', 'last_name', 'phone'];
        $updateData = array_intersect_key($data, array_flip($allowedFields));

        if (empty($updateData)) {
            $this->error('No valid fields to update', 400);
        }

        $success = $this->customerModel->update($customerPayload['customer_id'], $updateData);

        if (!$success) {
            $this->error('Failed to update profile', 500);
        }

        $customer = $this->customerModel->find($customerPayload['customer_id']);
        $this->success($customer, 'Profile updated successfully');
    }

    /**
     * Change password
     * POST /api/stores/{store_id}/customers/change-password
     * 
     * @OA\Post(
     *     path="/api/stores/{store_id}/customers/change-password",
     *     tags={"Customer Profile"},
     *     summary="Change customer password",
     *     description="Change customer's password. Guest accounts cannot change password.",
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
     *             required={"current_password", "new_password"},
     *             @OA\Property(property="current_password", type="string", format="password", example="oldpassword123"),
     *             @OA\Property(property="new_password", type="string", format="password", minLength=6, example="newpassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password changed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Password changed successfully")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Guest accounts cannot change password", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Current password is incorrect", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Failed to change password", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function changePassword(int $storeId): void
    {
        $customerPayload = CustomerJWTService::getCustomerFromRequest();

        if (!$customerPayload) {
            $this->error('Unauthorized', 401);
        }

        if ($customerPayload['is_guest']) {
            $this->error('Guest accounts cannot change password', 400);
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $errors = $this->validate($data, [
            'current_password' => 'required',
            'new_password' => 'required|min:6'
        ]);

        if (!empty($errors)) {
            $this->error('Validation failed', 422, $errors);
        }

        // Get customer
        $customer = $this->customerModel->find($customerPayload['customer_id']);

        // Verify current password
        if (!$this->customerModel->verifyPassword($customer, $data['current_password'])) {
            $this->error('Current password is incorrect', 401);
        }

        // Update password
        $passwordHash = password_hash($data['new_password'], PASSWORD_BCRYPT);
        $success = $this->customerModel->update($customer['id'], [
            'password_hash' => $passwordHash
        ]);

        if (!$success) {
            $this->error('Failed to change password', 500);
        }

        $this->success(null, 'Password changed successfully');
    }

    /**
     * Logout customer
     * POST /api/stores/{store_id}/customers/logout
     * 
     * @OA\Post(
     *     path="/api/stores/{store_id}/customers/logout",
     *     tags={"Customer Auth"},
     *     summary="Logout customer",
     *     description="Logout customer. Client should remove token from storage.",
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
     *         description="Logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Logged out successfully")
     *         )
     *     )
     * )
     */
    public function logout(int $storeId): void
    {
        // Client-side should remove token
        $this->success(null, 'Logged out successfully');
    }
}
