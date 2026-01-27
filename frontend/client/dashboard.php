<?php
$pageTitle = 'Dashboard';
$pageDescription = 'Welcome to your e-commerce dashboard';
include '../shared/header-client.php';
?>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- My Stores -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-green-600">store</span>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-gray-900" id="myStores">-</h3>
        <p class="text-sm text-gray-500">My Stores</p>
    </div>

    <!-- My Products -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-purple-600">inventory_2</span>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-gray-900" id="myProducts">-</h3>
        <p class="text-sm text-gray-500">Total Products</p>
    </div>

    <!-- My Orders -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-orange-600">shopping_cart</span>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-gray-900" id="myOrders">-</h3>
        <p class="text-sm text-gray-500">Total Orders</p>
    </div>
</div>

<!-- My Stores -->
<div class="bg-white rounded-xl border border-gray-200">
    <div class="p-6 border-b flex items-center justify-between">
        <h2 class="text-lg font-bold text-gray-900">My Stores</h2>
        <a href="/client/stores.php" class="text-primary font-semibold hover:underline text-sm">View All</a>
    </div>
    <div id="storesList" class="p-6">
        <div class="flex items-center justify-center p-8">
            <span class="material-symbols-outlined animate-spin text-4xl text-primary">refresh</span>
        </div>
    </div>
</div>

<?php include '../shared/footer-client.php'; ?>

<script src="/assets/js/services/store.service.js"></script>
<script src="/assets/js/services/product.service.js"></script>
<script src="/assets/js/services/order.service.js"></script>

<script>
    // Load dashboard data
    async function loadDashboard() {
        try {
            // Get current user to filter by client
            const user = auth.getUser();
            const clientId = user.id;

            // Load stores first
            const storesResponse = await storeService.getAll({
                client_id: clientId
            });

            const stores = storesResponse.data?.stores || [];
            const totalStores = storesResponse.data?.pagination?.total || 0;

            // Update stores stat
            document.getElementById('myStores').textContent = totalStores;

            // Aggregate products and orders across all stores
            let totalProducts = 0;
            let totalOrders = 0;

            if (stores.length > 0) {
                // Get products and orders for each store
                const productPromises = stores.map(store =>
                    productService.getAll({
                        store_id: store.id,
                        limit: 1
                    })
                    .then(res => res.data?.pagination?.total || 0)
                    .catch(() => 0)
                );

                const orderPromises = stores.map(store =>
                    orderService.getAll({
                        store_id: store.id,
                        limit: 1
                    })
                    .then(res => res.data?.pagination?.total || 0)
                    .catch(() => 0)
                );

                const [productCounts, orderCounts] = await Promise.all([
                    Promise.all(productPromises),
                    Promise.all(orderPromises)
                ]);

                totalProducts = productCounts.reduce((sum, count) => sum + count, 0);
                totalOrders = orderCounts.reduce((sum, count) => sum + count, 0);
            }

            // Update stats
            document.getElementById('myProducts').textContent = totalProducts;
            document.getElementById('myOrders').textContent = totalOrders;

            // Display stores
            displayStores(stores);

        } catch (error) {
            console.error('Error loading dashboard:', error);
            utils.toast('Failed to load dashboard data', 'error');
        }
    }

    function displayStores(stores) {
        const container = document.getElementById('storesList');

        if (stores.length === 0) {
            container.innerHTML = components.emptyState('No stores yet', 'store');
            return;
        }

        let html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">';
        stores.forEach(store => {
            html += `
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow">
                    <div class="flex items-start justify-between mb-3">
                        <h3 class="font-bold text-gray-900">${store.store_name}</h3>
                        ${components.statusBadge(store.status)}
                    </div>
                    <p class="text-sm text-gray-600 mb-3">${store.domain || 'No domain configured'}</p>
                    <a href="/client/stores.php?id=${store.id}" class="text-primary text-sm font-semibold hover:underline">
                        View Details →
                    </a>
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
    }

    // Load on page load
    loadDashboard();
</script>