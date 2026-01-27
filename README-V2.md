# E-commerce Platform v2.0 - MVC Architecture

A modern, scalable multi-tenant e-commerce platform built with PHP MVC architecture and RESTful APIs.

## 📚 Quick Links

- **[API Documentation](md-docs/SWAGGER-SETUP.md)** - Interactive Swagger UI at `/docs.html`
- **[JWT Authentication Guide](md-docs/JWT-AUTHENTICATION.md)** - Security implementation
- **[API Endpoints Reference](md-docs/API-DOCUMENTATION.md)** - Complete endpoint listing
- **[Setup Guide](md-docs/SETUP-GUIDE.md)** - Detailed setup instructions
- **[Architecture Diagram](md-docs/ARCHITECTURE-DIAGRAM.md)** - System architecture overview
- **[How to Create Store](md-docs/HOW-TO-CREATE-STORE.md)** - Store creation guide

## 🏗️ Architecture

### Backend (MVC)

- **Models**: Database interaction and business logic
- **Controllers**: Request handling and response formatting
- **Services**: Complex business logic and integrations
- **Routes**: REST API endpoint definitions

### Frontend (Separate)

- Pure HTML/CSS/JavaScript
- Consumes backend REST APIs
- Tailwind CSS for styling
- Modular JavaScript architecture

## 📁 Project Structure

```
e-commerce-store/
├── backend/                      # Backend API (MVC Architecture)
│   ├── config/
│   │   ├── config.php           # Application configuration
│   │   └── database.php         # Database connection handler
│   ├── core/
│   │   ├── Model.php            # Base model class
│   │   ├── Controller.php       # Base controller class
│   │   └── Router.php           # Routing system
│   ├── models/
│   │   ├── Client.php           # Client model
│   │   ├── Store.php            # Store model
│   │   ├── Product.php          # Product model
│   │   ├── Order.php            # Order model
│   │   └── Template.php         # Template model
│   ├── controllers/
│   │   ├── AuthController.php   # Authentication endpoints
│   │   ├── ClientController.php # Client management
│   │   ├── StoreController.php  # Store management
│   │   ├── ProductController.php # Product management
│   │   ├── OrderController.php  # Order management
│   │   └── TemplateController.php # Template management
│   ├── services/
│   │   ├── JWTService.php       # JWT token handling
│   │   └── StoreGeneratorService.php # Store generation
│   ├── middleware/
│   │   ├── AuthMiddleware.php   # Authentication middleware
│   │   └── CorsMiddleware.php   # CORS handling
│   ├── helpers/
│   │   ├── Validator.php        # Input validation
│   │   └── Response.php         # Response formatter
│   ├── database/
│   │   └── Database.php         # Database connection
│   ├── public/
│   │   ├── index.php            # API entry point
│   │   ├── router.php           # Dev server router
│   │   ├── openapi.json         # OpenAPI specification
│   │   ├── docs.html            # Swagger UI
│   │   ├── .htaccess            # URL rewriting
│   │   └── stores/              # Generated store files
│   └── bootstrap.php            # Application bootstrap
│
├── frontend/                     # Frontend Application
│   ├── auth/
│   │   ├── login.php            # Login page
│   │   └── register.php         # Registration page
│   ├── admin/                   # Admin Dashboard
│   │   ├── dashboard.php        # Admin overview
│   │   ├── clients.php          # Client management
│   │   ├── stores.php           # Store management
│   │   ├── create-store.php     # Create new store
│   │   ├── edit-store.php       # Edit existing store
│   │   ├── customize-store.php  # Store customization
│   │   └── templates.php        # Template management
│   ├── client/                  # Client Dashboard
│   │   ├── dashboard.php        # Client overview
│   │   ├── stores.php           # My stores
│   │   └── products.php         # My products
│   ├── shared/                  # Shared Layouts
│   │   ├── header-admin.php     # Admin layout header
│   │   ├── footer-admin.php     # Admin layout footer
│   │   ├── header-client.php    # Client layout header
│   │   └── footer-client.php    # Client layout footer
│   ├── assets/
│   │   └── js/
│   │       ├── core/            # Core functionality
│   │       │   ├── api.js       # API client
│   │       │   └── auth.js      # Authentication service
│   │       ├── services/        # API service modules
│   │       │   ├── client.service.js
│   │       │   ├── store.service.js
│   │       │   ├── product.service.js
│   │       │   ├── order.service.js
│   │       │   └── template.service.js
│   │       ├── utils/           # Utility functions
│   │       │   ├── helpers.js   # Helper functions
│   │       │   └── components.js # UI components
│   │       ├── api.js           # Legacy API client
│   │       ├── auth.js          # Legacy auth
│   │       ├── store.js         # Store frontend script
│   │       ├── admin-clients.js # Admin clients page
│   │       ├── admin-stores.js  # Admin stores page
│   │       └── client-products.js # Client products page
│   └── index.php                # Root redirect
│
├── md-docs/                     # Documentation
│   ├── SWAGGER-SETUP.md         # API documentation guide
│   ├── JWT-AUTHENTICATION.md    # Auth guide
│   ├── API-DOCUMENTATION.md     # API reference
│   ├── MIGRATION-GUIDE.md       # Migration guide
│   └── INSTALLATION.md          # Installation guide
│
├── store-templates/             # Store HTML templates
│   └── campmart-style.html      # Sample template
│
├── vendor/                      # Composer dependencies
├── .env                         # Environment variables
├── .gitignore                   # Git ignore rules
├── composer.json                # Composer dependencies
├── composer.lock                # Locked versions
├── README.md                    # Main README
└── README-V2.md                 # This file

```

## 🚀 Installation

### Prerequisites

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Apache/Nginx with mod_rewrite enabled
- Composer (optional, for future dependencies)

### Setup Steps

1. **Clone the repository**

   ```bash
   git clone <repository-url>
   cd e-commerce-store
   ```

````

2. **Configure Apache Virtual Host** (or use MAMP/XAMPP)

   ```apache
   <VirtualHost *:80>
       ServerName ecommerce.local
       DocumentRoot "/path/to/e-commerce-store"

       <Directory "/path/to/e-commerce-store/backend/public">
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

3. **Update hosts file** (optional)

   ```
   127.0.0.1   ecommerce.local
   ```

4. **Import Database**

   The database schema is located in the `database` folder at project root:

   ```bash
   mysql -u root -p < database/schema.sql
   ```

   Or import via phpMyAdmin/MySQL Workbench using `database/schema.sql`

5. **Configure Database Connection**
   Edit `backend/config/config.php`:

   ```php
   'database' => [
       'host' => 'localhost',
       'name' => 'ecommerce_platform',
       'username' => 'root',
       'password' => 'your_password',
       ...
   ]
   ```

6. **Set Permissions** (Linux/Mac)

   ```bash
   chmod -R 755 backend/public/stores/
   chmod -R 755 uploads/
   ```

7. **Start the Application**

   Open two terminal windows:

   **Terminal 1 - Backend API:**
   ```bash
   cd backend/public
   php -S localhost:8000 router.php
   ```

   **Terminal 2 - Frontend:**
   ```bash
   php -S localhost:3000 -t frontend
   ```

8. **Access the Application**
   - **Admin Login**: `http://localhost:3000/auth/login.php`
   - **Client Login**: `http://localhost:3000/auth/login.php`
   - **Admin Dashboard**: `http://localhost:3000/admin/dashboard.php`
   - **Client Dashboard**: `http://localhost:3000/client/dashboard.php`
   - **API Documentation**: `http://localhost:8000/docs.html`
   - **API Base URL**: `http://localhost:8000/api`
   - **Generated Stores**: `http://localhost:8000/stores/store-{id}/`

   **Default Login Credentials:**
   - Admin: Check database after import (or create via SQL)
   - Client: Register via `http://localhost:3000/auth/register.php`

## 🛍️ Store Generation

The platform dynamically generates static HTML stores for each client:

1. **Customize Store**: Use the admin customize page to set:
   - Primary and accent colors
   - Logo and hero background images
   - Font family and button styles
   - Product grid layout (columns)
   - Store tagline and description

2. **Generate Store**: Click "Generate Store" to create static HTML files in `backend/public/stores/store-{id}/`

3. **Access Generated Store**: Visit `http://localhost:8000/stores/store-{id}/`

4. **Features**:
   - Fully customizable branding
   - Product display with images and pricing
   - Shopping cart (localStorage)
   - Responsive design (Tailwind CSS)
   - No backend dependencies (static files)
   - Public product API for dynamic loading

## 📡 API Documentation

### Base URL

```
http://localhost:8000/api
```

### Authentication

The API uses JWT (JSON Web Tokens) for authentication. Include the token in the Authorization header:

```
Authorization: Bearer <your_jwt_token>
```

**Public Endpoints** (No authentication required):
- `POST /api/auth/admin/login`
- `POST /api/auth/client/login`
- `POST /api/auth/client/register`
- `GET /api/products` (for public store display)
- `GET /api/products/{id}` (for public store display)

**Protected Endpoints** require valid JWT token in Authorization header.

### Endpoints

#### Clients

```http
GET    /api/clients              # Get all clients
GET    /api/clients/{id}         # Get single client
POST   /api/clients              # Create client
PUT    /api/clients/{id}         # Update client
DELETE /api/clients/{id}         # Delete client
```

#### Stores

```http
GET    /api/stores               # Get all stores
GET    /api/stores/{id}          # Get single store
POST   /api/stores               # Create store
PUT    /api/stores/{id}          # Update store
DELETE /api/stores/{id}          # Delete store
POST   /api/stores/{id}/generate # Generate store files
```

#### Products

```http
GET    /api/products             # Get all products (requires store_id param)
GET    /api/products/{id}        # Get single product
POST   /api/products             # Create product
PUT    /api/products/{id}        # Update product
DELETE /api/products/{id}        # Delete product
GET    /api/products/low-stock   # Get low stock products
```

#### Orders

```http
GET    /api/orders               # Get all orders (requires store_id param)
GET    /api/orders/{id}          # Get single order
POST   /api/orders               # Create order
PUT    /api/orders/{id}/status   # Update order status
GET    /api/orders/stats         # Get order statistics
```

#### Templates

```http
GET    /api/templates            # Get all templates
GET    /api/templates/{id}       # Get single template
POST   /api/templates            # Create template (Admin only)
PUT    /api/templates/{id}       # Update template (Admin only)
DELETE /api/templates/{id}       # Delete template (Admin only)
```

#### Authentication

```http
POST   /api/auth/admin/login     # Admin login
POST   /api/auth/client/login    # Client login
POST   /api/auth/client/register # Client registration
GET    /api/auth/verify          # Verify token
POST   /api/auth/refresh         # Refresh token
POST   /api/auth/logout          # Logout
POST   /api/auth/change-password # Change password
```

#### Store Customization

```http
PUT    /api/stores/{id}/customization # Update store customization
POST   /api/stores/{id}/generate      # Generate static store files
```

### Request Example

```javascript
// Create a new store
const response = await fetch("http://localhost:8000/api/stores", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
    "Authorization": "Bearer YOUR_JWT_TOKEN"
  },
  body: JSON.stringify({
    client_id: 1,
    store_name: "My Awesome Store",
    store_slug: "my-awesome-store",
    primary_color: "#064E3B",
    accent_color: "#BEF264",
  }),
});

const data = await response.json();
```

### Response Format

```json
{
    "success": true,
    "message": "Store created successfully",
    "data": {
        "id": 1,
        "store_name": "My Awesome Store",
        "store_slug": "my-awesome-store",
        ...
    },
    "timestamp": "2026-01-26 10:30:00"
}
```

## 🛠️ Development

### Adding New Features

1. **Create Model** (if needed)

   ```php
   namespace App\Models;
   class YourModel extends Model {
       protected string $table = 'your_table';
       protected array $fillable = ['field1', 'field2'];
   }
   ```

2. **Create Controller**

   ```php
   namespace App\Controllers;
   class YourController extends Controller {
       public function index() {
           // Your logic
           $this->success($data);
       }
   }
   ```

3. **Register Routes** in `backend/public/index.php`

   ```php
   $router->get('/api/your-resource', [YourController::class, 'index']);
   ```

4. **Create Frontend JS** in `frontend/assets/js/`
   ```javascript
   const yourAPI = {
     getAll: () => api.get("/api/your-resource"),
   };
   ```

### Database Structure

The application uses MySQL database with the following main tables:
- `users` - Admin and client users
- `clients` - Client profiles and business information
- `stores` - Store configurations and settings
- `store_customization` - Store branding and design customization
- `products` - Product catalog per store
- `orders` - Order records and tracking
- `templates` - Store design templates

**Database Migrations**: Currently manual. Execute SQL files:

```bash
mysql -u root -p ecommerce_platform < database/your_migration.sql
```

## 🔒 Security Features

- ✅ PDO prepared statements (SQL injection protection)
- ✅ Password hashing (bcrypt)
- ✅ JWT authentication (tokens with expiration)
- ✅ CORS configuration
- ✅ Input validation and sanitization
- ✅ XSS protection (output escaping)
- ✅ Authentication middleware for protected routes
- ✅ Role-based access control (Admin/Client)
- 🔄 Rate limiting (planned)
- 🔄 CSRF protection (planned)

## 📝 License

MIT License - feel free to use this project for your own purposes.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📧 Support

For issues and questions, please create an issue in the repository.
````
