<?php
$pageTitle = 'Dashboard';
$pageDescription = 'Overview of your e-commerce platform';
include '../shared/header-admin.php';
?>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Clients -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-600">group</span>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-gray-900" id="totalClients">-</h3>
        <p class="text-sm text-gray-500">Total Clients</p>
    </div>

    <!-- Total Stores -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-green-600">store</span>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-gray-900" id="totalStores">-</h3>
        <p class="text-sm text-gray-500">Total Stores</p>
    </div>

    <!-- Total Products -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-purple-600">inventory_2</span>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-gray-900" id="totalProducts">-</h3>
        <p class="text-sm text-gray-500">Total Products</p>
    </div>

    <!-- Total Orders -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-orange-600">shopping_cart</span>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-gray-900" id="totalOrders">-</h3>
        <p class="text-sm text-gray-500">Total Orders</p>
    </div>
</div>

<!-- Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Clients -->
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="p-6 border-b">
            <h2 class="text-lg font-bold text-gray-900">Recent Clients</h2>
        </div>
        <div id="recentClients" class="p-6">
            <div class="flex justify-center items-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
            </div>
        </div>
    </div>

    <!-- Recent Stores -->
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="p-6 border-b">
            <h2 class="text-lg font-bold text-gray-900">Recent Stores</h2>
        </div>
        <div id="recentStores" class="p-6">
            <div class="flex justify-center items-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
            </div>
        </div>
    </div>
</div>

<?php include '../shared/footer-admin.php'; ?>

<!-- Services -->
<script src="/assets/js/services/client.service.js"></script>
<script src="/assets/js/services/store.service.js"></script>

<script>
    // Load dashboard data
    async function loadDashboard() {
        try {
            // Load stats in parallel (only clients and stores for admin)
            const [clients, stores] = await Promise.all([
                clientService.getAll({
                    limit: 5
                }),
                storeService.getAll({
                    limit: 5
                })
            ]);

            // Update stats
            document.getElementById('totalClients').textContent = clients.data?.pagination?.total || 0;
            document.getElementById('totalStores').textContent = stores.data?.pagination?.total || 0;

            // For products and orders, we'll count across all stores
            let totalProducts = 0;
            let totalOrders = 0;

            if (stores.data?.data) {
                for (const store of stores.data.data) {
                    // Could add API calls here to get totals per store if needed
                    // For now, just showing placeholder counts
                }
            }

            document.getElementById('totalProducts').textContent = totalProducts;
            document.getElementById('totalOrders').textContent = totalOrders;

            // Display recent clients
            displayRecentClients(clients.data?.clients || []);

            // Display recent stores
            displayRecentStores(stores.data?.stores || []);

        } catch (error) {
            console.error('Error loading dashboard:', error);
            helpers.toast('Failed to load dashboard data', 'error');
        }
    }

    function displayRecentClients(clients) {
        const container = document.getElementById('recentClients');

        if (clients.length === 0) {
            container.innerHTML = components.emptyState('No clients yet');
            return;
        }

        let html = '<div class="space-y-4">';
        clients.forEach(client => {
            html += `
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-gray-900">${client.name}</p>
                        <p class="text-sm text-gray-500">${client.email}</p>
                    </div>
                    ${components.statusBadge(client.status)}
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
    }

    function displayRecentStores(stores) {
        const container = document.getElementById('recentStores');

        if (stores.length === 0) {
            container.innerHTML = components.emptyState('No stores yet');
            return;
        }

        let html = '<div class="space-y-4">';
        stores.forEach(store => {
            html += `
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-gray-900">${store.store_name}</p>
                        <p class="text-sm text-gray-500">${store.domain || 'No domain'}</p>
                    </div>
                    ${components.statusBadge(store.status)}
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
    }

    // Load on page load
    loadDashboard();
</script>