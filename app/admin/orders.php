<?php
$pageTitle = 'Orders Management';
$pageDescription = 'Manage all orders across all stores';
include '../shared/header-admin.php';
?>

<!-- Page Header -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Orders Management</h1>
        <p class="text-gray-500 mt-1">View and manage orders from all stores</p>
    </div>
</div>

<!-- Filters and Search -->
<div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Store Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Store</label>
            <select id="filterStore" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">All Stores</option>
            </select>
        </div>

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

        <!-- Date Range -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
            <input type="date" id="filterFromDate" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
            <input type="date" id="filterToDate" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>
    </div>

    <!-- Search -->
    <div class="mt-4">
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
    <div class="flex gap-3 mt-4">
        <button onclick="applyFilters()" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
            Apply Filters
        </button>
        <button onclick="clearFilters()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
            Clear Filters
        </button>
        <button onclick="exportOrders()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors ml-auto">
            <span class="material-symbols-outlined inline-block align-middle">download</span>
            Export
        </button>
    </div>
</div>

<!-- Statistics Cards -->
<div id="statsContainer" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6 hidden">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-500">Total Orders</span>
            <span class="material-symbols-outlined text-blue-600">shopping_bag</span>
        </div>
        <p class="text-2xl font-bold text-gray-900" id="statTotalOrders">0</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-500">Total Revenue</span>
            <span class="material-symbols-outlined text-green-600">payments</span>
        </div>
        <p class="text-2xl font-bold text-gray-900" id="statTotalRevenue">₦0</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-500">Pending Orders</span>
            <span class="material-symbols-outlined text-yellow-600">pending</span>
        </div>
        <p class="text-2xl font-bold text-gray-900" id="statPendingOrders">0</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-500">Completed Orders</span>
            <span class="material-symbols-outlined text-green-600">check_circle</span>
        </div>
        <p class="text-2xl font-bold text-gray-900" id="statCompletedOrders">0</p>
    </div>
</div>

<!-- Orders Table -->
<div class="bg-white rounded-xl border border-gray-200">
    <div class="p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">All Orders</h2>

        <!-- Loading State -->
        <div id="loadingState" class="flex items-center justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="hidden text-center py-12">
            <span class="material-symbols-outlined text-gray-400 text-6xl mb-4">inbox</span>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No orders found</h3>
            <p class="text-gray-500">Try adjusting your filters or search query</p>
        </div>

        <!-- Orders Table -->
        <div id="ordersTable" class="hidden overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-primary focus:ring-primary">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Store</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Payment</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody" class="divide-y divide-gray-200">
                    <!-- Rows will be inserted here -->
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div id="paginationContainer" class="hidden flex items-center justify-between mt-6">
            <div class="text-sm text-gray-500">
                Showing <span id="showingFrom">1</span> to <span id="showingTo">20</span> of <span id="totalOrders">0</span> orders
            </div>
            <div class="flex gap-2">
                <button onclick="previousPage()" id="prevBtn" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Previous
                </button>
                <button onclick="nextPage()" id="nextBtn" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Next
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions Bar (shown when items selected) -->
<div id="bulkActionsBar" class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 bg-gray-900 text-white rounded-full px-6 py-3 shadow-lg">
    <div class="flex items-center gap-4">
        <span id="selectedCount" class="font-medium">0 selected</span>
        <div class="h-6 w-px bg-gray-700"></div>
        <select id="bulkAction" class="bg-gray-800 text-white rounded px-3 py-1 text-sm">
            <option value="">Bulk Action</option>
            <option value="processing">Mark as Processing</option>
            <option value="shipped">Mark as Shipped</option>
            <option value="delivered">Mark as Delivered</option>
            <option value="cancelled">Mark as Cancelled</option>
        </select>
        <button onclick="applyBulkAction()" class="bg-primary hover:bg-primary/90 px-4 py-1 rounded text-sm font-medium">
            Apply
        </button>
        <button onclick="clearSelection()" class="text-gray-400 hover:text-white">
            <span class="material-symbols-outlined text-lg">close</span>
        </button>
    </div>
</div>

<!-- Order Details Modal -->
<div id="orderModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Order Details</h2>
            <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div id="orderModalContent" class="p-6">
            <!-- Order details will be loaded here -->
        </div>
    </div>
</div>

<script src="/assets/js/api.js"></script>
<script src="/assets/js/auth.js"></script>
<script src="/assets/js/services/admin-orders.js"></script>
<script>
    const apiClient = new APIClient();
    const authService = new AuthService(apiClient);
    const orderService = new AdminOrderService(apiClient);

    let currentPage = 1;
    let totalPages = 1;
    let selectedOrders = new Set();
    let currentStoreId = null;
    let stores = [];

    // Initialize
    document.addEventListener('DOMContentLoaded', async () => {
        await authService.requireAuth('/auth/login.php');
        await loadStores();
        await loadOrders();

        // Setup search with debounce
        let searchTimeout;
        document.getElementById('searchOrders').addEventListener('input', (e) => {
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
    });

    // Load stores for filter
    async function loadStores() {
        try {
            const response = await apiClient.get('/api/stores');
            stores = response.data.stores || [];

            const filterStore = document.getElementById('filterStore');
            stores.forEach(store => {
                const option = document.createElement('option');
                option.value = store.id;
                option.textContent = store.name;
                filterStore.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading stores:', error);
        }
    }

    // Load orders
    async function loadOrders() {
        try {
            document.getElementById('loadingState').classList.remove('hidden');
            document.getElementById('emptyState').classList.add('hidden');
            document.getElementById('ordersTable').classList.add('hidden');
            document.getElementById('paginationContainer').classList.add('hidden');

            // Get selected store
            const storeFilter = document.getElementById('filterStore').value;

            if (!storeFilter && stores.length > 0) {
                // If no store selected, show message
                document.getElementById('loadingState').classList.add('hidden');
                document.getElementById('emptyState').classList.remove('hidden');
                document.getElementById('emptyState').querySelector('p').textContent = 'Please select a store to view orders';
                return;
            }

            if (!storeFilter) {
                document.getElementById('loadingState').classList.add('hidden');
                document.getElementById('emptyState').classList.remove('hidden');
                document.getElementById('emptyState').querySelector('p').textContent = 'No stores available';
                return;
            }

            // Build filters
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

            const response = await orderService.getOrders(storeFilter, filters);

            if (response.success) {
                const orders = response.data.orders || [];
                const pagination = response.data.pagination || {};
                const stats = response.data.stats || {};

                // Update stats
                if (stats) {
                    document.getElementById('statsContainer').classList.remove('hidden');
                    document.getElementById('statTotalOrders').textContent = stats.total_orders || 0;
                    document.getElementById('statTotalRevenue').textContent = orderService.formatCurrency(stats.total_revenue || 0);
                    document.getElementById('statPendingOrders').textContent = stats.pending_orders || 0;
                    document.getElementById('statCompletedOrders').textContent = stats.completed_orders || 0;
                }

                if (orders.length === 0) {
                    document.getElementById('loadingState').classList.add('hidden');
                    document.getElementById('emptyState').classList.remove('hidden');
                } else {
                    renderOrders(orders);
                    updatePagination(pagination);
                    document.getElementById('loadingState').classList.add('hidden');
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

    // Render orders table
    function renderOrders(orders) {
        const tbody = document.getElementById('ordersTableBody');
        tbody.innerHTML = '';

        orders.forEach(order => {
            const storeName = stores.find(s => s.id == order.store_id)?.name || 'Unknown Store';

            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';
            row.innerHTML = `
                <td class="px-4 py-3">
                    <input type="checkbox" value="${order.id}" class="order-checkbox rounded border-gray-300 text-primary focus:ring-primary">
                </td>
                <td class="px-4 py-3">
                    <span class="font-medium text-gray-900">#${order.id}</span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-900">${storeName}</td>
                <td class="px-4 py-3">
                    <div>
                        <div class="text-sm font-medium text-gray-900">${order.customer_name}</div>
                        <div class="text-sm text-gray-500">${order.customer_email}</div>
                    </div>
                </td>
                <td class="px-4 py-3 text-sm font-medium text-gray-900">${orderService.formatCurrency(order.total_amount)}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full ${orderService.getStatusBadgeClass(order.status)}">
                        ${order.status}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full ${orderService.getPaymentStatusBadgeClass(order.payment_status)}">
                        ${order.payment_status}
                    </span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-500">${orderService.formatDate(order.created_at)}</td>
                <td class="px-4 py-3">
                    <button onclick="viewOrder(${order.store_id}, ${order.id})" class="text-primary hover:text-primary/80">
                        <span class="material-symbols-outlined">visibility</span>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });

        // Add checkbox event listeners
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

        document.getElementById('prevBtn').disabled = currentPage === 1;
        document.getElementById('nextBtn').disabled = currentPage === totalPages;
    }

    // Pagination functions
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

    // Filter functions
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

    // View order details
    async function viewOrder(storeId, orderId) {
        try {
            document.getElementById('orderModal').classList.remove('hidden');
            document.getElementById('orderModalContent').innerHTML = '<div class="flex justify-center py-12"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div></div>';

            const response = await orderService.getOrder(storeId, orderId);

            if (response.success) {
                renderOrderDetails(response.data, storeId);
            }
        } catch (error) {
            console.error('Error loading order details:', error);
            showNotification('Failed to load order details', 'error');
            closeOrderModal();
        }
    }

    // Render order details in modal
    function renderOrderDetails(order, storeId) {
        const content = document.getElementById('orderModalContent');
        content.innerHTML = `
            <div class="space-y-6">
                <!-- Order Info -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Order ID</p>
                        <p class="font-semibold">#${order.id}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Date</p>
                        <p class="font-semibold">${orderService.formatDate(order.created_at)}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full ${orderService.getStatusBadgeClass(order.status)}">${order.status}</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Payment Status</p>
                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full ${orderService.getPaymentStatusBadgeClass(order.payment_status)}">${order.payment_status}</span>
                    </div>
                </div>

                <!-- Customer Info -->
                <div>
                    <h3 class="font-semibold mb-2">Customer Information</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p><span class="font-medium">Name:</span> ${order.customer_name}</p>
                        <p><span class="font-medium">Email:</span> ${order.customer_email}</p>
                        <p><span class="font-medium">Phone:</span> ${order.customer_phone || 'N/A'}</p>
                    </div>
                </div>

                <!-- Order Items -->
                <div>
                    <h3 class="font-semibold mb-2">Order Items</h3>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        ${order.items && order.items.length > 0 ? order.items.map(item => `
                            <div class="flex items-center justify-between p-4 border-b border-gray-200 last:border-0">
                                <div>
                                    <p class="font-medium">${item.product_name || 'Product'}</p>
                                    <p class="text-sm text-gray-500">Qty: ${item.quantity}</p>
                                </div>
                                <p class="font-medium">${orderService.formatCurrency(item.price * item.quantity)}</p>
                            </div>
                        `).join('') : '<p class="p-4 text-gray-500">No items</p>'}
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="border-t pt-4">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">${orderService.formatCurrency(order.total_amount - (order.shipping_cost || 0))}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Shipping</span>
                        <span class="font-medium">${orderService.formatCurrency(order.shipping_cost || 0)}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold border-t pt-2">
                        <span>Total</span>
                        <span>${orderService.formatCurrency(order.total_amount)}</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-3">
                    <select id="updateStatus" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Update Status...</option>
                        <option value="pending" ${order.status === 'pending' ? 'selected' : ''}>Pending</option>
                        <option value="processing" ${order.status === 'processing' ? 'selected' : ''}>Processing</option>
                        <option value="shipped" ${order.status === 'shipped' ? 'selected' : ''}>Shipped</option>
                        <option value="delivered" ${order.status === 'delivered' ? 'selected' : ''}>Delivered</option>
                        <option value="cancelled" ${order.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                    </select>
                    <button onclick="updateOrderStatus(${storeId}, ${order.id})" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary/90">
                        Update
                    </button>
                </div>
            </div>
        `;
    }

    // Update order status from modal
    async function updateOrderStatus(storeId, orderId) {
        const status = document.getElementById('updateStatus').value;
        if (!status) {
            showNotification('Please select a status', 'error');
            return;
        }

        try {
            const response = await orderService.updateStatus(storeId, orderId, status);
            if (response.success) {
                showNotification('Order status updated successfully', 'success');
                closeOrderModal();
                loadOrders();
            }
        } catch (error) {
            console.error('Error updating order status:', error);
            showNotification('Failed to update order status', 'error');
        }
    }

    // Close modal
    function closeOrderModal() {
        document.getElementById('orderModal').classList.add('hidden');
    }

    // Update bulk actions bar
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

    // Apply bulk action
    async function applyBulkAction() {
        const action = document.getElementById('bulkAction').value;
        if (!action) {
            showNotification('Please select an action', 'error');
            return;
        }

        const storeId = document.getElementById('filterStore').value;
        if (!storeId) {
            showNotification('Please select a store first', 'error');
            return;
        }

        if (selectedOrders.size === 0) {
            showNotification('No orders selected', 'error');
            return;
        }

        try {
            const response = await orderService.bulkUpdate(storeId, Array.from(selectedOrders), action);
            if (response.success) {
                showNotification(`Updated ${response.data.updated} orders successfully`, 'success');
                clearSelection();
                loadOrders();
            }
        } catch (error) {
            console.error('Error applying bulk action:', error);
            showNotification('Failed to update orders', 'error');
        }
    }

    // Clear selection
    function clearSelection() {
        selectedOrders.clear();
        document.querySelectorAll('.order-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('selectAll').checked = false;
        updateBulkActionsBar();
    }

    // Export orders
    function exportOrders() {
        showNotification('Export feature coming soon', 'info');
    }

    // Notification helper
    function showNotification(message, type = 'info') {
        // You can implement a toast notification here
        alert(message);
    }
</script>

<?php include '../shared/footer-admin.php'; ?>