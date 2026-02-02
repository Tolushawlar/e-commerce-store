<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\CustomerAddress;
use App\Services\CustomerJWTService;

/**
 * Address Controller
 * Handles customer address management
 */
class AddressController extends Controller
{
    private CustomerAddress $addressModel;

    public function __construct()
    {
        $this->addressModel = new CustomerAddress();
    }

    /**
     * Get all addresses for customer
     * GET /api/stores/{store_id}/addresses
     * 
     * @OA\Get(
     *     path="/api/stores/{store_id}/addresses",
     *     tags={"Customer Addresses"},
     *     summary="Get customer addresses",
     *     description="Retrieve all addresses for the authenticated customer",
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
     *         description="Addresses retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="address_line1", type="string", example="123 Main Street"),
     *                     @OA\Property(property="address_line2", type="string", example="Apt 4B"),
     *                     @OA\Property(property="city", type="string", example="Lagos"),
     *                     @OA\Property(property="state", type="string", example="Lagos State"),
     *                     @OA\Property(property="postal_code", type="string", example="100001"),
     *                     @OA\Property(property="country", type="string", example="Nigeria"),
     *                     @OA\Property(property="address_type", type="string", enum={"shipping", "billing"}, example="shipping"),
     *                     @OA\Property(property="is_default", type="boolean", example=true)
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

        $addresses = $this->addressModel->getByCustomer($customerPayload['customer_id']);

        $this->success($addresses);
    }

    /**
     * Get single address
     * GET /api/stores/{store_id}/addresses/{id}
     * 
     * @OA\Get(
     *     path="/api/stores/{store_id}/addresses/{id}",
     *     tags={"Customer Addresses"},
     *     summary="Get single address",
     *     description="Retrieve details of a specific address",
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
     *         description="Address ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Address retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=404, description="Address not found", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function show(int $storeId, int $id): void
    {
        $customerPayload = CustomerJWTService::getCustomerFromRequest();

        if (!$customerPayload) {
            $this->error('Unauthorized', 401);
        }

        $address = $this->addressModel->find($id);

        if (!$address || $address['customer_id'] != $customerPayload['customer_id']) {
            $this->error('Address not found', 404);
        }

        $this->success($address);
    }

    /**
     * Create new address
     * POST /api/stores/{store_id}/addresses
     * 
     * @OA\Post(
     *     path="/api/stores/{store_id}/addresses",
     *     tags={"Customer Addresses"},
     *     summary="Create new address",
     *     description="Create a new address for the authenticated customer",
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
     *             required={"address_line1", "city", "state"},
     *             @OA\Property(property="address_line1", type="string", example="123 Main Street"),
     *             @OA\Property(property="address_line2", type="string", example="Apt 4B"),
     *             @OA\Property(property="city", type="string", example="Lagos"),
     *             @OA\Property(property="state", type="string", example="Lagos State"),
     *             @OA\Property(property="postal_code", type="string", example="100001"),
     *             @OA\Property(property="country", type="string", example="Nigeria", default="Nigeria"),
     *             @OA\Property(property="address_type", type="string", enum={"shipping", "billing"}, example="shipping", default="shipping")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Address created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Address created successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=422, description="Validation failed", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Failed to create address", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function store(int $storeId): void
    {
        $customerPayload = CustomerJWTService::getCustomerFromRequest();

        if (!$customerPayload) {
            $this->error('Unauthorized', 401);
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $errors = $this->validate($data, [
            'address_line1' => 'required',
            'city' => 'required',
            'state' => 'required'
        ]);

        if (!empty($errors)) {
            $this->error('Validation failed', 422, $errors);
        }

        // Add customer_id to data
        $data['customer_id'] = $customerPayload['customer_id'];

        // Set default values
        $data['address_type'] = $data['address_type'] ?? 'shipping';
        $data['country'] = $data['country'] ?? 'Nigeria';

        $addressId = $this->addressModel->createAddress($data);

        if (!$addressId) {
            $this->error('Failed to create address', 500);
        }

        $address = $this->addressModel->find($addressId);

        $this->success($address, 'Address created successfully', 201);
    }

    /**
     * Update address
     * PUT /api/stores/{store_id}/addresses/{id}
     * 
     * @OA\Put(
     *     path="/api/stores/{store_id}/addresses/{id}",
     *     tags={"Customer Addresses"},
     *     summary="Update address",
     *     description="Update an existing customer address",
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
     *         description="Address ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="address_line1", type="string", example="123 Main Street"),
     *             @OA\Property(property="address_line2", type="string", example="Apt 4B"),
     *             @OA\Property(property="city", type="string", example="Lagos"),
     *             @OA\Property(property="state", type="string", example="Lagos State"),
     *             @OA\Property(property="postal_code", type="string", example="100001"),
     *             @OA\Property(property="country", type="string", example="Nigeria")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Address updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Address updated successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=404, description="Address not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Failed to update address", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function update(int $storeId, int $id): void
    {
        $customerPayload = CustomerJWTService::getCustomerFromRequest();

        if (!$customerPayload) {
            $this->error('Unauthorized', 401);
        }

        $data = json_decode(file_get_contents('php://input'), true);

        // Validate ownership
        $address = $this->addressModel->find($id);
        if (!$address || $address['customer_id'] != $customerPayload['customer_id']) {
            $this->error('Address not found', 404);
        }

        // Update address
        $success = $this->addressModel->updateAddress(
            $id,
            $customerPayload['customer_id'],
            $data
        );

        if (!$success) {
            $this->error('Failed to update address', 500);
        }

        $address = $this->addressModel->find($id);

        $this->success($address, 'Address updated successfully');
    }

    /**
     * Delete address
     * DELETE /api/stores/{store_id}/addresses/{id}
     * 
     * @OA\Delete(
     *     path="/api/stores/{store_id}/addresses/{id}",
     *     tags={"Customer Addresses"},
     *     summary="Delete address",
     *     description="Delete a customer address",
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
     *         description="Address ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Address deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Address deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=404, description="Address not found or failed to delete", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function destroy(int $storeId, int $id): void
    {
        $customerPayload = CustomerJWTService::getCustomerFromRequest();

        if (!$customerPayload) {
            $this->error('Unauthorized', 401);
        }

        $success = $this->addressModel->deleteAddress(
            $id,
            $customerPayload['customer_id']
        );

        if (!$success) {
            $this->error('Address not found or failed to delete', 404);
        }

        $this->success(null, 'Address deleted successfully');
    }

    /**
     * Set address as default
     * POST /api/stores/{store_id}/addresses/{id}/set-default
     * 
     * @OA\Post(
     *     path="/api/stores/{store_id}/addresses/{id}/set-default",
     *     tags={"Customer Addresses"},
     *     summary="Set address as default",
     *     description="Set a specific address as the default address for the customer",
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
     *         description="Address ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Default address updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Default address updated")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=404, description="Address not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=500, description="Failed to set default address", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    public function setDefault(int $storeId, int $id): void
    {
        $customerPayload = CustomerJWTService::getCustomerFromRequest();

        if (!$customerPayload) {
            $this->error('Unauthorized', 401);
        }

        // Validate ownership
        $address = $this->addressModel->find($id);
        if (!$address || $address['customer_id'] != $customerPayload['customer_id']) {
            $this->error('Address not found', 404);
        }

        $success = $this->addressModel->setAsDefault($id, $customerPayload['customer_id']);

        if (!$success) {
            $this->error('Failed to set default address', 500);
        }

        $this->success(null, 'Default address updated');
    }
}
