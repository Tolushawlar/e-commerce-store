<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Client;

/**
 * Client Controller
 */
class ClientController extends Controller
{
    private Client $clientModel;

    public function __construct()
    {
        $this->clientModel = new Client();
    }

    /**
     * @OA\Get(
     *     path="/api/clients",
     *     tags={"Clients"},
     *     summary="Get all clients",
     *     description="Retrieve paginated list of clients with statistics (Admin only)",
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
     *         name="status",
     *         in="query",
     *         description="Filter by status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"active", "inactive", "suspended"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of clients",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="clients", type="array", @OA\Items(ref="#/components/schemas/Client")),
     *                 @OA\Property(property="pagination", type="object",
     *                     @OA\Property(property="page", type="integer", example=1),
     *                     @OA\Property(property="limit", type="integer", example=20),
     *                     @OA\Property(property="total", type="integer", example=100),
     *                     @OA\Property(property="pages", type="integer", example=5)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function index(): void
    {
        $page = (int)$this->query('page', 1);
        $limit = (int)$this->query('limit', 20);
        $offset = ($page - 1) * $limit;

        $status = $this->query('status');
        $search = $this->query('search');
        $conditions = $status ? ['status' => $status] : [];

        $clients = $this->clientModel->withStats($conditions, $limit, $offset, $search);
        $total = $this->clientModel->count($conditions, $search);

        $this->success([
            'clients' => $clients,
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
     *     path="/api/clients/{id}",
     *     tags={"Clients"},
     *     summary="Get single client",
     *     description="Retrieve client details with stores (Admin only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Client ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client details",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Client")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Client not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function show(string $id): void
    {
        $client = $this->clientModel->withStores((int)$id);

        if (!$client) {
            $this->error('Client not found', 404);
        }

        $this->success($client);
    }

    /**
     * @OA\Post(
     *     path="/api/clients",
     *     tags={"Clients"},
     *     summary="Create new client",
     *     description="Create a new client account (Admin only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "subscription_plan"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", minLength=8, example="password123"),
     *             @OA\Property(property="company_name", type="string", nullable=true, example="Acme Inc"),
     *             @OA\Property(property="phone", type="string", nullable=true, example="+1234567890"),
     *             @OA\Property(property="subscription_plan", type="string", enum={"basic", "standard", "premium"}, example="standard"),
     *             @OA\Property(property="status", type="string", enum={"active", "inactive", "suspended"}, example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Client created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Client created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Client")
     *         )
     *     ),
     *     @OA\Response(response=409, description="Email already exists", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function store(): void
    {
        $data = $this->input();

        // Validation
        $errors = $this->validate($data, [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'company_name' => 'max:100',
            'phone' => 'max:20',
            'subscription_plan' => 'required'
        ]);

        if (!empty($errors)) {
            $this->error('Validation failed', 422, $errors);
        }

        // Check if email already exists
        if ($this->clientModel->findByEmail($data['email'])) {
            $this->error('Email already exists', 409);
        }

        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['status'] = $data['status'] ?? 'active';

        $clientId = $this->clientModel->create($data);

        if ($clientId) {
            $client = $this->clientModel->find($clientId);
            $this->success($client, 'Client created successfully', 201);
        } else {
            $this->error('Failed to create client', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/clients/{id}",
     *     tags={"Clients"},
     *     summary="Update client",
     *     description="Update client information (Admin only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Client ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="company_name", type="string", nullable=true, example="Acme Inc"),
     *             @OA\Property(property="phone", type="string", nullable=true, example="+1234567890"),
     *             @OA\Property(property="subscription_plan", type="string", enum={"basic", "standard", "premium"}, example="premium"),
     *             @OA\Property(property="status", type="string", enum={"active", "inactive", "suspended"}, example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Client updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Client")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Client not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=409, description="Email already exists", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function update(string $id): void
    {
        $clientId = (int)$id;

        if (!$this->clientModel->find($clientId)) {
            $this->error('Client not found', 404);
        }

        $data = $this->input();

        // Validation
        $errors = $this->validate($data, [
            'name' => 'min:2|max:100',
            'email' => 'email',
            'company_name' => 'max:100',
            'phone' => 'max:20'
        ]);

        if (!empty($errors)) {
            $this->error('Validation failed', 422, $errors);
        }

        // Check email uniqueness if email is being updated
        if (isset($data['email'])) {
            $existingClient = $this->clientModel->findByEmail($data['email']);
            if ($existingClient && $existingClient['id'] != $clientId) {
                $this->error('Email already exists', 409);
            }
        }

        // Don't allow password update through this endpoint
        unset($data['password']);

        if ($this->clientModel->update($clientId, $data)) {
            $client = $this->clientModel->find($clientId);
            $this->success($client, 'Client updated successfully');
        } else {
            $this->error('Failed to update client', 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/clients/{id}",
     *     tags={"Clients"},
     *     summary="Delete client",
     *     description="Delete a client and all associated data (Admin only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Client ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Client deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Client not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function destroy(string $id): void
    {
        $clientId = (int)$id;

        if (!$this->clientModel->find($clientId)) {
            $this->error('Client not found', 404);
        }

        if ($this->clientModel->delete($clientId)) {
            $this->success(null, 'Client deleted successfully');
        } else {
            $this->error('Failed to delete client', 500);
        }
    }
}
