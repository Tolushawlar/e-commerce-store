<?php
$pageTitle = 'Stores';
$pageDescription = 'Manage client stores';
include '../shared/header-admin.php';
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
    <button onclick="window.location.href='/admin/create-store.php'" class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 font-semibold">
        <span class="material-symbols-outlined">add</span>
        Add Store
    </button>
</div>

<!-- Stores Table -->
<div class="bg-white rounded-xl border border-gray-200">
    <div id="storesTable">
        <div class="flex items-center justify-center p-12">
            <span class="material-symbols-outlined animate-spin text-4xl text-primary">refresh</span>
        </div>
    </div>
</div>

<!-- Pagination -->
<div id="pagination"></div>

<!-- Create/Edit Modal -->
<div id="storeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-auto">
        <div class="p-6 border-b flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900" id="modalTitle">Create Store</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="storeForm" onsubmit="handleSubmit(event)" class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Form Column -->
                <div class="space-y-4">
                    <input type="hidden" id="storeId">

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Client *</label>
                        <select id="client_id" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary">
                            <option value="">Select Client</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Store Name *</label>
                        <input type="text" id="name" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary"
                            placeholder="My Awesome Store" oninput="updatePreview()">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Store Slug *</label>
                        <input type="text" id="slug" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary"
                            placeholder="my-awesome-store">
                        <p class="text-xs text-gray-500 mt-1">URL-friendly name (lowercase, no spaces)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Domain</label>
                        <input type="text" id="domain"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary"
                            placeholder="example.com">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Description</label>
                        <textarea id="description"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary h-24"
                            placeholder="Brief description of the store" oninput="updatePreview()"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Status *</label>
                        <select id="status" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                </div>

                <!-- Preview Column -->
                <div>
                    <h3 class="text-sm font-bold text-gray-900 mb-2">Live Preview</h3>
                    <div id="storePreview" class="border border-gray-200 rounded-xl overflow-hidden">
                        <div class="h-16 flex items-center px-4 bg-primary">
                            <div class="w-8 h-8 bg-accent rounded-lg flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-lg">shopping_bag</span>
                            </div>
                            <span class="ml-2 text-white font-bold" id="previewStoreName">Store Name</span>
                        </div>
                        <div class="p-6 text-center" style="background: linear-gradient(135deg, #064E3B, #065f46);">
                            <h1 class="text-2xl font-bold text-white mb-2">Welcome to <span id="previewStoreTitle">Your Store</span></h1>
                            <p class="text-white/80 text-sm" id="previewDescription">Store description will appear here</p>
                            <button type="button" class="mt-4 px-6 py-2 rounded-lg font-semibold bg-accent text-primary">Shop Now</button>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-100 rounded-lg p-4 text-center">
                                    <div class="w-full h-20 bg-gray-200 rounded mb-2"></div>
                                    <p class="text-sm font-semibold">Sample Product</p>
                                    <p class="text-primary font-bold">₦12,500</p>
                                </div>
                                <div class="bg-gray-100 rounded-lg p-4 text-center">
                                    <div class="w-full h-20 bg-gray-200 rounded mb-2"></div>
                                    <p class="text-sm font-semibold">Sample Product</p>
                                    <p class="text-primary font-bold">₦8,900</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-6 border-t mt-6">
                <button type="submit" id="submitBtn"
                    class="flex-1 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90">
                    Save Store
                </button>
                <button type="button" onclick="closeModal()"
                    class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<?php include '../shared/footer-admin.php'; ?>

<script src="/assets/js/services/store.service.js"></script>
<script src="/assets/js/services/client.service.js"></script>

<script>
    let currentPage = 1;
    const limit = 20;
    let searchTimeout;

    // Load stores with filters
    async function loadStores(page = 1) {
        if (page < 1) return;

        currentPage = page;
        const table = document.getElementById('storesTable');
        table.innerHTML = components.spinner;

        try {
            const params = {
                page: currentPage,
                limit: limit
            };

            const searchQuery = document.getElementById('searchInput').value;
            const statusFilter = document.getElementById('statusFilter').value;

            if (searchQuery) params.search = searchQuery;
            if (statusFilter) params.status = statusFilter;

            const response = await storeService.getAll(params);

            if (response.success) {
                displayStores(response.data.stores || []);
                displayPagination(response.data.pagination);
            }
        } catch (error) {
            table.innerHTML = components.errorState('Failed to load stores');
            utils.toast(error.message, 'error');
        }
    }

    // Display stores in table
    function displayStores(stores) {
        const table = document.getElementById('storesTable');

        if (stores.length === 0) {
            table.innerHTML = components.emptyState('No stores found', 'store');
            return;
        }

        let html = '<table class="min-w-full divide-y divide-gray-200">';

        // Header
        html += `
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Store</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Domain</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
        `;

        // Body
        html += '<tbody class="bg-white divide-y divide-gray-200">';
        stores.forEach(store => {
            html += `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center text-accent">
                                <span class="material-symbols-outlined">storefront</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">${store.store_name}</p>
                                <p class="text-sm text-gray-500">${store.store_slug}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">${store.client_name || 'N/A'}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${store.domain || '-'}</td>
                    <td class="px-6 py-4">${components.statusBadge(store.status)}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${utils.formatDate(store.created_at)}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="window.location.href='/admin/customize-store.php?id=${store.id}'" 
                                class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg" title="Customize">
                                <span class="material-symbols-outlined text-sm">palette</span>
                            </button>
                            <button onclick="generateStore(${store.id})" 
                                class="p-2 text-green-600 hover:bg-green-50 rounded-lg" title="Generate">
                                <span class="material-symbols-outlined text-sm">refresh</span>
                            </button>
                            <button onclick="window.location.href='/admin/edit-store.php?id=${store.id}'" 
                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Edit">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </button>
                            <button onclick="deleteStore(${store.id}, '${store.store_name}')" 
                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Delete">
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
        html += '</tbody></table>';
        table.innerHTML = html;
    }

    // Display pagination
    function displayPagination(pagination) {
        const container = document.getElementById('pagination');
        if (!pagination || pagination.total_pages <= 1) {
            container.innerHTML = '';
            return;
        }

        container.innerHTML = components.pagination(
            pagination.current_page,
            pagination.total_pages,
            'loadStores'
        );
    }

    // Search with debounce
    function handleSearch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadStores(1);
        }, 500);
    }

    // Show create modal
    async function showCreateModal() {
        document.getElementById('modalTitle').textContent = 'Create Store';
        document.getElementById('storeForm').reset();
        document.getElementById('storeId').value = '';

        // Load clients for dropdown
        await loadClients();

        document.getElementById('storeModal').classList.remove('hidden');
        updatePreview();
    }

    // Load clients for dropdown
    async function loadClients() {
        try {
            const response = await clientService.getAll({
                limit: 1000
            });
            const select = document.getElementById('client_id');
            select.innerHTML = '<option value="">Select Client</option>';

            if (response.success && response.data.clients) {
                response.data.clients.forEach(client => {
                    const option = document.createElement('option');
                    option.value = client.id;
                    option.textContent = `${client.name} ${client.company_name ? '(' + client.company_name + ')' : ''}`;
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading clients:', error);
        }
    }

    // Edit store
    async function editStore(id) {
        try {
            const response = await storeService.getById(id);
            const store = response.data;

            document.getElementById('modalTitle').textContent = 'Edit Store';
            document.getElementById('storeId').value = store.id;
            document.getElementById('client_id').value = store.client_id;
            document.getElementById('name').value = store.store_name;
            document.getElementById('slug').value = store.store_slug;
            document.getElementById('domain').value = store.domain || '';
            document.getElementById('description').value = store.description || '';
            document.getElementById('status').value = store.status;

            await loadClients();
            document.getElementById('client_id').value = store.client_id;

            document.getElementById('storeModal').classList.remove('hidden');
            updatePreview();
        } catch (error) {
            utils.toast(error.message, 'error');
        }
    }

    // Generate store
    async function generateStore(id) {
        if (!confirm('Generate/regenerate the store HTML? This will overwrite existing files.')) {
            return;
        }

        try {
            const response = await storeService.generate(id);
            utils.toast(response.message || 'Store generated successfully', 'success');
        } catch (error) {
            utils.toast(error.message, 'error');
        }
    }

    // Delete store
    function deleteStore(id, name) {
        utils.confirm(
            `Are you sure you want to delete "${name}"? This action cannot be undone.`,
            async () => {
                try {
                    await storeService.delete(id);
                    utils.toast('Store deleted successfully', 'success');
                    loadStores(currentPage);
                } catch (error) {
                    utils.toast(error.message, 'error');
                }
            }
        );
    }

    // Handle form submit
    async function handleSubmit(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Saving...';

        const storeId = document.getElementById('storeId').value;
        const data = {
            client_id: parseInt(document.getElementById('client_id').value),
            store_name: document.getElementById('name').value,
            store_slug: document.getElementById('slug').value,
            status: document.getElementById('status').value,
        };

        const domain = document.getElementById('domain').value;
        const description = document.getElementById('description').value;

        if (domain) data.domain = domain;
        if (description) data.description = description;

        try {
            if (storeId) {
                await storeService.update(storeId, data);
                utils.toast('Store updated successfully', 'success');
            } else {
                await storeService.create(data);
                utils.toast('Store created successfully', 'success');
            }

            closeModal();
            loadStores(currentPage);
        } catch (error) {
            utils.toast(error.message, 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Save Store';
        }
    }

    // Update preview
    function updatePreview() {
        const name = document.getElementById('name').value || 'Store Name';
        const description = document.getElementById('description').value || 'Store description will appear here';

        document.getElementById('previewStoreName').textContent = name;
        document.getElementById('previewStoreTitle').textContent = name;
        document.getElementById('previewDescription').textContent = description;
    }

    // Auto-generate slug from name
    document.getElementById('name').addEventListener('input', function(e) {
        const slug = e.target.value
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        document.getElementById('slug').value = slug;
        updatePreview();
    });

    // Close modal
    function closeModal() {
        document.getElementById('storeModal').classList.add('hidden');
    }

    // Load stores on page load
    loadStores();
</script>