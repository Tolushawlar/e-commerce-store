# cPanel Deployment Guide

Complete guide for deploying the e-commerce platform to cPanel with wildcard subdomain support for multi-tenant stores.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [DNS Configuration](#dns-configuration)
3. [Directory Structure](#directory-structure)
4. [Wildcard Subdomain Setup](#wildcard-subdomain-setup)
5. [File Upload](#file-upload)
6. [Composer Installation](#composer-installation)
7. [Database Setup](#database-setup)
8. [Environment Configuration](#environment-configuration)
9. [File Permissions](#file-permissions)
10. [SSL Certificate](#ssl-certificate)
11. [Store Generation Workflow](#store-generation-workflow)
12. [Testing](#testing)
13. [Cron Jobs](#cron-jobs)
14. [Troubleshooting](#troubleshooting)

---

## Prerequisites

- cPanel account with:
  - PHP 8.2 or higher
  - MySQL/MariaDB database access
  - SSH access (recommended) or FTP
  - Ability to create addon/wildcard domains
  - SSL certificate support
- Domain name (e.g., livepetal.com)
- Access to domain DNS settings

---

## DNS Configuration

Configure DNS records for your domain to support wildcard subdomains:

### Add Wildcard A Record

In your domain registrar's DNS settings:

```
Type: A Record
Host: @
Points to: Your cPanel Server IP
TTL: 3600 (or default)

Type: A Record
Host: *
Points to: Your cPanel Server IP
TTL: 3600 (or default)
```

**Example for Cloudflare:**

- `@` → `198.51.100.1` (your server IP)
- `*` → `198.51.100.1` (same IP)

**Propagation:** DNS changes can take 1-48 hours to fully propagate.

---

## Directory Structure

Recommended structure in your cPanel account:

```
public_html/                 # Main website root
├── .htaccess               # Main routing and security rules
├── index.html              # Landing page (from app/index.html)
├── api/                    # Backend API
│   ├── index.php          # API gateway
│   ├── .htaccess          # API-specific rules
│   └── stores/            # Generated stores directory
│       └── {store-slug}/  # Individual store files
│           ├── index.html
│           ├── products.html
│           └── ...
├── admin/                  # Admin dashboard
│   └── index.html
├── assets/                 # Static assets
│   ├── css/
│   ├── js/
│   └── images/
├── backend/                # PHP backend files
│   ├── controllers/
│   ├── models/
│   ├── services/
│   ├── middleware/
│   ├── helpers/
│   ├── config/
│   └── database/
├── vendor/                 # Composer dependencies
├── storage/               # Writable storage
│   └── rate_limits/
├── .env                   # Environment configuration (SECURE!)
└── composer.json
```

---

## Wildcard Subdomain Setup

You have two methods to set up wildcard subdomain routing:

### Method 1: cPanel Subdomain Manager (Recommended)

1. In cPanel, go to **Domains** → **Subdomains**
2. Create a subdomain:
   - **Subdomain**: `*` (asterisk for wildcard)
   - **Document Root**: `/public_html/api/stores`
3. Click **Create**

This routes all `{anything}.livepetal.com` requests to `/public_html/api/stores/`

### Method 2: .htaccess Rewrite Rules (Alternative)

If Method 1 isn't available, add to `/public_html/.htaccess`:

```apache
# Wildcard Subdomain Routing for Stores
RewriteEngine On

# Check if the request is for a subdomain (not www or empty)
RewriteCond %{HTTP_HOST} ^([^.]+)\.livepetal\.com$ [NC]
RewriteCond %{HTTP_HOST} !^www\. [NC]
RewriteCond %1 !^(api|admin|mail|ftp|cpanel|webmail)$ [NC]

# Extract subdomain and route to store directory
RewriteCond %{REQUEST_URI} !^/api/stores/
RewriteRule ^(.*)$ /api/stores/%1/$1 [L]

# If store file doesn't exist, try index.html
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^api/stores/([^/]+)/(.*)$ /api/stores/$1/index.html [L]
```

**How it works:**

- `store-name.livepetal.com/products.html` → `/api/stores/store-name/products.html`
- `store-name.livepetal.com` → `/api/stores/store-name/index.html`

---

## File Upload

Upload all project files to cPanel:

### Using FTP/SFTP

1. Use FileZilla, WinSCP, or cPanel File Manager
2. Upload entire project to `public_html/`
3. Ensure directory structure matches above layout

### Using SSH (Faster)

```bash
# On your local machine
tar -czf e-commerce.tar.gz * .env .htaccess

# Upload to server
scp e-commerce.tar.gz username@yourserver.com:~/

# On the server
cd public_html/
tar -xzf ~/e-commerce.tar.gz
rm ~/e-commerce.tar.gz
```

### Exclude from Upload

- `node_modules/` (not needed for production)
- `.git/` (version control)
- Local `.env` file (create new on server)
- `README.md` files (optional)

---

## Composer Installation

Install PHP dependencies on the server:

### Using SSH

```bash
cd public_html/

# If Composer not installed globally, download it
curl -sS https://getcomposer.org/installer | php

# Install dependencies (production mode)
php composer.phar install --no-dev --optimize-autoloader

# Or if Composer is global
composer install --no-dev --optimize-autoloader
```

### Using cPanel Terminal

Same commands as SSH method above.

**Flags explained:**

- `--no-dev`: Skip development dependencies
- `--optimize-autoloader`: Optimize for performance

---

## Database Setup

### 1. Create Database in cPanel

**cPanel → MySQL Databases:**

1. Create new database: `username_ecommerce`
2. Create database user: `username_dbuser`
3. Set strong password
4. Add user to database with **ALL PRIVILEGES**

### 2. Import Schema

**Using phpMyAdmin:**

1. Select your database
2. Go to **Import** tab
3. Import in this order:
   - Main schema file (create tables)
   - `backend/database/add_token_security.sql` (security tables)
   - Any other migration files

**Using SSH/command line:**

```bash
mysql -u username_dbuser -p username_ecommerce < backend/database/schema.sql
mysql -u username_dbuser -p username_ecommerce < backend/database/add_token_security.sql
```

### 3. Verify Tables

Run in phpMyAdmin SQL tab:

```sql
SHOW TABLES;
```

Should see tables including:

- `users`, `products`, `categories`, `orders`
- `token_blacklist`, `token_devices`, `security_events`
- `stores`, `store_templates`, etc.

---

## Environment Configuration

Create `.env` file in `public_html/`:

```env
# Application Environment
APP_ENV=production
APP_DEBUG=false
APP_URL=https://livepetal.com

# Database Configuration
DB_HOST=localhost
DB_NAME=username_ecommerce
DB_USER=username_dbuser
DB_PASS=your_secure_password_here
DB_PORT=3306

# JWT Configuration
JWT_SECRET=your_very_long_random_secret_key_here_min_32_chars
JWT_ACCESS_EXPIRY=900          # 15 minutes
JWT_REFRESH_EXPIRY=604800      # 7 days

# Security Configuration
SECURITY_STRICT_FINGERPRINT=false  # Set true for stricter device validation
CORS_ALLOWED_ORIGINS=https://livepetal.com,https://*.livepetal.com

# Rate Limiting
RATE_LIMIT_ENABLED=true
RATE_LIMIT_MAX_REQUESTS=60
RATE_LIMIT_WINDOW=60

# Cloudinary Configuration
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret

# Paystack Configuration
PAYSTACK_PUBLIC_KEY=pk_live_your_public_key
PAYSTACK_SECRET_KEY=sk_live_your_secret_key
PAYSTACK_WEBHOOK_SECRET=your_webhook_secret

# Email Configuration (if using email notifications)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_FROM_ADDRESS=noreply@livepetal.com
MAIL_FROM_NAME="LivePetal Store"

# Sentry (Optional - Error Tracking)
SENTRY_DSN=your_sentry_dsn_here
SENTRY_ENVIRONMENT=production
```

**Generate JWT Secret:**

```bash
php -r "echo bin2hex(random_bytes(32));"
```

**Security:** Make sure `.env` is NOT accessible via web:

```apache
# Add to .htaccess
<Files ".env">
    Order allow,deny
    Deny from all
</Files>
```

---

## File Permissions

Set correct permissions for security:

### Using SSH

```bash
cd public_html/

# Set directories to 755
find . -type d -exec chmod 755 {} \;

# Set files to 644
find . -type f -exec chmod 644 {} \;

# Make writable directories (storage, uploads)
chmod 775 storage/
chmod 775 storage/rate_limits/
chmod 775 api/stores/

# Secure .env file
chmod 600 .env

# Make cleanup script executable
chmod 755 backend/helpers/cleanup_tokens.php
```

### Using cPanel File Manager

Right-click → **Permissions** → Set:

- Directories: `755` (rwxr-xr-x)
- Files: `644` (rw-r--r--)
- `.env`: `600` (rw-------)
- `storage/`: `775` (rwxrwxr-x)

---

## SSL Certificate

Install SSL to enable HTTPS for all subdomains:

### Option 1: Let's Encrypt (Free - Recommended)

**cPanel → SSL/TLS Status:**

1. Enable **AutoSSL**
2. Request SSL for:
   - `livepetal.com`
   - `www.livepetal.com`
   - `*.livepetal.com` (wildcard)

**Note:** Wildcard SSL may require DNS validation. Some cPanel versions auto-renew Let's Encrypt.

### Option 2: Custom Wildcard SSL

If Let's Encrypt doesn't support wildcards on your cPanel:

1. Purchase/obtain wildcard SSL for `*.livepetal.com`
2. **cPanel → SSL/TLS → Install and Manage SSL**
3. Upload:
   - Certificate (CRT)
   - Private Key (KEY)
   - Certificate Authority Bundle (CA)
4. Install for domain

### Force HTTPS

Add to top of `.htaccess`:

```apache
# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## Store Generation Workflow

How generated stores work in production:

### 1. Admin Creates Store

Admin dashboard → **Stores** → **Create New Store**

- Enter store details (name, slug, template)
- Click **Generate Store**

### 2. Backend Generates Files

API endpoint: `POST /api/stores/{id}/generate`

- Fetches template from `store-templates/`
- Replaces placeholders with store data
- Creates files in `/api/stores/{slug}/`:

  ```
  api/stores/my-store/
  ├── index.html
  ├── products.html
  ├── cart.html
  ├── checkout.html
  └── ...
  ```

### 3. Subdomain Access

Store accessible at:

- `https://my-store.livepetal.com` → `/api/stores/my-store/index.html`
- `https://my-store.livepetal.com/products.html` → `/api/stores/my-store/products.html`

### 4. Verify Routing

Check `.htaccess` routing works:

```bash
# Should show store homepage
curl https://test-store.livepetal.com

# Should show products page
curl https://test-store.livepetal.com/products.html
```

---

## Testing

### 1. API Health Check

**Test endpoint:**

```bash
curl https://livepetal.com/api/health
```

**Expected response:**

```json
{
  "status": "healthy",
  "timestamp": "2026-02-13T10:00:00Z"
}
```

### 2. Admin Login

1. Go to `https://livepetal.com/admin`
2. Login with admin credentials
3. Verify dashboard loads

### 3. Create Test Store

1. In admin panel, go to **Stores**
2. Create new store:
   - Name: "Test Store"
   - Slug: "test-store"
   - Template: Any available
3. Click **Generate Store**
4. Wait for success message

### 4. Access Test Store

Visit `https://test-store.livepetal.com`

- Should show store homepage
- Check navigation links work
- Verify products load from API

### 5. Test JWT Authentication

```bash
# Login
curl -X POST https://livepetal.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# Should return access_token and refresh_token
```

### 6. Test Security Features

- Logout and verify token blacklisted
- Check device fingerprinting works
- View security events at `/api/security/events`

### 7. Test Payment (If Configured)

1. Add products to cart in test store
2. Proceed to checkout
3. Use Paystack test card
4. Verify order created in admin

---

## Cron Jobs

Set up automated maintenance tasks:

### Token Cleanup Cron

**cPanel → Cron Jobs:**

- **Minute:** `0`
- **Hour:** `2`
- **Day:** `*`
- **Month:** `*`
- **Weekday:** `*`
- **Command:**

  ```bash
  0 2 * * * /usr/bin/php /home/username/public_html/backend/helpers/cleanup_tokens.php
  /usr/bin/php /home/username/public_html/backend/helpers/cleanup_tokens.php
  ```

**What it does:**

- Removes expired tokens from blacklist (30+ days)
- Deletes old security events (90+ days)
- Removes inactive device records (90+ days)

**Verify path to PHP:**

```bash
which php
# Use the output in cron command
```

### Optional: Database Backup Cron

```bash
0 3 * * * /usr/bin/mysqldump -u username_dbuser -p PASSWORD username_ecommerce > /home/username/backups/db_$(date +\%Y\%m\%d).sql
```

---

## Troubleshooting

### Issue: 500 Internal Server Error

**Possible causes:**

1. PHP version compatibility → Check cPanel PHP Selector (8.2+)
2. Missing `.env` file → Create with correct values
3. Wrong file permissions → Set as specified above
4. Composer dependencies missing → Run `composer install`
5. Database connection error → Verify `.env` DB credentials

**Check error logs:**

- cPanel → **Metrics** → **Errors**
- Or SSH: `tail -f ~/public_html/error_log`

### Issue: Subdomain Not Routing to Store

**Checks:**

1. DNS wildcard configured? → Check DNS settings
2. Wildcard subdomain created in cPanel? → Verify in Domains
3. `.htaccess` rules correct? → Review rewrite rules
4. Store files exist? → Check `/api/stores/{slug}/` directory
5. SSL working for subdomain? → Test HTTPS access

**Debug routing:**

```bash
# Should show store HTML
curl -I https://store-slug.livepetal.com
```

### Issue: Database Connection Failed

**Checks:**

1. Database exists? → cPanel → MySQL Databases
2. User has privileges? → Add user to database with ALL
3. Correct credentials in `.env`? → Match cPanel settings
4. Database server: Use `localhost` not IP

**Test connection:**

```php
<?php
// test-db.php
$host = 'localhost';
$db   = 'username_ecommerce';
$user = 'username_dbuser';
$pass = 'password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    echo "✅ Database connection successful!";
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
```

Access: `https://livepetal.com/test-db.php` (DELETE after testing)

### Issue: JWT Token Not Working

**Checks:**

1. JWT_SECRET set in `.env`? → Must be 32+ characters
2. Token expired? → Check JWT_ACCESS_EXPIRY
3. Token blacklisted? → Check `token_blacklist` table
4. Device fingerprint mismatch? → Check security events

**Test token generation:**

```bash
curl -X POST https://livepetal.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}' \
  -v
```

### Issue: Rate Limiting Not Working

**Checks:**

1. Rate limit enabled in `.env`? → `RATE_LIMIT_ENABLED=true`
2. Storage directory writable? → `chmod 775 storage/rate_limits/`
3. Middleware active? → Check `api/index.php`

**Test rate limiting:**
Make 70+ requests in 60 seconds → Should get 429 Too Many Requests

### Issue: File Upload/Cloudinary Error

**Checks:**

1. Cloudinary credentials in `.env`? → Verify API keys
2. Upload directory writable? → Check permissions
3. PHP upload limits? → cPanel → **MultiPHP INI Editor**
   - `upload_max_filesize = 20M`
   - `post_max_size = 20M`

---

## Security Checklist

Before going live:

- [ ] `.env` file secured (chmod 600, blocked in `.htaccess`)
- [ ] `APP_DEBUG=false` in production
- [ ] Strong database password (16+ characters)
- [ ] JWT_SECRET is random and long (32+ characters)
- [ ] SSL certificate installed and working
- [ ] HTTPS forced via `.htaccess`
- [ ] File permissions set correctly (755/644)
- [ ] Database user has minimum required privileges
- [ ] Paystack live keys (not test keys)
- [ ] Cloudinary configured for production
- [ ] Rate limiting enabled
- [ ] Token cleanup cron job active
- [ ] Error reporting disabled (`display_errors=Off`)
- [ ] Security headers in `.htaccess`:

  ```apache
  Header set X-Content-Type-Options "nosniff"
  Header set X-Frame-Options "SAMEORIGIN"
  Header set X-XSS-Protection "1; mode=block"
  Header set Referrer-Policy "strict-origin-when-cross-origin"
  ```

---

## Performance Optimization

### Enable OPcache

**cPanel → MultiPHP INI Editor:**

```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
```

### Enable Gzip Compression

Add to `.htaccess`:

```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
</IfModule>
```

### Browser Caching

Add to `.htaccess`:

```apache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

---

## Post-Deployment

### Monitor

1. **Error Logs:** Regularly check cPanel error logs
2. **Security Events:** Review `/api/security/events` for suspicious activity
3. **Database Size:** Monitor growth, optimize if needed
4. **Disk Space:** Check `storage/` and `api/stores/` directories

### Maintenance

1. **Backups:** Regular database and file backups
2. **Updates:** Keep Composer dependencies updated
3. **Security:** Review security events weekly
4. **Cleanup:** Cron job should handle token cleanup automatically

### Scaling

If traffic grows:

1. Enable caching (Redis/Memcached)
2. Use CDN for static assets
3. Consider dedicated server or VPS
4. Implement database query optimization

---

## Support

For issues not covered in this guide:

1. Check application error logs
2. Review cPanel error logs
3. Test with debugging enabled (temporarily set `APP_DEBUG=true`)
4. Verify all prerequisites met

---

**Last Updated:** February 13, 2026
**Version:** 1.0.0
