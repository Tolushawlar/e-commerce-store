<?php
$pageTitle = 'Clients';
$pageDescription = 'Manage client accounts';
include '../shared/header-admin.php';
?>

<!-- Header Actions -->
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-4">
        <input type="text" id="searchInput" placeholder="Search clients..."
            class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
            oninput="handleSearch()">
        <select id="statusFilter" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary"
            onchange="loadClients()">
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="suspended">Suspended</option>
        </select>
    </div>
    <button onclick="showCreateModal()" class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 font-semibold">
        <span class="material-symbols-outlined">add</span>
        Add Client
    </button>
</div>

<!-- Clients Table -->
<div class="bg-white rounded-xl border border-gray-200">
    <div id="clientsTable">
        <div class="flex items-center justify-center p-12">
            <span class="material-symbols-outlined animate-spin text-4xl text-primary">refresh</span>
        </div>
    </div>
</div>

<!-- Pagination -->
<div id="pagination"></div>

<!-- Create/Edit Modal -->
<div id="clientModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-auto">
        <div class="p-6 border-b flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900" id="modalTitle">Create Client</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="clientForm" onsubmit="handleSubmit(event)" class="p-6 space-y-4">
            <input type="hidden" id="clientId">

            <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Full Name *</label>
                <input type="text" id="name" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Email *</label>
                <input type="email" id="email" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Password <span class="text-gray-500 font-normal">(Leave blank to keep current)</span></label>
                <input type="password" id="password" minlength="8"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Company Name</label>
                <input type="text" id="company_name"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Phone</label>
                <input type="tel" id="phone"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Subscription Plan *</label>
                <select id="subscription_plan" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary">
                    <option value="basic">Basic</option>
                    <option value="standard">Standard</option>
                    <option value="premium">Premium</option>
                </select>
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

            <div class="flex gap-3 pt-4">
                <button type="submit" id="submitBtn"
                    class="flex-1 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90">
                    Save Client
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

<script src="/assets/js/services/client.service.js"></script>

<script>
    let currentPage = 1;
    const limit = 20;
    let searchTimeout;

    // Load clients with filters
    async function loadClients(page = 1) {
        // Prevent loading invalid pages
        if (page < 1) return;

        currentPage = page;
        const table = document.getElementById('clientsTable');
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

            const response = await clientService.getAll(params);

            if (response.success) {
                displayClients(response.data.clients || []);
                displayPagination(response.data.pagination);
            }
        } catch (error) {
            table.innerHTML = components.errorState('Failed to load clients');
            utils.toast(error.message, 'error');
        }
    }

    // Display clients in table
    function displayClients(clients) {
        const table = document.getElementById('clientsTable');

        if (clients.length === 0) {
            table.innerHTML = components.emptyState('No clients found', 'group');
            return;
        }

        let html = '<table class="min-w-full divide-y divide-gray-200">';

        // Header
        html += `
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Company</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joined</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
        `;

        // Body
        html += '<tbody class="bg-white divide-y divide-gray-200">';
        clients.forEach(client => {
            html += `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div>
                            <p class="font-semibold text-gray-900">${client.name}</p>
                            <p class="text-sm text-gray-500">${client.email}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">${client.company_name || '-'}</td>
                    <td class="px-6 py-4 text-sm">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-semibold">${client.subscription_plan}</span>
                    </td>
                    <td class="px-6 py-4">${components.statusBadge(client.status)}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${utils.formatDate(client.created_at)}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="editClient(${client.id})" 
                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Edit">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </button>
                            <button onclick="deleteClient(${client.id}, '${client.name}')" 
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
            'loadClients'
        );
    }

    // Search with debounce
    function handleSearch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadClients(1);
        }, 500);
    }

    // Show create modal
    function showCreateModal() {
        document.getElementById('modalTitle').textContent = 'Create Client';
        document.getElementById('clientForm').reset();
        document.getElementById('clientId').value = '';
        document.getElementById('password').required = true;
        document.getElementById('clientModal').classList.remove('hidden');
    }

    // Edit client
    async function editClient(id) {
        try {
            const response = await clientService.getById(id);
            const client = response.data;

            document.getElementById('modalTitle').textContent = 'Edit Client';
            document.getElementById('clientId').value = client.id;
            document.getElementById('name').value = client.name;
            document.getElementById('email').value = client.email;
            document.getElementById('company_name').value = client.company_name || '';
            document.getElementById('phone').value = client.phone || '';
            document.getElementById('subscription_plan').value = client.subscription_plan;
            document.getElementById('status').value = client.status;
            document.getElementById('password').required = false;

            document.getElementById('clientModal').classList.remove('hidden');
        } catch (error) {
            utils.toast(error.message, 'error');
        }
    }

    // Delete client
    function deleteClient(id, name) {
        utils.confirm(
            `Are you sure you want to delete "${name}"? This action cannot be undone.`,
            async () => {
                try {
                    await clientService.delete(id);
                    utils.toast('Client deleted successfully', 'success');
                    loadClients(currentPage);
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

        const clientId = document.getElementById('clientId').value;
        const data = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            subscription_plan: document.getElementById('subscription_plan').value,
            status: document.getElementById('status').value,
        };

        const companyName = document.getElementById('company_name').value;
        const phone = document.getElementById('phone').value;
        const password = document.getElementById('password').value;

        if (companyName) data.company_name = companyName;
        if (phone) data.phone = phone;
        if (password) data.password = password;

        try {
            if (clientId) {
                await clientService.update(clientId, data);
                utils.toast('Client updated successfully', 'success');
            } else {
                await clientService.create(data);
                utils.toast('Client created successfully', 'success');
            }

            closeModal();
            loadClients(currentPage);
        } catch (error) {
            utils.toast(error.message, 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Save Client';
        }
    }

    // Close modal
    function closeModal() {
        document.getElementById('clientModal').classList.add('hidden');
    }

    // Load clients on page load
    loadClients();
</script>