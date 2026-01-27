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

### MAMP Setup (macOS)

Follow these steps to set up and run the application using MAMP on macOS:

#### 1. Install MAMP

1. Download MAMP from [https://www.mamp.info/en/downloads/](https://www.mamp.info/en/downloads/)
2. Install MAMP (the free version works fine)
3. Open MAMP application

#### 2. Configure MAMP

1. **Start MAMP**:
   - Open MAMP application
   - Click "Start" to start Apache and MySQL servers
   - Wait for both servers to turn green

2. **Set Document Root**:
   - Click "MAMP" → "Preferences" (or press `Cmd + ,`)
   - Go to "Web Server" tab
   - Click "Select" next to "Document Root"
   - Navigate to and select your project folder: `/Users/YourUsername/path/to/e-commerce-store`
   - Click "OK" to save

3. **Check PHP Version**:
   - In Preferences, go to "PHP" tab
   - Ensure PHP 8.0 or higher is selected
   - Click "OK"

#### 3. Clone/Place the Project

```bash
# Navigate to your desired location (e.g., Documents)
cd ~/Documents/LivePetal\ Projects/

# If cloning for the first time
git clone <repository-url> e-commerce-store
cd e-commerce-store
```

#### 4. Setup Database

1. **Access phpMyAdmin**:
   - Open your browser
   - Go to `http://localhost:8888/phpMyAdmin/` (default MAMP port)
   - Default credentials:
     - Username: `root`
     - Password: `root`

2. **Create Database**:
   - Click "New" in the left sidebar
   - Database name: `ecommerce_platform`
   - Collation: `utf8mb4_general_ci`
   - Click "Create"

3. **Import Schema**:
   - Select `ecommerce_platform` database from the left sidebar
   - Click "Import" tab
   - Click "Choose File"
   - Navigate to `e-commerce-store/backend/database/schema.sql`
   - Click "Go" at the bottom of the page
   - Wait for success message

4. **Import Default Templates** (Optional):
   - Still in the Import tab
   - Import `backend/database/insert_default_templates.sql`
   - Click "Go"

#### 5. Configure Application

Edit the database configuration file:

```bash
# Open config file in your preferred editor
nano backend/config/config.php
# or
open -a "Visual Studio Code" backend/config/config.php
```

Update the database settings:

```php
'database' => [
    'host' => 'localhost',
    'name' => 'ecommerce_platform',
    'username' => 'root',
    'password' => 'root',  // MAMP default password
    'charset' => 'utf8mb4',
    'port' => '8889'        // MAMP default MySQL port (or 3306 for some versions)
],
```

**Note**: Check your MAMP start page (`http://localhost:8888/MAMP/`) to verify the MySQL port. It's usually `8889` for MAMP but can be `3306`.

#### 6. Set Folder Permissions

```bash
# Navigate to project root
cd ~/Documents/LivePetal\ Projects/e-commerce-store

# Set permissions for store files
chmod -R 755 backend/public/stores/

# Create uploads directory if it doesn't exist
mkdir -p uploads
chmod -R 755 uploads/
```

#### 7. Start the Application

Open **two separate Terminal windows**:

**Terminal 1 - Backend API Server:**
```bash
cd ~/Documents/LivePetal\ Projects/e-commerce-store/backend/public
php -S localhost:8000 router.php
```

You should see:
```
PHP 8.x.x Development Server (http://localhost:8000) started
```

**Terminal 2 - Frontend Server:**
```bash
cd ~/Documents/LivePetal\ Projects/e-commerce-store
php -S localhost:3000 -t frontend
```

You should see:
```
PHP 8.x.x Development Server (http://localhost:3000) started
```

**Important**: Keep both terminal windows running while using the application.

#### 8. Access the Application

Open your browser and navigate to:

- **Frontend (Login/Register)**: `http://localhost:3000/auth/login.php`
- **Admin Dashboard**: `http://localhost:3000/admin/dashboard.php`
- **Client Dashboard**: `http://localhost:3000/client/dashboard.php`
- **API Documentation**: `http://localhost:8000/docs.html`
- **API Base URL**: `http://localhost:8000/api`
- **Generated Stores**: `http://localhost:8000/stores/store-{id}/`

#### 9. Create Admin User

If no admin user exists, create one via phpMyAdmin:

1. Go to `http://localhost:8888/phpMyAdmin/`
2. Select `ecommerce_platform` database
3. Click on `users` table
4. Click "Insert" tab
5. Fill in:
   - `username`: admin
   - `email`: admin@example.com
   - `password`: Use this PHP snippet to generate hash:
     ```bash
     php -r "echo password_hash('admin123', PASSWORD_BCRYPT);"
     ```
   - `role`: super_admin
   - `is_active`: 1
6. Click "Go"

Now you can login with:
- Username: `admin`
- Password: `admin123`

#### 10. Troubleshooting

**"php: command not found" Error**:

If you get this error, it means PHP is not in your system PATH. MAMP includes its own PHP, so you need to use the full path:

**Option 1: Use MAMP's PHP directly (Recommended)**
```bash
# For MAMP (adjust version number to match your MAMP PHP version)
/Applications/MAMP/bin/php/php8.2.0/bin/php -S localhost:8000 router.php

# To find your exact PHP version in MAMP:
ls /Applications/MAMP/bin/php/
```

**Option 2: Add MAMP PHP to PATH temporarily**
```bash
# Add to current terminal session only
export PATH=/Applications/MAMP/bin/php/php8.2.0/bin:$PATH

# Now you can use php command normally
php -S localhost:8000 router.php
```

**Option 3: Add MAMP PHP to PATH permanently**
```bash
# Open your shell profile
nano ~/.zshrc  # for zsh (macOS default)
# or
nano ~/.bash_profile  # for bash

# Add this line (adjust version to match yours):
export PATH=/Applications/MAMP/bin/php/php8.2.0/bin:$PATH

# Save and reload
source ~/.zshrc  # or source ~/.bash_profile
```

**Database Connection Error**:
- Verify MAMP MySQL is running (green light in MAMP)
- Check MySQL port in MAMP start page
- Update `backend/config/config.php` with correct port
- Verify database name is `ecommerce_platform`

**Port Already in Use**:
```bash
# If port 8000 or 3000 is taken, use different ports:
php -S localhost:8001 router.php  # Backend
php -S localhost:3001 -t frontend  # Frontend
```

**Permission Denied Errors**:
```bash
# Reset permissions
chmod -R 755 backend/public/stores/
chmod -R 755 uploads/
```

**White Screen/500 Error**:
- Check PHP error logs in MAMP: `Applications/MAMP/logs/php_error.log`
- Enable error display in `backend/config/config.php`:
  ```php
  'app' => [
      'debug' => true,
      'environment' => 'development',
  ]
  ```

**API Not Working**:
- Ensure backend server is running on port 8000
- Check `frontend/assets/js/core/api.js` has correct API URL
- Verify CORS is enabled in `backend/middleware/CorsMiddleware.php`

#### 11. Stopping the Application

To stop the servers:

1. **Stop PHP Development Servers**:
   - Press `Ctrl + C` in both Terminal windows

2. **Stop MAMP** (Optional):
   - Open MAMP application
   - Click "Stop" to stop Apache and MySQL

**Note**: You can keep MAMP running for database access via phpMyAdmin.

### Alternative Setup (Apache/Nginx Virtual Host)

For production or Apache/Nginx virtual host setup, follow these steps:

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

## � Deployment to cPanel

### Prerequisites

- cPanel hosting account with PHP 8.0+ support
- MySQL database access
- SSH access (optional, but recommended)
- Domain or subdomain configured

### Step 1: Prepare Your Project

Before uploading, prepare your project locally:

1. **Remove Development Files**:
   ```bash
   # Remove unnecessary files
   rm -rf .git
   rm -rf node_modules
   rm .env.example
   ```

2. **Create Production Configuration**:
   - Make a copy of your working `config.php`
   - Ensure debug mode is disabled:
     ```php
     'app' => [
         'debug' => false,
         'environment' => 'production',
     ]
     ```

### Step 2: Upload Files to cPanel

**Option A: Using File Manager (Recommended for beginners)**

1. **Login to cPanel**:
   - Go to your hosting provider's cPanel URL
   - Enter your credentials

2. **Navigate to File Manager**:
   - Click "File Manager" in the Files section
   - Navigate to `public_html` (or your domain's root directory)

3. **Create Project Structure**:
   - Create a folder structure like this:
     ```
     public_html/
     ├── api/                    # Backend files (from backend/public/)
     │   ├── index.php
     │   ├── .htaccess
     │   ├── openapi.json
     │   ├── docs.html
     │   └── stores/
     ├── app/                    # Frontend files (from frontend/)
     │   ├── auth/
     │   ├── admin/
     │   ├── client/
     │   ├── assets/
     │   └── shared/
     ├── backend/                # Backend core (non-public files)
     │   ├── config/
     │   ├── controllers/
     │   ├── models/
     │   ├── services/
     │   ├── middleware/
     │   ├── helpers/
     │   ├── core/
     │   └── bootstrap.php
     ├── vendor/                 # If using Composer
     ├── store-templates/
     └── .htaccess               # Root .htaccess
     ```

4. **Upload Files**:
   - Click "Upload" in File Manager
   - Upload all backend files to `backend/` folder
   - Upload `backend/public/*` files to `api/` folder
   - Upload all frontend files to `app/` folder
   - Upload vendor folder and other root files

**Option B: Using FTP/SFTP (Recommended for large projects)**

1. **Use FileZilla or similar FTP client**:
   - Host: Your domain or FTP hostname
   - Username: Your cPanel username
   - Password: Your cPanel password
   - Port: 21 (FTP) or 22 (SFTP)

2. **Upload Structure**:
   - Connect to your server
   - Navigate to `public_html/`
   - Upload files according to the structure above

**Option C: Using SSH and Git (Advanced)**

```bash
# SSH into your server
ssh username@yourdomain.com

# Navigate to public_html
cd public_html

# Clone your repository
git clone <your-repo-url> temp
mv temp/* .
mv temp/.* .
rm -rf temp

# Reorganize files as per structure above
```

### Step 3: Setup MySQL Database

1. **Create Database**:
   - In cPanel, click "MySQL Databases"
   - Create a new database (e.g., `username_ecommerce`)
   - Note: cPanel adds your username prefix automatically

2. **Create Database User**:
   - In the same page, create a new MySQL user
   - Set a strong password
   - Click "Create User"

3. **Grant Privileges**:
   - Scroll to "Add User to Database"
   - Select your user and database
   - Click "Add"
   - Grant "ALL PRIVILEGES"
   - Click "Make Changes"

4. **Import Database Schema**:

   **Option A: Using phpMyAdmin**:
   - In cPanel, click "phpMyAdmin"
   - Select your database from the left sidebar
   - Click "Import" tab
   - Choose `backend/database/schema.sql`
   - Click "Go"
   - Repeat for `insert_default_templates.sql`

   **Option B: Using SSH**:
   ```bash
   mysql -u username_dbuser -p username_ecommerce < backend/database/schema.sql
   ```

### Step 4: Configure Application

1. **Update Database Configuration**:
   
   Edit `backend/config/config.php`:
   ```php
   'database' => [
       'host' => 'localhost',  // Usually localhost on cPanel
       'name' => 'username_ecommerce',  // Your database name with prefix
       'username' => 'username_dbuser',  // Your database user with prefix
       'password' => 'your_strong_password',
       'charset' => 'utf8mb4',
       'port' => '3306'
   ],
   
   'app' => [
       'name' => 'E-commerce Platform',
       'url' => 'https://yourdomain.com',  // Your actual domain
       'api_url' => 'https://yourdomain.com/api',  // API endpoint
       'debug' => false,  // MUST be false in production
       'environment' => 'production',
   ],
   
   'jwt' => [
       'secret' => 'CHANGE_THIS_TO_RANDOM_STRING',  // Generate new secret!
       'algorithm' => 'HS256',
       'expiration' => 86400, // 24 hours
   ],
   
   'cors' => [
       'allowed_origins' => [
           'https://yourdomain.com',
           'https://www.yourdomain.com'
       ],
       'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
       'allowed_headers' => ['Content-Type', 'Authorization'],
       'credentials' => true,
   ]
   ```

2. **Generate New JWT Secret**:
   ```bash
   # Run this to generate a random secret
   php -r "echo bin2hex(random_bytes(32));"
   ```

3. **Update Frontend API Configuration**:
   
   Edit `app/assets/js/core/api.js`:
   ```javascript
   const API_BASE_URL = 'https://yourdomain.com/api';
   ```

### Step 5: Configure .htaccess Files

1. **Root .htaccess** (`public_html/.htaccess`):
   ```apache
   # Redirect HTTP to HTTPS
   RewriteEngine On
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   
   # Redirect root to app
   RewriteCond %{REQUEST_URI} ^/$
   RewriteRule ^(.*)$ /app/ [L]
   ```

2. **API .htaccess** (`public_html/api/.htaccess`):
   ```apache
   <IfModule mod_rewrite.c>
       RewriteEngine On
       
       # Handle OPTIONS requests
       RewriteCond %{REQUEST_METHOD} OPTIONS
       RewriteRule ^(.*)$ $1 [R=200,L]
       
       # Route all requests to index.php
       RewriteCond %{REQUEST_FILENAME} !-f
       RewriteCond %{REQUEST_FILENAME} !-d
       RewriteRule ^(.*)$ index.php [QSA,L]
   </IfModule>
   
   # Security Headers
   <IfModule mod_headers.c>
       Header set Access-Control-Allow-Origin "*"
       Header set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
       Header set Access-Control-Allow-Headers "Content-Type, Authorization"
       Header set X-Content-Type-Options "nosniff"
       Header set X-Frame-Options "SAMEORIGIN"
       Header set X-XSS-Protection "1; mode=block"
   </IfModule>
   
   # Deny access to sensitive files
   <FilesMatch "^\.">
       Order allow,deny
       Deny from all
   </FilesMatch>
   
   # PHP Settings
   <IfModule mod_php7.c>
       php_value upload_max_filesize 20M
       php_value post_max_size 25M
       php_value max_execution_time 300
       php_value memory_limit 256M
   </IfModule>
   ```

3. **Backend .htaccess** (`public_html/backend/.htaccess`):
   ```apache
   # Deny all direct access to backend folder
   Order deny,allow
   Deny from all
   ```

### Step 6: Set Folder Permissions

Using File Manager or SSH, set these permissions:

```bash
# Via SSH
cd ~/public_html

# Set folder permissions
chmod 755 api/
chmod 755 api/stores/
chmod 755 backend/
chmod 644 backend/config/config.php  # Protect config file

# Create and set uploads directory
mkdir -p uploads
chmod 755 uploads/

# Make stores directory writable
chmod 775 api/stores/
```

**Using cPanel File Manager**:
- Right-click folder → "Change Permissions"
- Folders: 755 (rwxr-xr-x)
- Files: 644 (rw-r--r--)
- `api/stores/`: 775 (rwxrwxr-x)

### Step 7: Update PHP Settings (if needed)

1. **Create php.ini** in root:
   
   Create `public_html/php.ini`:
   ```ini
   upload_max_filesize = 20M
   post_max_size = 25M
   max_execution_time = 300
   memory_limit = 256M
   display_errors = Off
   log_errors = On
   error_log = /home/username/public_html/error_log
   ```

2. **Or use .user.ini** (some hosts prefer this):
   ```ini
   upload_max_filesize = 20M
   post_max_size = 25M
   ```

### Step 8: Create Admin User

Connect to your database via phpMyAdmin and run:

```sql
INSERT INTO users (username, email, password, role, is_active, created_at) 
VALUES (
    'admin',
    'admin@yourdomain.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',  -- password: 'password'
    'super_admin',
    1,
    NOW()
);
```

**Generate a new password hash**:
```bash
php -r "echo password_hash('your_secure_password', PASSWORD_BCRYPT);"
```

### Step 9: Test the Deployment

1. **Test API**:
   - Visit `https://yourdomain.com/api`
   - Should return a JSON response or API info

2. **Test API Documentation**:
   - Visit `https://yourdomain.com/api/docs.html`
   - Swagger UI should load

3. **Test Frontend**:
   - Visit `https://yourdomain.com/app/auth/login.php`
   - Login page should display

4. **Test Authentication**:
   - Login with admin credentials
   - Should redirect to dashboard

5. **Test Store Generation**:
   - Create a test store
   - Generate store files
   - Visit `https://yourdomain.com/api/stores/store-1/`

### Step 10: Enable SSL/HTTPS (Highly Recommended)

1. **Using cPanel SSL**:
   - Go to cPanel → "SSL/TLS"
   - Click "Manage SSL sites"
   - Select your domain
   - Install Let's Encrypt certificate (usually free)

2. **Force HTTPS**:
   - Ensure root `.htaccess` redirects HTTP to HTTPS
   - Update all URLs in config to use `https://`

### Troubleshooting cPanel Deployment

**500 Internal Server Error**:
```bash
# Check error logs
tail -f ~/public_html/error_log

# Common fixes:
# 1. Wrong file permissions
chmod 644 api/.htaccess
chmod 755 api/

# 2. PHP syntax error - enable error display temporarily
# Add to api/index.php (REMOVE after fixing):
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

**Database Connection Failed**:
- Verify database name includes cPanel username prefix
- Check user has been added to database
- Verify password is correct
- Try 'localhost', '127.0.0.1', or your server's hostname

**CORS Errors**:
- Check `backend/middleware/CorsMiddleware.php`
- Verify allowed origins include your domain
- Ensure `.htaccess` has CORS headers

**Routes Not Working (404 errors)**:
- Verify `mod_rewrite` is enabled (contact hosting support)
- Check `.htaccess` file is uploaded to `api/` folder
- Verify `.htaccess` syntax is correct

**Store Generation Fails**:
```bash
# Check folder permissions
chmod 775 api/stores/

# Check if directory exists
mkdir -p api/stores
chown username:username api/stores
```

**File Upload Issues**:
```bash
# Create uploads directory
mkdir -p uploads
chmod 775 uploads

# Check PHP limits in php.ini
```

**Performance Issues**:
- Enable OPcache in cPanel → Select PHP Version → Extensions
- Enable compression in `.htaccess`:
  ```apache
  <IfModule mod_deflate.c>
      AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
  </IfModule>
  ```

### Post-Deployment Checklist

- [ ] Database imported successfully
- [ ] Admin user created and can login
- [ ] API endpoints responding correctly
- [ ] Frontend pages loading properly
- [ ] SSL/HTTPS enabled and working
- [ ] Store generation working
- [ ] File uploads working
- [ ] Error logging configured
- [ ] Backup strategy in place
- [ ] Debug mode disabled
- [ ] Strong passwords set for database and admin
- [ ] JWT secret changed from default
- [ ] CORS configured for your domain
- [ ] File permissions set correctly

### Backup Strategy

1. **Database Backups** (cPanel):
   - cPanel → Backup Wizard
   - Schedule automatic backups
   - Download backups regularly

2. **File Backups**:
   ```bash
   # Create a backup
   tar -czf backup-$(date +%Y%m%d).tar.gz public_html/
   
   # Download via FTP or cPanel File Manager
   ```

3. **Automated Backups** (Advanced):
   - Set up cron job for daily backups
   - Use cPanel backup tools
   - Consider third-party backup services

### Security Best Practices

1. **Protect Config Files**:
   ```apache
   # Add to backend/.htaccess
   <Files config.php>
       Order allow,deny
       Deny from all
   </Files>
   ```

2. **Hide Backend Directory**:
   - Keep backend folder outside public_html if possible
   - Or use .htaccess to deny access

3. **Regular Updates**:
   - Keep PHP version updated
   - Update dependencies regularly
   - Monitor security advisories

4. **Monitor Logs**:
   - Check error logs regularly
   - Set up log rotation
   - Monitor access logs for suspicious activity

### Alternative: Subdomain Setup

To use a subdomain for API:

1. **Create Subdomain**:
   - cPanel → Domains → Subdomains
   - Create: `api.yourdomain.com`
   - Document root: `public_html/api`

2. **Update Configuration**:
   ```php
   'app' => [
       'api_url' => 'https://api.yourdomain.com',
   ]
   ```

3. **Update Frontend**:
   ```javascript
   const API_BASE_URL = 'https://api.yourdomain.com';
   ```

## �🛍️ Store Generation

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
