<?php
$pageTitle = 'Order Management';
$pageDescription = 'Track and manage your store orders';
include '../shared/header-client.php';
?>

<!-- Page Heading & Store Selector -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">Orders</h1>
        <p class="text-gray-600 dark:text-gray-400 text-sm">Track and manage your store orders, shipments, and returns.</p>
    </div>
    <div class="flex gap-3">
        <!-- Store Selector -->
        <div class="relative group">
            <select id="storeSelector"
                class="px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary transition-all appearance-none pr-10">
                <option value="">Loading stores...</option>
            </select>
            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                expand_more
            </span>
        </div>

        <!-- Refresh Button -->
        <button onclick="refreshOrders()"
            class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm text-gray-900 dark:text-white">
            <span class="material-symbols-outlined" style="font-size: 18px;">refresh</span>
            Refresh
        </button>

        <!-- Export Button -->
        <button onclick="openExportModal()"
            class="flex items-center gap-2 px-4 py-2 bg-primary text-[#0d1b18] rounded-lg text-sm font-bold hover:bg-primary/90 transition-colors shadow-sm">
            <span class="material-symbols-outlined" style="font-size: 18px;">download</span>
            Export
        </button>

        <!-- Dark Mode Toggle -->
        <button onclick="toggleDarkMode()"
            class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-500 dark:text-gray-400 transition-colors"
            aria-label="Toggle dark mode">
            <span class="material-symbols-outlined dark-mode-icon">dark_mode</span>
            <span class="material-symbols-outlined light-mode-icon hidden">light_mode</span>
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div id="statsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
    <!-- Loading Skeletons -->
    <div class="stats-skeleton grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 col-span-full">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
                <div class="h-8 w-16 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
            </div>
            <div class="h-4 w-20 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
                <div class="h-8 w-16 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
            </div>
            <div class="h-4 w-20 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
                <div class="h-8 w-16 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
            </div>
            <div class="h-4 w-20 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
                <div class="h-8 w-16 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
            </div>
            <div class="h-4 w-20 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
                <div class="h-8 w-16 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
            </div>
            <div class="h-4 w-20 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
        </div>
    </div>

    <!-- Actual Stats Content -->
    <div class="stats-content hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 col-span-full">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group hover:shadow-lg transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900 dark:to-green-800 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-green-600 dark:text-green-400">shopping_cart</span>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1" id="statTotalOrders">0</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Orders</p>
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-green-100 dark:bg-green-900 rounded-full opacity-20 group-hover:scale-110 transition-transform"></div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group hover:shadow-lg transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900 dark:to-blue-800 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">payments</span>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1" id="statTotalRevenue">₦0</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Revenue</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group hover:shadow-lg transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900 dark:to-orange-800 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-orange-600 dark:text-orange-400">schedule</span>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1" id="statPendingOrders">0</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Pending</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group hover:shadow-lg transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900 dark:to-purple-800 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-purple-600 dark:text-purple-400">local_shipping</span>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1" id="statShippedOrders">0</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Shipped</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group hover:shadow-lg transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-pink-100 to-pink-200 dark:from-pink-900 dark:to-pink-800 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-pink-600 dark:text-pink-400">check_circle</span>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1" id="statCompletedOrders">0</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Completed</p>
        </div>
    </div>
</div>

<!-- Filters Section -->
<div id="filtersContainer" class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 mb-8">
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="col-span-1 md:col-span-2">
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Search Orders</label>
            <div class="flex items-center bg-gray-50 dark:bg-gray-900/50 rounded-lg px-3 py-2 border border-gray-200 dark:border-gray-700 focus-within:border-primary/50 transition-colors">
                <span class="material-symbols-outlined text-gray-400" style="font-size: 20px;">search</span>
                <input type="text" id="searchOrders" placeholder="Search by order ID, customer..."
                    class="bg-transparent border-none text-sm w-full focus:ring-0 text-gray-900 dark:text-white placeholder-gray-400 ml-2" />
            </div>
        </div>
        <div class="col-span-1">
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Status</label>
            <select id="filterStatus"
                class="w-full bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm py-2 px-3 text-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <div class="col-span-1">
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Payment</label>
            <select id="filterPaymentStatus"
                class="w-full bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm py-2 px-3 text-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                <option value="">All Payments</option>
                <option value="pending">Pending</option>
                <option value="paid">Paid</option>
                <option value="failed">Failed</option>
            </select>
        </div>
        <div class="col-span-1">
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Actions</label>
            <div class="flex gap-2">
                <button onclick="applyFilters()"
                    class="flex-1 px-3 py-2 bg-primary hover:bg-primary-dark text-[#0d1b18] rounded-lg text-sm font-bold transition-all">
                    Apply
                </button>
                <button onclick="clearFilters()"
                    class="px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    Clear
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Orders Table -->
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-900/30">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-gray-600 dark:text-gray-400">shopping_bag</span>
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Order History</h3>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-500 dark:text-gray-400" id="orderCount">0 orders</span>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loadingState" class="p-12">
        <div class="space-y-4">
            <div class="flex items-center gap-4 animate-pulse">
                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded"></div>
                <div class="flex-1">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/4 mb-2"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                </div>
            </div>
            <div class="flex items-center gap-4 animate-pulse">
                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded"></div>
                <div class="flex-1">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/4 mb-2"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                </div>
            </div>
            <div class="flex items-center gap-4 animate-pulse">
                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded"></div>
                <div class="flex-1">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/4 mb-2"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="hidden text-center py-16">
        <span class="material-symbols-outlined text-6xl text-gray-300 dark:text-gray-600 mb-4 block">receipt_long</span>
        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No orders yet</h4>
        <p class="text-gray-500 dark:text-gray-400 mb-4">Your store orders will appear here once customers start purchasing</p>
        <button onclick="refreshOrders()"
            class="px-4 py-2 bg-primary text-[#0d1b18] rounded-lg hover:bg-primary/90 transition-colors font-bold">
            Refresh Orders
        </button>
    </div>

    <!-- Error State -->
    <div id="errorState" class="hidden text-center py-16">
        <span class="material-symbols-outlined text-6xl text-red-300 mb-4 block">error</span>
        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Failed to load orders</h4>
        <p class="text-gray-500 dark:text-gray-400 mb-4">We couldn't load your orders. Please try again.</p>
        <button onclick="refreshOrders()"
            class="px-4 py-2 bg-primary text-[#0d1b18] rounded-lg hover:bg-primary/90 transition-colors font-bold">
            Try Again
        </button>
    </div>

    <!-- Orders Table Content -->
    <div id="ordersTable" class="hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Payment</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody" class="divide-y divide-gray-200 dark:divide-gray-700">
                    <!-- Will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<div id="pagination" class="mt-6"></div>

<!-- Order Details Panel -->
<div id="orderModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50" role="dialog" aria-labelledby="modalOrderId" aria-modal="true">
    <div id="orderPanel" class="absolute top-0 right-0 h-full w-full md:w-[400px] bg-white dark:bg-gray-800 shadow-2xl border-l border-gray-200 dark:border-gray-700 transform translate-x-full transition-transform duration-300 flex flex-col">
        <div class="p-6 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/20">
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white" id="modalOrderId">Order Details</h2>
                <p class="text-xs text-gray-500 mt-1" id="modalOrderDate">Loading...</p>
            </div>
            <button onclick="closeOrderModal()" class="p-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg text-gray-500 transition-colors" aria-label="Close panel">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div id="orderModalContent" class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">
            <!-- Will be populated by JavaScript -->
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div id="updateStatusModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" role="dialog" aria-labelledby="updateStatusTitle" aria-modal="true">
    <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full shadow-2xl border border-gray-200 dark:border-gray-700 transform transition-all">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white" id="updateStatusTitle">Update Order Status</h2>
            <button onclick="closeUpdateStatusModal()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg text-gray-500 transition-colors" aria-label="Close">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form onsubmit="submitStatusUpdate(event)" class="p-6">
            <input type="hidden" id="updateStatusOrderId">

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Select New Status</label>
                <div class="space-y-2">
                    <label class="flex items-center p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all status-option">
                        <input type="radio" name="orderStatus" value="pending" class="w-4 h-4 text-primary focus:ring-primary">
                        <div class="ml-3 flex-1 flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Pending</span>
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">Pending</span>
                        </div>
                    </label>
                    <label class="flex items-center p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all status-option">
                        <input type="radio" name="orderStatus" value="processing" class="w-4 h-4 text-primary focus:ring-primary">
                        <div class="ml-3 flex-1 flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Processing</span>
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">Processing</span>
                        </div>
                    </label>
                    <label class="flex items-center p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all status-option">
                        <input type="radio" name="orderStatus" value="shipped" class="w-4 h-4 text-primary focus:ring-primary">
                        <div class="ml-3 flex-1 flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Shipped</span>
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">Shipped</span>
                        </div>
                    </label>
                    <label class="flex items-center p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all status-option">
                        <input type="radio" name="orderStatus" value="delivered" class="w-4 h-4 text-primary focus:ring-primary">
                        <div class="ml-3 flex-1 flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Delivered</span>
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Delivered</span>
                        </div>
                    </label>
                    <label class="flex items-center p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all status-option">
                        <input type="radio" name="orderStatus" value="cancelled" class="w-4 h-4 text-primary focus:ring-primary">
                        <div class="ml-3 flex-1 flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Cancelled</span>
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Cancelled</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeUpdateStatusModal()"
                    class="flex-1 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors text-gray-900 dark:text-white">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 px-4 py-2 bg-primary hover:bg-primary/90 text-[#0d1b18] rounded-lg text-sm font-bold shadow-lg transition-all">
                    Update Status
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Update Payment Status Modal -->
<div id="updatePaymentStatusModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" role="dialog" aria-labelledby="updatePaymentStatusTitle" aria-modal="true">
    <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full shadow-2xl border border-gray-200 dark:border-gray-700 transform transition-all">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white" id="updatePaymentStatusTitle">Update Payment Status</h2>
            <button onclick="closeUpdatePaymentStatusModal()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg text-gray-500 transition-colors" aria-label="Close">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form onsubmit="submitPaymentStatusUpdate(event)" class="p-6">
            <input type="hidden" id="updatePaymentStatusOrderId">

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Select Payment Status</label>
                <div class="space-y-2">
                    <label class="flex items-center p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all payment-status-option">
                        <input type="radio" name="paymentStatus" value="pending" class="w-4 h-4 text-primary focus:ring-primary">
                        <div class="ml-3 flex-1 flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Pending</span>
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400">Pending</span>
                        </div>
                    </label>
                    <label class="flex items-center p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all payment-status-option">
                        <input type="radio" name="paymentStatus" value="paid" class="w-4 h-4 text-primary focus:ring-primary">
                        <div class="ml-3 flex-1 flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Paid</span>
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Paid</span>
                        </div>
                    </label>
                    <label class="flex items-center p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all payment-status-option">
                        <input type="radio" name="paymentStatus" value="failed" class="w-4 h-4 text-primary focus:ring-primary">
                        <div class="ml-3 flex-1 flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Failed</span>
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Failed</span>
                        </div>
                    </label>
                    <label class="flex items-center p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all payment-status-option">
                        <input type="radio" name="paymentStatus" value="refunded" class="w-4 h-4 text-primary focus:ring-primary">
                        <div class="ml-3 flex-1 flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Refunded</span>
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">Refunded</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeUpdatePaymentStatusModal()"
                    class="flex-1 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors text-gray-900 dark:text-white">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 px-4 py-2 bg-primary hover:bg-primary/90 text-[#0d1b18] rounded-lg text-sm font-bold shadow-lg transition-all">
                    Update Payment Status
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Export Modal -->
<div id="exportModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" role="dialog" aria-labelledby="exportTitle" aria-modal="true">
    <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full shadow-2xl border border-gray-200 dark:border-gray-700 transform transition-all">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-primary">download</span>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white" id="exportTitle">Export Orders</h2>
            </div>
            <button onclick="closeExportModal()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg text-gray-500 transition-colors" aria-label="Close">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <!-- Export Type -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">Export Data</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors export-type-option">
                        <input type="radio" name="exportType" value="filtered" checked class="text-primary focus:ring-primary">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">Current Filtered Data</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400" id="filteredCount">Export orders matching current filters</div>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors export-type-option">
                        <input type="radio" name="exportType" value="all" class="text-primary focus:ring-primary">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">All Orders</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Export all orders from this store</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Date Range (Optional) -->
            <div id="dateRangeSection" class="space-y-3">
                <label class="block text-sm font-semibold text-gray-900 dark:text-white">Date Range (Optional)</label>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">From</label>
                        <input type="date" id="exportFromDate" 
                            class="w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">To</label>
                        <input type="date" id="exportToDate"
                            class="w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary">
                    </div>
                </div>
            </div>

            <!-- Format Selection -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">Export Format</label>
                <select id="exportFormat" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary">
                    <option value="csv">CSV (Excel Compatible)</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-4">
                <button onclick="closeExportModal()" type="button"
                    class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
                <button onclick="handleExport()" type="button" id="exportBtn"
                    class="flex-1 px-4 py-2 bg-primary hover:bg-primary/90 text-[#0d1b18] rounded-lg font-bold shadow-lg transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined" style="font-size: 18px;">download</span>
                    Export
                </button>
            </div>
        </div>
    </div>
</div>

<?php include '../shared/footer-client.php'; ?>

<style>
    /* Dark mode icons toggle */
    html.dark .dark-mode-icon {
        display: none;
    }

    html.dark .light-mode-icon {
        display: inline-block !important;
    }

    /* Smooth transitions */
    * {
        transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
    }

    /* Custom scrollbar for panel */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: rgba(156, 163, 175, 0.5);
        border-radius: 20px;
    }

    /* Status option selected state */
    .status-option:has(input:checked) {
        border-color: var(--primary-color, #86efac);
        background-color: rgba(134, 239, 172, 0.1);
    }

    /* Payment status option selected state */
    .payment-status-option:has(input:checked) {
        border-color: var(--primary-color, #86efac);
        background-color: rgba(134, 239, 172, 0.1);
    }

    /* Export type option selected state */
    .export-type-option:has(input:checked) {
        border-color: var(--primary-color, #86efac);
        background-color: rgba(134, 239, 172, 0.1);
    }
</style>

<script src="/assets/js/services/store.service.js"></script>
<script src="/assets/js/services/client-orders.service.js"></script>
<script src="/assets/js/services/export.service.js"></script>

<script>
    // Initialize order service (ClientOrderService is a class, needs instantiation)
    const orderService = new ClientOrderService(new APIClient());
    const exportService = new ExportService(new APIClient());

    // Global state
    let currentPage = 1;
    let totalPages = 1;
    let currentStoreId = null;
    let stores = [];

    // Dark Mode Management
    function toggleDarkMode() {
        const html = document.documentElement;
        const isDark = html.classList.contains('dark');

        if (isDark) {
            html.classList.remove('dark');
            localStorage.setItem('darkMode', 'false');
        } else {
            html.classList.add('dark');
            localStorage.setItem('darkMode', 'true');
        }
    }

    // Initialize dark mode from localStorage
    function initDarkMode() {
        const darkMode = localStorage.getItem('darkMode');
        if (darkMode === 'true') {
            document.documentElement.classList.add('dark');
        }
    }

    // Show loading states
    function showLoadingStates() {
        // Stats cards
        document.querySelector('.stats-skeleton').classList.remove('hidden');
        document.querySelector('.stats-content').classList.add('hidden');

        // Orders table
        document.getElementById('loadingState').classList.remove('hidden');
        document.getElementById('emptyState').classList.add('hidden');
        document.getElementById('errorState').classList.add('hidden');
        document.getElementById('ordersTable').classList.add('hidden');
    }

    // Show content states
    function showContentStates() {
        // Stats cards
        document.querySelector('.stats-skeleton').classList.add('hidden');
        document.querySelector('.stats-content').classList.remove('hidden');

        // Orders table
        document.getElementById('loadingState').classList.add('hidden');
    }

    // Show empty state
    function showEmptyState() {
        document.getElementById('loadingState').classList.add('hidden');
        document.getElementById('emptyState').classList.remove('hidden');
        document.getElementById('errorState').classList.add('hidden');
        document.getElementById('ordersTable').classList.add('hidden');
    }

    // Show error state
    function showErrorState() {
        document.getElementById('loadingState').classList.add('hidden');
        document.getElementById('emptyState').classList.add('hidden');
        document.getElementById('errorState').classList.remove('hidden');
        document.getElementById('ordersTable').classList.add('hidden');
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', async () => {
        initDarkMode();
        await auth.requireAuth('/auth/login.php');
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

        // Store selector change
        document.getElementById('storeSelector').addEventListener('change', (e) => {
            currentStoreId = e.target.value;
            if (currentStoreId) {
                loadStoreData();
            }
        });
    });

    // Load user's stores
    async function loadStores() {
        try {
            const user = auth.getUser();
            const response = await storeService.getAll({
                client_id: user.id
            });

            if (response.success) {
                stores = response.data.stores || [];

                const selector = document.getElementById('storeSelector');

                if (stores.length === 0) {
                    selector.innerHTML = '<option value="">No stores found</option>';
                    showEmptyState();
                    return;
                }

                // Populate store selector
                selector.innerHTML = '<option value="">Select a store...</option>' +
                    stores.map(store =>
                        `<option value="${store.id}">${store.store_name}</option>`
                    ).join('');

                if (stores.length > 0) {
                    currentStoreId = stores[0].id;
                    selector.value = currentStoreId;
                    await loadStoreData();
                }
            }
        } catch (error) {
            console.error('Failed to load stores:', error);
            utils.toast('Failed to load stores', 'error');
        }
    }

    // Load store data (stats and orders)
    async function loadStoreData() {
        showLoadingStates();
        await Promise.all([loadStats(), loadOrders()]);
    }

    // Load statistics
    async function loadStats() {
        if (!currentStoreId) return;

        try {
            const response = await orderService.getStats(currentStoreId);
            const stats = response.data.overview; // Stats are nested in overview

            const totalOrdersEl = document.getElementById('statTotalOrders');
            const totalRevenueEl = document.getElementById('statTotalRevenue');
            const pendingOrdersEl = document.getElementById('statPendingOrders');
            const shippedOrdersEl = document.getElementById('statShippedOrders');
            const completedOrdersEl = document.getElementById('statCompletedOrders');

            if (totalOrdersEl) totalOrdersEl.textContent = (stats.total_orders || 0).toLocaleString();
            if (totalRevenueEl) totalRevenueEl.textContent = `₦${parseFloat(stats.total_revenue || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            if (pendingOrdersEl) pendingOrdersEl.textContent = (stats.pending_orders || 0).toLocaleString();
            if (shippedOrdersEl) shippedOrdersEl.textContent = (stats.shipped_orders || 0).toLocaleString();
            if (completedOrdersEl) completedOrdersEl.textContent = (stats.delivered_orders || 0).toLocaleString();

            showContentStates();
        } catch (error) {
            console.error('Failed to load stats:', error);
        }
    }

    // Load orders
    async function loadOrders() {
        if (!currentStoreId) return;

        try {
            const filters = {
                page: currentPage,
                search: document.getElementById('searchOrders').value,
                status: document.getElementById('filterStatus').value,
                payment_status: document.getElementById('filterPaymentStatus').value
            };

            const response = await orderService.getOrders(currentStoreId, filters);
            const orders = response.data.orders || []; // Orders are nested in data.orders
            const pagination = response.data.pagination || {
                total: 0,
                page: 1,
                limit: 20,
                pages: 1
            }; // Pagination is nested in data.pagination

            if (orders.length === 0) {
                showEmptyState();
                const orderCountEl = document.getElementById('orderCount');
                if (orderCountEl) orderCountEl.textContent = '0 orders';
                return;
            }

            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('emptyState').classList.add('hidden');
            document.getElementById('errorState').classList.add('hidden');
            document.getElementById('ordersTable').classList.remove('hidden');
            const orderCountEl = document.getElementById('orderCount');
            if (orderCountEl) orderCountEl.textContent = `${pagination.total} order${pagination.total !== 1 ? 's' : ''}`;

            renderOrders(orders);
            updatePagination(pagination);
        } catch (error) {
            console.error('Failed to load orders:', error);
            showErrorState();
        }
    }

    // Render orders
    function renderOrders(orders) {
        const tbody = document.getElementById('ordersTableBody');
        tbody.innerHTML = '';

        orders.forEach(order => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors';
            row.innerHTML = `
                <td class="px-6 py-4">
                    <span class="text-sm font-bold text-primary">#${order.id}</span>
                </td>
                <td class="px-6 py-4">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">${order.customer_name || 'N/A'}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">${order.customer_email || ''}</p>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="text-sm text-gray-900 dark:text-white">${new Date(order.created_at).toLocaleDateString()}</span>
                </td>
                <td class="px-6 py-4">
                    <span class="text-sm font-bold text-gray-900 dark:text-white">₦${parseFloat(order.total_amount).toLocaleString()}</span>
                </td>
                <td class="px-6 py-4">
                    ${getStatusBadge(order.status)}
                </td>
                <td class="px-6 py-4">
                    ${getPaymentBadge(order.payment_status)}
                </td>
                <td class="px-6 py-4 text-right">
                    <button onclick="viewOrder(${order.id})" 
                        class="text-primary hover:text-primary-dark font-medium text-sm transition-colors">
                        View Details
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    // Get status badge
    function getStatusBadge(status) {
        const badges = {
            pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
            processing: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            shipped: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
            delivered: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            cancelled: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
        };
        const className = badges[status] || badges.pending;
        return `<span class="px-2.5 py-1 rounded-full text-xs font-semibold ${className}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
    }

    // Get payment badge
    function getPaymentBadge(status) {
        const badges = {
            pending: 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
            paid: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            failed: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
            refunded: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400'
        };
        const className = badges[status] || badges.pending;
        return `<span class="px-2.5 py-1 rounded-full text-xs font-semibold ${className}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
    }

    // Get payment method display name
    function getPaymentMethodName(method) {
        const methods = {
            'cash_on_delivery': 'Cash on Delivery',
            'bank_transfer': 'Bank Transfer',
            'card': 'Card Payment',
            'wallet': 'Wallet'
        };
        return methods[method] || (method ? method.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'N/A');
    }

    // Update pagination
    function updatePagination(pagination) {
        totalPages = pagination.pages || 1;
        const total = pagination.total || 0;
        const limit = pagination.limit || 20;

        if (totalPages <= 1) {
            document.getElementById('pagination').innerHTML = '';
            return;
        }

        let html = '<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-6 py-4 flex items-center justify-between shadow-sm">';

        // Previous
        html += `
            <button onclick="previousPage()" ${currentPage === 1 ? 'disabled' : ''} 
                class="flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50 dark:hover:bg-gray-700'} transition-colors text-gray-900 dark:text-white">
                <span class="material-symbols-outlined" style="font-size: 18px;">chevron_left</span>
                Previous
            </button>
        `;

        // Pages
        html += '<div class="flex items-center gap-2">';
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                html += `
                    <button onclick="goToPage(${i})" 
                        class="px-3 py-1.5 rounded-lg ${i === currentPage ? 'bg-primary text-[#0d1b18] font-bold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'} transition-colors">
                        ${i}
                    </button>
                `;
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                html += '<span class="text-gray-400">...</span>';
            }
        }
        html += '</div>';

        // Next
        html += `
            <button onclick="nextPage()" ${currentPage === totalPages ? 'disabled' : ''} 
                class="flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50 dark:hover:bg-gray-700'} transition-colors text-gray-900 dark:text-white">
                Next
                <span class="material-symbols-outlined" style="font-size: 18px;">chevron_right</span>
            </button>
        `;

        html += '</div>';
        document.getElementById('pagination').innerHTML = html;
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

    function goToPage(page) {
        currentPage = page;
        loadOrders();
    }

    // Filters
    function applyFilters() {
        currentPage = 1;
        loadOrders();
    }

    function clearFilters() {
        document.getElementById('filterStatus').value = '';
        document.getElementById('filterPaymentStatus').value = '';
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
            const response = await orderService.getOrder(currentStoreId, orderId);
            const order = response.data;

            document.getElementById('modalOrderId').textContent = `Order #${order.id}`;
            const orderDate = new Date(order.created_at);
            document.getElementById('modalOrderDate').textContent = `Placed on ${orderDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })} at ${orderDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}`;
            renderOrderDetails(order);

            // Show modal and slide panel in
            const modal = document.getElementById('orderModal');
            const panel = document.getElementById('orderPanel');
            modal.classList.remove('hidden');
            setTimeout(() => {
                panel.classList.remove('translate-x-full');
            }, 10);
        } catch (error) {
            console.error('Failed to load order details:', error);
            utils.toast('Failed to load order details', 'error');
        }
    }

    // Render order details
    // prettier-ignore
    function renderOrderDetails(order) {
        const content = document.getElementById('orderModalContent');

        // Get initials for customer avatar
        const getInitials = (name) => {
            if (!name) return '??';
            const parts = name.split(' ');
            return parts.length > 1 ? parts[0][0] + parts[1][0] : parts[0][0];
        };

        // Calculate totals
        const subtotal = order.items && order.items.length > 0 ?
            order.items.reduce((sum, item) => sum + (item.quantity * parseFloat(item.price)), 0) :
            parseFloat(order.total_amount || 0);
        const shipping = parseFloat(order.shipping_cost || 0);
        const tax = parseFloat(order.tax_amount || 0);
        const total = parseFloat(order.total_amount || 0);

        content.innerHTML = `
                <!-- Customer Section -->
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Customer</h3>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="size-10 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400 font-bold">
                            ${getInitials(order.customer_name)}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">${order.customer_name || 'N/A'}</p>
                            <p class="text-xs text-primary cursor-pointer hover:underline">View Profile</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        ${order.customer_email ? `
                            <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
                                <span class="material-symbols-outlined text-gray-400 text-[18px]">mail</span>
                                ${order.customer_email}
                            </div>
                        ` : ''}
                        ${order.customer_phone ? `
                            <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
                                <span class="material-symbols-outlined text-gray-400 text-[18px]">call</span>
                                ${order.customer_phone}
                            </div>
                        ` : ''}
                    </div>
                </div>

                <!-- Shipping Address Section -->
                ${order.shipping_address ? `
                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Shipping Address</h3>
                            <span class="text-xs">${getStatusBadge(order.status)}</span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed whitespace-pre-line">${order.shipping_address}</p>
                    </div>
                ` : ''}

                <!-- Order Items Section -->
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">
                        Items (${order.items ? order.items.length : 0})
                    </h3>
                    <div class="space-y-3">
                        ${order.items && order.items.length > 0 ? order.items.map(item => {
                            let imageUrl = '/assets/images/placeholder.png';
                            if (item.images && item.images.length > 0) {
                                const primaryImage = item.images.find(img => img.is_primary);
                                imageUrl = primaryImage ? primaryImage.image_url : item.images[0].image_url;
                            } else if (item.image_url) {
                                imageUrl = item.image_url.split(',')[0];
                            } else if (item.product_image) {
                                imageUrl = item.product_image;
                            }
                            return `<div class="flex gap-3"><div class="size-12 rounded-lg bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 flex-shrink-0 overflow-hidden flex items-center justify-center">${imageUrl !== '/assets/images/placeholder.png' ? `<img src="${imageUrl}" alt="${item.product_name}" class="w-full h-full object-cover">` : `<span class="material-symbols-outlined text-gray-400">inventory_2</span>`}</div><div class="flex-1"><div class="flex justify-between mb-1"><p class="text-sm font-medium text-gray-900 dark:text-white">${item.product_name}</p><p class="text-sm font-semibold text-gray-900 dark:text-white">₦${parseFloat(item.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</p></div><p class="text-xs text-gray-400">Qty: ${item.quantity}${item.variant ? ' • ' + item.variant : ''}</p></div></div>`;
                        }).join('') : `<p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No items found</p>`}
                    </div>
                </div>

                <!-- Order Totals Section -->
                <div class="border-t border-gray-100 dark:border-gray-800 pt-4">
                    <div class="flex justify-between py-1 text-sm text-gray-500">
                        <span>Subtotal</span>
                        <span>₦${subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
                    </div>
                    ${
                        shipping > 0 ? `
                        <div class="flex justify-between py-1 text-sm text-gray-500">
                            <span>Shipping</span>
                            <span>₦${shipping.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
                        </div>
                    ` : ''
                    }
                    ${
                        tax > 0 ? `
                        <div class="flex justify-between py-1 text-sm text-gray-500">
                            <span>Tax</span>
                            <span>₦${tax.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
                        </div>
                    ` : ''
                    }
                    <div class="flex justify-between py-3 mt-2 border-t border-gray-100 dark:border-gray-800 text-base font-bold text-gray-900 dark:text-white">
                        <span>Total</span>
                        <span>₦${total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Payment Method</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">${getPaymentMethodName(order.payment_method)}</span>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-gray-200 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Payment Status</span>
                        ${getPaymentBadge(order.payment_status)}
                    </div>
                    ${order.payment_reference ? `
                        <div class="flex justify-between items-center pt-3 border-t border-gray-200 dark:border-gray-700">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Reference</span>
                            <span class="text-xs font-mono text-gray-700 dark:text-gray-300">${order.payment_reference}</span>
                        </div>
                    ` : ''}
                </div>

                <!-- Action Buttons -->
                <div class="space-y-2">
                    <div class="flex gap-3">
                        <button onclick="closeOrderModal()"
                            class="flex-1 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors shadow-sm text-gray-900 dark:text-white">
                            Close
                        </button>
                        ${
                            order.status !== 'delivered' && order.status !== 'cancelled' ? `
                            <button onclick="updateOrderStatus(${order.id})" 
                                class="flex-1 px-4 py-2 bg-primary hover:bg-primary/90 text-[#0d1b18] rounded-lg text-sm font-bold shadow-lg transition-all">
                                Update Status
                            </button>
                        ` : ''
                        }
                    </div>
                    ${
                        order.payment_status !== 'paid' && order.payment_status !== 'refunded' ? `
                        <button onclick="updatePaymentStatus(${order.id})" 
                            class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-bold shadow-lg transition-all">
                            Update Payment Status
                        </button>
                    ` : ''
                    }
                </div>
            </div>
    `;
    }

    // Update order status
    function updateOrderStatus(orderId) {
        document.getElementById('updateStatusOrderId').value = orderId;
        document.getElementById('updateStatusModal').classList.remove('hidden');
        // Reset radio buttons
        document.querySelectorAll('input[name="orderStatus"]').forEach(radio => radio.checked = false);
    }

    // Submit status update
    async function submitStatusUpdate(event) {
        event.preventDefault();
        
        const orderId = document.getElementById('updateStatusOrderId').value;
        const selectedStatus = document.querySelector('input[name="orderStatus"]:checked');
        
        if (!selectedStatus) {
            utils.toast('Please select a status', 'error');
            return;
        }
        
        const newStatus = selectedStatus.value;
        
        try {
            await orderService.updateStatus(currentStoreId, orderId, newStatus);
            utils.toast('Order status updated successfully', 'success');
            closeUpdateStatusModal();
            closeOrderModal();
            loadOrders();
        } catch (error) {
            console.error('Failed to update order status:', error);
            utils.toast('Failed to update order status', 'error');
        }
    }

    // Close update status modal
    function closeUpdateStatusModal() {
        document.getElementById('updateStatusModal').classList.add('hidden');
    }

    // Update payment status
    function updatePaymentStatus(orderId) {
        document.getElementById('updatePaymentStatusOrderId').value = orderId;
        document.getElementById('updatePaymentStatusModal').classList.remove('hidden');
        // Reset radio buttons
        document.querySelectorAll('input[name="paymentStatus"]').forEach(radio => radio.checked = false);
    }

    // Submit payment status update
    async function submitPaymentStatusUpdate(event) {
        event.preventDefault();
        
        const orderId = document.getElementById('updatePaymentStatusOrderId').value;
        const selectedStatus = document.querySelector('input[name="paymentStatus"]:checked');
        
        if (!selectedStatus) {
            utils.toast('Please select a payment status', 'error');
            return;
        }
        
        const newPaymentStatus = selectedStatus.value;
        
        try {
            await orderService.updatePaymentStatus(currentStoreId, orderId, newPaymentStatus);
            utils.toast('Payment status updated successfully', 'success');
            closeUpdatePaymentStatusModal();
            closeOrderModal();
            loadOrders();
        } catch (error) {
            console.error('Failed to update payment status:', error);
            utils.toast('Failed to update payment status', 'error');
        }
    }

    // Close update payment status modal
    function closeUpdatePaymentStatusModal() {
        document.getElementById('updatePaymentStatusModal').classList.add('hidden');
    }

    // Close modal
    function closeOrderModal() {
        const panel = document.getElementById('orderPanel');
        const modal = document.getElementById('orderModal');

        // Slide panel out
        panel.classList.add('translate-x-full');

        // Hide modal after animation
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Export modal functions
    function openExportModal() {
        document.getElementById('exportModal').classList.remove('hidden');
    }

    function closeExportModal() {
        document.getElementById('exportModal').classList.add('hidden');
    }

    async function handleExport() {
        try {
            const exportType = document.querySelector('input[name="exportType"]:checked').value;
            const exportFormat = document.getElementById('exportFormat').value;

            // Build filters
            const filters = {};

            // If exporting filtered data, use current filters
            if (exportType === 'filtered') {
                const statusFilter = document.getElementById('filterStatus').value;
                const paymentStatusFilter = document.getElementById('filterPaymentStatus').value;
                const searchInput = document.getElementById('searchOrders').value;

                if (statusFilter) filters.status = statusFilter;
                if (paymentStatusFilter) filters.payment_status = paymentStatusFilter;
                if (searchInput) filters.search = searchInput;
            }

            // Add date range if specified
            const dateFrom = document.getElementById('exportFromDate').value;
            const dateTo = document.getElementById('exportToDate').value;
            if (dateFrom) filters.from_date = dateFrom;
            if (dateTo) filters.to_date = dateTo;

            // Trigger export
            await exportService.exportOrders(currentStoreId, filters, exportFormat);
            
            // Show success message
            utils.toast('Export started! Your download will begin shortly.', 'success');
            
            // Close modal
            closeExportModal();
        } catch (error) {
            console.error('Export failed:', error);
            utils.toast(error.message || 'Failed to export data', 'error');
        }
    }

    // Initialize page
    document.addEventListener('DOMContentLoaded', () => {
        loadStores();
    });
</script>