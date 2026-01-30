<?php

/**
 * @OA\Info(
 *   version="2.0.0",
 *   title="LivePetal E-Commerce Platform API",
 *   description="Multi-tenant e-commerce platform with JWT authentication. This API allows super admins to manage clients and their stores, while clients can manage their products and orders.",
 *   @OA\Contact(
 *     email="support@livepetal.com",
 *     name="API Support"
 *   )
 * )
 * 
 * @OA\Server(
 *   url="http://localhost:8000/backend/public",
 *   description="Local Development Server"
 * )
 * 
 * @OA\Server(
 *   url="https://api.livepetal.com",
 *   description="Production Server"
 * )
 * 
 * @OA\SecurityScheme(
 *   securityScheme="bearerAuth",
 *   type="http",
 *   scheme="bearer",
 *   bearerFormat="JWT",
 *   description="Enter JWT token obtained from login endpoint"
 * )
 * 
 * @OA\Tag(
 *     name="Authentication",
 *     description="User authentication and token management endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Clients",
 *     description="Client management endpoints (Admin only)"
 * )
 * 
 * @OA\Tag(
 *     name="Stores",
 *     description="Store management endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Products",
 *     description="Product management endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Orders",
 *     description="Order management and statistics endpoints"
 * )
 * 
 * @OA\Schema(
 *     schema="Error",
 *     required={"success", "message"},
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Error description"),
 *     @OA\Property(property="errors", type="object", nullable=true, description="Validation errors if applicable")
 * )
 * 
 * @OA\Schema(
 *     schema="Success",
 *     required={"success", "message"},
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Operation successful"),
 *     @OA\Property(property="data", type="object", nullable=true)
 * )
 * 
 * @OA\Schema(
 *     schema="User",
 *     required={"id", "email", "role"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="role", type="string", enum={"admin", "client"}, example="client"),
 *     @OA\Property(property="name", type="string", example="John Doe")
 * )
 * 
 * @OA\Schema(
 *     schema="Client",
 *     required={"id", "name", "email", "status"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="client@example.com"),
 *     @OA\Property(property="company_name", type="string", nullable=true, example="Acme Inc"),
 *     @OA\Property(property="phone", type="string", nullable=true, example="+1234567890"),
 *     @OA\Property(property="subscription_plan", type="string", enum={"basic", "standard", "premium"}, example="standard"),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive", "suspended"}, example="active"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-01-26T10:30:00Z"),
 *     @OA\Property(property="store_count", type="integer", nullable=true, example=3, description="Number of stores (only in withStats)"),
 *     @OA\Property(property="order_count", type="integer", nullable=true, example=45, description="Number of orders (only in withStats)")
 * )
 * 
 * @OA\Schema(
 *     schema="Store",
 *     required={"id", "client_id", "name", "status"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="client_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="My Online Store"),
 *     @OA\Property(property="domain", type="string", nullable=true, example="mystore.com"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Best products online"),
 *     @OA\Property(property="logo_url", type="string", nullable=true, example="/uploads/logos/store1.png"),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive", "maintenance"}, example="active"),
 *     @OA\Property(property="template", type="string", example="default"),
 *     @OA\Property(property="customization", type="object", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-01-26T10:30:00Z")
 * )
 * 
 * @OA\Schema(
 *     schema="Product",
 *     required={"id", "store_id", "name", "price", "stock_quantity"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="store_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Premium Widget"),
 *     @OA\Property(property="description", type="string", nullable=true, example="High quality product"),
 *     @OA\Property(property="price", type="number", format="float", example=29.99),
 *     @OA\Property(property="stock_quantity", type="integer", example=100),
 *     @OA\Property(property="sku", type="string", nullable=true, example="WIDGET-001"),
 *     @OA\Property(property="category", type="string", nullable=true, example="Electronics"),
 *     @OA\Property(property="image_url", type="string", nullable=true, example="/uploads/products/widget1.jpg"),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive", "out_of_stock"}, example="active"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-01-26T10:30:00Z")
 * )
 * 
 * @OA\Schema(
 *     schema="Order",
 *     required={"id", "store_id", "customer_name", "customer_email", "total_amount", "status"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="store_id", type="integer", example=1),
 *     @OA\Property(property="customer_name", type="string", example="Jane Smith"),
 *     @OA\Property(property="customer_email", type="string", format="email", example="jane@example.com"),
 *     @OA\Property(property="customer_phone", type="string", nullable=true, example="+1234567890"),
 *     @OA\Property(property="total_amount", type="number", format="float", example=149.99),
 *     @OA\Property(property="status", type="string", enum={"pending", "processing", "completed", "cancelled"}, example="pending"),
 *     @OA\Property(property="payment_method", type="string", nullable=true, example="credit_card"),
 *     @OA\Property(property="shipping_address", type="string", nullable=true, example="123 Main St, City"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-01-26T10:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-01-26T11:00:00Z")
 * )
 */
