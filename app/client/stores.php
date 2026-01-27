<?php
$pageTitle = 'My Stores';
$pageDescription = 'Manage your online stores';
include '../shared/header-client.php';
?>

<!-- Header Actions -->
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-4">
        <input type="text" id="searchInput" placeholder="Search stores..."
            class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
            oninput="handleSearch()">
        <select id="statusFilter" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary"
            onchange="loadStores()">
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="suspended">Suspended</option>
        </select>
    </div>
</div>

<!-- Stores Grid -->
<div id="storesGrid">
    <div class="flex items-center justify-center p-12">
        <span class="material-symbols-outlined animate-spin text-4xl text-primary">refresh</span>
    </div>
</div>

<!-- Pagination -->
<div id="pagination" class="mt-6"></div>

<!-- Store Details Modal -->
<div id="storeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-3xl w-full max-h-[90vh] overflow-auto">
        <div class="p-6 border-b flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900" id="modalTitle">Store Details</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6">
            <div id="storeDetails" class="space-y-6">
                <!-- Store details will be loaded here -->
            </div>
        </div>
        <div class="p-6 border-t bg-gray-50 flex gap-3 justify-end">
            <button onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 font-semibold">
                Close
            </button>
            <button onclick="visitStore()" id="visitStoreBtn" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 font-semibold">
                <span class="material-symbols-outlined inline-block align-middle text-sm">open_in_new</span>
                Visit Store
            </button>
        </div>
    </div>
</div>

<?php include '../shared/footer-client.php'; ?>

<script src="/assets/js/services/store.service.js"></script>
<script src="/assets/js/services/product.service.js"></script>
<script src="/assets/js/services/order.service.js"></script>

<script>
    let currentPage = 1;
    let currentSearch = '';
    let currentStatus = '';
    let selectedStore = null;

    // Load stores
    async function loadStores(page = 1) {
        try {
            currentPage = page;
            const user = auth.getUser();
            const params = {
                client_id: user.id,
                page: currentPage,
                limit: 12
            };

            if (currentSearch) params.search = currentSearch;
            if (currentStatus) params.status = currentStatus;

            const response = await storeService.getAll(params);
            const stores = response.data?.stores || [];
            const pagination = response.data?.pagination || {};

            displayStores(stores);
            displayPagination(pagination);

        } catch (error) {
            console.error('Error loading stores:', error);
            document.getElementById('storesGrid').innerHTML = components.errorState('Failed to load stores');
        }
    }

    // Display stores in grid
    function displayStores(stores) {
        const container = document.getElementById('storesGrid');

        if (stores.length === 0) {
            container.innerHTML = components.emptyState(
                currentSearch ? 'No stores found' : 'You don\'t have any stores yet',
                'store'
            );
            return;
        }

        let html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">';

        stores.forEach(store => {
            html += `
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Store Header -->
                    <div class="h-32 bg-gradient-to-br from-primary to-primary/80 relative">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="material-symbols-outlined text-6xl text-accent opacity-20">storefront</span>
                        </div>
                        <div class="absolute top-3 right-3">
                            ${components.statusBadge(store.status)}
                        </div>
                    </div>

                    <!-- Store Content -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">${store.store_name}</h3>
                        <p class="text-sm text-gray-500 mb-4">
                            <span class="material-symbols-outlined text-xs align-middle">link</span>
                            ${store.domain || store.store_slug}
                        </p>
                        
                        ${store.description ? `
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">${utils.truncate(store.description, 80)}</p>
                        ` : ''}

                        <!-- Store Stats -->
                        <div class="grid grid-cols-2 gap-3 mb-4 pt-4 border-t">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-primary" id="products-${store.id}">-</p>
                                <p class="text-xs text-gray-500">Products</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-primary" id="orders-${store.id}">-</p>
                                <p class="text-xs text-gray-500">Orders</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <button onclick="viewStoreDetails(${store.id})" 
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 font-semibold text-sm">
                                <span class="material-symbols-outlined text-sm align-middle">visibility</span>
                                View
                            </button>
                            <button onclick="manageProducts(${store.id})" 
                                class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 font-semibold text-sm">
                                <span class="material-symbols-outlined text-sm align-middle">inventory_2</span>
                                Products
                            </button>
                        </div>
                    </div>
                </div>
            `;

            // Load store stats asynchronously
            loadStoreStats(store.id);
        });

        html += '</div>';
        container.innerHTML = html;
    }

    // Load store statistics
    async function loadStoreStats(storeId) {
        try {
            const [productsRes, ordersRes] = await Promise.all([
                productService.getAll({
                    store_id: storeId,
                    limit: 1
                }).catch(() => ({
                    data: {
                        pagination: {
                            total: 0
                        }
                    }
                })),
                orderService.getAll({
                    store_id: storeId,
                    limit: 1
                }).catch(() => ({
                    data: {
                        pagination: {
                            total: 0
                        }
                    }
                }))
            ]);

            const productsCount = productsRes.data?.pagination?.total || 0;
            const ordersCount = ordersRes.data?.pagination?.total || 0;

            const productsEl = document.getElementById(`products-${storeId}`);
            const ordersEl = document.getElementById(`orders-${storeId}`);

            if (productsEl) productsEl.textContent = productsCount;
            if (ordersEl) ordersEl.textContent = ordersCount;

        } catch (error) {
            console.error(`Error loading stats for store ${storeId}:`, error);
        }
    }

    // Display pagination
    function displayPagination(pagination) {
        const container = document.getElementById('pagination');

        if (!pagination || pagination.pages <= 1) {
            container.innerHTML = '';
            return;
        }

        const {
            page,
            pages
        } = pagination;
        let html = '<div class="flex items-center justify-center gap-2">';

        // Previous button
        html += `
            <button 
                onclick="loadStores(${page - 1})"
                ${page === 1 ? 'disabled' : ''}
                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed font-semibold">
                <span class="material-symbols-outlined text-sm">chevron_left</span>
            </button>
        `;

        // Page numbers
        for (let i = 1; i <= pages; i++) {
            if (i === 1 || i === pages || (i >= page - 2 && i <= page + 2)) {
                html += `
                    <button 
                        onclick="loadStores(${i})"
                        class="w-10 h-10 rounded-lg font-semibold ${i === page ? 'bg-primary text-white' : 'border border-gray-300 hover:bg-gray-50'}">
                        ${i}
                    </button>
                `;
            } else if (i === page - 3 || i === page + 3) {
                html += '<span class="px-2">...</span>';
            }
        }

        // Next button
        html += `
            <button 
                onclick="loadStores(${page + 1})"
                ${page === pages ? 'disabled' : ''}
                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed font-semibold">
                <span class="material-symbols-outlined text-sm">chevron_right</span>
            </button>
        `;

        html += '</div>';
        container.innerHTML = html;
    }

    // Search handler with debounce
    const handleSearch = utils.debounce(function() {
        currentSearch = document.getElementById('searchInput').value;
        loadStores(1);
    }, 300);

    // Status filter handler
    document.getElementById('statusFilter').addEventListener('change', function() {
        currentStatus = this.value;
        loadStores(1);
    });

    // View store details
    async function viewStoreDetails(storeId) {
        try {
            const response = await storeService.getById(storeId);
            selectedStore = response.data;

            const details = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Store Name</label>
                        <p class="text-gray-900">${selectedStore.store_name}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Store Slug</label>
                        <p class="text-gray-900">${selectedStore.store_slug}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Domain</label>
                        <p class="text-gray-900">${selectedStore.domain || 'Not configured'}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Status</label>
                        <div>${components.statusBadge(selectedStore.status)}</div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Description</label>
                        <p class="text-gray-900">${selectedStore.description || 'No description'}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Created</label>
                        <p class="text-gray-900">${utils.formatDate(selectedStore.created_at)}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Last Updated</label>
                        <p class="text-gray-900">${utils.formatDate(selectedStore.updated_at)}</p>
                    </div>
                </div>

                <!-- Design Settings -->
                <div class="mt-6 pt-6 border-t">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Design Settings</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Primary Color</label>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded border" style="background-color: ${selectedStore.primary_color}"></div>
                                <span class="text-sm text-gray-600">${selectedStore.primary_color}</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Accent Color</label>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded border" style="background-color: ${selectedStore.accent_color}"></div>
                                <span class="text-sm text-gray-600">${selectedStore.accent_color}</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Font Family</label>
                            <p class="text-sm text-gray-900">${selectedStore.font_family || 'Default'}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Button Style</label>
                            <p class="text-sm text-gray-900">${selectedStore.button_style || 'Rounded'}</p>
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('storeDetails').innerHTML = details;
            document.getElementById('storeModal').classList.remove('hidden');

        } catch (error) {
            console.error('Error loading store details:', error);
            utils.toast('Failed to load store details', 'error');
        }
    }

    // Close modal
    function closeModal() {
        document.getElementById('storeModal').classList.add('hidden');
        selectedStore = null;
    }

    // Visit store
    function visitStore() {
        if (selectedStore) {
            const url = selectedStore.domain ?
                `http://${selectedStore.domain}` :
                `http://localhost:3000/${selectedStore.store_slug}`;
            window.open(url, '_blank');
        }
    }

    // Manage products
    function manageProducts(storeId) {
        window.location.href = `/client/products.php?store_id=${storeId}`;
    }

    // Initialize
    loadStores();
</script>