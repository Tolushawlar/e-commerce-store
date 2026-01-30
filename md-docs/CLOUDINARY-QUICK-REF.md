# Cloudinary Image Upload - Quick Reference

## Quick Setup (5 Steps)

1. **Install Package** (Already Done ✓)
   ```bash
   composer require cloudinary/cloudinary_php:^2.0
   ```

2. **Get Cloudinary Credentials**
   - Sign up at [cloudinary.com](https://cloudinary.com)
   - Get: Cloud Name, API Key, API Secret

3. **Configure .env**
   ```env
   CLOUDINARY_CLOUD_NAME=your_cloud_name
   CLOUDINARY_API_KEY=your_api_key
   CLOUDINARY_API_SECRET=your_api_secret
   ```

4. **Include Service in Frontend**
   ```html
   <script src="/app/assets/js/services/image.service.js"></script>
   ```

5. **Test It**
   Visit: `/app/demo/image-upload-demo.html`

---

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/images/upload` | Upload single image |
| POST | `/api/images/upload-multiple` | Upload multiple images |
| POST | `/api/images/upload-from-url` | Upload from external URL |
| DELETE | `/api/images/{publicId}` | Delete image |
| GET | `/api/images/{publicId}/details` | Get image info |
| POST | `/api/images/transform` | Get transformed URL |

**Auth Required**: All endpoints require JWT token in Authorization header.

---

## Frontend Usage Examples

### Basic Upload
```javascript
const file = document.getElementById('fileInput').files[0];
const result = await imageService.uploadImage(file, { folder: 'products' });
console.log(result.url); // Use this URL in your database
```

### Multiple Upload
```javascript
const files = document.getElementById('filesInput').files;
const result = await imageService.uploadMultiple(files, { folder: 'products' });
result.uploaded.forEach(img => console.log(img.url));
```

### Upload from URL
```javascript
const result = await imageService.uploadFromUrl('https://example.com/image.jpg');
console.log(result.url);
```

### Delete Image
```javascript
await imageService.deleteImage('ecommerce/products/sample-123');
```

### Quick Upload (File Picker)
```javascript
imageService.triggerUpload(
  { folder: 'products' },
  (result) => console.log('Success:', result.url),
  (error) => console.error('Error:', error)
);
```

### Preview Before Upload
```javascript
const file = fileInput.files[0];
const preview = await imageService.createPreview(file);
document.getElementById('preview').src = preview;
```

### Validate Image
```javascript
const validation = imageService.validateImage(file, {
  maxSize: 5 * 1024 * 1024, // 5MB
  allowedFormats: ['jpg', 'jpeg', 'png', 'webp']
});

if (!validation.valid) {
  alert('Errors: ' + validation.errors.join(', '));
}
```

---

## Backend Usage (PHP)

### Upload in Controller
```php
use App\Services\CloudinaryService;

$cloudinary = new CloudinaryService();

// Upload from $_FILES
$result = $cloudinary->uploadImage($_FILES['image'], 'products');
echo $result['url']; // Save to database

// Upload from URL
$result = $cloudinary->uploadFromUrl('https://example.com/img.jpg');

// Delete image
$cloudinary->deleteImage('ecommerce/products/sample-123');

// Get transformed URL
$url = $cloudinary->getTransformedUrl('ecommerce/sample', [
    'width' => 800,
    'height' => 600,
    'crop' => 'fill'
]);
```

---

## Common Use Cases

### Product Image Upload
```javascript
// In product form
async function saveProduct() {
  const imageFile = document.getElementById('product-image').files[0];
  
  // Upload image first
  const imageResult = await imageService.uploadImage(imageFile, {
    folder: 'products'
  });
  
  // Save product with image URL
  const product = {
    name: 'Product Name',
    image_url: imageResult.url,
    price: 99.99
  };
  
  await api.post('/api/products', product);
}
```

### Store Logo Upload
```javascript
async function updateStoreLogo(storeId) {
  const result = await imageService.uploadImage(logoFile, {
    folder: `stores/${storeId}/logo`,
    public_id: 'logo' // Will overwrite existing
  });
  
  await api.put(`/api/stores/${storeId}`, {
    logo_url: result.url
  });
}
```

### Category Icon Upload
```javascript
const result = await imageService.uploadImage(iconFile, {
  folder: 'categories'
});

await api.post('/api/categories', {
  name: 'Electronics',
  icon_url: result.url
});
```

---

## Configuration

Edit `backend/config/config.php`:

```php
'cloudinary' => [
    'max_file_size' => 5 * 1024 * 1024, // 5MB
    'allowed_formats' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
    'folder' => 'ecommerce',
]
```

---

## Folder Structure Recommendation

```
ecommerce/
├── products/
│   ├── product-1.jpg
│   └── product-2.jpg
├── stores/
│   ├── store-1/
│   │   ├── logo.png
│   │   └── banner.jpg
│   └── store-2/
│       └── logo.png
├── categories/
│   └── electronics.jpg
└── users/
    └── avatars/
```

---

## Error Handling

```javascript
try {
  const result = await imageService.uploadImage(file);
} catch (error) {
  // Common errors:
  // - "File size exceeds 5MB"
  // - "Invalid file format"
  // - "Cloudinary is not properly configured"
  // - "No file uploaded"
  console.error('Upload failed:', error.message);
}
```

---

## Testing

### Test with cURL
```bash
curl -X POST http://localhost:8000/api/images/upload \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -F "image=@/path/to/image.jpg" \
  -F "folder=test"
```

### Test Demo Page
1. Open: `/app/demo/image-upload-demo.html`
2. Login to get JWT token
3. Try different upload methods
4. Check results

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| "Not configured" error | Add credentials to .env file |
| "Invalid credentials" | Check Cloud Name, API Key, Secret |
| "File too large" | Compress image or increase max_file_size |
| "Invalid format" | Use jpg, png, gif, webp, or svg |
| No preview | Check browser console for errors |

---

## Next Steps

1. ✓ Cloudinary setup complete
2. ✓ API endpoints ready
3. ✓ Frontend service ready
4. ✓ Demo page available

**Now you can:**
- Integrate with Product creation
- Add to Store customization
- Update Category management
- Build image galleries

---

## Resources

- **Full Docs**: `/md-docs/CLOUDINARY-IMAGE-UPLOAD.md`
- **Demo Page**: `/app/demo/image-upload-demo.html`
- **API Docs**: `http://localhost:8000/api/docs` (Swagger)
- **Cloudinary Docs**: https://cloudinary.com/documentation
