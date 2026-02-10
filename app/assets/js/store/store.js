/**
 * Store Frontend JavaScript
 * Handles product loading and display for generated stores
 */

if (typeof API_BASE_URL === "undefined") {
  var API_BASE_URL = window.location.origin;
}

// Global variables to store products and config
let allProducts = [];
let currentConfig = null;
let selectedCategoryId = null;
let categoriesData = [];
let searchQuery = '';

/**
 * Load products for a store
 */
async function loadProducts(config) {
  const productsContainer = document.getElementById("products-container");

  if (!productsContainer) {
    console.error("Products container element not found");
    return;
  }

  // Store config globally
  currentConfig = config;

  // Show loading state
  productsContainer.innerHTML = `
        <div class="flex items-center justify-center py-12">
            <div class="text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900 mx-auto mb-4"></div>
                <p class="text-gray-600">Loading products...</p>
            </div>
        </div>
    `;

  try {
    const storeId = typeof config === "object" ? config.store_id : config;
    const groupByCategory = config.groupByCategory || false;
    const productGridColumns = config.productGridColumns || 4;
    const showCategoryImages = config.showCategoryImages !== false;

    // Fetch products from API
    const response = await fetch(
      `${API_BASE_URL}/api/products?store_id=${storeId}&limit=100&status=active`,
    );

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();

    if (data.success && data.data && data.data.products) {
      // Store all products globally
      allProducts = data.data.products;
      
      if (groupByCategory) {
        // Fetch categories and group products
        await displayProductsByCategory(
          storeId,
          data.data.products,
          productGridColumns,
          showCategoryImages,
        );
      } else {
        // Display products in simple grid
        displayProductsGrid(data.data.products, productGridColumns);
      }
    } else {
      showEmptyState();
    }
  } catch (error) {
    console.error("Error loading products:", error);
    showErrorState();
  }
}

/**
 * Display products in simple grid
 */
function displayProductsGrid(products, gridColumns = 4) {
  const productsContainer = document.getElementById("products-container");

  if (!products || products.length === 0) {
    // Show context-aware empty state
    if (searchQuery) {
      // Empty state for search
      productsContainer.innerHTML = `
        <div class="flex items-center justify-center py-12">
          <div class="text-center">
            <span class="material-symbols-outlined text-6xl text-gray-400 mb-4">search_off</span>
            <p class="text-gray-600 text-lg">No results for "${escapeHtml(searchQuery)}"</p>
            <p class="text-gray-500 text-sm mt-2">Try a different search term or browse our categories</p>
          </div>
        </div>
      `;
    } else if (selectedCategoryId !== null) {
      // Empty state for category
      const category = categoriesData.find(cat => cat.id === selectedCategoryId);
      const categoryName = category ? category.name : 'this category';
      productsContainer.innerHTML = `
        <div class="flex items-center justify-center py-12">
          <div class="text-center">
            <span class="material-symbols-outlined text-6xl text-gray-400 mb-4">inventory_2</span>
            <p class="text-gray-600 text-lg">No products in ${categoryName}</p>
            <p class="text-gray-500 text-sm mt-2">Try browsing other categories!</p>
          </div>
        </div>
      `;
    } else {
      // Default empty state
      showEmptyState();
    }
    return;
  }

  // Map grid columns to responsive classes
  const gridClasses = {
    1: "grid-cols-1",
    2: "grid-cols-1 sm:grid-cols-2",
    3: "grid-cols-1 sm:grid-cols-2 lg:grid-cols-3",
    4: "grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4",
  };

  const gridClass = gridClasses[gridColumns] || gridClasses[4];

  let html = `<div class="grid ${gridClass} gap-4 md:gap-6">`;

  products.forEach((product) => {
    html += renderProductCard(product);
  });

  html += "</div>";
  productsContainer.innerHTML = html;
}

/**
 * Display products grouped by categories
 */
async function displayProductsByCategory(
  storeId,
  products,
  gridColumns = 4,
  showCategoryImages = true,
) {
  const productsContainer = document.getElementById("products-container");

  if (!products || products.length === 0) {
    showEmptyState();
    return;
  }

  try {
    // Fetch categories
    const response = await fetch(
      `${API_BASE_URL}/api/categories?store_id=${storeId}&status=active`,
    );
    const categoryData = await response.json();

    if (!categoryData.success) {
      // Fallback to simple grid if categories fail to load
      displayProductsGrid(products, gridColumns);
      return;
    }

    const categories = categoryData.data.categories;

    // Group products by category
    const productsByCategory = {};
    const uncategorized = [];

    products.forEach((product) => {
      if (product.category_id) {
        if (!productsByCategory[product.category_id]) {
          productsByCategory[product.category_id] = [];
        }
        productsByCategory[product.category_id].push(product);
      } else {
        uncategorized.push(product);
      }
    });

    let html = "";

    // Map grid columns to responsive classes
    const gridClasses = {
      1: "grid-cols-1",
      2: "grid-cols-1 sm:grid-cols-2",
      3: "grid-cols-1 sm:grid-cols-2 lg:grid-cols-3",
      4: "grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4",
    };
    const gridClass = gridClasses[gridColumns] || gridClasses[4];

    // Render each category section
    categories.forEach((category) => {
      const categoryProducts = productsByCategory[category.id];
      if (categoryProducts && categoryProducts.length > 0) {
        html += `
          <div class="mb-12">
            <div class="flex items-center gap-3 mb-6">
              ${showCategoryImages && category.icon ? `<span class="material-symbols-outlined text-3xl" style="color: ${category.color || "var(--primary)"};">${escapeHtml(category.icon)}</span>` : ""}
              <div>
                <h3 class="text-2xl font-bold" style="color: var(--primary);">${escapeHtml(category.name)}</h3>
                ${category.description ? `<p class="text-gray-600 text-sm">${escapeHtml(category.description)}</p>` : ""}
              </div>
            </div>
            <div class="grid ${gridClass} gap-4 md:gap-6">
        `;

        categoryProducts.forEach((product) => {
          html += renderProductCard(product);
        });

        html += `
            </div>
          </div>
        `;
      }
    });

    // Render uncategorized products if any
    if (uncategorized.length > 0) {
      html += `
        <div class="mb-12">
          <h3 class="text-2xl font-bold mb-6" style="color: var(--primary);">Other Products</h3>
          <div class="grid ${gridClass} gap-4 md:gap-6">
      `;

      uncategorized.forEach((product) => {
        html += renderProductCard(product);
      });

      html += `
          </div>
        </div>
      `;
    }

    productsContainer.innerHTML = html;
  } catch (error) {
    console.error("Error grouping products by category:", error);
    // Fallback to simple grid
    displayProductsGrid(products, gridColumns);
  }
}

/**
 * Render a single product card
 */
function renderProductCard(product) {
  const image = product.image_url
    ? product.image_url.split(",")[0]
    : product.images?.find((img) => img.is_primary)?.image_url ||
      product.images?.[0]?.image_url;
  const price = formatCurrency(product.price);
  const inStock = product.stock_quantity > 0;

  return `
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow flex flex-col h-full">
        <a href="product.html?id=${product.id}" class="block">
            <div class="aspect-square bg-gray-100 relative">
                ${
                  image
                    ? `<img src="${image}" alt="${escapeHtml(product.name)}" class="w-full h-full object-cover">`
                    : `<div class="w-full h-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-6xl text-gray-400">inventory_2</span>
                    </div>`
                }
                ${!inStock ? '<div class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">Out of Stock</div>' : ""}
            </div>
        </a>
        <div class="p-4 flex flex-col flex-grow">
            <a href="product.html?id=${product.id}" class="block mb-2">
                <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2 hover:text-opacity-80 min-h-[3rem]">${escapeHtml(product.name)}</h3>
            </a>
            ${product.category_name ? `<p class="text-xs text-gray-500 mb-3">${escapeHtml(product.category_name)}</p>` : ""}
            <div class="mt-auto space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xl font-bold" style="color: var(--primary);">${price}</span>
                </div>
                ${
                  inStock
                    ? `<button onclick="addToCart(${product.id})" class="w-full px-4 py-2.5 text-sm font-semibold rounded-lg text-white hover:brightness-110 transition-all" style="background-color: var(--primary); display: block;">
                        Add to Cart
                    </button>`
                    : `<button disabled class="w-full px-4 py-2.5 text-sm font-semibold rounded-lg bg-gray-300 text-gray-500 cursor-not-allowed" style="display: block;">
                        Unavailable
                    </button>`
                }
            </div>
        </div>
    </div>
  `;
}

/**
 * Display products in the grid (legacy - keeping for backwards compatibility)
 */
function displayProducts(products) {
  displayProductsGrid(products, 4);
}

/**
 * Show empty state
 */
function showEmptyState() {
  const productsContainer = document.getElementById("products-container");
  productsContainer.innerHTML = `
        <div class="flex items-center justify-center py-12">
            <div class="text-center">
                <span class="material-symbols-outlined text-6xl text-gray-400 mb-4">inventory_2</span>
                <p class="text-gray-600 text-lg">No products available yet</p>
                <p class="text-gray-500 text-sm mt-2">Check back soon for new arrivals!</p>
            </div>
        </div>
    `;
}

/**
 * Show error state
 */
function showErrorState() {
  const productsContainer = document.getElementById("products-container");
  productsContainer.innerHTML = `
        <div class="flex items-center justify-center py-12">
            <div class="text-center">
                <span class="material-symbols-outlined text-6xl text-red-400 mb-4">error</span>
                <p class="text-gray-600 text-lg">Failed to load products</p>
                <p class="text-gray-500 text-sm mt-2">Please try again later</p>
                <button onclick="location.reload()" class="mt-4 px-6 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800">
                    Retry
                </button>
            </div>
        </div>
    `;
}

/**
 * Format currency (Nigerian Naira)
 */
function formatCurrency(amount) {
  return "₦" + Number(amount).toLocaleString("en-NG");
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
  if (!text) return "";
  const map = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#039;",
  };
  return text.replace(/[&<>"']/g, (m) => map[m]);
}

/**
 * Add to cart (integrated with CartService)
 */
async function addToCart(productId) {
  try {
    // Check if user is authenticated
    const isAuthenticated = typeof CustomerAuth !== 'undefined' && CustomerAuth.isAuthenticated();
    
    if (isAuthenticated && typeof window.CartService !== 'undefined') {
      // Use CartService for authenticated users
      const storeId = window.storeConfig?.store_id || window.storeConfig?.storeId;
      const result = await window.CartService.addItem(storeId, productId, 1);

      if (result.success) {
        // Update cart badge
        if (result.data && result.data.items) {
          const totalItems = result.data.items.reduce((sum, item) => sum + parseInt(item.quantity || 0), 0);
          const badge = document.getElementById("cart-badge");
          if (badge) {
            badge.textContent = totalItems;
            badge.classList.toggle("hidden", totalItems === 0);
          }
        }

        // Dispatch event for other components
        window.dispatchEvent(new CustomEvent('cartUpdated', {
          detail: { storeId: storeId, items: result.data.items }
        }));

        showToast("Product added to cart!");
      } else {
        showToast(result.message || "Failed to add to cart", "error");
      }
    } else {
      // Guest user - use localStorage
      const storeId = window.storeConfig?.store_id || window.storeConfig?.storeId;
      
      // Fetch current product data to ensure we have all details
      const response = await fetch(`${API_BASE_URL}/api/products/${productId}`);
      const data = await response.json();

      if (!data.success || !data.data) {
        showToast("Failed to add to cart", "error");
        return;
      }

      const product = data.data;

      // Get cart from localStorage
      let cart = JSON.parse(
        localStorage.getItem(`cart_${storeId}`) || "[]"
      );

      // Check if product already in cart
      const existingItemIndex = cart.findIndex(
        (item) => item.product_id === parseInt(productId)
      );

      if (existingItemIndex > -1) {
        cart[existingItemIndex].quantity += 1;
      } else {
        // Use consistent structure with cart.service.js
        cart.push({
          product_id: parseInt(productId),
          product_name: product.name,
          price: parseFloat(product.price),
          quantity: 1,
          image_url: product.image_url || product.images?.[0]?.image_url,
          stock_quantity: product.stock_quantity
        });
      }

      // Save cart
      localStorage.setItem(`cart_${storeId}`, JSON.stringify(cart));

      // Update cart badge
      updateCartBadgeManual(cart);

      // Dispatch event for other components
      window.dispatchEvent(new CustomEvent('cartUpdated', {
        detail: { storeId: storeId, items: cart }
      }));

      showToast("Product added to cart!");
    }
  } catch (error) {
    console.error('Error adding to cart:', error);
    showToast("Failed to add to cart. Please try again.", "error");
  }
}

/**
 * Update cart badge manually (fallback if CartService not loaded)
 */
function updateCartBadgeManual(cart = null) {
  const badge = document.getElementById("cart-badge");
  if (badge) {
    const storeId = window.storeConfig?.store_id || window.storeConfig?.storeId;
    
    // Use provided cart or fetch from localStorage
    if (!cart) {
      const key = storeId ? `cart_${storeId}` : "cart";
      cart = JSON.parse(localStorage.getItem(key) || "[]");
    }
    
    const count = cart.reduce((total, item) => total + (item.quantity || 0), 0);
    badge.textContent = count;
    if (count > 0) {
      badge.classList.remove("hidden");
    } else {
      badge.classList.add("hidden");
    }
  }
}

/**
 * Filter products by category
 */
function filterProductsByCategory(categoryId) {
  selectedCategoryId = categoryId;
  
  // Clear search when filtering by category
  searchQuery = '';
  const searchInput = document.getElementById('search-input');
  if (searchInput) {
    searchInput.value = '';
  }
  
  const productGridColumns = currentConfig?.productGridColumns || 4;
  const productsSection = document.getElementById('products-section');
  const sectionTitle = document.getElementById('products-section-title');
  
  // Update section title
  if (sectionTitle) {
    if (categoryId === null) {
      sectionTitle.textContent = 'All Products';
    } else {
      const category = categoriesData.find(cat => cat.id === categoryId);
      if (category) {
        sectionTitle.textContent = category.name;
      }
    }
  }
  
  // Scroll to products section smoothly
  if (productsSection) {
    productsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
  
  if (categoryId === null) {
    // Show all products
    displayProductsGrid(allProducts, productGridColumns);
  } else {
    // Filter products by category
    const filteredProducts = allProducts.filter(product => product.category_id === categoryId);
    displayProductsGrid(filteredProducts, productGridColumns);
  }
  
  // Update active state on category buttons
  updateCategoryActiveState(categoryId);
}

/**
 * Update active state on category buttons
 */
function updateCategoryActiveState(activeCategoryId) {
  const categoryButtons = document.querySelectorAll('[data-category-id]');
  categoryButtons.forEach(button => {
    const categoryId = button.getAttribute('data-category-id');
    const isActive = categoryId === String(activeCategoryId) || (categoryId === 'all' && activeCategoryId === null);
    
    if (isActive) {
      button.classList.add('border-primary', 'shadow-md', 'bg-primary/5');
      button.classList.remove('border-slate-100');
    } else {
      button.classList.remove('border-primary', 'shadow-md', 'bg-primary/5');
      button.classList.add('border-slate-100');
    }
  });
}

/**
 * Load and display categories
 */
async function loadCategories(storeId) {
  const categoriesContainer = document.getElementById("categories-container");
  const categoriesSection = document.getElementById("categories-section");
  
  if (!categoriesContainer) {
    console.error("Categories container element not found");
    return;
  }

  // Check if groupByCategory is enabled in config
  if (currentConfig && !currentConfig.groupByCategory) {
    console.log('[Categories] groupByCategory is disabled, hiding categories section');
    if (categoriesSection) {
      categoriesSection.style.display = 'none';
    }
    return;
  }

  try {
    // Fetch categories from API
    const response = await fetch(
      `${API_BASE_URL}/api/categories?store_id=${storeId}&status=active`,
    );

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();

    if (data.success && data.data && data.data.categories) {
      const categories = data.data.categories;
      
      // Store categories globally for reference
      categoriesData = categories;
      
      if (categories.length === 0) {
        if (categoriesSection) {
          categoriesSection.style.display = 'none';
        } else {
          categoriesContainer.style.display = 'none';
        }
        return;
      }

      let html = '';
      
      // Add "All Categories" button
      html += `
        <div onclick="filterProductsByCategory(null)" data-category-id="all" class="flex flex-col items-center gap-3 p-4 rounded-2xl border border-primary bg-primary/5 shadow-md hover:shadow-lg transition-all cursor-pointer min-w-[140px]">
          <div class="size-14 rounded-full bg-slate-50 flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-3xl">grid_view</span>
          </div>
          <span class="text-sm font-bold">All</span>
        </div>
      `;
      
      categories.forEach((category) => {
        const icon = category.icon || 'category';
        const color = category.color || 'var(--primary)';
        
        html += `
          <div onclick="filterProductsByCategory(${category.id})" data-category-id="${category.id}" class="flex flex-col items-center gap-3 p-4 rounded-2xl border border-slate-100 bg-white hover:border-primary hover:shadow-md transition-all cursor-pointer min-w-[140px]">
            <div class="size-14 rounded-full bg-slate-50 flex items-center justify-center" style="color: ${escapeHtml(color)};">
              <span class="material-symbols-outlined text-3xl">${escapeHtml(icon)}</span>
            </div>
            <span class="text-sm font-bold">${escapeHtml(category.name)}</span>
          </div>
        `;
      });
      
      categoriesContainer.innerHTML = html;
    } else {
      if (categoriesSection) {
        categoriesSection.style.display = 'none';
      } else {
        categoriesContainer.style.display = 'none';
      }
    }
  } catch (error) {
    console.error("Error loading categories:", error);
    if (categoriesSection) {
      categoriesSection.style.display = 'none';
    } else {
      categoriesContainer.style.display = 'none';
    }
  }
}

/**
 * Show toast notification
 */
function showToast(message, type = "success") {
  const toast = document.createElement("div");
  const bgColor = type === "error" ? "bg-red-500" : type === "warning" ? "bg-orange-500" : "bg-green-500";
  toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300`;
  toast.textContent = message;

  document.body.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = "0";
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

/**
 * Search products by query
 */
function searchProducts(query) {
  searchQuery = query.trim();
  
  // Update section title
  const sectionTitle = document.getElementById('products-section-title');
  
  if (!searchQuery) {
    // If empty search, reset to show all products
    selectedCategoryId = null;
    updateCategoryActiveState(null);
    if (sectionTitle) {
      sectionTitle.textContent = 'Our Products';
    }
    displayProductsGrid(allProducts, currentConfig?.productGridColumns || 4);
    return;
  }
  
  // Filter products by search query (case-insensitive)
  const query_lower = searchQuery.toLowerCase();
  const filteredProducts = allProducts.filter(product => {
    return (
      product.name?.toLowerCase().includes(query_lower) ||
      product.description?.toLowerCase().includes(query_lower) ||
      product.category_name?.toLowerCase().includes(query_lower)
    );
  });
  
  // Clear category selection when searching
  selectedCategoryId = null;
  updateCategoryActiveState(null);
  
  // Update section title
  if (sectionTitle) {
    sectionTitle.textContent = `Search Results for "${searchQuery}"`;
  }
  
  // Display filtered products
  displayProductsGrid(filteredProducts, currentConfig?.productGridColumns || 4);
  
  // Scroll to products section
  const productsSection = document.getElementById('products-section');
  if (productsSection) {
    productsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
}

/**
 * Clear search and reset to all products
 */
function clearSearch() {
  searchQuery = '';
  selectedCategoryId = null;
  updateCategoryActiveState(null);
  
  const searchInput = document.getElementById('search-input');
  if (searchInput) {
    searchInput.value = '';
  }
  
  const sectionTitle = document.getElementById('products-section-title');
  if (sectionTitle) {
    sectionTitle.textContent = 'Our Products';
  }
  
  displayProductsGrid(allProducts, currentConfig?.productGridColumns || 4);
}

/**
 * Initialize search event listeners
 */
function initSearchListeners() {
  const searchInput = document.getElementById('search-input');
  const searchButton = document.getElementById('search-button');
  
  if (!searchInput || !searchButton) return;
  
  // Search on button click
  searchButton.addEventListener('click', () => {
    const query = searchInput.value;
    searchProducts(query);
  });
  
  // Search on Enter key
  searchInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
      const query = searchInput.value;
      searchProducts(query);
    }
  });
  
  // Optional: Real-time search (debounced)
  let searchTimeout;
  searchInput.addEventListener('input', (e) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      const query = e.target.value;
      if (query.length >= 2 || query.length === 0) {
        searchProducts(query);
      }
    }, 500); // Debounce by 500ms
  });
}
