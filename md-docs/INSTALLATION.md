# Ecommerce Platform - Installation Guide

## Prerequisites
- MAMP/XAMPP/WAMP server
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web browser

## Installation Steps

### 1. Download and Setup
1. Clone or download the project to your MAMP/XAMPP htdocs folder
2. Ensure the project is in `/Applications/MAMP/htdocs/ecommerce-platform/`

### 2. Database Setup
1. Start MAMP/XAMPP
2. Open phpMyAdmin (usually at http://localhost:8888/phpMyAdmin/)
3. Import the database schema:
   - Go to the SQL tab
   - Copy and paste the contents of `database/schema.sql`
   - Click "Go" to execute

### 3. Configuration
1. Update database credentials in `config/database.php` if needed:
   ```php
   private $host = 'localhost';
   private $db_name = 'ecommerce_platform';
   private $username = 'root';
   private $password = 'root'; // Change if different
   ```

### 4. Access the Platform

#### Super Admin Dashboard
- URL: `http://localhost:8888/ecommerce-platform/super-admin/`
- Default login: admin@platform.com / password

#### Client Dashboard
- URL: `http://localhost:8888/ecommerce-platform/client-dashboard/`
- Create clients through super admin panel

#### API Endpoints
- Base URL: `http://localhost:8888/ecommerce-platform/api/`
- Available endpoints:
  - `/products` - Product management
  - `/orders` - Order management
  - `/clients` - Client management
  - `/stores` - Store management

## Features Overview

### Super Admin Features
- ✅ Dashboard with platform statistics
- ✅ Client management (add, edit, delete)
- ✅ Store management and monitoring
- ✅ Template management
- ✅ Platform analytics
- ✅ User role management

### Client Dashboard Features
- ✅ Store overview and statistics
- ✅ Product management (add, edit, delete)
- ✅ Order management and tracking
- ✅ Customer management
- ✅ Store customization
- ✅ Analytics and reporting

### Store Features (Customer-facing)
- ✅ Responsive ecommerce storefront
- ✅ Product catalog and search
- ✅ Shopping cart functionality
- ✅ Customer registration/login
- ✅ Order placement and tracking
- ✅ Multiple payment options

## Customization

### Store Templates
- Templates are stored in `/store-templates/`
- Use template variables like `{{store_name}}`, `{{primary_color}}`
- Create new templates by copying and modifying existing ones

### Styling
- Main styles in `/assets/css/style.css`
- Uses Tailwind CSS for rapid development
- CampMart-inspired design system

### API Extensions
- Add new endpoints in `/api/index.php`
- Follow RESTful conventions
- Include proper error handling and validation

## Troubleshooting

### Common Issues
1. **Database Connection Error**
   - Check MAMP/XAMPP is running
   - Verify database credentials in config
   - Ensure database exists

2. **Permission Errors**
   - Check file permissions on uploads folder
   - Ensure web server can write to necessary directories

3. **API Not Working**
   - Check .htaccess configuration
   - Verify API endpoints are accessible
   - Check browser console for errors

### Support
For technical support or feature requests, please refer to the project documentation or contact the development team.

## Security Notes
- Change default admin credentials immediately
- Use HTTPS in production
- Implement proper input validation
- Regular security updates recommended