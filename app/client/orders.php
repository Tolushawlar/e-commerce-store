<?php
$pageTitle = 'My Orders';
$pageDescription = 'Manage orders for your stores';
include '../shared/header-client.php';
?>

<!-- Page Header -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Order Management</h1>
        <p class="text-gray-500 mt-1">View and manage your store orders</p>
    </div>
    <div class="flex gap-3">
        <button onclick="refreshOrders()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
            <span class="material-symbols-outlined inline-block align-middle">refresh</span>
            Refresh
        </button>
    </div>
</div>

<!-- Store Selector -->
<div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">Select Store</label>
    <select id="storeSelector" class="w-full md:w-96 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
        <option value="">Loading stores...</option>
    </select>
</div>

<!-- Statistics Dashboard -->
<div id="statsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6 hidden">
    <!-- Total Orders -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between mb-4">
            <span class="material-symbols-outlined text-4xl opacity-80">shopping_bag</span>
            <div class="text-right">
                <p class="text-3xl font-bold" id="statTotalOrders">0</p>
                <p class="text-sm opacity-90">Total Orders</p>
            </div>
        </div>
        <div class="text-xs opacity-75">All time</div>
    </div>

    <!-- Total Revenue -->
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between mb-4">
            <span class="material-symbols-outlined text-4xl opacity-80">payments</span>
            <div class="text-right">
                <p class="text-3xl font-bold" id="statTotalRevenue">₦0</p>
                <p class="text-sm opacity-90">Total Revenue</p>
            </div>
        </div>
        <div class="text-xs opacity-75">Total earnings</div>
    </div>

    <!-- Pending Orders -->
    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between mb-4">
            <span class="material-symbols-outlined text-4xl opacity-80">pending_actions</span>
            <div class="text-right">
                <p class="text-3xl font-bold" id="statPendingOrders">0</p>
                <p class="text-sm opacity-90">Pending Orders</p>
            </div>
        </div>
        <div class="text-xs opacity-75">Needs attention</div>
    </div>

    <!-- Completed Orders -->
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between mb-4">
            <span class="material-symbols-outlined text-4xl opacity-80">task_alt</span>
            <div class="text-right">
                <p class="text-3xl font-bold" id="statCompletedOrders">0</p>
                <p class="text-sm opacity-90">Completed</p>
            </div>
        </div>
        <div class="text-xs opacity-75">Successfully delivered</div>
    </div>
</div>

<!-- Filters and Actions -->
<div id="filtersContainer" class="hidden bg-white rounded-xl border border-gray-200 p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
        <!-- Status Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Order Status</label>
            <select id="filterStatus" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        <!-- Payment Status Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
            <select id="filterPaymentStatus" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">All Payment Status</option>
                <option value="pending">Pending</option>
                <option value="paid">Paid</option>
                <option value="failed">Failed</option>
                <option value="refunded">Refunded</option>
            </select>
        </div>

        <!-- Date From -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
            <input type="date" id="filterFromDate" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>

        <!-- Date To -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
            <input type="date" id="filterToDate" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>
    </div>

    <!-- Search -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Search Orders</label>
        <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
            <input
                type="text"
                id="searchOrders"
                placeholder="Search by order ID, customer name, or email..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-3">
        <button onclick="applyFilters()" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
            <span class="material-symbols-outlined inline-block align-middle text-sm">filter_list</span>
            Apply Filters
        </button>
        <button onclick="clearFilters()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
            Clear
        </button>
    </div>
</div>

<!-- Orders Content -->
<div id="ordersContent" class="hidden">
    <!-- Quick Stats Bar -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 flex items-center justify-between">
        <div class="flex gap-6">
            <div>
                <p class="text-xs text-gray-500 uppercase">Today's Orders</p>
                <p class="text-xl font-bold text-gray-900" id="todayOrders">0</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase">This Week</p>
                <p class="text-xl font-bold text-gray-900" id="weekOrders">0</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase">This Month</p>
                <p class="text-xl font-bold text-gray-900" id="monthOrders">0</p>
            </div>
        </div>
        <button onclick="exportOrders()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
            <span class="material-symbols-outlined inline-block align-middle text-sm">download</span>
            Export
        </button>
    </div>

    <!-- Orders List -->
    <div class="bg-white rounded-xl border border-gray-200">
        <!-- Loading State -->
        <div id="loadingState" class="p-12 text-center">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
            <p class="mt-4 text-gray-500">Loading orders...</p>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="hidden p-12 text-center">
            <span class="material-symbols-outlined text-gray-300 text-6xl mb-4">inbox</span>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No orders yet</h3>
            <p class="text-gray-500">Orders will appear here once customers start placing them</p>
        </div>

        <!-- Orders Table -->
        <div id="ordersTable" class="hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-primary focus:ring-primary">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Items</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Payment</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody" class="divide-y divide-gray-200">
                        <!-- Rows will be inserted here -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div id="paginationContainer" class="hidden border-t border-gray-200 px-6 py-4 flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    Showing <span id="showingFrom">1</span> to <span id="showingTo">20</span> of <span id="totalOrders">0</span> orders
                </div>
                <div class="flex gap-2">
                    <button onclick="previousPage()" id="prevBtn" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="material-symbols-outlined text-sm">chevron_left</span>
                    </button>
                    <div class="flex items-center px-4 py-2 text-sm font-medium text-gray-700">
                        Page <span id="currentPageNum" class="mx-1">1</span> of <span id="totalPagesNum">1</span>
                    </div>
                    <button onclick="nextPage()" id="nextBtn" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="material-symbols-outlined text-sm">chevron_right</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions Bar -->
<div id="bulkActionsBar" class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 bg-gray-900 text-white rounded-full px-6 py-3 shadow-xl z-50">
    <div class="flex items-center gap-4">
        <span id="selectedCount" class="font-medium">0 selected</span>
        <div class="h-6 w-px bg-gray-700"></div>
        <select id="bulkAction" class="bg-gray-800 text-white rounded px-3 py-1.5 text-sm border-0 focus:ring-2 focus:ring-primary">
            <option value="">Select Action</option>
            <option value="processing">Mark as Processing</option>
            <option value="shipped">Mark as Shipped</option>
            <option value="delivered">Mark as Delivered</option>
            <option value="cancelled">Mark as Cancelled</option>
        </select>
        <button onclick="applyBulkAction()" class="bg-primary hover:bg-primary/90 px-5 py-1.5 rounded text-sm font-medium transition-colors">
            Apply
        </button>
        <button onclick="clearSelection()" class="text-gray-400 hover:text-white transition-colors">
            <span class="material-symbols-outlined text-lg">close</span>
        </button>
    </div>
</div>

<!-- Order Details Modal -->
<div id="orderModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-5xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between z-10">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Order Details</h2>
                <p class="text-sm text-gray-500 mt-1" id="orderModalSubtitle">Order #<span id="orderIdDisplay"></span></p>
            </div>
            <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <span class="material-symbols-outlined text-2xl">close</span>
            </button>
        </div>

        <!-- Modal Content -->
        <div id="orderModalContent" class="p-6">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<?php include '../shared/footer-client.php'; ?>

<script src="/assets/js/services/client-orders.js"></script>
<script>
    const apiClient = new APIClient();
    const authService = new AuthService(apiClient);
    const orderService = new ClientOrderService(apiClient);

    let currentPage = 1;
    let totalPages = 1;
    let selectedOrders = new Set();
    let currentStoreId = null;
    let stores = [];

    // Initialize
    document.addEventListener('DOMContentLoaded', async () => {
        await authService.requireAuth('/auth/login.php');
        await loadStores();

        // Setup search with debounce
        let searchTimeout;
        document.getElementById('searchOrders').addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentPage = 1;
                loadOrders();
            }, 500);
        });

        // Select all checkbox
        document.getElementById('selectAll').addEventListener('change', (e) => {
            const checkboxes = document.querySelectorAll('.order-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = e.target.checked;
                if (e.target.checked) {
                    selectedOrders.add(parseInt(cb.value));
                } else {
                    selectedOrders.delete(parseInt(cb.value));
                }
            });
            updateBulkActionsBar();
        });

        // Store selector change
        document.getElementById('storeSelector').addEventListener('change', (e) => {
            currentStoreId = e.target.value;
            if (currentStoreId) {
                loadStoreData();
            } else {
                hideStoreData();
            }
        });
    });

    // Load user's stores
    async function loadStores() {
        try {
            const user = auth.getUser();
            const response = await apiClient.get(`/api/stores?client_id=${user.id}`);
            stores = response.data.stores || [];

            const selector = document.getElementById('storeSelector');
            selector.innerHTML = '<option value="">Select a store...</option>';

            stores.forEach(store => {
                const option = document.createElement('option');
                option.value = store.id;
                option.textContent = store.store_name;
                selector.appendChild(option);
            });

            // Auto-select if only one store
            if (stores.length === 1) {
                selector.value = stores[0].id;
                currentStoreId = stores[0].id;
                loadStoreData();
            }
        } catch (error) {
            console.error('Error loading stores:', error);
            showNotification('Failed to load stores', 'error');
        }
    }

    // Load store data (stats and orders)
    async function loadStoreData() {
        document.getElementById('filtersContainer').classList.remove('hidden');
        document.getElementById('ordersContent').classList.remove('hidden');
        await Promise.all([loadStats(), loadOrders()]);
    }

    // Hide store data
    function hideStoreData() {
        document.getElementById('statsContainer').classList.add('hidden');
        document.getElementById('filtersContainer').classList.add('hidden');
        document.getElementById('ordersContent').classList.add('hidden');
    }

    // Load statistics
    async function loadStats() {
        try {
            const response = await orderService.getStats(currentStoreId);

            if (response.success && response.data.overview) {
                const stats = response.data.overview;

                document.getElementById('statsContainer').classList.remove('hidden');
                document.getElementById('statTotalOrders').textContent = stats.total_orders || 0;
                document.getElementById('statTotalRevenue').textContent = orderService.formatCurrency(stats.total_revenue || 0);
                document.getElementById('statPendingOrders').textContent = stats.pending_orders || 0;
                document.getElementById('statCompletedOrders').textContent = stats.completed_orders || 0;
            }
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    // Load orders
    async function loadOrders() {
        if (!currentStoreId) return;

        try {
            document.getElementById('loadingState').classList.remove('hidden');
            document.getElementById('emptyState').classList.add('hidden');
            document.getElementById('ordersTable').classList.add('hidden');

            const filters = {
                page: currentPage,
                limit: 20
            };

            const status = document.getElementById('filterStatus').value;
            const paymentStatus = document.getElementById('filterPaymentStatus').value;
            const fromDate = document.getElementById('filterFromDate').value;
            const toDate = document.getElementById('filterToDate').value;
            const search = document.getElementById('searchOrders').value;

            if (status) filters.status = status;
            if (paymentStatus) filters.payment_status = paymentStatus;
            if (fromDate) filters.from_date = fromDate;
            if (toDate) filters.to_date = toDate;
            if (search) filters.search = search;

            const response = await orderService.getOrders(currentStoreId, filters);

            if (response.success) {
                const orders = response.data.orders || [];
                const pagination = response.data.pagination || {};

                document.getElementById('loadingState').classList.add('hidden');

                if (orders.length === 0) {
                    document.getElementById('emptyState').classList.remove('hidden');
                } else {
                    renderOrders(orders);
                    updatePagination(pagination);
                    document.getElementById('ordersTable').classList.remove('hidden');
                    document.getElementById('paginationContainer').classList.remove('hidden');
                }
            }
        } catch (error) {
            console.error('Error loading orders:', error);
            showNotification('Failed to load orders', 'error');
            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('emptyState').classList.remove('hidden');
        }
    }

    // Render orders
    function renderOrders(orders) {
        const tbody = document.getElementById('ordersTableBody');
        tbody.innerHTML = '';

        orders.forEach(order => {
            const itemCount = order.items?.length || 0;
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 transition-colors';
            row.innerHTML = `
                <td class="px-6 py-4">
                    <input type="checkbox" value="${order.id}" class="order-checkbox rounded border-gray-300 text-primary focus:ring-primary">
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">${orderService.getStatusIcon(order.status)}</span>
                        <span class="font-semibold text-gray-900">#${order.id}</span>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div>
                        <div class="font-medium text-gray-900">${order.customer_name}</div>
                        <div class="text-sm text-gray-500">${order.customer_email}</div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        ${itemCount} ${itemCount === 1 ? 'item' : 'items'}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="font-semibold text-gray-900">${orderService.formatCurrency(order.total_amount)}</div>
                    ${order.payment_method ? `<div class="text-xs text-gray-500">${order.payment_method}</div>` : ''}
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ${orderService.getStatusBadgeClass(order.status)}">
                        ${order.status}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ${orderService.getPaymentStatusBadgeClass(order.payment_status)}">
                        ${order.payment_status}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">${orderService.formatDate(order.created_at).split(',')[0]}</div>
                    <div class="text-xs text-gray-500">${orderService.formatDate(order.created_at).split(',')[1]}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="flex gap-2">
                        <button onclick="viewOrder(${order.id})" class="text-primary hover:text-primary/80 transition-colors" title="View Details">
                            <span class="material-symbols-outlined">visibility</span>
                        </button>
                        ${order.status !== 'delivered' && order.status !== 'cancelled' ? `
                            <button onclick="quickUpdateStatus(${order.id})" class="text-blue-600 hover:text-blue-700 transition-colors" title="Update Status">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                        ` : ''}
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });

        // Add checkbox listeners
        document.querySelectorAll('.order-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                const orderId = parseInt(e.target.value);
                if (e.target.checked) {
                    selectedOrders.add(orderId);
                } else {
                    selectedOrders.delete(orderId);
                }
                updateBulkActionsBar();
            });
        });
    }

    // Update pagination
    function updatePagination(pagination) {
        totalPages = pagination.pages || 1;
        const total = pagination.total || 0;
        const limit = pagination.limit || 20;
        const from = total === 0 ? 0 : ((currentPage - 1) * limit) + 1;
        const to = Math.min(currentPage * limit, total);

        document.getElementById('showingFrom').textContent = from;
        document.getElementById('showingTo').textContent = to;
        document.getElementById('totalOrders').textContent = total;
        document.getElementById('currentPageNum').textContent = currentPage;
        document.getElementById('totalPagesNum').textContent = totalPages;

        document.getElementById('prevBtn').disabled = currentPage === 1;
        document.getElementById('nextBtn').disabled = currentPage === totalPages;
    }

    // Pagination
    function previousPage() {
        if (currentPage > 1) {
            currentPage--;
            loadOrders();
        }
    }

    function nextPage() {
        if (currentPage < totalPages) {
            currentPage++;
            loadOrders();
        }
    }

    // Filters
    function applyFilters() {
        currentPage = 1;
        loadOrders();
    }

    function clearFilters() {
        document.getElementById('filterStatus').value = '';
        document.getElementById('filterPaymentStatus').value = '';
        document.getElementById('filterFromDate').value = '';
        document.getElementById('filterToDate').value = '';
        document.getElementById('searchOrders').value = '';
        currentPage = 1;
        loadOrders();
    }

    function refreshOrders() {
        if (currentStoreId) {
            loadStoreData();
        }
    }

    // View order details
    async function viewOrder(orderId) {
        try {
            document.getElementById('orderModal').classList.remove('hidden');
            document.getElementById('orderIdDisplay').textContent = orderId;
            document.getElementById('orderModalContent').innerHTML = `
                <div class="flex justify-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
                </div>
            `;

            const response = await orderService.getOrder(currentStoreId, orderId);

            if (response.success) {
                renderOrderDetails(response.data);
            }
        } catch (error) {
            console.error('Error loading order details:', error);
            showNotification('Failed to load order details', 'error');
            closeOrderModal();
        }
    }

    // Render order details
    function renderOrderDetails(order) {
        const content = document.getElementById('orderModalContent');
        const canUpdate = order.status !== 'delivered' && order.status !== 'cancelled';

        content.innerHTML = `
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Order Info & Items -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Order Information -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Order Information</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Order ID</p>
                                <p class="font-semibold">#${order.id}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Order Date</p>
                                <p class="font-semibold">${orderService.formatDate(order.created_at)}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Status</p>
                                <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-full ${orderService.getStatusBadgeClass(order.status)}">${order.status}</span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Payment</p>
                                <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-full ${orderService.getPaymentStatusBadgeClass(order.payment_status)}">${order.payment_status}</span>
                            </div>
                            <div class="col-span-2">
                                <p class="text-sm text-gray-500">Payment Method</p>
                                <p class="font-semibold capitalize">${order.payment_method || 'N/A'}</p>
                            </div>
                            ${order.tracking_number ? `
                                <div class="col-span-2">
                                    <p class="text-sm text-gray-500">Tracking Number</p>
                                    <p class="font-mono font-semibold">${order.tracking_number}</p>
                                </div>
                            ` : ''}
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-lg font-semibold">Order Items</h3>
                        </div>
                        <div class="divide-y divide-gray-200">
                            ${order.items && order.items.length > 0 ? order.items.map(item => `
                                <div class="px-6 py-4 flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        ${item.product_image ? `
                                            <img src="${item.product_image}" alt="${item.product_name}" class="w-16 h-16 object-cover rounded-lg">
                                        ` : `
                                            <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <span class="material-symbols-outlined text-gray-400">image</span>
                                            </div>
                                        `}
                                        <div>
                                            <p class="font-medium text-gray-900">${item.product_name || 'Product'}</p>
                                            <p class="text-sm text-gray-500">Qty: ${item.quantity} × ${orderService.formatCurrency(item.price)}</p>
                                        </div>
                                    </div>
                                    <p class="font-semibold text-gray-900">${orderService.formatCurrency(item.price * item.quantity)}</p>
                                </div>
                            `).join('') : '<p class="px-6 py-4 text-gray-500">No items</p>'}
                        </div>
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-medium">${orderService.formatCurrency((order.total_amount || 0) - (order.shipping_cost || 0))}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Shipping</span>
                                <span class="font-medium">${orderService.formatCurrency(order.shipping_cost || 0)}</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold border-t pt-2">
                                <span>Total</span>
                                <span class="text-primary">${orderService.formatCurrency(order.total_amount)}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Customer & Actions -->
                <div class="space-y-6">
                    <!-- Customer Information -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Customer</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500">Name</p>
                                <p class="font-medium">${order.customer_name}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="font-medium">${order.customer_email}</p>
                            </div>
                            ${order.customer_phone ? `
                                <div>
                                    <p class="text-sm text-gray-500">Phone</p>
                                    <p class="font-medium">${order.customer_phone}</p>
                                </div>
                            ` : ''}
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    ${order.shipping_address ? `
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold mb-4">Shipping Address</h3>
                            <div class="text-sm space-y-1">
                                <p>${order.shipping_address.address_line1}</p>
                                ${order.shipping_address.address_line2 ? `<p>${order.shipping_address.address_line2}</p>` : ''}
                                <p>${order.shipping_address.city}, ${order.shipping_address.state}</p>
                                <p>${order.shipping_address.country} ${order.shipping_address.postal_code || ''}</p>
                            </div>
                        </div>
                    ` : ''}

                    <!-- Order Notes -->
                    ${order.order_notes ? `
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex gap-2">
                                <span class="material-symbols-outlined text-yellow-600 text-sm">info</span>
                                <div>
                                    <p class="text-sm font-medium text-yellow-800">Customer Note</p>
                                    <p class="text-sm text-yellow-700 mt-1">${order.order_notes}</p>
                                </div>
                            </div>
                        </div>
                    ` : ''}

                    <!-- Quick Actions -->
                    ${canUpdate ? `
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Update Order Status</label>
                                <select id="modalUpdateStatus" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                                    <option value="pending" ${order.status === 'pending' ? 'selected' : ''}>Pending</option>
                                    <option value="processing" ${order.status === 'processing' ? 'selected' : ''}>Processing</option>
                                    <option value="shipped" ${order.status === 'shipped' ? 'selected' : ''}>Shipped</option>
                                    <option value="delivered" ${order.status === 'delivered' ? 'selected' : ''}>Delivered</option>
                                    <option value="cancelled" ${order.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Update Payment Status</label>
                                <select id="modalUpdatePayment" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                                    <option value="pending" ${order.payment_status === 'pending' ? 'selected' : ''}>Pending</option>
                                    <option value="paid" ${order.payment_status === 'paid' ? 'selected' : ''}>Paid</option>
                                    <option value="failed" ${order.payment_status === 'failed' ? 'selected' : ''}>Failed</option>
                                    <option value="refunded" ${order.payment_status === 'refunded' ? 'selected' : ''}>Refunded</option>
                                </select>
                            </div>

                            ${!order.tracking_number ? `
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tracking Number</label>
                                    <input type="text" id="modalTrackingNumber" placeholder="Enter tracking number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                                </div>
                            ` : ''}

                            <div class="flex gap-2">
                                <button onclick="saveOrderUpdates(${order.id})" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                                    Save Updates
                                </button>
                                <button onclick="printOrder(${order.id})" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                    <span class="material-symbols-outlined text-sm">print</span>
                                </button>
                            </div>
                        </div>
                    ` : `
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-blue-700">This order is ${order.status} and cannot be modified.</p>
                        </div>
                    `}
                </div>
            </div>
        `;
    }

    // Save order updates
    async function saveOrderUpdates(orderId) {
        try {
            const updates = [];

            // Get current values
            const newStatus = document.getElementById('modalUpdateStatus')?.value;
            const newPayment = document.getElementById('modalUpdatePayment')?.value;
            const tracking = document.getElementById('modalTrackingNumber')?.value;

            // Update status
            if (newStatus) {
                const statusRes = await orderService.updateStatus(currentStoreId, orderId, newStatus);
                if (statusRes.success) updates.push('status');
            }

            // Update payment
            if (newPayment) {
                const paymentRes = await orderService.updatePaymentStatus(currentStoreId, orderId, newPayment);
                if (paymentRes.success) updates.push('payment status');
            }

            // Add tracking
            if (tracking && tracking.trim()) {
                const trackingRes = await orderService.addTracking(currentStoreId, orderId, tracking.trim());
                if (trackingRes.success) updates.push('tracking number');
            }

            if (updates.length > 0) {
                showNotification(`Updated ${updates.join(', ')} successfully`, 'success');
                closeOrderModal();
                loadOrders();
            } else {
                showNotification('No changes to save', 'info');
            }
        } catch (error) {
            console.error('Error saving updates:', error);
            showNotification('Failed to save updates', 'error');
        }
    }

    // Quick update status
    async function quickUpdateStatus(orderId) {
        const status = prompt('Enter new status (pending, processing, shipped, delivered, cancelled):');
        if (status && ['pending', 'processing', 'shipped', 'delivered', 'cancelled'].includes(status.toLowerCase())) {
            try {
                const response = await orderService.updateStatus(currentStoreId, orderId, status.toLowerCase());
                if (response.success) {
                    showNotification('Order status updated', 'success');
                    loadOrders();
                }
            } catch (error) {
                console.error('Error updating status:', error);
                showNotification('Failed to update status', 'error');
            }
        } else if (status !== null) {
            showNotification('Invalid status', 'error');
        }
    }

    // Close modal
    function closeOrderModal() {
        document.getElementById('orderModal').classList.add('hidden');
    }

    // Bulk actions
    function updateBulkActionsBar() {
        const bar = document.getElementById('bulkActionsBar');
        const count = selectedOrders.size;

        if (count > 0) {
            bar.classList.remove('hidden');
            document.getElementById('selectedCount').textContent = `${count} selected`;
        } else {
            bar.classList.add('hidden');
        }
    }

    async function applyBulkAction() {
        const action = document.getElementById('bulkAction').value;
        if (!action) {
            showNotification('Please select an action', 'error');
            return;
        }

        if (selectedOrders.size === 0) {
            showNotification('No orders selected', 'error');
            return;
        }

        if (!confirm(`Update ${selectedOrders.size} orders to ${action}?`)) {
            return;
        }

        try {
            const response = await orderService.bulkUpdate(currentStoreId, Array.from(selectedOrders), action);
            if (response.success) {
                showNotification(`Updated ${response.data.updated} orders`, 'success');
                clearSelection();
                loadOrders();
            }
        } catch (error) {
            console.error('Error applying bulk action:', error);
            showNotification('Failed to update orders', 'error');
        }
    }

    function clearSelection() {
        selectedOrders.clear();
        document.querySelectorAll('.order-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('selectAll').checked = false;
        updateBulkActionsBar();
    }

    // Export
    function exportOrders() {
        showNotification('Export feature coming soon', 'info');
    }

    // Print order
    function printOrder(orderId) {
        window.print();
    }

    // Notification - using utils.toast() from helpers.js
    function showNotification(message, type = 'info') {
        utils.toast(message, type);
    }
</script>