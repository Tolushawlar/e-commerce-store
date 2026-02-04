# E-Commerce Multi-Tenant Platform

A modern, feature-rich multi-tenant e-commerce platform built with PHP MVC architecture. This platform enables businesses to create and manage multiple online stores with dedicated client dashboards, comprehensive product management, and a powerful admin panel.

## 🚀 Features

### 🎯 Core Platform Features

- **Multi-Tenant Architecture** - Support for unlimited stores and clients
- **JWT Authentication** - Secure token-based authentication for admin, clients, and customers
- **RESTful API** - Complete REST API with Swagger/OpenAPI documentation
- **Role-Based Access Control** - Separate dashboards for Super Admin, Clients, and Customers
- **Dark Mode Support** - Full dark/light theme switching across all interfaces
- **Real-time Analytics** - Comprehensive dashboards with charts and statistics
- **Responsive Design** - Mobile-first design using Tailwind CSS

### 👨‍💼 Super Admin Features

- **Client Management** - Create, edit, and manage client accounts
- **Store Management** - Monitor all stores across the platform
- **Template System** - Pre-built store templates (Bold Modern, Classic, Minimal, Premium Luxury)
- **Category Management** - Platform-wide category management with hierarchies
- **Platform Analytics** - Revenue tracking, order statistics, and performance metrics
- **User Management** - Comprehensive user role and permission management

### 🏪 Client Dashboard Features

- **Multi-Store Management** - Create and manage multiple online stores
- **Product Management** - Full CRUD operations with:
  - Cloudinary image upload (up to 5 images per product)
  - Bulk image management
  - Category assignment
  - Stock tracking
  - Pricing controls
- **Order Management** - Track and manage customer orders with:
  - Order status updates
  - Payment tracking
  - Order details view
  - Statistics and analytics
- **Category System** - Create hierarchical categories with:
  - Custom icons (Material Symbols)
  - Color coding
  - Parent-child relationships
  - Display order management
- **Store Customization** - Customize store appearance and settings
- **Analytics Dashboard** - Revenue trends, order statistics, and performance metrics with charts
- **Customer Management** - View and manage store customers

### 🛒 Customer Store Features

- **Product Browsing** - Browse products with search and filtering
- **Shopping Cart** - Add to cart, update quantities, remove items
- **Checkout System** - Complete checkout with Paystack payment integration
- **Customer Accounts** - Registration, login, and profile management
- **Order History** - View past orders and track status
- **Multiple Addresses** - Save and manage shipping addresses
- **Order Tracking** - Real-time order status updates

### 🔧 Developer Features

- **MVC Architecture** - Clean separation of concerns
- **PSR-4 Autoloading** - Modern PHP class autoloading
- **Middleware System** - CORS and authentication middleware
- **Error Handling** - Sentry integration for error tracking
- **API Documentation** - Auto-generated OpenAPI/Swagger docs
- **Environment Configuration** - .env file support
- **Database Migrations** - Version-controlled database changes

## 🛠 Tech Stack

### Backend

- **PHP 8.0+** - Modern PHP with type declarations
- **MySQL/MariaDB** - Relational database
- **Composer** - Dependency management
- **JWT** - Token-based authentication
- **Cloudinary PHP SDK** - Cloud-based image management
- **Sentry PHP** - Error tracking and monitoring
- **Swagger PHP** - API documentation generation

### Frontend

- **Vanilla JavaScript** - No framework dependencies
- **Tailwind CSS** - Utility-first CSS framework
- **Material Symbols** - Icon system
- **Chart.js** - Data visualization
- **Fetch API** - HTTP requests
- **Service-based Architecture** - Modular JavaScript services

### Services & Integrations

- **Cloudinary** - Image hosting and optimization
- **Paystack** - Payment processing
- **Sentry** - Error tracking
- **JWT** - Authentication tokens

## 📁 Project Structure

```
e-commerce-store/
├── api/                          # API endpoint entry point
│   ├── index.php                # Main API router
│   ├── router.php               # Development server router
│   ├── docs.html                # Swagger UI documentation
│   ├── openapi.json             # OpenAPI specification
│   └── stores/                  # Per-store static files
│
├── app/                          # Frontend application
│   ├── index.php                # Root redirect
│   ├── auth/                    # Authentication pages
│   │   ├── login.php           # Login (admin/client)
│   │   └── register.php        # Client registration
│   ├── admin/                   # Super admin dashboard
│   │   ├── dashboard.php       # Admin analytics
│   │   ├── clients.php         # Client management
│   │   ├── stores.php          # Store monitoring
│   │   ├── categories.php      # Category management
│   │   ├── templates.php       # Template management
│   │   ├── create-store.php    # Store creation
│   │   ├── edit-store.php      # Store editing
│   │   └── customize-store.php # Store customization
│   ├── client/                  # Client dashboard
│   │   ├── dashboard.php       # Client analytics
│   │   ├── stores.php          # My stores
│   │   ├── products.php        # Product management
│   │   ├── orders.php          # Order management
│   │   ├── categories.php      # Category management
│   │   └── store-settings.php  # Store settings
│   ├── shared/                  # Shared layouts
│   │   ├── header-admin.php    # Admin header
│   │   ├── footer-admin.php    # Admin footer
│   │   ├── header-client.php   # Client header
│   │   └── footer-client.php   # Client footer
│   ├── store/                   # Customer storefront (future)
│   └── assets/                  # Frontend assets
│       └── js/                  # JavaScript files
│           ├── core/           # Core utilities
│           ├── services/       # API services
│           │   ├── store.service.js
│           │   ├── product.service.js
│           │   ├── order.service.js
│           │   ├── category.service.js
│           │   ├── client-orders.js
│           │   ├── dashboard.service.js
│           │   └── image.service.js
│           └── utils/          # Helper utilities
│
├── backend/                      # Backend PHP code
│   ├── bootstrap.php            # Application bootstrap
│   ├── swagger.php              # Swagger configuration
│   ├── config/                  # Configuration files
│   │   ├── config.php          # Main configuration
│   │   ├── Database.php        # Database connection
│   │   └── sentry.php          # Sentry setup
│   ├── core/                    # Core classes
│   │   ├── Controller.php      # Base controller
│   │   ├── Model.php           # Base model
│   │   └── Router.php          # Router class
│   ├── controllers/             # API controllers
│   │   ├── AuthController.php
│   │   ├── ClientController.php
│   │   ├── StoreController.php
│   │   ├── ProductController.php
│   │   ├── OrderController.php
│   │   ├── CategoryController.php
│   │   ├── TemplateController.php
│   │   ├── ImageController.php
│   │   ├── CartController.php
│   │   ├── CheckoutController.php
│   │   ├── CustomerController.php
│   │   ├── DashboardController.php
│   │   ├── AddressController.php
│   │   ├── PaymentController.php
│   │   └── AdminOrderController.php
│   ├── models/                  # Data models
│   │   ├── Client.php
│   │   ├── Store.php
│   │   ├── Product.php
│   │   ├── Order.php
│   │   ├── Category.php
│   │   ├── Template.php
│   │   ├── SuperAdmin.php
│   │   ├── StoreCustomer.php
│   │   ├── ShoppingCart.php
│   │   └── CustomerAddress.php
│   ├── middleware/              # Middleware
│   │   ├── AuthMiddleware.php  # JWT authentication
│   │   └── CorsMiddleware.php  # CORS handling
│   ├── services/                # Business logic services
│   │   ├── CloudinaryService.php
│   │   ├── CustomerJWTService.php
│   │   ├── PaystackService.php
│   │   └── StoreGeneratorService.php
│   ├── helpers/                 # Helper functions
│   │   └── functions.php       # Utility functions
│   └── database/                # Database migrations
│       ├── schema.sql
│       ├── add_categories_table.sql
│       ├── add_store_category_settings.sql
│       └── [other migrations]
│
├── store-templates/              # Store template files
│   ├── bold-modern.html
│   ├── classic-ecommerce.html
│   ├── minimal-clean.html
│   ├── premium-luxury.html
│   ├── campmart-style.html
│   ├── cart.html
│   ├── checkout.html
│   ├── orders.html
│   ├── profile.html
│   ├── login.html
│   └── order-success.html
│
├── md-docs/                      # Documentation
│   ├── INSTALLATION.md
│   ├── ARCHITECTURE-DIAGRAM.md
│   ├── API-DOCUMENTATION.md
│   ├── STRUCTURE.md
│   ├── CATEGORY-SYSTEM.md
│   ├── CLOUDINARY-IMPLEMENTATION-SUMMARY.md
│   ├── JWT-AUTHENTICATION.md
│   ├── TEMPLATE-SYSTEM.md
│   ├── CHECKOUT-SYSTEM.md
│   ├── CUSTOMER-SYSTEM.md
│   └── [other docs]
│
├── vendor/                       # Composer dependencies
├── node_modules/                 # NPM dependencies
├── composer.json                 # PHP dependencies
├── package.json                  # Node.js scripts
├── .env.example                  # Environment template
├── .env                          # Environment config
├── .gitignore                    # Git ignore rules
└── .htaccess                     # Apache configuration
```

## 🚀 Getting Started

### Prerequisites

- PHP 8.0 or higher
- MySQL 5.7+ or MariaDB
- Composer
- Node.js (for development scripts)
- Cloudinary account (for image uploads)
- Paystack account (for payments)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/Tolushawlar/e-commerce-store.git
   cd e-commerce-store
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   # Edit .env with your database and API credentials
   ```

4. **Setup database**
   - Create a MySQL database
   - Import `backend/database/schema.sql`
   - Run migrations in `backend/database/`

5. **Configure Cloudinary**
   - Add your Cloudinary credentials to `.env`:
     ```
     CLOUDINARY_CLOUD_NAME=your_cloud_name
     CLOUDINARY_API_KEY=your_api_key
     CLOUDINARY_API_SECRET=your_api_secret
     ```

6. **Start development servers**
   ```bash
   npm run dev
   ```
   This starts:
   - API server on `http://localhost:8000`
   - Frontend server on `http://localhost:3000`

### Default Access

**Super Admin**
- URL: `http://localhost:3000/admin/`
- Create admin account via database or use registration

**Client Dashboard**
- URL: `http://localhost:3000/client/`
- Register at `http://localhost:3000/auth/register.php`

**API Documentation**
- URL: `http://localhost:8000/docs.html`
- Swagger UI with interactive API testing

## 📖 API Documentation

The platform includes comprehensive API documentation:

- **Swagger UI**: `http://localhost:8000/docs.html`
- **OpenAPI Spec**: `http://localhost:8000/openapi.json`
- **Detailed Docs**: See `md-docs/API-DOCUMENTATION.md`

### Main API Endpoints

```
Authentication
POST   /api/auth/login          # Login (admin/client)
POST   /api/auth/register       # Register client
POST   /api/auth/verify         # Verify JWT token

Clients
GET    /api/clients             # List all clients
POST   /api/clients             # Create client
GET    /api/clients/{id}        # Get client details
PUT    /api/clients/{id}        # Update client
DELETE /api/clients/{id}        # Delete client

Stores
GET    /api/stores              # List stores
POST   /api/stores              # Create store
GET    /api/stores/{id}         # Get store details
PUT    /api/stores/{id}         # Update store
DELETE /api/stores/{id}         # Delete store

Products
GET    /api/products            # List products
POST   /api/products            # Create product
GET    /api/products/{id}       # Get product details
PUT    /api/products/{id}       # Update product
DELETE /api/products/{id}       # Delete product

Orders
GET    /api/orders              # List orders
GET    /api/orders/{id}         # Get order details
PUT    /api/orders/{id}/status  # Update order status

Categories
GET    /api/categories          # List categories
POST   /api/categories          # Create category
PUT    /api/categories/{id}     # Update category
DELETE /api/categories/{id}     # Delete category

Images
POST   /api/images/upload       # Upload image to Cloudinary
DELETE /api/images/delete       # Delete image from Cloudinary

Dashboard
GET    /api/dashboard/stats     # Get dashboard statistics
```

## 🔐 Security Features

- **JWT Authentication** - Secure token-based auth
- **Password Hashing** - bcrypt password hashing
- **CORS Protection** - Configurable CORS middleware
- **SQL Injection Prevention** - PDO prepared statements
- **XSS Protection** - Output escaping
- **CSRF Protection** - Token validation
- **Rate Limiting** - API rate limiting (configurable)
- **Environment Variables** - Sensitive data in .env

## 📊 Database Schema

The platform uses a normalized MySQL database with the following main tables:

- `super_admins` - Platform administrators
- `clients` - Store owners/clients
- `stores` - Individual online stores
- `products` - Product catalog
- `orders` - Customer orders
- `order_items` - Order line items
- `categories` - Product categories
- `templates` - Store templates
- `store_customers` - Customer accounts
- `shopping_carts` - Shopping cart items
- `customer_addresses` - Delivery addresses

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 👥 Authors

- **Tolushawlar** - *Initial work* - [GitHub](https://github.com/Tolushawlar)

## 🙏 Acknowledgments

- Design inspired by CampMart marketplace
- Built with modern PHP best practices
- Uses Cloudinary for image management
- Integrated with Paystack for payments
- Material Symbols for icons
- Chart.js for analytics visualization

## 📧 Support

For support and questions:
- Open an issue on GitHub
- Check the documentation in `md-docs/`
- Review the API documentation at `/docs.html`

## 🗺 Roadmap

- [ ] Multi-currency support
- [ ] Advanced analytics and reporting
- [ ] Email notifications
- [ ] SMS notifications
- [ ] Inventory management
- [ ] Shipping integrations
- [ ] Tax calculation
- [ ] Discount/coupon system
- [ ] Product reviews and ratings
- [ ] Wishlist functionality
- [ ] Advanced search and filters
- [ ] SEO optimization
- [ ] PWA support