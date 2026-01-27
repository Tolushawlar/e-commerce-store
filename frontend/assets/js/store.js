/**
 * Store Frontend JavaScript
 * Handles product loading and display for generated stores
 */

// API base URL - adjust based on your environment
const API_BASE_URL = window.location.origin;

/**
 * Load products for a store
 */
async function loadProducts(storeId) {
  const productsGrid = document.getElementById("products-grid");

  if (!productsGrid) {
    console.error("Products grid element not found");
    return;
  }

  // Show loading state
  productsGrid.innerHTML = `
        <div class="col-span-full flex items-center justify-center py-12">
            <div class="text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900 mx-auto mb-4"></div>
                <p class="text-gray-600">Loading products...</p>
            </div>
        </div>
    `;

  try {
    // Fetch products from API
    const response = await fetch(
      `${API_BASE_URL}/api/products?store_id=${storeId}&limit=12&status=active`,
    );

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();

    if (data.success && data.data && data.data.products) {
      displayProducts(data.data.products);
    } else {
      showEmptyState();
    }
  } catch (error) {
    console.error("Error loading products:", error);
    showErrorState();
  }
}

/**
 * Display products in the grid
 */
function displayProducts(products) {
  const productsGrid = document.getElementById("products-grid");

  if (!products || products.length === 0) {
    showEmptyState();
    return;
  }

  let html = "";

  products.forEach((product) => {
    const image = product.image_url ? product.image_url.split(",")[0] : "";
    const price = formatCurrency(product.price);
    const inStock = product.stock_quantity > 0;

    html += `
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
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
                    ${product.category ? `<p class="text-xs text-gray-500 mb-2">${escapeHtml(product.category)}</p>` : ""}
                    <div class="flex items-center justify-between">
                        <span class="text-lg font-bold" style="color: var(--primary);">${price}</span>
                        ${
                          inStock
                            ? `<button onclick="addToCart(${product.id})" class="px-4 py-2 text-sm font-semibold rounded-lg text-white" style="background-color: var(--primary);">
                                Add to Cart
                            </button>`
                            : `<button disabled class="px-4 py-2 text-sm font-semibold rounded-lg bg-gray-300 text-gray-500 cursor-not-allowed">
                                Unavailable
                            </button>`
                        }
                    </div>
                </div>
            </div>
        `;
  });

  productsGrid.innerHTML = html;
}

/**
 * Show empty state
 */
function showEmptyState() {
  const productsGrid = document.getElementById("products-grid");
  productsGrid.innerHTML = `
        <div class="col-span-full flex items-center justify-center py-12">
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
  const productsGrid = document.getElementById("products-grid");
  productsGrid.innerHTML = `
        <div class="col-span-full flex items-center justify-center py-12">
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
 * Add to cart (placeholder function)
 */
function addToCart(productId) {
  // Get existing cart from localStorage
  let cart = JSON.parse(localStorage.getItem("cart") || "[]");

  // Check if product already in cart
  const existingItem = cart.find((item) => item.productId === productId);

  if (existingItem) {
    existingItem.quantity += 1;
  } else {
    cart.push({
      productId: productId,
      quantity: 1,
    });
  }

  // Save cart
  localStorage.setItem("cart", JSON.stringify(cart));

  // Show success message
  showToast("Product added to cart!");
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
