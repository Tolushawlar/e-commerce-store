# Cloudinary Image Upload Integration

This document explains how to set up and use Cloudinary image upload functionality in the e-commerce platform.

## Overview

The platform now supports image uploads to Cloudinary, a cloud-based image management service. This provides:
- Fast, reliable image hosting
- Automatic image optimization
- On-the-fly image transformations
- CDN delivery worldwide
- Secure image storage

## Features

### Backend
- **CloudinaryService**: Service class for all Cloudinary operations
- **ImageController**: RESTful API endpoints for image management
- **Swagger Documentation**: Complete API documentation
- **File Validation**: Size and format validation
- **Error Handling**: Comprehensive error logging with Sentry

### Frontend
- **imageService.js**: JavaScript helper for easy uploads
- **Preview Generation**: Create previews before upload
- **Multiple Upload**: Support for bulk uploads
- **Drag & Drop Ready**: Compatible with drag-drop libraries

## Setup Instructions

### 1. Install Cloudinary PHP SDK

```bash
composer install
```

The Cloudinary package is already added to `composer.json`. Run the above command to install it.

### 2. Get Cloudinary Credentials

1. Create a free account at [cloudinary.com](https://cloudinary.com)
2. Go to your Dashboard
3. Copy your credentials:
   - Cloud Name
   - API Key
   - API Secret

### 3. Configure Environment Variables

Create or update your `.env` file in the project root:

```env
# Cloudinary Configuration
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
CLOUDINARY_UPLOAD_PRESET=ecommerce_uploads
CLOUDINARY_FOLDER=ecommerce
```

### 4. Verify Configuration

The system automatically loads these variables from `.env`. You can verify configuration by checking:

```php
$config = require 'backend/config/config.php';
var_dump($config['cloudinary']);
```

## API Endpoints

All endpoints require authentication (JWT token in Authorization header).

### Upload Single Image
```http
POST /api/images/upload
Content-Type: multipart/form-data

Form Data:
- image: [File]
- folder: "products" (optional)
- public_id: "custom-id" (optional)
```

**Response:**
```json
{
  "success": true,
  "message": "Image uploaded successfully",
  "data": {
    "url": "https://res.cloudinary.com/demo/image/upload/v1234567890/sample.jpg",
    "public_id": "ecommerce/sample",
    "width": 1920,
    "height": 1080,
    "format": "jpg",
    "bytes": 245678
  }
}
```

### Upload Multiple Images
```http
POST /api/images/upload-multiple
Content-Type: multipart/form-data

Form Data:
- images[]: [File] (multiple files)
- folder: "products" (optional)
```

### Upload from URL
```http
POST /api/images/upload-from-url
Content-Type: application/json

{
  "url": "https://example.com/image.jpg",
  "folder": "products"
}
```

### Delete Image
```http
DELETE /api/images/{publicId}
```

The public_id should be URL encoded if it contains slashes.

### Get Image Details
```http
GET /api/images/{publicId}/details
```

### Get Transformed URL
```http
POST /api/images/transform
Content-Type: application/json

{
  "public_id": "ecommerce/sample",
  "width": 800,
  "height": 600,
  "crop": "fill",
  "quality": "auto"
}
```

## Frontend Usage

### Include the Service

Add to your HTML:
```html
<script src="/app/assets/js/services/image.service.js"></script>
```

### Upload Single Image

```javascript
// Method 1: Direct upload
const fileInput = document.getElementById('image-input');
const file = fileInput.files[0];

try {
  const result = await imageService.uploadImage(file, {
    folder: 'products'
  });
  
  console.log('Upload successful:', result.url);
  // Use result.url to display or save to database
} catch (error) {
  console.error('Upload failed:', error);
}

// Method 2: Trigger file picker
imageService.triggerUpload(
  { folder: 'products' },
  (result) => {
    console.log('Upload successful:', result.url);
  },
  (error) => {
    console.error('Upload failed:', error);
  }
);
```

### Upload Multiple Images

```javascript
const fileInput = document.getElementById('multiple-images');
const files = fileInput.files;

try {
  const result = await imageService.uploadMultiple(files, {
    folder: 'products'
  });
  
  console.log('Uploaded:', result.uploaded.length);
  console.log('Failed:', result.failed.length);
  
  result.uploaded.forEach(img => {
    console.log('Image URL:', img.url);
  });
} catch (error) {
  console.error('Upload failed:', error);
}
```

### Create Preview Before Upload

```javascript
const file = fileInput.files[0];

try {
  const previewUrl = await imageService.createPreview(file);
  document.getElementById('preview').src = previewUrl;
} catch (error) {
  console.error('Preview failed:', error);
}
```

### Validate File Before Upload

```javascript
const file = fileInput.files[0];

const validation = imageService.validateImage(file, {
  maxSize: 5 * 1024 * 1024, // 5MB
  allowedFormats: ['jpg', 'jpeg', 'png', 'webp']
});

if (!validation.valid) {
  console.error('Validation errors:', validation.errors);
  return;
}

// Proceed with upload
```

### Delete Image

```javascript
const publicId = 'ecommerce/products/sample-123';

try {
  const result = await imageService.deleteImage(publicId);
  console.log('Image deleted successfully');
} catch (error) {
  console.error('Delete failed:', error);
}
```

## Complete Example: Product Image Upload

```html
<form id="product-form">
  <input type="file" id="product-image" accept="image/*">
  <div id="preview-container"></div>
  <button type="submit">Save Product</button>
</form>

<script>
const imageInput = document.getElementById('product-image');
const previewContainer = document.getElementById('preview-container');
let uploadedImageUrl = null;

// Preview on file selection
imageInput.addEventListener('change', async (e) => {
  const file = e.target.files[0];
  if (!file) return;
  
  // Validate
  const validation = imageService.validateImage(file);
  if (!validation.valid) {
    alert('Invalid image: ' + validation.errors.join(', '));
    return;
  }
  
  // Show preview
  try {
    const preview = await imageService.createPreview(file);
    previewContainer.innerHTML = `<img src="${preview}" style="max-width: 200px;">`;
  } catch (error) {
    console.error('Preview error:', error);
  }
});

// Upload on form submit
document.getElementById('product-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const file = imageInput.files[0];
  if (!file) {
    alert('Please select an image');
    return;
  }
  
  try {
    // Upload to Cloudinary
    const result = await imageService.uploadImage(file, {
      folder: 'products'
    });
    
    uploadedImageUrl = result.url;
    
    // Now save product with image URL
    const productData = {
      name: 'Product Name',
      image_url: uploadedImageUrl,
      // ... other fields
    };
    
    await api.post('/api/products', productData);
    alert('Product saved successfully!');
    
  } catch (error) {
    alert('Upload failed: ' + error.message);
  }
});
</script>
```

## Image Folders Organization

Recommended folder structure in Cloudinary:
- `products/` - Product images
- `stores/logos/` - Store logos
- `stores/banners/` - Store banners
- `categories/` - Category images
- `users/avatars/` - User profile pictures

## Configuration Options

Edit `backend/config/config.php` to change:

```php
'cloudinary' => [
    'max_file_size' => 5 * 1024 * 1024, // 5MB
    'allowed_formats' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
    'folder' => 'ecommerce', // Default folder
]
```

## Security Considerations

1. **Authentication Required**: All upload endpoints require valid JWT token
2. **File Validation**: Server validates file type and size
3. **Secure URLs**: All images use HTTPS
4. **API Secrets**: Keep API keys secure in .env file (never commit to git)

## Error Handling

The service includes comprehensive error handling:

```javascript
try {
  const result = await imageService.uploadImage(file);
} catch (error) {
  // Error types:
  // - Validation errors (file size, format)
  // - Network errors (connection failed)
  // - API errors (Cloudinary errors)
  // - Authentication errors (invalid token)
  
  console.error('Upload error:', error.message);
}
```

## Testing

### Test Upload Endpoint

```bash
curl -X POST http://localhost:8000/api/images/upload \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -F "image=@/path/to/image.jpg" \
  -F "folder=test"
```

### Test in Browser Console

```javascript
// Get file from input
const file = document.querySelector('input[type="file"]').files[0];

// Upload
imageService.uploadImage(file, { folder: 'test' })
  .then(result => console.log('Success:', result))
  .catch(error => console.error('Error:', error));
```

## Swagger Documentation

View complete API documentation at:
```
http://localhost:8000/api/docs
```

All image endpoints are documented under the "Images" tag.

## Troubleshooting

### "Cloudinary is not properly configured"
- Check your .env file has all required variables
- Verify variable names match exactly
- Restart your PHP server after changing .env

### "Upload failed: Invalid credentials"
- Double-check your Cloudinary API key and secret
- Ensure no extra spaces in .env file
- Verify cloud name is correct

### "File size exceeds maximum"
- Default limit is 5MB
- Change in config.php if needed
- Consider image compression before upload

### "Invalid file format"
- Check allowed_formats in config
- Ensure file extension matches MIME type
- Use proper image files only

## Next Steps

1. Integrate with existing models (Product, Store, Category)
2. Add image upload UI to admin panels
3. Implement drag-and-drop upload
4. Add image cropping/editing before upload
5. Create image galleries for products

## Support

For Cloudinary-specific issues, refer to:
- [Cloudinary PHP Documentation](https://cloudinary.com/documentation/php_integration)
- [Cloudinary API Reference](https://cloudinary.com/documentation/image_upload_api_reference)
