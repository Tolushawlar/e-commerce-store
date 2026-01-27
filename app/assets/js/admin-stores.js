/**
 * Super Admin - Stores Management
 */

let currentPage = 1;
const limit = 20;

// Load stores on page load
document.addEventListener("DOMContentLoaded", () => {
  loadStores();
});

/**
 * Load all stores
 */
async function loadStores(page = 1, filters = {}) {
  const tableBody = document.getElementById("stores-table");

  if (!tableBody) return;

  UI.showLoading(tableBody);

  try {
    const params = { page, limit, ...filters };
    const response = await storeAPI.getAll(params);

    if (response.success) {
      renderStoresTable(response.data.stores, tableBody);
      updatePagination(response.data.pagination);
    } else {
      UI.showError(tableBody, response.message);
    }
  } catch (error) {
    UI.showError(tableBody, error.message);
  }
}

/**
 * Render stores table
 */
function renderStoresTable(stores, tableBody) {
  if (stores.length === 0) {
    tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                    No stores found
                </td>
            </tr>
        `;
    return;
  }

  tableBody.innerHTML = stores
    .map(
      (store) => `
        <tr class="hover:bg-gray-50 cursor-pointer" onclick="viewStore(${store.id})">
            <td class="px-6 py-4">
                <div class="font-semibold text-gray-900">${store.store_name}</div>
                <div class="text-sm text-gray-500">/${store.store_slug}</div>
            </td>
            <td class="px-6 py-4 text-gray-700">${store.client_name || "-"}</td>
            <td class="px-6 py-4">
                <div class="flex gap-2">
                    <div class="w-6 h-6 rounded-full border-2" style="background-color: ${store.primary_color}"></div>
                    <div class="w-6 h-6 rounded-full border-2" style="background-color: ${store.accent_color}"></div>
                </div>
            </td>
            <td class="px-6 py-4">
                <span class="px-3 py-1 text-xs font-bold rounded-full ${getStatusBadgeClass(store.status)}">
                    ${store.status.toUpperCase()}
                </span>
            </td>
            <td class="px-6 py-4 text-gray-500 text-sm">${UI.formatDate(store.created_at)}</td>
            <td class="px-6 py-4">
                <div class="flex gap-2">
                    <button onclick="customizeStore(${store.id}); event.stopPropagation();" class="text-blue-600 hover:text-blue-800" title="Customize">
                        <span class="material-symbols-outlined text-lg">palette</span>
                    </button>
                    <button onclick="generateStore(${store.id}); event.stopPropagation();" class="text-green-600 hover:text-green-800" title="Generate">
                        <span class="material-symbols-outlined text-lg">build</span>
                    </button>
                    <button onclick="deleteStore(${store.id}); event.stopPropagation();" class="text-red-600 hover:text-red-800" title="Delete">
                        <span class="material-symbols-outlined text-lg">delete</span>
                    </button>
                </div>
            </td>
        </tr>
    `,
    )
    .join("");
}

/**
 * Generate store files
 */
async function generateStore(id) {
  if (!confirm("Generate store files for this store?")) {
    return;
  }

  try {
    const response = await storeAPI.generate(id);

    if (response.success) {
      alert(`Store generated successfully!\nURL: ${response.data.store_url}`);
    }
  } catch (error) {
    alert("Error: " + error.message);
  }
}

/**
 * Delete store
 */
async function deleteStore(id) {
  if (
    !confirm(
      "Are you sure you want to delete this store? This action cannot be undone.",
    )
  ) {
    return;
  }

  try {
    const response = await storeAPI.delete(id);

    if (response.success) {
      UI.showSuccess("Store deleted successfully!");
      loadStores();
    }
  } catch (error) {
    alert("Error: " + error.message);
  }
}

/**
 * Helper functions
 */
function getStatusBadgeClass(status) {
  return status === "active"
    ? "bg-green-100 text-green-800"
    : "bg-gray-100 text-gray-800";
}

function updatePagination(pagination) {
  console.log("Pagination:", pagination);
}

function viewStore(id) {
  window.open(`/stores/store-${id}/`, "_blank");
}

function customizeStore(id) {
  window.location.href = `customize-store.php?id=${id}`;
}
