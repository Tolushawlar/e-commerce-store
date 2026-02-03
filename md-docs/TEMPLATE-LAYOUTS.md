# Template Layouts - Design Differences

## Overview

Each template now has a **completely unique layout**, not just different colors. Here's what makes each one distinct:

---

## 1. CampMart Style (campmart-style.html)

**Design Philosophy**: Modern marketplace with bold hero section

### Layout Features
- **Header**: Sticky header with centered logo, search bar, and cart
- **Hero**: Full-width gradient background with large typography
- **Navigation**: Clean horizontal menu
- **Product Grid**: 4-column grid with rounded cards
- **Footer**: 4-column footer with links and social icons

### Visual Style
- Large hero with overlapping elements
- Bold accent colors throughout
- Rounded corners everywhere
- Material icons for UI elements

### Best For
- General marketplaces
- Campus/student stores
- Modern brands

---

## 2. Minimal Clean (minimal-clean.html)

**Design Philosophy**: Less is more - focus on whitespace

### Layout Features
- **Header**: Simple border-bottom with centered navigation
- **Hero**: Centered text-only hero, no background image
- **Navigation**: Minimal text links, no icons
- **Product Grid**: 3-column grid with lots of spacing
- **Footer**: Single-line minimal footer

### Visual Style
- Maximum whitespace
- Light gray backgrounds only
- Thin lines and borders
- Ultra-simple product cards
- No decorative elements

### Best For
- Professional stores
- Minimalist brands
- Art/design portfolios

---

## 3. Bold Modern (bold-modern.html)

**Design Philosophy**: Energetic and vibrant with strong CTAs

### Layout Features
- **Header**: Bold header with gradient logo and uppercase nav
- **Hero**: Split-screen hero with decorative elements
- **Navigation**: Uppercase links with "NEW IN", "TRENDING"
- **Product Grid**: 4-column with hover effects and shadows
- **Footer**: Dark footer with bright accents

### Visual Style
- Gradient backgrounds
- Bold uppercase typography (Poppins font)
- Bright accent badges
- Strong shadows and depth
- Floating decorative shapes

### Best For
- Fashion brands
- Youth-oriented stores
- Trend-focused businesses

---

## 4. Classic Ecommerce (classic-ecommerce.html)

**Design Philosophy**: Traditional online store layout

### Layout Features
- **Top Bar**: Promotional banner at top
- **Header**: Logo + search + account/cart
- **Navigation**: Category menu with "All Categories" dropdown
- **Sidebar**: Left sidebar with filters and categories
- **Product Grid**: 3-column grid in main content area
- **Footer**: Newsletter signup + 4-column links

### Visual Style
- Grid-based layout
- Sidebar navigation (12-column grid)
- Traditional product cards
- Breadcrumbs
- Filter panels
- Roboto font (web-safe)

### Best For
- Traditional retail
- Multi-category stores
- B2C ecommerce

---

## 5. Premium Luxury (premium-luxury.html)

**Design Philosophy**: Sophisticated elegance for high-end products

### Layout Features
- **Header**: Centered brand name with minimal navigation
- **Hero**: Large full-height hero with decorative borders
- **Navigation**: Uppercase spaced-out links
- **Product Grid**: 3-column with tall aspect ratios
- **Footer**: Elegant 4-column with refined typography

### Visual Style
- Serif font (Playfair Display) for headings
- Lots of negative space
- Dark backgrounds with light text
- Subtle borders and decorative elements
- Tall product images (3:4 aspect ratio)
- Gold/accent color highlights

### Best For
- Luxury brands
- High-end products
- Fashion boutiques
- Jewelry stores

---

## Key Differences Summary

| Feature | CampMart | Minimal | Bold | Classic | Luxury |
|---------|----------|---------|------|---------|--------|
| **Font** | Plus Jakarta Sans | Inter | Poppins | Roboto | Playfair + Lato |
| **Hero Style** | Full gradient | Text-only | Split-screen | Banner | Full-height |
| **Product Grid** | 4 columns | 3 columns | 4 columns | 3 columns (+ sidebar) | 3 columns |
| **Navigation** | Horizontal | Minimal | Uppercase bold | Category menu | Centered |
| **Complexity** | Medium | Low | High | High | Medium |
| **Sidebar** | No | No | No | Yes | No |
| **Visual Weight** | Bold | Light | Very bold | Traditional | Elegant |

---

## Template Selection Guide

### Choose **CampMart Style** if you want:
- Modern, friendly marketplace
- Balanced design
- Versatile for most products

### Choose **Minimal Clean** if you want:
- Ultra-simple design
- Focus on products, not decoration
- Professional, refined look

### Choose **Bold Modern** if you want:
- Eye-catching, energetic design
- Strong brand personality
- Youth appeal

### Choose **Classic Ecommerce** if you want:
- Familiar shopping experience
- Category browsing
- Traditional ecommerce features

### Choose **Premium Luxury** if you want:
- Sophisticated, elegant design
- High-end brand positioning
- Emphasis on quality over quantity

---

## Technical Implementation

Each template:
- ✅ Has unique HTML structure
- ✅ Uses different layouts and components
- ✅ Supports same placeholders
- ✅ Works with store customization (colors)
- ✅ Is fully responsive
- ✅ Includes product loading via API

All templates share:
- Same placeholder system
- Same JavaScript integration
- Same product loading logic
- Compatibility with StoreGeneratorService

---

## Adding Custom Templates

To create your own template:

1. Create new HTML file in `store-templates/`
2. Use existing placeholders: `{{store_name}}`, etc.
3. Design unique layout and styling
4. Add to `populate_templates.php` configuration
5. Run population script
6. Test with a new store

Example:
```php
6 => [
    'file' => 'my-custom-template.html',
    'description' => 'My unique custom design'
]
```
