/**
 * Client Dashboard - Products Management
 */

const storeId =
  new URLSearchParams(window.location.search).get("store_id") || 1; // Get from session/URL

document.addEventListener("DOMContentLoaded", () => {
  loadProducts();
});

/**
 * Load products for store
 */
async function loadProducts(filters = {}) {
  const productsContainer = document.getElementById("products-list");

  if (!productsContainer) return;

  UI.showLoading(productsContainer);

  try {
    const response = await productAPI.getByStore(storeId, filters);

    if (response.success) {
      renderProducts(response.data.products, productsContainer);
    } else {
      UI.showError(productsContainer, response.message);
    }
  } catch (error) {
    UI.showError(productsContainer, error.message);
  }
}

/**
 * Render products
 */
function renderProducts(products, container) {
  if (products.length === 0) {
    container.innerHTML = `
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 text-lg">No products found</p>
                <button onclick="window.location.href='add-product.php'" class="mt-4 px-6 py-3 bg-primary text-white rounded-xl font-semibold">
                    Add Your First Product
                </button>
            </div>
        `;
    return;
  }

  container.innerHTML = products
    .map(
      (product) => `
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
            <div class="aspect-square bg-gray-100 relative">
                ${product.image_url ? `<img src="${product.image_url}" alt="${product.name}" class="w-full h-full object-cover">` : ""}
                <span class="absolute top-2 right-2 px-2 py-1 text-xs font-bold rounded-full ${product.status === "active" ? "bg-green-100 text-green-800" : "bg-gray-100 text-gray-800"}">
                    ${product.status.toUpperCase()}
                </span>
            </div>
            <div class="p-4">
                <h3 class="font-bold text-gray-900 mb-1">${product.name}</h3>
                <p class="text-sm text-gray-600 mb-2">${product.category || "Uncategorized"}</p>
                <div class="flex items-center justify-between mb-3">
                    <p class="text-lg font-bold text-primary">${UI.formatCurrency(product.price)}</p>
                    <p class="text-sm text-gray-500">Stock: ${product.stock_quantity}</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="editProduct(${product.id})" class="flex-1 py-2 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200">
                        Edit
                    </button>
                    <button onclick="deleteProduct(${product.id})" class="px-4 py-2 bg-red-50 text-red-600 rounded-lg font-semibold hover:bg-red-100">
                        <span class="material-symbols-outlined text-lg">delete</span>
                    </button>
                </div>
            </div>
        </div>
    `,
    )
    .join("");
}

/**
 * Handle product form submission
 */
async function handleProductSubmit(e) {
  e.preventDefault();

  const formData = new FormData(e.target);
  const data = Object.fromEntries(formData.entries());
  data.store_id = storeId;

  try {
    const response = await productAPI.create(data);

    if (response.success) {
      UI.showSuccess("Product created successfully!");
      window.location.href = "products.php";
    }
  } catch (error) {
    alert("Error: " + error.message);
  }
}

/**
 * Delete product
 */
async function deleteProduct(id) {
  if (!confirm("Are you sure you want to delete this product?")) {
    return;
  }

  try {
    const response = await productAPI.delete(id);

    if (response.success) {
      UI.showSuccess("Product deleted successfully!");
      loadProducts();
    }
  } catch (error) {
    alert("Error: " + error.message);
  }
}

function editProduct(id) {
  window.location.href = `edit-product.php?id=${id}`;
}
