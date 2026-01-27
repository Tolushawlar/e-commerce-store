# E-commerce Platform - Complete Architecture

```
┌─────────────────────────────────────────────────────────────────────┐
│                         FRONTEND (Port 3000)                         │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  ┌─────────────┐  ┌──────────────┐  ┌──────────────┐              │
│  │    Auth     │  │    Admin     │  │   Client     │              │
│  │             │  │              │  │              │              │
│  │ • login     │  │ • dashboard  │  │ • dashboard  │              │
│  │ • register  │  │ • clients    │  │ • stores     │              │
│  └─────────────┘  │ • stores     │  │ • products   │              │
│                   │ • products   │  │ • orders     │              │
│                   │ • orders     │  └──────────────┘              │
│                   │ • templates  │                                │
│                   └──────────────┘                                │
│                                                                     │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │                    Shared Layouts                             │ │
│  │  • header-admin.php    • footer-admin.php                    │ │
│  │  • header-client.php   • footer-client.php                   │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │                  JavaScript Layer                             │ │
│  │                                                               │ │
│  │  ┌─────────────┐  ┌──────────────┐  ┌────────────────┐      │ │
│  │  │    Core     │  │   Services   │  │    Utils       │      │ │
│  │  │             │  │              │  │                │      │ │
│  │  │ • api.js    │  │ • client     │  │ • helpers.js   │      │ │
│  │  │ • auth.js   │  │ • store      │  │ • components   │      │ │
│  │  │             │  │ • product    │  │                │      │ │
│  │  │             │  │ • order      │  │                │      │ │
│  │  └─────────────┘  └──────────────┘  └────────────────┘      │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
                                  │
                                  │ HTTP Requests
                                  │ JWT Token
                                  ▼
┌─────────────────────────────────────────────────────────────────────┐
│                        BACKEND API (Port 8000)                       │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │                     Public Directory                          │  │
│  │                                                               │  │
│  │  • index.php ────────► Router ────────► Controllers          │  │
│  │  • router.php (Dev)                                          │  │
│  │  • openapi.json ─────► Swagger UI (docs.html)                │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                      │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │                     Controllers                               │  │
│  │                                                               │  │
│  │  • AuthController     (8 endpoints)                          │  │
│  │  • ClientController   (5 endpoints)                          │  │
│  │  • StoreController    (6 endpoints)                          │  │
│  │  • ProductController  (6 endpoints)                          │  │
│  │  • OrderController    (5 endpoints)                          │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                           │                                          │
│                           ▼                                          │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │                       Models                                  │  │
│  │                                                               │  │
│  │  • Client  • Store  • Product  • Order                       │  │
│  │                                                               │  │
│  │  (Database interaction & business logic)                     │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                           │                                          │
│                           ▼                                          │
│  ┌──────────────────────────────────────────────────────────────┐  │
│  │                      Services                                 │  │
│  │                                                               │  │
│  │  • JWTService              (Token management)                │  │
│  │  • StoreGeneratorService   (Store file generation)           │  │
│  └──────────────────────────────────────────────────────────────┘  │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘
                                  │
                                  │ SQL Queries
                                  ▼
┌─────────────────────────────────────────────────────────────────────┐
│                           DATABASE                                   │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐           │
│  │  users   │  │ clients  │  │  stores  │  │ products │           │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘           │
│                                                                      │
│  ┌──────────┐  ┌──────────────┐  ┌──────────────┐                 │
│  │  orders  │  │ order_items  │  │   sessions   │                 │
│  └──────────┘  └──────────────┘  └──────────────┘                 │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘


━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━


                          REQUEST FLOW EXAMPLE

┌──────────────┐
│ User clicks  │
│ "Login" on   │  1. User submits login form
│ Frontend     │────────────────────────────────────┐
└──────────────┘                                     │
                                                     ▼
                                          ┌─────────────────────┐
                                          │ auth.adminLogin()   │
                                          │ (frontend/core/     │
                                          │  auth.js)           │
                                          └─────────────────────┘
                                                     │
                     2. POST /api/auth/admin/login  │
                        with email & password       │
                                                     ▼
                                          ┌─────────────────────┐
                                          │ API Client          │
                                          │ (api.js)            │
                                          │ • Adds headers      │
                                          │ • Makes HTTP req    │
                                          └─────────────────────┘
                                                     │
                                                     │ HTTP Request
                                                     ▼
┌─────────────────────────────────────────────────────────────────┐
│                    BACKEND (Port 8000)                          │
│                                                                 │
│  index.php → Router → AuthController::adminLogin()             │
│                              │                                  │
│                              ▼                                  │
│                    Validate credentials                         │
│                              │                                  │
│                              ▼                                  │
│                    Query users table                            │
│                              │                                  │
│                              ▼                                  │
│                    Generate JWT token                           │
│                    (JWTService)                                 │
│                              │                                  │
│                              ▼                                  │
│                    Return {success, token, user}                │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
                                  │
                  3. JSON Response │
                     {success: true,
                      token: "eyJ...",
                      user: {...}}
                                  │
                                  ▼
                       ┌───────────────────┐
                       │ auth.saveAuth()   │
                       │ • Store token     │
                       │ • Store user      │
                       │ • localStorage    │
                       └───────────────────┘
                                  │
                   4. Redirect to │
                      dashboard   │
                                  ▼
                       ┌───────────────────┐
                       │ /admin/           │
                       │ dashboard.php     │
                       │                   │
                       │ Loads with JWT    │
                       │ token in          │
                       │ Authorization     │
                       │ header            │
                       └───────────────────┘


━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━


                         ENDPOINT MAPPING

┌────────────────────────────────────────────────────────────────────┐
│                    FRONTEND → BACKEND                              │
├────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  Frontend Service              Backend Endpoint                    │
│  ─────────────────            ────────────────────                 │
│                                                                     │
│  auth.adminLogin()        → POST /api/auth/admin/login             │
│  auth.clientLogin()       → POST /api/auth/client/login            │
│  auth.register()          → POST /api/auth/register                │
│  auth.logout()            → POST /api/auth/logout                  │
│  auth.getCurrentUser()    → GET  /api/auth/me                      │
│  auth.refreshToken()      → POST /api/auth/refresh                 │
│                                                                     │
│  clientService.getAll()   → GET  /api/clients?page=1&limit=20      │
│  clientService.getById()  → GET  /api/clients/{id}                 │
│  clientService.create()   → POST /api/clients                      │
│  clientService.update()   → PUT  /api/clients/{id}                 │
│  clientService.delete()   → DELETE /api/clients/{id}               │
│                                                                     │
│  storeService.getAll()    → GET  /api/stores?client_id=1           │
│  storeService.getById()   → GET  /api/stores/{id}                  │
│  storeService.create()    → POST /api/stores                       │
│  storeService.update()    → PUT  /api/stores/{id}                  │
│  storeService.delete()    → DELETE /api/stores/{id}                │
│  storeService.generate()  → POST /api/stores/{id}/generate         │
│                                                                     │
│  productService.getAll()  → GET  /api/products?store_id=1          │
│  productService.getLowStock() → GET /api/products/low-stock        │
│                                                                     │
│  orderService.getStats()  → GET  /api/orders/stats?store_id=1      │
│  orderService.updateStatus() → PUT /api/orders/{id}/status         │
│                                                                     │
└────────────────────────────────────────────────────────────────────┘


━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━


                      AUTHENTICATION FLOW

┌──────────────┐         ┌──────────────┐         ┌──────────────┐
│   Browser    │         │   Frontend   │         │   Backend    │
│  localStorage│         │   (JS)       │         │   API        │
└──────────────┘         └──────────────┘         └──────────────┘
       │                        │                         │
       │   1. Check token       │                         │
       │◄───────────────────────│                         │
       │                        │                         │
       │   2. Token exists      │                         │
       ├───────────────────────►│                         │
       │                        │                         │
       │                        │   3. GET /api/auth/me   │
       │                        │    Authorization:       │
       │                        │    Bearer eyJ...        │
       │                        ├────────────────────────►│
       │                        │                         │
       │                        │   4. Verify JWT         │
       │                        │      Decode token       │
       │                        │      Check expiry       │
       │                        │◄────────────────────────│
       │                        │                         │
       │                        │   5. {user: {...}}      │
       │                        │◄────────────────────────│
       │                        │                         │
       │   6. Update user data  │                         │
       │◄───────────────────────│                         │
       │                        │                         │
       │                        │   7. Load page content  │
       │                        ├────────────────────────►│
       │                        │                         │


━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━


                        SECURITY LAYERS

┌────────────────────────────────────────────────────────────────────┐
│                         FRONTEND                                    │
│                                                                     │
│  Layer 1: Page-level Auth Check                                   │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │ auth.requireAdmin()   // Redirect if not admin               │ │
│  │ auth.requireClient()  // Redirect if not client              │ │
│  │ auth.requireAuth()    // Redirect if not authenticated       │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  Layer 2: Role-based UI                                           │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │ if (auth.isAdmin()) {                                        │ │
│  │   // Show admin features                                     │ │
│  │ }                                                             │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  Layer 3: Token Management                                        │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │ • Auto-attach JWT to all requests                            │ │
│  │ • Store securely in localStorage                             │ │
│  │ • Clear on logout                                            │ │
│  └──────────────────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────────────────┘
                                  │
                                  ▼
┌────────────────────────────────────────────────────────────────────┐
│                         BACKEND                                     │
│                                                                     │
│  Layer 1: JWT Verification                                         │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │ AuthMiddleware::handle()                                     │ │
│  │ • Verify token signature                                     │ │
│  │ • Check expiration                                           │ │
│  │ • Extract user data                                          │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  Layer 2: Controller Authorization                                │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │ requireAdmin()  // Admin-only endpoints                      │ │
│  │ checkOwnership() // Resource ownership check                 │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                                                                     │
│  Layer 3: Database Validation                                     │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │ • Validate data types                                        │ │
│  │ • Check constraints                                          │ │
│  │ • Prepared statements (SQL injection prevention)            │ │
│  └──────────────────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────────────────┘
```
