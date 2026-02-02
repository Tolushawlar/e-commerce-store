/**
 * Store Frontend JavaScript
 * Handles product loading and display for generated stores
 */

// API base URL - adjust based on your environment
const API_BASE_URL = window.location.origin;

/**
 * Load products for a store
 */
async function loadProducts(config) {
  const productsContainer = document.getElementById("products-container");

  if (!productsContainer) {
    console.error("Products container element not found");
    return;
  }

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
    const storeId = typeof config === "object" ? config.storeId : config;
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
    showEmptyState();
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
    <a href="product.html?id=${product.id}" class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow block">
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
        <div class="p-4">
            <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">${escapeHtml(product.name)}</h3>
            ${product.category_name ? `<p class="text-xs text-gray-500 mb-2">${escapeHtml(product.category_name)}</p>` : ""}
            <div class="flex items-center justify-between">
                <span class="text-lg font-bold" style="color: var(--primary);">${price}</span>
                ${
                  inStock
                    ? `<button onclick="event.preventDefault(); addToCart(${product.id})" class="px-4 py-2 text-sm font-semibold rounded-lg text-white" style="background-color: var(--primary);">
                        Add to Cart
                    </button>`
                    : `<button disabled class="px-4 py-2 text-sm font-semibold rounded-lg bg-gray-300 text-gray-500 cursor-not-allowed">
                        Unavailable
                    </button>`
                }
            </div>
        </div>
    </a>
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
function addToCart(productId) {
  // Use CartService if available
  if (window.CartService) {
    CartService.addItem(productId, 1);
    showToast("Product added to cart!");
  } else {
    // Fallback to localStorage
    const storeId = window.storeConfig?.store_id || window.storeConfig?.storeId;
    const key = storeId ? `cart_${storeId}` : "cart";
    let cart = JSON.parse(localStorage.getItem(key) || "[]");
    const existingItem = cart.find((item) => item.productId === productId);

    if (existingItem) {
      existingItem.quantity += 1;
    } else {
      cart.push({
        productId: productId,
        quantity: 1,
      });
    }

    localStorage.setItem(key, JSON.stringify(cart));
    showToast("Product added to cart!");

    // Update cart badge manually if CartService not available
    updateCartBadgeManual();
  }
}

/**
 * Update cart badge manually (fallback if CartService not loaded)
 */
function updateCartBadgeManual() {
  const badge = document.getElementById("cart-badge");
  if (badge) {
    const storeId = window.storeConfig?.store_id || window.storeConfig?.storeId;
    const key = storeId ? `cart_${storeId}` : "cart";
    const cart = JSON.parse(localStorage.getItem(key) || "[]");
    const count = cart.reduce((total, item) => total + item.quantity, 0);
    badge.textContent = count;
    if (count > 0) {
      badge.classList.remove("hidden");
    } else {
      badge.classList.add("hidden");
    }
  }
}

/**
 * Show toast notification
 */
function showToast(message) {
  const toast = document.createElement("div");
  toast.className =
    "fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300";
  toast.textContent = message;

  document.body.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = "0";
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}
