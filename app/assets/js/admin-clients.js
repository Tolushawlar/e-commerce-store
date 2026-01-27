/**
 * Super Admin - Clients Management
 */

let currentPage = 1;
const limit = 20;

// Load clients on page load
document.addEventListener("DOMContentLoaded", () => {
  loadClients();

  // Setup event listeners
  document
    .getElementById("addClientBtn")
    ?.addEventListener("click", openAddClientModal);
  document
    .getElementById("clientForm")
    ?.addEventListener("submit", handleClientSubmit);
});

/**
 * Load all clients
 */
async function loadClients(page = 1) {
  const tableBody = document.getElementById("clients-table");

  if (!tableBody) return;

  UI.showLoading(tableBody);

  try {
    const response = await clientAPI.getAll({ page, limit });

    if (response.success) {
      renderClientsTable(response.data.clients, tableBody);
      updatePagination(response.data.pagination);
    } else {
      UI.showError(tableBody, response.message);
    }
  } catch (error) {
    UI.showError(tableBody, error.message);
  }
}

/**
 * Render clients table
 */
function renderClientsTable(clients, tableBody) {
  if (clients.length === 0) {
    tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                    No clients found
                </td>
            </tr>
        `;
    return;
  }

  tableBody.innerHTML = clients
    .map(
      (client) => `
        <tr class="hover:bg-gray-50 cursor-pointer" onclick="viewClient(${client.id})">
            <td class="px-6 py-4">
                <div class="font-semibold text-gray-900">${client.name}</div>
                <div class="text-sm text-gray-500">${client.email}</div>
            </td>
            <td class="px-6 py-4 text-gray-700">${client.company_name || "-"}</td>
            <td class="px-6 py-4">
                <span class="px-3 py-1 text-xs font-bold rounded-full ${getPlanBadgeClass(client.subscription_plan)}">
                    ${client.subscription_plan.toUpperCase()}
                </span>
            </td>
            <td class="px-6 py-4">
                <span class="px-3 py-1 text-xs font-bold rounded-full ${getStatusBadgeClass(client.status)}">
                    ${client.status.toUpperCase()}
                </span>
            </td>
            <td class="px-6 py-4 text-gray-500 text-sm">${UI.formatDate(client.created_at)}</td>
            <td class="px-6 py-4">
                <div class="flex gap-2">
                    <button onclick="editClient(${client.id}); event.stopPropagation();" class="text-blue-600 hover:text-blue-800">
                        <span class="material-symbols-outlined text-lg">edit</span>
                    </button>
                    <button onclick="deleteClient(${client.id}); event.stopPropagation();" class="text-red-600 hover:text-red-800">
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
 * Handle client form submission
 */
async function handleClientSubmit(e) {
  e.preventDefault();

  const formData = new FormData(e.target);
  const data = Object.fromEntries(formData.entries());

  try {
    const response = await clientAPI.create(data);

    if (response.success) {
      UI.showSuccess("Client created successfully!");
      closeAddClientModal();
      loadClients();
    }
  } catch (error) {
    alert("Error: " + error.message);
  }
}

/**
 * Delete client
 */
async function deleteClient(id) {
  if (!confirm("Are you sure you want to delete this client?")) {
    return;
  }

  try {
    const response = await clientAPI.delete(id);

    if (response.success) {
      UI.showSuccess("Client deleted successfully!");
      loadClients();
    }
  } catch (error) {
    alert("Error: " + error.message);
  }
}

/**
 * Helper functions
 */
function getPlanBadgeClass(plan) {
  const classes = {
    basic: "bg-gray-100 text-gray-800",
    pro: "bg-blue-100 text-blue-800",
    enterprise: "bg-purple-100 text-purple-800",
  };
  return classes[plan] || classes.basic;
}

function getStatusBadgeClass(status) {
  const classes = {
    active: "bg-green-100 text-green-800",
    inactive: "bg-gray-100 text-gray-800",
    suspended: "bg-red-100 text-red-800",
  };
  return classes[status] || classes.inactive;
}

function updatePagination(pagination) {
  // Implement pagination UI update
  console.log("Pagination:", pagination);
}

function openAddClientModal() {
  // Implement modal open
  console.log("Open add client modal");
}

function closeAddClientModal() {
  // Implement modal close
  console.log("Close add client modal");
}

function viewClient(id) {
  window.location.href = `client-details.php?id=${id}`;
}

function editClient(id) {
  // Implement edit functionality
  console.log("Edit client:", id);
}
