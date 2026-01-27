# Restructure Project for Production Deployment

## Why Restructure Locally?

Instead of reorganizing files during deployment to cPanel, it's **highly recommended** to restructure your local project to match the production structure first. This approach offers several benefits:

- ✅ **Simpler Deployment**: Upload entire folder as-is, no reorganization needed
- ✅ **Better Security**: Public/non-public file separation from the start
- ✅ **Easier Testing**: Test production structure locally before deploying
- ✅ **Fewer Errors**: No path mismatches between dev and production
- ✅ **Version Control**: Track the actual production structure in Git
- ✅ **One-Click Deploy**: Use Git clone/pull directly to production

## Before You Start

### Current Structure

```
e-commerce-store/
├── backend/
│   ├── public/          # API entry point
│   ├── config/
│   ├── controllers/
│   └── ...
├── frontend/            # Frontend files
└── vendor/
```

### Target Structure (Production-Ready)

```
e-commerce-store/
├── api/                 # Public API files (was backend/public/)
├── app/                 # Public frontend files (was frontend/)
├── backend/             # Non-public backend core
├── vendor/
└── store-templates/
```

## Step-by-Step Restructuring Guide

### 1. Backup Your Project

**Windows (PowerShell)**:

```powershell
cd "C:\Users\Dell\OneDrive\Documents\LivePetal Projects"
Copy-Item -Path "e-commerce-store" -Destination "e-commerce-store-backup" -Recurse
```

**Mac/Linux**:

```bash
cp -r e-commerce-store e-commerce-store-backup
```

### 2. Reorganize Folder Structure

**Windows (PowerShell)**:

```powershell
cd "C:\Users\Dell\OneDrive\Documents\LivePetal Projects\e-commerce-store"

# Create new directories
New-Item -ItemType Directory -Path "api" -Force
New-Item -ItemType Directory -Path "app" -Force

# Move backend public files to api/
Move-Item -Path "backend\public\*" -Destination "api\" -Force

# Remove empty public directory
Remove-Item "backend\public" -Force

# Move frontend to app/
Move-Item -Path "frontend\*" -Destination "app\" -Force

# Remove empty frontend directory
Remove-Item "frontend" -Force
```

**Mac/Linux**:

```bash
cd ~/Documents/LivePetal\ Projects/e-commerce-store

# Create new directories
mkdir -p api
mkdir -p app

# Move backend public files to api/
mv backend/public/* api/
mv backend/public/.htaccess api/ 2>/dev/null || true

# Remove empty public directory
rmdir backend/public

# Move frontend to app/
mv frontend/* app/
mv frontend/.htaccess app/ 2>/dev/null || true

# Remove empty frontend directory
rmdir frontend
```

### 3. Update File Paths

#### api/index.php

Update the bootstrap require path:

```php
<?php
// Change from:
require_once __DIR__ . '/../bootstrap.php';

// To:
require_once __DIR__ . '/../backend/bootstrap.php';

// Rest of the file remains the same
```

#### backend/bootstrap.php

Verify vendor autoload path (should already be correct):

```php
<?php
// Should be:
require_once __DIR__ . '/../vendor/autoload.php';
```

#### backend/config/config.php

Add or update the `paths` section:

```php
return [
    'database' => [
        // ... existing database config
    ],
    
    'app' => [
        // ... existing app config
    ],
    
    // Add this section if it doesn't exist
    'paths' => [
        'root' => dirname(__DIR__, 2),  // Go up 2 levels to project root
        'backend' => dirname(__DIR__),   // Current backend directory
        'public' => dirname(__DIR__, 2) . '/api',  // API public directory
        'storage' => dirname(__DIR__, 2) . '/storage',
        'templates' => dirname(__DIR__, 2) . '/store-templates',
    ],
    
    // ... rest of config
];
```

#### backend/services/StoreGeneratorService.php

Find and update the stores directory path methods:

```php
// Find this method (or similar):
private function getStoresDirectory(): string
{
    // Change from:
    return __DIR__ . '/../public/stores';
    
    // To:
    return dirname(__DIR__, 2) . '/api/stores';
}

// If there's a template path method:
private function getTemplatePath(): string
{
    return dirname(__DIR__, 2) . '/store-templates';
}
```

### 4. Create Root .htaccess

Create `.htaccess` in the project root (e-commerce-store/.htaccess):

```apache
# Security - Deny access to sensitive directories
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirect HTTP to HTTPS (uncomment when SSL is configured on production)
    # RewriteCond %{HTTPS} off
    # RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
    
    # Redirect root to app
    RewriteCond %{REQUEST_URI} ^/$
    RewriteRule ^(.*)$ /app/ [L]
</IfModule>

# Deny direct access to sensitive directories
RedirectMatch 403 ^/backend/
RedirectMatch 403 ^/vendor/
RedirectMatch 403 ^/md-docs/
RedirectMatch 403 ^/store-templates/
```

### 5. Update .gitignore

Update or create `.gitignore` in project root:

```
# Environment
.env
.env.local

# Dependencies
/vendor/

# Generated stores
/api/stores/*
!/api/stores/.gitkeep

# Uploads
/uploads/*
!/uploads/.gitkeep

# Logs
*.log
error_log

# OS Files
.DS_Store
Thumbs.db
desktop.ini

# IDE
.vscode/
.idea/
*.sublime-*

# Backup
*-backup/
```

### 6. Create .gitkeep Files

Ensure empty directories are tracked:

**Windows (PowerShell)**:

```powershell
New-Item -ItemType File -Path "api\stores\.gitkeep" -Force
New-Item -ItemType Directory -Path "uploads" -Force
New-Item -ItemType File -Path "uploads\.gitkeep" -Force
```

**Mac/Linux**:

```bash
touch api/stores/.gitkeep
mkdir -p uploads && touch uploads/.gitkeep
```

### 7. Update Development Server Commands

Update how you start your development servers:

#### Backend API

**Old command**:

```bash
cd backend/public
php -S localhost:8000 router.php
```

**New command**:

```bash
cd api
php -S localhost:8000 router.php
```

Or with full path (Windows):

```powershell
cd "C:\Users\Dell\OneDrive\Documents\LivePetal Projects\e-commerce-store\api"
php -S localhost:8000 router.php
```

#### Frontend

**Old command**:

```bash
php -S localhost:3000 -t frontend
```

**New command** (from project root):

```bash
php -S localhost:3000 -t app
```

Or with full path (Windows):

```powershell
cd "C:\Users\Dell\OneDrive\Documents\LivePetal Projects\e-commerce-store"
php -S localhost:3000 -t app
```

## Testing Your Restructured Project

### 1. Start Both Servers

Open **two separate terminal windows**:

**Terminal 1 - Backend API**:

```powershell
cd "C:\Users\Dell\OneDrive\Documents\LivePetal Projects\e-commerce-store\api"
php -S localhost:8000 router.php
```

**Terminal 2 - Frontend**:

```powershell
cd "C:\Users\Dell\OneDrive\Documents\LivePetal Projects\e-commerce-store"
php -S localhost:3000 -t app
```

### 2. Test Checklist

Visit each URL and verify it works:

- [ ] **Frontend Login**: `http://localhost:3000/auth/login.php`
- [ ] **Frontend Register**: `http://localhost:3000/auth/register.php`
- [ ] **Admin Dashboard**: `http://localhost:3000/admin/dashboard.php`
- [ ] **Client Dashboard**: `http://localhost:3000/client/dashboard.php`
- [ ] **API Health**: `http://localhost:8000/api`
- [ ] **API Docs**: `http://localhost:8000/docs.html`
- [ ] **Login with existing credentials** - Should work
- [ ] **Create a new store** - Should work
- [ ] **Generate store files** - Check `api/stores/store-{id}/` folder created
- [ ] **View generated store**: `http://localhost:8000/stores/store-{id}/`
- [ ] **Check browser console** - No 404 errors
- [ ] **Check terminal logs** - No PHP errors

### 3. Common Issues and Fixes

#### Issue: "No such file or directory" errors

**Fix**: Check file paths in updated files:

- `api/index.php` - bootstrap path
- `backend/services/StoreGeneratorService.php` - stores directory path

#### Issue: Login redirects to wrong URL

**Fix**: Check `app/assets/js/core/api.js`:

```javascript
const API_BASE_URL = 'http://localhost:8000/api';
```

#### Issue: Store generation fails

**Fix**: Verify folder permissions:

```powershell
# Windows - ensure api/stores exists and is writable
icacls "api\stores" /grant Users:F
```

#### Issue: 404 on API routes

**Fix**: Ensure `.htaccess` exists in `api/` folder

## Committing Your Changes

Once everything is tested and working:

```bash
# Check what's changed
git status

# Add all changes
git add .

# Commit with descriptive message
git commit -m "Restructure project for production deployment

- Moved backend/public to api/
- Moved frontend to app/
- Updated all file paths
- Added production .htaccess
- Updated .gitignore for new structure"

# Push to your branch
git push origin adedamola
```

## Deploying to cPanel After Restructure

Once restructured, deployment becomes **much simpler**:

### Method 1: Git Clone (Recommended)

```bash
# SSH into cPanel
ssh username@yourdomain.com

# Navigate to public_html
cd ~/public_html

# Clone repository
git clone https://github.com/Tolushawlar/e-commerce-store.git .

# Install dependencies (if needed)
composer install --no-dev

# Set permissions
chmod -R 755 api/
chmod 775 api/stores/
```

**To update later**:

```bash
cd ~/public_html
git pull origin master
```

### Method 2: Direct Upload

1. ZIP your entire project locally
2. Login to cPanel File Manager
3. Navigate to `public_html/`
4. Upload ZIP file
5. Extract ZIP
6. Done! Structure already matches production

### Method 3: FTP Upload

1. Connect via FileZilla to your cPanel
2. Navigate to `public_html/`
3. Upload entire `e-commerce-store/` folder contents
4. Folder structure transfers directly - no reorganization needed

## Post-Deployment on cPanel

After uploading to cPanel:

1. **Configure Database** - Update `backend/config/config.php` with cPanel database credentials
2. **Import Database** - Use phpMyAdmin to import `backend/database/schema.sql`
3. **Set Permissions**:

   ```bash
   chmod 755 api/
   chmod 775 api/stores/
   chmod 644 backend/config/config.php
   ```

4. **Create Admin User** - Via phpMyAdmin
5. **Enable SSL** - Through cPanel (recommended)
6. **Test Your Site**:
   - `https://yourdomain.com/app/` - Frontend
   - `https://yourdomain.com/api/` - API
   - `https://yourdomain.com/api/docs.html` - API Docs

## Reverting If Needed

If something goes wrong during restructure, restore your backup:

**Windows**:

```powershell
cd "C:\Users\Dell\OneDrive\Documents\LivePetal Projects"
Remove-Item "e-commerce-store" -Recurse -Force
Rename-Item "e-commerce-store-backup" "e-commerce-store"
cd e-commerce-store
```

**Mac/Linux**:

```bash
cd ~/Documents/LivePetal\ Projects/
rm -rf e-commerce-store
mv e-commerce-store-backup e-commerce-store
cd e-commerce-store
```

## Final Structure Overview

After successful restructure:

```
e-commerce-store/
├── api/                        # Public API (accessible via web)
│   ├── index.php               # API entry point
│   ├── router.php              # Dev server router
│   ├── .htaccess               # Apache rewrite rules
│   ├── docs.html               # Swagger UI
│   ├── openapi.json            # API specification
│   └── stores/                 # Generated store files
│       └── .gitkeep
├── app/                        # Public frontend (accessible via web)
│   ├── auth/                   # Login/Register pages
│   ├── admin/                  # Admin dashboard
│   ├── client/                 # Client dashboard
│   ├── assets/                 # JS/CSS/Images
│   ├── shared/                 # Shared layouts
│   └── index.php               # Frontend entry
├── backend/                    # Non-public backend core
│   ├── config/
│   │   ├── config.php          # App configuration
│   │   └── Database.php        # DB connection
│   ├── controllers/            # Business logic
│   ├── models/                 # Data models
│   ├── services/               # Services
│   ├── middleware/             # Middleware
│   ├── helpers/                # Helper functions
│   ├── core/                   # Core framework
│   ├── database/               # SQL schemas
│   └── bootstrap.php           # App bootstrap
├── vendor/                     # Composer dependencies (non-public)
├── store-templates/            # HTML templates (non-public)
├── md-docs/                    # Documentation (non-public)
├── uploads/                    # User uploads
│   └── .gitkeep
├── .htaccess                   # Root htaccess (security)
├── .gitignore                  # Git ignore rules
├── composer.json               # Dependencies
└── README-V2.md                # Documentation
```

## Benefits Summary

✅ **Security**: Sensitive files (backend/, vendor/) not directly web-accessible
✅ **Simplicity**: Deploy by simple upload or `git clone`
✅ **Consistency**: Same structure in dev and production
✅ **Maintainability**: Easier to manage and update
✅ **Performance**: Optimized for production use

---

**Need Help?** See the main [README-V2.md](../README-V2.md) for full deployment guide.
