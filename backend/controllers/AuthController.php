<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Client;
use App\Models\SuperAdmin;
use App\Helpers\JWT;

/**
 * Authentication Controller
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/admin/login",
     *     tags={"Authentication"},
     *     summary="Super Admin Login",
     *     description="Authenticate super admin and receive JWT token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@platform.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="username", type="string", example="admin"),
     *                     @OA\Property(property="email", type="string", example="admin@platform.com"),
     *                     @OA\Property(property="role", type="string", example="admin")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Invalid credentials", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function adminLogin(): void
    {
        $email = $this->input('email');
        $password = $this->input('password');

        // Validation
        $errors = $this->validate([
            'email' => $email,
            'password' => $password
        ], [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!empty($errors)) {
            $this->error('Validation failed', 422, $errors);
        }

        // Find admin
        $adminModel = new SuperAdmin();
        $admin = $adminModel->findByEmail($email);

        if (!$admin || !password_verify($password, $admin['password'])) {
            $this->error('Invalid credentials', 401);
        }

        // Generate token pair
        $tokens = JWT::generateTokenPair([
            'user_id' => $admin['id'],
            'email' => $admin['email'],
            'role' => 'admin',
            'type' => 'super_admin'
        ]);

        $this->success([
            'token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
            'expires_in' => $tokens['expires_in'],
            'user' => [
                'id' => $admin['id'],
                'username' => $admin['username'],
                'email' => $admin['email'],
                'role' => 'admin'
            ]
        ], 'Login successful');
    }

    /**
     * @OA\Post(
     *     path="/api/auth/client/login",
     *     tags={"Authentication"},
     *     summary="Client Login",
     *     description="Authenticate client and receive JWT token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="client@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."),
     *                 @OA\Property(property="user", ref="#/components/schemas/User")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Invalid credentials", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=403, description="Account not active", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function clientLogin(): void
    {
        $email = $this->input('email');
        $password = $this->input('password');

        // Validation
        $errors = $this->validate([
            'email' => $email,
            'password' => $password
        ], [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!empty($errors)) {
            $this->error('Validation failed', 422, $errors);
        }

        // Find client
        $clientModel = new Client();
        $client = $clientModel->findByEmail($email);

        if (!$client || !password_verify($password, $client['password'])) {
            $this->error('Invalid credentials', 401);
        }

        // Check if client is active
        if ($client['status'] !== 'active') {
            $this->error('Account is not active', 403);
        }

        // Generate token pair
        $tokens = JWT::generateTokenPair([
            'user_id' => $client['id'],
            'email' => $client['email'],
            'role' => 'client',
            'type' => 'client',
            'subscription_plan' => $client['subscription_plan']
        ]);

        $this->success([
            'token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
            'expires_in' => $tokens['expires_in'],
            'user' => [
                'id' => $client['id'],
                'name' => $client['name'],
                'email' => $client['email'],
                'company_name' => $client['company_name'],
                'subscription_plan' => $client['subscription_plan'],
                'role' => 'client'
            ]
        ], 'Login successful');
    }

    /**
     * @OA\Post(
     *     path="/api/auth/client/register",
     *     tags={"Authentication"},
     *     summary="Client Registration",
     *     description="Register a new client account and receive JWT token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", minLength=8, example="password123"),
     *             @OA\Property(property="company_name", type="string", nullable=true, example="Acme Inc"),
     *             @OA\Property(property="phone", type="string", nullable=true, example="+1234567890"),
     *             @OA\Property(property="subscription_plan", type="string", enum={"basic", "standard", "premium"}, example="basic")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Registration successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Registration successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."),
     *                 @OA\Property(property="user", ref="#/components/schemas/Client")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=409, description="Email already exists", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Failed to create account", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function clientRegister(): void
    {
        $data = $this->input();

        // Validation
        $errors = $this->validate($data, [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'company_name' => 'max:100',
            'phone' => 'max:20'
        ]);

        if (!empty($errors)) {
            $this->error('Validation failed', 422, $errors);
        }

        $clientModel = new Client();

        // Check if email already exists
        if ($clientModel->findByEmail($data['email'])) {
            $this->error('Email already exists', 409);
        }

        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['subscription_plan'] = $data['subscription_plan'] ?? 'basic';
        $data['status'] = 'active';

        $clientId = $clientModel->create($data);

        if ($clientId) {
            $client = $clientModel->find($clientId);

            // Generate token pair
            $tokens = JWT::generateTokenPair([
                'user_id' => $client['id'],
                'email' => $client['email'],
                'role' => 'client',
                'type' => 'client',
                'subscription_plan' => $client['subscription_plan']
            ]);

            $this->success([
                'token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
                'expires_in' => $tokens['expires_in'],
                'user' => $client
            ], 'Registration successful', 201);
        } else {
            $this->error('Failed to create account', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/auth/verify",
     *     tags={"Authentication"},
     *     summary="Verify Token",
     *     description="Verify if a JWT token is valid and get user information",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token is valid",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Token is valid"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="valid", type="boolean", example=true),
     *                 @OA\Property(property="user", type="object"),
     *                 @OA\Property(property="payload", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Invalid or expired token", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=404, description="User not found", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function verify(): void
    {
        $token = JWT::getTokenFromRequest();

        if (!$token) {
            $this->error('No token provided', 401);
        }

        try {
            $payload = JWT::decode($token);

            // Get fresh user data
            if ($payload['type'] === 'super_admin') {
                $adminModel = new SuperAdmin();
                $user = $adminModel->find($payload['user_id']);
            } else {
                $clientModel = new Client();
                $user = $clientModel->find($payload['user_id']);
            }

            if (!$user) {
                $this->error('User not found', 404);
            }

            $this->success([
                'valid' => true,
                'user' => $user,
                'payload' => $payload
            ], 'Token is valid');
        } catch (\Exception $e) {
            $this->error('Invalid or expired token', 401);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/refresh",
     *     tags={"Authentication"},
     *     summary="Refresh Access Token",
     *     description="Exchange refresh token for a new access token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"refresh_token"},
     *             @OA\Property(property="refresh_token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."),
     *                 @OA\Property(property="expires_in", type="integer", example=900)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Invalid or expired refresh token", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function refresh(): void
    {
        $refreshToken = $this->input('refresh_token');

        if (!$refreshToken) {
            $this->error('Refresh token is required', 422);
        }

        try {
            // Decode and verify refresh token
            $payload = JWT::decode($refreshToken);

            // Verify it's a refresh token
            if (!isset($payload['type']) || $payload['type'] !== 'refresh') {
                $this->error('Invalid token type', 401);
            }

            // Generate new access token (keeping user data from refresh token)
            unset($payload['type']); // Remove refresh type flag
            unset($payload['exp']); // Remove old expiration
            unset($payload['iat']); // Remove old issued at

            $newAccessToken = JWT::encode($payload, null, 900); // 15 minutes

            $this->success([
                'token' => $newAccessToken,
                'expires_in' => 900
            ]);
        } catch (\Exception $e) {
            $this->error('Invalid or expired refresh token', 401);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     tags={"Authentication"},
     *     summary="Logout",
     *     description="Logout user (client-side token removal)",
     *     security={{"bearerAuth":{}}},
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
    public function logout(): void
    {
        // With JWT, logout is handled client-side by removing the token
        // Optionally, implement token blacklist here

        $this->success(null, 'Logged out successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/auth/change-password",
     *     tags={"Authentication"},
     *     summary="Change Password",
     *     description="Change authenticated user's password",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"current_password", "new_password", "confirm_password"},
     *             @OA\Property(property="current_password", type="string", format="password", example="oldpassword123"),
     *             @OA\Property(property="new_password", type="string", format="password", minLength=8, example="newpassword123"),
     *             @OA\Property(property="confirm_password", type="string", format="password", example="newpassword123")
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
     *     @OA\Response(response=400, description="Passwords do not match", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Current password is incorrect or unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function changePassword(): void
    {
        $currentPassword = $this->input('current_password');
        $newPassword = $this->input('new_password');
        $confirmPassword = $this->input('confirm_password');

        // Validation
        $errors = $this->validate([
            'current_password' => $currentPassword,
            'new_password' => $newPassword,
            'confirm_password' => $confirmPassword
        ], [
            'current_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required'
        ]);

        if (!empty($errors)) {
            $this->error('Validation failed', 422, $errors);
        }

        if ($newPassword !== $confirmPassword) {
            $this->error('Passwords do not match', 400);
        }

        // Get authenticated user from middleware
        $authUser = $_REQUEST['auth_user'] ?? null;

        if (!$authUser) {
            $this->error('Unauthorized', 401);
        }

        // Get user
        if ($authUser['type'] === 'super_admin') {
            $adminModel = new SuperAdmin();
            $user = $adminModel->find($authUser['user_id']);
        } else {
            $clientModel = new Client();
            $user = $clientModel->find($authUser['user_id']);
        }

        if (!$user || !password_verify($currentPassword, $user['password'])) {
            $this->error('Current password is incorrect', 401);
        }

        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        if ($authUser['type'] === 'super_admin') {
            $adminModel->update($authUser['user_id'], ['password' => $hashedPassword]);
        } else {
            $clientModel->update($authUser['user_id'], ['password' => $hashedPassword]);
        }

        $this->success(null, 'Password changed successfully');
    }
}
