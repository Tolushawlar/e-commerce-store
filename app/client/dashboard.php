<?php
$pageTitle = 'Dashboard';
$pageDescription = 'Store analytics and insights';
include '../shared/header-client.php';
?>

<!-- Page Heading & Store Selector -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Overview</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Welcome back! Here's what's happening with your store.</p>
    </div>
    <div class="flex items-center gap-3">
        <!-- Period Selector -->
        <select id="periodSelector" onchange="changePeriod(this.value)"
            class="px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary transition-all"
            aria-label="Select time period">
            <option value="7">Last 7 days</option>
            <option value="30" selected>Last 30 days</option>
            <option value="90">Last 90 days</option>
        </select>

        <!-- Store Selector -->
        <div class="relative group">
            <select id="storeSelector" onchange="changeStore(this.value)"
                class="px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary transition-all appearance-none pr-10"
                aria-label="Select store">
                <option value="">Loading stores...</option>
            </select>
            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                expand_more
            </span>
        </div>

        <!-- Dark Mode Toggle -->
        <button onclick="toggleDarkMode()"
            class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-500 dark:text-gray-400 transition-colors"
            aria-label="Toggle dark mode">
            <span class="material-symbols-outlined dark-mode-icon">dark_mode</span>
            <span class="material-symbols-outlined light-mode-icon hidden">light_mode</span>
        </button>
    </div>
</div>

<!-- Stats Grid (KPIs) -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Revenue Card -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group hover:shadow-lg transition-all"
        role="region" aria-label="Revenue statistics">
        <!-- Loading Skeleton -->
        <div class="stat-skeleton">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
            </div>
            <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-1/2 mb-2 animate-pulse"></div>
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/3 animate-pulse"></div>
        </div>

        <!-- Actual Content -->
        <div class="stat-content hidden">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900 dark:to-green-800 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-green-600 dark:text-green-400">payments</span>
                </div>
                <span class="revenue-trend px-2 py-1 rounded-full text-xs font-semibold"></span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1" id="revenueValue">₦0.00</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Revenue</p>
        </div>

        <!-- Background Decoration -->
        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-green-100 dark:bg-green-900 rounded-full opacity-20 group-hover:scale-110 transition-transform"></div>
    </div>

    <!-- Orders Card -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden hover:shadow-lg transition-all"
        role="region" aria-label="Orders statistics">
        <!-- Loading Skeleton -->
        <div class="stat-skeleton">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
            </div>
            <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-1/2 mb-2 animate-pulse"></div>
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/3 animate-pulse"></div>
        </div>

        <!-- Actual Content -->
        <div class="stat-content hidden">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900 dark:to-blue-800 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">shopping_bag</span>
                </div>
                <span class="orders-trend px-2 py-1 rounded-full text-xs font-semibold"></span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1" id="ordersValue">0</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Orders</p>
        </div>
    </div>

    <!-- Customers Card -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden hover:shadow-lg transition-all"
        role="region" aria-label="Customers statistics">
        <!-- Loading Skeleton -->
        <div class="stat-skeleton">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
            </div>
            <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-1/2 mb-2 animate-pulse"></div>
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/3 animate-pulse"></div>
        </div>

        <!-- Actual Content -->
        <div class="stat-content hidden">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900 dark:to-purple-800 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-purple-600 dark:text-purple-400">group</span>
                </div>
                <span class="customers-trend px-2 py-1 rounded-full text-xs font-semibold"></span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1" id="customersValue">0</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Customers</p>
        </div>
    </div>

    <!-- Conversion Rate Card -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden hover:shadow-lg transition-all"
        role="region" aria-label="Conversion rate statistics">
        <!-- Loading Skeleton -->
        <div class="stat-skeleton">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
            </div>
            <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-1/2 mb-2 animate-pulse"></div>
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/3 animate-pulse"></div>
        </div>

        <!-- Actual Content -->
        <div class="stat-content hidden">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900 dark:to-orange-800 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-orange-600 dark:text-orange-400">trending_up</span>
                </div>
                <span class="conversion-trend px-2 py-1 rounded-full text-xs font-semibold"></span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1" id="conversionValue">0%</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Conversion Rate</p>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Revenue Chart -->
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700"
        role="region" aria-label="Revenue chart">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Revenue Overview</h3>

        <!-- Loading Skeleton -->
        <div class="chart-skeleton">
            <div class="h-64 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
        </div>

        <!-- Chart Canvas -->
        <div class="chart-content hidden">
            <canvas id="revenueChart" height="250" role="img" aria-label="Revenue over time chart"></canvas>
        </div>

        <!-- Empty State -->
        <div class="chart-empty hidden text-center py-12">
            <span class="material-symbols-outlined text-6xl text-gray-300 dark:text-gray-600">insert_chart</span>
            <h4 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No revenue data yet</h4>
            <p class="mt-2 text-gray-500 dark:text-gray-400">Revenue data will appear here once you start receiving orders</p>
        </div>

        <!-- Error State -->
        <div class="chart-error hidden text-center py-12">
            <span class="material-symbols-outlined text-6xl text-red-300">error</span>
            <h4 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Failed to load chart</h4>
            <p class="mt-2 text-gray-500 dark:text-gray-400">We couldn't load the revenue data</p>
            <button onclick="loadDashboard()" class="mt-4 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                Try Again
            </button>
        </div>
    </div>

    <!-- Order Status Distribution -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700"
        role="region" aria-label="Order status distribution">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Order Status</h3>

        <!-- Loading Skeleton -->
        <div class="chart-skeleton">
            <div class="h-64 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
        </div>

        <!-- Chart Canvas -->
        <div class="chart-content hidden">
            <canvas id="orderStatusChart" height="250" role="img" aria-label="Order status distribution chart"></canvas>
        </div>

        <!-- Empty State -->
        <div class="chart-empty hidden text-center py-12">
            <span class="material-symbols-outlined text-6xl text-gray-300 dark:text-gray-600">donut_small</span>
            <h4 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No orders yet</h4>
            <p class="mt-2 text-gray-500 dark:text-gray-400">Order statistics will appear here</p>
        </div>
    </div>
</div>

<!-- Bottom Section: Top Products Table -->
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
    role="region" aria-label="Top products">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Top Products</h3>
        <a href="/client/products.php" class="text-primary hover:text-primary/80 text-sm font-semibold transition-colors">
            View All →
        </a>
    </div>

    <!-- Loading Skeleton -->
    <div class="products-skeleton p-6">
        <div class="space-y-4">
            <div class="flex items-center gap-4 animate-pulse" role="status" aria-label="Loading">
                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded"></div>
                <div class="flex-1">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/3 mb-2"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
                </div>
            </div>
            <div class="flex items-center gap-4 animate-pulse">
                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded"></div>
                <div class="flex-1">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/3 mb-2"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
                </div>
            </div>
            <div class="flex items-center gap-4 animate-pulse">
                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded"></div>
                <div class="flex-1">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/3 mb-2"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actual Content -->
    <div class="products-content hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Sold</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Revenue</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody id="topProductsTable" class="divide-y divide-gray-200 dark:divide-gray-700">
                    <!-- Rows will be inserted here -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Empty State -->
    <div class="products-empty hidden text-center py-12">
        <span class="material-symbols-outlined text-6xl text-gray-300 dark:text-gray-600">inventory_2</span>
        <h4 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No products sold yet</h4>
        <p class="mt-2 text-gray-500 dark:text-gray-400">Your top-selling products will appear here</p>
        <a href="/client/products.php" class="inline-block mt-4 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
            Add Products
        </a>
    </div>

    <!-- Error State -->
    <div class="products-error hidden text-center py-12">
        <span class="material-symbols-outlined text-6xl text-red-300">error</span>
        <h4 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Failed to load products</h4>
        <p class="mt-2 text-gray-500 dark:text-gray-400">We couldn't load your top products</p>
        <button onclick="loadTopProducts()" class="mt-4 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
            Try Again
        </button>
    </div>
</div>

<?php include '../shared/footer-client.php'; ?>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<!-- Services -->
<script src="/assets/js/services/store.service.js"></script>
<script src="/assets/js/services/dashboard.service.js"></script>

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
</style>

<script>
    // Global state
    let currentStore = null;
    let currentPeriod = 30;
    let revenueChartInstance = null;
    let orderStatusChartInstance = null;
    let userStores = [];

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

        // Update charts with new theme
        if (revenueChartInstance) updateChartTheme(revenueChartInstance);
        if (orderStatusChartInstance) updateChartTheme(orderStatusChartInstance);
    }

    // Initialize dark mode from localStorage
    function initDarkMode() {
        const darkMode = localStorage.getItem('darkMode');
        if (darkMode === 'true') {
            document.documentElement.classList.add('dark');
        }
    }

    // Change store
    function changeStore(storeId) {
        if (storeId && storeId !== currentStore?.id) {
            currentStore = userStores.find(s => s.id == storeId);
            loadDashboard();
        }
    }

    // Change period
    function changePeriod(period) {
        currentPeriod = parseInt(period);
        loadDashboard();
    }

    // Load user stores
    async function loadUserStores() {
        try {
            const user = auth.getUser();
            const response = await storeService.getAll({
                client_id: user.id
            });

            if (response.success) {
                userStores = response.data.stores || [];

                const selector = document.getElementById('storeSelector');
                if (userStores.length === 0) {
                    selector.innerHTML = '<option value="">No stores found</option>';
                    showEmptyStoreState();
                    return;
                }

                // Populate store selector
                selector.innerHTML = userStores.map(store =>
                    `<option value="${store.id}">${store.store_name}</option>`
                ).join('');

                // Set first store as current
                currentStore = userStores[0];
                selector.value = currentStore.id;

                // Load dashboard data
                loadDashboard();
            }
        } catch (error) {
            console.error('Error loading stores:', error);
            utils.toast('Failed to load stores', 'error');
        }
    }

    // Main dashboard loader
    async function loadDashboard() {
        if (!currentStore) return;

        // Show loading states
        showLoadingStates();

        try {
            // Load all dashboard data in parallel
            const [stats, revenueData, orderStatusData, topProducts] = await Promise.all([
                DashboardService.getStats(currentStore.id, currentPeriod),
                DashboardService.getRevenueChart(currentStore.id, currentPeriod),
                DashboardService.getOrderStatus(currentStore.id, currentPeriod),
                DashboardService.getTopProducts(currentStore.id, 5, currentPeriod)
            ]);

            // Update stats cards
            if (stats.success) {
                updateStatsCards(stats.data);
            }

            // Update revenue chart
            if (revenueData.success) {
                updateRevenueChart(revenueData.data);
            }

            // Update order status chart
            if (orderStatusData.success) {
                updateOrderStatusChart(orderStatusData.data);
            }

            // Update top products table
            if (topProducts.success) {
                updateTopProducts(topProducts.data);
            }

        } catch (error) {
            console.error('Dashboard error:', error);
            showErrorStates();
            utils.toast('Failed to load dashboard data', 'error');
        }
    }

    // Show loading states
    function showLoadingStates() {
        // Stats cards
        document.querySelectorAll('.stat-skeleton').forEach(el => el.classList.remove('hidden'));
        document.querySelectorAll('.stat-content').forEach(el => el.classList.add('hidden'));

        // Charts
        document.querySelectorAll('.chart-skeleton').forEach(el => el.classList.remove('hidden'));
        document.querySelectorAll('.chart-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.chart-empty').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.chart-error').forEach(el => el.classList.add('hidden'));

        // Products
        document.querySelector('.products-skeleton').classList.remove('hidden');
        document.querySelector('.products-content').classList.add('hidden');
        document.querySelector('.products-empty').classList.add('hidden');
        document.querySelector('.products-error').classList.add('hidden');
    }

    // Show error states
    function showErrorStates() {
        // Stats cards - show zero values
        document.querySelectorAll('.stat-skeleton').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.stat-content').forEach(el => el.classList.remove('hidden'));

        // Charts
        document.querySelectorAll('.chart-skeleton').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.chart-error').forEach(el => el.classList.remove('hidden'));

        // Products
        document.querySelector('.products-skeleton').classList.add('hidden');
        document.querySelector('.products-error').classList.remove('hidden');
    }

    // Update stats cards
    function updateStatsCards(data) {
        // Hide skeletons, show content
        document.querySelectorAll('.stat-skeleton').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.stat-content').forEach(el => el.classList.remove('hidden'));

        // Revenue
        document.getElementById('revenueValue').textContent = data.revenue.formatted;
        updateTrendBadge('.revenue-trend', data.revenue.trend);

        // Orders
        document.getElementById('ordersValue').textContent = data.orders.current;
        updateTrendBadge('.orders-trend', data.orders.trend);

        // Customers
        document.getElementById('customersValue').textContent = data.customers.current;
        updateTrendBadge('.customers-trend', data.customers.trend);

        // Conversion Rate
        document.getElementById('conversionValue').textContent = data.conversion_rate.current + '%';
        updateTrendBadge('.conversion-trend', data.conversion_rate.trend);
    }

    // Update trend badge
    function updateTrendBadge(selector, trend) {
        const badge = document.querySelector(selector);
        if (!badge) {
            console.warn('Trend badge not found:', selector);
            return;
        }

        const isPositive = trend >= 0;

        badge.className = `px-2 py-1 rounded-full text-xs font-semibold ${
            isPositive 
                ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400' 
                : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400'
        }`;

        badge.innerHTML = `
            <span class="material-symbols-outlined text-xs align-middle">
                ${isPositive ? 'trending_up' : 'trending_down'}
            </span>
            ${Math.abs(trend)}%
        `;
    }

    // Update revenue chart
    function updateRevenueChart(data) {
        const canvas = document.getElementById('revenueChart');
        const container = canvas.closest('.chart-content');
        const skeleton = canvas.closest('[role="region"]').querySelector('.chart-skeleton');
        const emptyState = canvas.closest('[role="region"]').querySelector('.chart-empty');

        if (!data.data || data.data.every(v => v === 0)) {
            skeleton.classList.add('hidden');
            emptyState.classList.remove('hidden');
            container.classList.add('hidden');
            return;
        }

        skeleton.classList.add('hidden');
        emptyState.classList.add('hidden');
        container.classList.remove('hidden');

        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#9CA3AF' : '#6B7280';
        const gridColor = isDark ? 'rgba(75, 85, 99, 0.2)' : 'rgba(229, 231, 235, 1)';

        if (revenueChartInstance) {
            revenueChartInstance.destroy();
        }

        const ctx = canvas.getContext('2d');
        revenueChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Revenue',
                    data: data.data,
                    borderColor: '#064E3B',
                    backgroundColor: 'rgba(6, 78, 59, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#064E3B',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: isDark ? '#1F2937' : '#fff',
                        titleColor: isDark ? '#fff' : '#111',
                        bodyColor: isDark ? '#D1D5DB' : '#666',
                        borderColor: isDark ? '#374151' : '#E5E7EB',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: (context) => `${data.currency}${context.parsed.y.toLocaleString()}`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: textColor,
                            callback: (value) => `${data.currency}${value.toLocaleString()}`
                        },
                        grid: {
                            color: gridColor
                        }
                    },
                    x: {
                        ticks: {
                            color: textColor
                        },
                        grid: {
                            color: gridColor
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }

    // Update order status chart
    function updateOrderStatusChart(data) {
        const canvas = document.getElementById('orderStatusChart');
        const container = canvas.closest('.chart-content');
        const skeleton = canvas.closest('[role="region"]').querySelector('.chart-skeleton');
        const emptyState = canvas.closest('[role="region"]').querySelector('.chart-empty');

        if (!data.data || data.data.every(v => v === 0)) {
            skeleton.classList.add('hidden');
            emptyState.classList.remove('hidden');
            container.classList.add('hidden');
            return;
        }

        skeleton.classList.add('hidden');
        emptyState.classList.add('hidden');
        container.classList.remove('hidden');

        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#9CA3AF' : '#6B7280';

        // Calculate total and percentages
        const total = data.data.reduce((sum, val) => sum + val, 0);
        const labelsWithPercentages = data.labels.map((label, index) => {
            const percentage = total > 0 ? Math.round((data.data[index] / total) * 100) : 0;
            return `${label} (${percentage}%)`;
        });

        if (orderStatusChartInstance) {
            orderStatusChartInstance.destroy();
        }

        const ctx = canvas.getContext('2d');
        orderStatusChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labelsWithPercentages,
                datasets: [{
                    data: data.data,
                    backgroundColor: data.colors,
                    borderWidth: 2,
                    borderColor: isDark ? '#1F2937' : '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: textColor,
                            padding: 15,
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                        return {
                                            text: `${data.labels[i]} - ${value} orders`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            fontColor: textColor,
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: isDark ? '#1F2937' : '#fff',
                        titleColor: isDark ? '#fff' : '#111',
                        bodyColor: isDark ? '#D1D5DB' : '#666',
                        borderColor: isDark ? '#374151' : '#E5E7EB',
                        borderWidth: 1,
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const label = data.labels[context.dataIndex];
                                const value = context.parsed;
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Update chart theme on dark mode toggle
    function updateChartTheme(chart) {
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#9CA3AF' : '#6B7280';
        const gridColor = isDark ? 'rgba(75, 85, 99, 0.2)' : 'rgba(229, 231, 235, 1)';

        if (chart.options.scales) {
            chart.options.scales.y.ticks.color = textColor;
            chart.options.scales.y.grid.color = gridColor;
            chart.options.scales.x.ticks.color = textColor;
            chart.options.scales.x.grid.color = gridColor;
        }

        if (chart.options.plugins.legend) {
            chart.options.plugins.legend.labels.color = textColor;
        }

        chart.update();
    }

    // Update top products
    function updateTopProducts(data) {
        const table = document.getElementById('topProductsTable');
        const skeleton = document.querySelector('.products-skeleton');
        const content = document.querySelector('.products-content');
        const emptyState = document.querySelector('.products-empty');

        skeleton.classList.add('hidden');

        if (!data.products || data.products.length === 0) {
            content.classList.add('hidden');
            emptyState.classList.remove('hidden');
            return;
        }

        emptyState.classList.add('hidden');
        content.classList.remove('hidden');

        table.innerHTML = data.products.map(product => {
            // Calculate stock percentage
            const stockPercent = product.stock_quantity > 0 ?
                Math.min(100, (product.stock_quantity / 100) * 100) :
                0;

            // Determine stock status
            let stockStatus, stockClass;
            if (product.status === 'out_of_stock' || product.stock_quantity === 0) {
                stockStatus = 'Out of Stock';
                stockClass = 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
            } else if (product.stock_quantity < 10) {
                stockStatus = 'Low Stock';
                stockClass = 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400';
            } else {
                stockStatus = 'In Stock';
                stockClass = 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
            }

            return `
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        ${product.image_url 
                            ? `<img src="${product.image_url}" alt="${product.product_name}" class="w-10 h-10 rounded-lg object-cover flex-shrink-0">` 
                            : `<div class="w-10 h-10 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-gray-400">image</span>
                              </div>`
                        }
                        <div class="font-medium text-gray-900 dark:text-white">${product.product_name}</div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="text-sm text-gray-500 dark:text-gray-400">${product.category || 'Uncategorized'}</span>
                </td>
                <td class="px-6 py-4">
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">₦${product.price.toLocaleString()}</span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2 text-sm">
                        <div class="w-16 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full ${
                                product.stock_quantity < 10 ? 'bg-orange-500' : 'bg-primary'
                            }" style="width: ${stockPercent}%"></div>
                        </div>
                        <span class="${product.stock_quantity < 10 ? 'text-orange-600 dark:text-orange-400 font-medium' : 'text-gray-500 dark:text-gray-400'}">
                            ${product.stock_quantity} left
                        </span>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400 rounded-full text-sm font-semibold">
                        ${product.quantity}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <span class="font-bold text-gray-900 dark:text-white">₦${product.revenue.toLocaleString()}</span>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs font-medium rounded ${stockClass}">
                        ${stockStatus}
                    </span>
                </td>
            </tr>
        `;
        }).join('');
    }

    // Show empty store state
    function showEmptyStoreState() {
        const main = document.querySelector('main > div');
        main.innerHTML = `
            <div class="text-center py-20">
                <span class="material-symbols-outlined text-9xl text-gray-300 dark:text-gray-600">store</span>
                <h2 class="mt-6 text-2xl font-bold text-gray-900 dark:text-white">No stores yet</h2>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Create your first store to start selling online</p>
                <a href="/client/stores.php" class="inline-block mt-6 px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 font-semibold transition-colors">
                    Create Store
                </a>
            </div>
        `;
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        initDarkMode();
        loadUserStores();
    });
</script>