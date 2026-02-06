# Template-Specific Subpage System

## Overview

The ecommerce-store project now supports template-specific subpages that maintain consistent design patterns across all pages of a store. When a store is generated, all subpages (cart, checkout, login, profile, orders, order-success) will follow the same design template as the selected homepage template.

## How It Works

### Template Naming Convention

Template-specific subpages follow this naming pattern:
```
{template-name}-{page-type}.html
```

Examples:
- `bold-modern-cart.html`
- `classic-ecommerce-checkout.html`
- `minimal-clean-profile.html`

### Template Mapping

The system maps template names to file prefixes:

| Template Name | File Prefix |
|---------------|-------------|
| Bold Modern / Bold & Modern | `bold-modern` |
| Classic Ecommerce / Classic E-commerce | `classic-ecommerce` |
| Minimal Clean / Minimalist Clean | `minimal-clean` |
| Premium Luxury | `premium-luxury` |
| CampMart Style / Campmart Style | `campmart-style` |

### Fallback System

The system uses a three-tier fallback approach:

1. **Template-specific file**: `{template-name}-{page-type}.html`
2. **Generic template file**: `{page-type}.html`
3. **Basic fallback HTML**: Minimal HTML generated programmatically

## Implemented Templates

### Bold Modern Template
- **Design**: Bold, modern design with strong typography and vibrant colors
- **Features**: 
  - Gradient backgrounds and rounded corners
  - Bold, uppercase text with tight tracking
  - Scale hover effects and shadow elements
  - Consistent color scheme across all pages

**Files Created:**
- `bold-modern-cart.html`
- `bold-modern-checkout.html`
- `bold-modern-login.html`
- `bold-modern-profile.html`
- `bold-modern-orders.html`

### Classic Ecommerce Template
- **Design**: Traditional ecommerce layout with clean, professional styling
- **Features**:
  - Top notification bar
  - Classic header with search functionality
  - Breadcrumb navigation
  - Standard form layouts and buttons

**Files Created:**
- `classic-ecommerce-cart.html`
- `classic-ecommerce-profile.html`

### Minimal Clean Template
- **Design**: Minimalist approach with clean lines and subtle styling
- **Features**:
  - Light typography and spacing
  - Minimal borders and shadows
  - Clean, uncluttered layouts
  - Subtle hover effects

**Files Created:**
- `minimal-clean-cart.html`
- `minimal-clean-profile.html`

## Design Consistency Features

### Header & Navigation
Each template maintains its unique header design:
- **Bold Modern**: Gradient logo, bold uppercase navigation
- **Classic Ecommerce**: Traditional header with search bar and top notification
- **Minimal Clean**: Simple, light header with minimal elements

### Footer
Consistent footer design matching the template style:
- **Bold Modern**: Dark footer with accent colors and bold typography
- **Classic Ecommerce**: Multi-column footer with newsletter signup
- **Minimal Clean**: Simple, centered footer with minimal text

### Form Elements
All form inputs, buttons, and interactive elements follow the template's design language:
- **Bold Modern**: Rounded corners, bold fonts, scale effects
- **Classic Ecommerce**: Standard borders, traditional styling
- **Minimal Clean**: Minimal borders, light styling

### Color Schemes
Each template uses the store's configured primary and accent colors consistently across all pages.

## Store Generation Process

When a store is generated:

1. **Template Detection**: The system identifies the selected template
2. **Template Mapping**: Maps the template name to the appropriate file prefix
3. **File Generation**: For each subpage type:
   - Looks for template-specific file first
   - Falls back to generic template if not found
   - Generates basic HTML as final fallback
4. **Placeholder Replacement**: Replaces template placeholders with store data
5. **File Creation**: Saves all generated files to the store directory

## Benefits

### For Store Owners
- **Consistent Branding**: All pages maintain the same visual identity
- **Professional Appearance**: No design inconsistencies between pages
- **Better User Experience**: Familiar navigation and styling across the entire store

### For Developers
- **Maintainable Code**: Template-specific files are easier to maintain
- **Scalable System**: Easy to add new templates or modify existing ones
- **Flexible Fallbacks**: System gracefully handles missing template files

## Adding New Templates

To add a new template with consistent subpages:

1. **Create Homepage Template**: Add the main template file (e.g., `new-template.html`)
2. **Create Subpage Templates**: Create template-specific versions:
   - `new-template-cart.html`
   - `new-template-checkout.html`
   - `new-template-login.html`
   - `new-template-profile.html`
   - `new-template-orders.html`
   - `new-template-order-success.html`
3. **Update Template Mapping**: Add the template name mapping in `StoreGeneratorService.php`
4. **Test Generation**: Verify all pages generate correctly with consistent styling

## File Structure

```
store-templates/
├── bold-modern.html                 # Homepage template
├── bold-modern-cart.html           # Cart page
├── bold-modern-checkout.html       # Checkout page
├── bold-modern-login.html          # Login page
├── bold-modern-profile.html        # Profile page
├── bold-modern-orders.html         # Orders page
├── classic-ecommerce.html          # Homepage template
├── classic-ecommerce-cart.html     # Cart page
├── classic-ecommerce-profile.html  # Profile page
├── minimal-clean.html              # Homepage template
├── minimal-clean-cart.html         # Cart page
├── minimal-clean-profile.html      # Profile page
├── cart.html                       # Generic fallback
├── checkout.html                   # Generic fallback
├── login.html                      # Generic fallback
├── profile.html                    # Generic fallback
├── orders.html                     # Generic fallback
└── order-success.html              # Generic fallback
```

## Technical Implementation

The system is implemented in the `StoreGeneratorService` class with these key methods:

- `getTemplateName()`: Maps template names to file prefixes
- `generateTemplateSpecificHTML()`: Handles template-specific file generation with fallbacks
- `generateFallbackPageHTML()`: Creates basic HTML when no template is found

This ensures that every store has a complete set of pages with consistent design, regardless of which template is selected.