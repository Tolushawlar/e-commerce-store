# How to Create and Customize a Store

## Step-by-Step Guide

### 1. Create a New Store

#### Via Super Admin Dashboard:
1. **Access Super Admin Panel**
   - Go to `http://localhost:8888/ecommerce-platform/super-admin/`
   - Login with admin credentials

2. **Navigate to Store Creation**
   - Click "Stores" in sidebar
   - Click "Create New Store" button
   - Or go directly to `create-store.php`

3. **Fill Store Details**
   ```
   Client: Select from dropdown
   Store Name: "My Awesome Store"
   Store URL: "my-awesome-store" 
   Description: "Brief store description"
   Primary Color: #064E3B (or choose custom)
   Accent Color: #BEF264 (or choose custom)
   Template: CampMart Style
   ```

4. **Live Preview**
   - See real-time preview as you type
   - Colors update instantly
   - Store name reflects in header

5. **Create Store**
   - Click "Create Store" button
   - Store files are generated automatically
   - Redirected to customization page

### 2. Customize Your Store

#### Visual Customization Panel:
1. **Store Information**
   - Update store name, tagline, description
   - Changes reflect in real-time preview

2. **Brand Colors**
   - Choose primary color (headers, buttons)
   - Choose accent color (highlights, CTAs)
   - Use color picker or enter hex codes

3. **Layout Options**
   - Header style: Default/Centered/Minimal
   - Product grid: 3/4/5 columns
   - See changes instantly in preview

4. **Upload Assets**
   - Store logo (replaces default icon)
   - Hero background image
   - Drag & drop or browse files

### 3. Generated Store Structure

When you create a store, the system generates:

```
/stores/your-store-slug/
├── index.html          # Homepage
├── products.html       # Product catalog
├── cart.html          # Shopping cart
└── config.json        # Store configuration
```

### 4. Accessing Your Store

#### Store URLs:
- **Homepage**: `http://localhost:8888/ecommerce-platform/stores/your-store-slug/`
- **Products**: `http://localhost:8888/ecommerce-platform/stores/your-store-slug/products.html`
- **Cart**: `http://localhost:8888/ecommerce-platform/stores/your-store-slug/cart.html`

### 5. Adding Products

#### Via Client Dashboard:
1. **Access Client Dashboard**
   - Go to `http://localhost:8888/ecommerce-platform/client-dashboard/`
   - Login with client credentials

2. **Add Products**
   - Click "Products" → "Add New Product"
   - Fill product details with live preview
   - Upload product images
   - Set price and category

3. **Product Management**
   - Edit existing products
   - Manage inventory
   - Update pricing

### 6. Customization Features

#### Real-time Preview:
- ✅ Instant color changes
- ✅ Live text updates
- ✅ Layout modifications
- ✅ Visual feedback

#### Brand Customization:
- ✅ Custom colors (primary/accent)
- ✅ Logo upload
- ✅ Store name & tagline
- ✅ Description & messaging

#### Layout Options:
- ✅ Header styles
- ✅ Product grid layouts
- ✅ Responsive design
- ✅ Mobile optimization

### 7. Publishing Your Store

#### Make Store Live:
1. **Preview First**
   - Click "Preview Store" to test
   - Check all pages work correctly
   - Verify mobile responsiveness

2. **Publish Store**
   - Click "Publish Store" button
   - Store becomes publicly accessible
   - SEO-friendly URLs generated

### 8. Advanced Customization

#### Template Modification:
- Edit `/store-templates/campmart-style.html`
- Add custom CSS/JavaScript
- Modify layout structure
- Create new template variants

#### API Integration:
- Products loaded via `/api/products`
- Orders processed via `/api/orders`
- Real-time inventory updates
- Customer management

### 9. Store Management

#### Ongoing Management:
- **Analytics**: Track sales, visitors, conversions
- **Orders**: Process and fulfill customer orders
- **Customers**: Manage customer database
- **Inventory**: Update stock levels
- **Marketing**: Promotional campaigns

#### Multi-store Management:
- Super admin can manage all stores
- Client dashboard for individual stores
- Centralized analytics and reporting
- Bulk operations and updates

### 10. Quick Start Checklist

- [ ] Create client account (Super Admin)
- [ ] Create new store with basic info
- [ ] Customize colors and branding
- [ ] Upload logo and images
- [ ] Add initial products
- [ ] Preview store functionality
- [ ] Publish store live
- [ ] Test customer journey
- [ ] Set up payment processing
- [ ] Configure shipping options

### Tips for Success

1. **Choose Colors Wisely**: Use brand colors that reflect your business
2. **High-Quality Images**: Upload clear, professional product photos
3. **Clear Descriptions**: Write compelling product descriptions
4. **Mobile-First**: Always check mobile responsiveness
5. **Test Everything**: Test the complete customer journey
6. **SEO Optimization**: Use descriptive URLs and meta tags
7. **Regular Updates**: Keep products and content fresh

Your custom ecommerce store is now ready to start selling! 🚀