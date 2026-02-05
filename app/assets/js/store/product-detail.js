// Load configuration
let storeConfig = {};
let mainSwiper, thumbnailSwiper;
if (typeof API_BASE_URL === "undefined") {
  var API_BASE_URL = window.location.origin;
}

// Initialize page
document.addEventListener("DOMContentLoaded", async () => {
  await loadConfig();
  const productId = getProductIdFromUrl();

  if (!productId) {
    showError("Product not found");
    return;
  }

  await loadProductDetails(productId);
  initializeQuantityControls();
  initializeCartButton();
});

// Load store configuration
async function loadConfig() {
  try {
    const response = await fetch("config.json");
    storeConfig = await response.json();

    // Set store branding
    document.getElementById("store-name").textContent = storeConfig.name;
    document.getElementById("footer-store-name").textContent = storeConfig.name;

    // Set logo if available
    if (storeConfig.logo) {
      const logoElement = document.getElementById("store-logo");
      logoElement.src = storeConfig.logo;
      logoElement.classList.remove("hidden");
    }

    // Set theme colors
    if (storeConfig.primaryColor) {
      document.documentElement.style.setProperty(
        "--primary",
        storeConfig.primaryColor,
      );
    }
    if (storeConfig.accentColor) {
      document.documentElement.style.setProperty(
        "--accent",
        storeConfig.accentColor,
      );
    }
  } catch (error) {
    console.error("Error loading config:", error);
  }
}

// Get product ID from URL parameter
function getProductIdFromUrl() {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get("id");
}

// Load product details from API
async function loadProductDetails(productId) {
  try {
    const response = await fetch(`${API_BASE_URL}/api/products/${productId}`);

    if (!response.ok) {
      throw new Error("Product not found");
    }

    const data = await response.json();

    if (data.success && data.data) {
      displayProduct(data.data);
    } else {
      throw new Error("Invalid product data");
    }
  } catch (error) {
    console.error("Error loading product:", error);
    showError("Failed to load product details. Please try again.");
  }
}

// Display product information
function displayProduct(product) {
  // Set page title
  document.title = `${product.name} - ${storeConfig.name}`;
  document.getElementById("page-title").textContent =
    `${product.name} - ${storeConfig.name}`;

  // Breadcrumb
  document.getElementById("breadcrumb-category").textContent =
    product.category_name || "Products";
  document.getElementById("breadcrumb-product").textContent = product.name;

  // Category badge
  const categoryBadgeContainer = document.getElementById(
    "category-badge-container",
  );
  categoryBadgeContainer.innerHTML = `
        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-gray-100 text-gray-700">
            <span class="material-symbols-outlined text-sm">category</span>
            ${product.category_name || "Uncategorized"}
        </span>
    `;

  // Product name and SKU
  document.getElementById("product-name").textContent = product.name;
  document.getElementById("product-sku").textContent = product.sku
    ? `SKU: ${product.sku}`
    : "";

  // Price
  const priceElement = document.getElementById("product-price");
  priceElement.textContent = `₦${parseFloat(product.price).toLocaleString("en-NG", { minimumFractionDigits: 2 })}`;

  // Stock status
  const stockBadge = document.getElementById("stock-badge");
  const inStock = product.stock_quantity > 0;
  stockBadge.innerHTML = `
        <span class="material-symbols-outlined text-sm ${inStock ? "text-green-600" : "text-red-600"}">
            ${inStock ? "check_circle" : "cancel"}
        </span>
        <span class="text-sm font-semibold ${inStock ? "text-green-600" : "text-red-600"}">
            ${inStock ? `${product.stock_quantity} in stock` : "Out of stock"}
        </span>
    `;

  // Description
  document.getElementById("product-description").textContent =
    product.description || "No description available.";

  // Product details grid
  document.getElementById("detail-category").textContent =
    product.category_name || "Uncategorized";
  document.getElementById("detail-stock").textContent = product.stock_quantity;
  document.getElementById("detail-sku").textContent = product.sku || "N/A";

  const detailStatus = document.getElementById("detail-status");
  detailStatus.textContent = inStock ? "Available" : "Out of Stock";
  detailStatus.className = `font-semibold ${inStock ? "text-green-600" : "text-red-600"}`;

  // Set max quantity
  const qtyInput = document.getElementById("quantity");
  qtyInput.max = product.stock_quantity;

  // Disable add to cart if out of stock
  const addToCartBtn = document.getElementById("add-to-cart");
  if (!inStock) {
    addToCartBtn.disabled = true;
    addToCartBtn.classList.add("opacity-50", "cursor-not-allowed");
    addToCartBtn.innerHTML =
      '<span class="material-symbols-outlined">block</span> Out of Stock';
  }

  // Initialize image gallery
  initializeImageGallery(product.images || []);

  // Load related products
  loadRelatedProducts(product.category_id, product.id, storeConfig.store_id);
}

// Initialize image gallery with Swiper
function initializeImageGallery(images) {
  const mainWrapper = document.getElementById("main-swiper-wrapper");
  const thumbnailWrapper = document.getElementById("thumbnail-swiper-wrapper");

  // If no images, show placeholder
  if (!images || images.length === 0) {
    mainWrapper.innerHTML = `
            <div class="swiper-slide flex items-center justify-center bg-gray-100" style="min-height: 500px;">
                <div class="text-center text-gray-400">
                    <span class="material-symbols-outlined text-9xl">image</span>
                    <p class="mt-4 text-lg">No images available</p>
                </div>
            </div>
        `;
    thumbnailWrapper.innerHTML = "";
    return;
  }

  // Sort images to show primary first
  const sortedImages = [...images].sort((a, b) => b.is_primary - a.is_primary);

  // Populate main swiper
  mainWrapper.innerHTML = sortedImages
    .map(
      (image) => `
        <div class="swiper-slide">
            <img src="${image.image_url}" alt="Product Image" class="w-full h-auto object-cover" style="min-height: 500px; max-height: 600px;">
        </div>
    `,
    )
    .join("");

  // Populate thumbnail swiper
  thumbnailWrapper.innerHTML = sortedImages
    .map(
      (image) => `
        <div class="swiper-slide rounded-lg overflow-hidden border-2 border-gray-300">
            <img src="${image.image_url}" alt="Thumbnail" class="w-full h-24 object-cover cursor-pointer">
        </div>
    `,
    )
    .join("");

  // Initialize thumbnail swiper
  thumbnailSwiper = new Swiper(".thumbnail-swiper", {
    spaceBetween: 10,
    slidesPerView: 4,
    freeMode: true,
    watchSlidesProgress: true,
    breakpoints: {
      640: { slidesPerView: 5 },
      768: { slidesPerView: 6 },
      1024: { slidesPerView: 4 },
    },
  });

  // Initialize main swiper
  mainSwiper = new Swiper(".main-swiper", {
    spaceBetween: 10,
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    thumbs: {
      swiper: thumbnailSwiper,
    },
    loop: sortedImages.length > 1,
  });
}

// Load related products from same category
async function loadRelatedProducts(categoryId, currentProductId, storeId) {
  try {
    const response = await fetch(
      `${API_BASE_URL}/api/products?store_id=${storeId}`,
    );

    if (!response.ok) {
      throw new Error("Failed to load related products");
    }

    const data = await response.json();

    if (data.success && data.data) {
      // Filter products by category and exclude current product
      const relatedProducts = data.data.products
        .filter(
          (p) => p.category_id === categoryId && p.id !== currentProductId,
        )
        .slice(0, 4); // Limit to 4 products

      displayRelatedProducts(relatedProducts);
    }
  } catch (error) {
    console.error("Error loading related products:", error);
    document.getElementById("related-products").innerHTML = `
            <div class="col-span-full text-center text-gray-500 py-8">
                <p>Unable to load related products</p>
            </div>
        `;
  }
}

// Display related products
function displayRelatedProducts(products) {
  const container = document.getElementById("related-products");

  if (!products || products.length === 0) {
    container.innerHTML = `
            <div class="col-span-full text-center text-gray-500 py-8">
                <span class="material-symbols-outlined text-6xl mb-4">inventory_2</span>
                <p class="text-lg">No related products found</p>
            </div>
        `;
    return;
  }

  container.innerHTML = products
    .map((product) => {
      const primaryImage =
        product.images?.find((img) => img.is_primary)?.image_url ||
        product.images?.[0]?.image_url ||
        "https://via.placeholder.com/300x300?text=No+Image";

      const inStock = product.stock_quantity > 0;

      return `
            <a href="product.html?id=${product.id}" class="group bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="relative overflow-hidden bg-gray-100" style="height: 250px;">
                    <img src="${primaryImage}" alt="${product.name}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                    ${!inStock ? '<div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center"><span class="text-white font-bold text-lg">Out of Stock</span></div>' : ""}
                    ${product.category_name ? `<div class="absolute top-3 left-3"><span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-white text-gray-700 shadow">${product.category_name}</span></div>` : ""}
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-opacity-80" style="min-height: 2.5rem;">${product.name}</h3>
                    <div class="flex items-center justify-between">
                        <p class="text-xl font-bold" style="color: var(--primary);">₦${parseFloat(product.price).toLocaleString("en-NG", { minimumFractionDigits: 2 })}</p>
                        ${inStock ? `<span class="text-xs text-green-600 font-semibold">${product.stock_quantity} in stock</span>` : ""}
                    </div>
                </div>
            </a>
        `;
    })
    .join("");
}

// Initialize quantity controls
function initializeQuantityControls() {
  const qtyInput = document.getElementById("quantity");
  const decreaseBtn = document.getElementById("qty-decrease");
  const increaseBtn = document.getElementById("qty-increase");

  decreaseBtn.addEventListener("click", () => {
    const currentValue = parseInt(qtyInput.value) || 1;
    if (currentValue > 1) {
      qtyInput.value = currentValue - 1;
    }
  });

  increaseBtn.addEventListener("click", () => {
    const currentValue = parseInt(qtyInput.value) || 1;
    const maxValue = parseInt(qtyInput.max) || 999;
    if (currentValue < maxValue) {
      qtyInput.value = currentValue + 1;
    }
  });

  // Validate input
  qtyInput.addEventListener("input", () => {
    let value = parseInt(qtyInput.value) || 1;
    const maxValue = parseInt(qtyInput.max) || 999;

    if (value < 1) value = 1;
    if (value > maxValue) value = maxValue;

    qtyInput.value = value;
  });
}

// Initialize add to cart button
function initializeCartButton() {
  const addToCartBtn = document.getElementById("add-to-cart");

  addToCartBtn.addEventListener("click", () => {
    const quantity = parseInt(document.getElementById("quantity").value);
    const productId = getProductIdFromUrl();

    // Get cart from localStorage
    let cart = JSON.parse(
      localStorage.getItem(`cart_${storeConfig.store_id}`) || "[]",
    );

    // Check if product already in cart
    const existingItemIndex = cart.findIndex(
      (item) => item.productId === productId,
    );

    if (existingItemIndex > -1) {
      cart[existingItemIndex].quantity += quantity;
    } else {
      cart.push({
        productId,
        quantity,
        addedAt: new Date().toISOString(),
      });
    }

    // Save cart
    localStorage.setItem(`cart_${storeConfig.store_id}`, JSON.stringify(cart));

    // Update cart badge
    updateCartBadge(cart);

    // Show success message
    showSuccessMessage(`Added ${quantity} item(s) to cart!`);
  });
}

// Update cart badge
function updateCartBadge(cart = null) {
  if (!cart) {
    cart = JSON.parse(
      localStorage.getItem(`cart_${storeConfig.store_id}`) || "[]",
    );
  }

  const badge = document.getElementById("cart-badge");
  const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);

  if (totalItems > 0) {
    badge.textContent = totalItems;
    badge.classList.remove("hidden");
  } else {
    badge.classList.add("hidden");
  }
}

// Show success message
function showSuccessMessage(message) {
  // Create toast notification
  const toast = document.createElement("div");
  toast.className =
    "fixed top-20 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3 z-50 animate-slide-in";
  toast.innerHTML = `
        <span class="material-symbols-outlined">check_circle</span>
        <span>${message}</span>
    `;

  document.body.appendChild(toast);

  // Add slide-in animation
  toast.style.animation = "slideIn 0.3s ease-out";

  // Remove after 3 seconds
  setTimeout(() => {
    toast.style.animation = "slideOut 0.3s ease-out";
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// Show error message
function showError(message) {
  const mainElement = document.querySelector("main");
  mainElement.innerHTML = `
        <div class="flex flex-col items-center justify-center py-20">
            <span class="material-symbols-outlined text-9xl text-red-500 mb-4">error</span>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">${message}</h2>
            <p class="text-gray-600 mb-6">The product you're looking for could not be found.</p>
            <a href="index.html" class="px-6 py-3 rounded-lg font-bold text-white" style="background-color: var(--primary);">
                Back to Store
            </a>
        </div>
    `;
}

// Add animations
const style = document.createElement("style");
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Initialize cart badge on page load
updateCartBadge();
