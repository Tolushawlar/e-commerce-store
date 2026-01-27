# Migration Guide: v1.0 to v2.0

## Overview

This guide will help you migrate from the old procedural PHP structure to the new MVC architecture.

## What's Changed

### Architecture

- **Before**: Monolithic PHP files with mixed concerns
- **After**: Clean MVC separation with REST API

### File Structure

- **Before**: Everything in root with inline PHP
- **After**: Organized backend/ and frontend/ directories

### API Communication

- **Before**: Direct database queries in views
- **After**: RESTful API calls via JavaScript

## Migration Steps

### 1. Update File Paths

**Old super-admin structure:**

```
/super-admin/index.php
/super-admin/clients.php
/api/clients.php (mixed logic)
```

**New structure:**

```
/backend/controllers/ClientController.php (logic)
/backend/public/index.php (API routes)
/frontend/super-admin/clients.php (UI only)
```

### 2. Update API Calls

**Before (inline PHP):**

```php
<?php
$db = new Database();
$stmt = $db->prepare("SELECT * FROM clients");
$clients = $stmt->fetchAll();
?>
```

**After (JavaScript API):**

```javascript
const response = await clientAPI.getAll();
const clients = response.data.clients;
```

### 3. Update HTML Files

Add the API client script:

```html
<script src="/frontend/assets/js/api.js"></script>
<script src="/frontend/assets/js/admin-clients.js"></script>
```

### 4. Database Configuration

**Old:** `/config/database.php`

```php
class Database {
    private $host = 'localhost';
    // ...
}
```

**New:** `/backend/config/config.php`

```php
return [
    'database' => [
        'host' => 'localhost',
        // ...
    ]
];
```

### 5. Testing the Migration

1. **Test API Endpoints:**

```bash
curl http://localhost/backend/public/api/health
```

Expected response:

```json
{
  "success": true,
  "message": "API is running",
  "version": "2.0.0"
}
```

2. **Test Client API:**

```bash
curl http://localhost/backend/public/api/clients
```

3. **Test Frontend:**

- Open `/frontend/super-admin/clients.php`
- Check browser console for any JavaScript errors
- Verify data loads from API

## Common Issues & Solutions

### Issue 1: 404 on API Calls

**Cause**: .htaccess not working  
**Solution**: Enable mod_rewrite in Apache

```bash
sudo a2enmod rewrite
sudo service apache2 restart
```

### Issue 2: CORS Errors

**Cause**: Cross-origin restrictions  
**Solution**: Already handled in `backend/bootstrap.php`, but verify CORS headers

### Issue 3: Database Connection Failed

**Cause**: Wrong credentials  
**Solution**: Update `backend/config/config.php` with correct database credentials

### Issue 4: JavaScript API Not Defined

**Cause**: Script not loaded  
**Solution**: Ensure `/frontend/assets/js/api.js` is included before other scripts

## Backward Compatibility

### Old API Files

The old API files in `/api/` folder can remain for backward compatibility, but should be deprecated:

- ❌ `/api/clients.php` (old)
- ✅ `/backend/public/api/clients` (new)

### Gradual Migration

You can migrate page by page:

1. Keep old pages working
2. Create new page with API calls
3. Test thoroughly
4. Switch traffic to new page
5. Remove old page

## New Features in v2.0

1. **Pagination**: All list endpoints support pagination
2. **Filtering**: Query parameters for filtering data
3. **Validation**: Comprehensive input validation
4. **Error Handling**: Standardized error responses
5. **Code Organization**: Easy to maintain and extend

## Performance Improvements

- **Before**: Direct database queries on every request
- **After**:
  - Prepared statements (SQL injection protection)
  - Connection pooling via singleton pattern
  - Response caching (to be added)

## Security Enhancements

1. ✅ PDO prepared statements
2. ✅ Password hashing
3. ✅ Input validation
4. ✅ CORS configuration
5. 🔄 JWT authentication (in progress)

## Next Steps

1. Test all existing functionality
2. Implement JWT authentication
3. Add response caching
4. Implement rate limiting
5. Add comprehensive logging

## Rollback Plan

If issues arise, you can rollback:

1. Rename `/backend` to `/backend-v2`
2. Rename `/frontend` to `/frontend-v2`
3. Restore old files from backup
4. Update database if schema changed

## Support

For migration issues:

1. Check error logs in `/backend/logs/` (to be created)
2. Review browser console for JavaScript errors
3. Test API endpoints individually
4. Create an issue in the repository

## Checklist

- [ ] Database updated with latest schema
- [ ] Configuration file updated
- [ ] Apache/Nginx rewrite rules configured
- [ ] File permissions set correctly
- [ ] API health check passes
- [ ] Frontend can fetch data from API
- [ ] All CRUD operations working
- [ ] Error handling tested
- [ ] Performance acceptable

## Timeline

Recommended migration timeline:

- **Week 1**: Setup new structure, test API
- **Week 2**: Migrate super-admin pages
- **Week 3**: Migrate client dashboard
- **Week 4**: Testing and optimization

---

**Last Updated**: January 26, 2026  
**Version**: 2.0.0
