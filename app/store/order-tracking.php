<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Order</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 40px;
            bottom: -20px;
            width: 2px;
            background: #e5e7eb;
        }

        .timeline-item:last-child::before {
            display: none;
        }

        .timeline-item.active .timeline-dot {
            background: #2563eb;
            border-color: #2563eb;
        }

        .timeline-item.completed .timeline-dot {
            background: #10b981;
            border-color: #10b981;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-4">
                    <a href="/" class="text-gray-600 hover:text-gray-900">
                        <span class="material-symbols-outlined">home</span>
                    </a>
                    <h1 class="text-xl font-bold text-gray-900">Track Order</h1>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Search Section -->
        <div class="bg-white rounded-xl shadow-sm p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Track Your Order</h2>
            <p class="text-gray-600 mb-6">Enter your order ID or tracking number to get updates</p>

            <div class="flex gap-4">
                <div class="flex-1">
                    <input type="text" id="searchInput"
                        placeholder="Order ID or Tracking Number"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <button onclick="searchOrder()"
                    class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">search</span>
                    <span>Track</span>
                </button>
            </div>
        </div>

        <!-- Loading State -->
        <div id="loadingState" class="hidden bg-white rounded-xl shadow-sm p-12 text-center">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
            <p class="text-gray-600">Loading order details...</p>
        </div>

        <!-- Order Details -->
        <div id="orderDetails" class="hidden">
            <!-- Order Header -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-1">Order <span id="orderIdDisplay">#0000</span></h3>
                        <p class="text-sm text-gray-600">Placed on <span id="orderDate">-</span></p>
                    </div>
                    <div>
                        <span id="orderStatusBadge" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold">
                            <span class="material-symbols-outlined text-sm mr-1" id="statusIcon">info</span>
                            <span id="statusText">Pending</span>
                        </span>
                    </div>
                </div>

                <!-- Order Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-xs text-gray-500 uppercase mb-1">Total Amount</p>
                        <p class="text-lg font-bold text-gray-900" id="orderAmount">₦0</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase mb-1">Payment Status</p>
                        <p class="font-semibold" id="paymentStatus">-</p>
                    </div>
                    <div id="trackingSection" class="hidden">
                        <p class="text-xs text-gray-500 uppercase mb-1">Tracking Number</p>
                        <p class="font-mono font-semibold text-blue-600" id="trackingNumber">-</p>
                    </div>
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Order Progress</h3>
                <div id="orderTimeline" class="space-y-6 relative">
                    <!-- Timeline items will be added here -->
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h3>
                <div id="orderItemsList" class="space-y-4">
                    <!-- Items will be loaded here -->
                </div>
            </div>

            <!-- Shipping & Customer Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Shipping Address -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600">local_shipping</span>
                        Shipping Address
                    </h3>
                    <div id="shippingAddress" class="text-sm text-gray-600 space-y-1">
                        <p>-</p>
                    </div>
                </div>

                <!-- Customer Info -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-purple-600">person</span>
                        Customer Information
                    </h3>
                    <div class="text-sm text-gray-600 space-y-2">
                        <div>
                            <p class="text-xs text-gray-500">Name</p>
                            <p class="font-medium text-gray-900" id="customerName">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Email</p>
                            <p class="font-medium text-gray-900" id="customerEmail">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Phone</p>
                            <p class="font-medium text-gray-900" id="customerPhone">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Support -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                <p class="text-sm text-blue-900 mb-3">
                    <strong>Need help with your order?</strong>
                </p>
                <p class="text-sm text-blue-700">
                    Contact our support team at
                    <a href="mailto:support@store.com" class="font-medium underline">support@store.com</a>
                    or call <a href="tel:+2348000000000" class="font-medium underline">+234 800 000 0000</a>
                </p>
            </div>
        </div>

        <!-- Not Found State -->
        <div id="notFoundState" class="hidden bg-white rounded-xl shadow-sm p-12 text-center">
            <span class="material-symbols-outlined text-gray-300 text-6xl mb-4">search_off</span>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Order Not Found</h3>
            <p class="text-gray-600 mb-6">We couldn't find an order with that ID or tracking number</p>
            <button onclick="resetSearch()" class="text-blue-600 hover:text-blue-700 font-medium">
                Try Again
            </button>
        </div>
    </main>

    <script src="/assets/js/core/api.js"></script>
    <script src="/assets/js/services/cart.service.js"></script>
    <script src="/assets/js/services/checkout.service.js"></script>
    <script>
        const apiClient = new APIClient();
        const cartService = new CartService(apiClient);
        const checkoutService = new CheckoutService(apiClient);

        const urlParams = new URLSearchParams(window.location.search);
        const orderId = urlParams.get('order_id');
        const storeId = urlParams.get('store_id') || 1;

        // Initialize
        document.addEventListener('DOMContentLoaded', async () => {
            if (orderId) {
                document.getElementById('searchInput').value = orderId;
                await searchOrder();
            }

            // Enter key to search
            document.getElementById('searchInput').addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    searchOrder();
                }
            });
        });

        // Search order
        async function searchOrder() {
            const searchValue = document.getElementById('searchInput').value.trim();

            if (!searchValue) {
                alert('Please enter an order ID or tracking number');
                return;
            }

            hideAllStates();
            document.getElementById('loadingState').classList.remove('hidden');

            try {
                const response = await checkoutService.trackOrder(storeId, searchValue);

                if (response.success && response.data) {
                    renderOrderDetails(response.data);
                    document.getElementById('loadingState').classList.add('hidden');
                    document.getElementById('orderDetails').classList.remove('hidden');
                } else {
                    showNotFound();
                }
            } catch (error) {
                console.error('Error tracking order:', error);
                showNotFound();
            }
        }

        // Render order details
        function renderOrderDetails(order) {
            // Order ID
            document.getElementById('orderIdDisplay').textContent = `#${order.id}`;

            // Order date
            document.getElementById('orderDate').textContent = checkoutService.formatDate(order.created_at);

            // Status badge
            const statusBadge = document.getElementById('orderStatusBadge');
            statusBadge.className = `inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold ${checkoutService.getStatusBadgeClass(order.status)}`;
            document.getElementById('statusIcon').textContent = checkoutService.getStatusIcon(order.status);
            document.getElementById('statusText').textContent = order.status.charAt(0).toUpperCase() + order.status.slice(1);

            // Order amount
            document.getElementById('orderAmount').textContent = cartService.formatCurrency(order.total_amount);

            // Payment status
            document.getElementById('paymentStatus').textContent = order.payment_status.charAt(0).toUpperCase() + order.payment_status.slice(1);

            // Tracking number
            if (order.tracking_number) {
                document.getElementById('trackingSection').classList.remove('hidden');
                document.getElementById('trackingNumber').textContent = order.tracking_number;
            }

            // Timeline
            renderTimeline(order.status, order.created_at, order.updated_at);

            // Order items
            renderOrderItems(order.items || []);

            // Shipping address
            renderShippingAddress(order.shipping_address);

            // Customer info
            document.getElementById('customerName').textContent = order.customer_name || '-';
            document.getElementById('customerEmail').textContent = order.customer_email || '-';
            document.getElementById('customerPhone').textContent = order.customer_phone || '-';
        }

        // Render timeline
        function renderTimeline(currentStatus, createdAt, updatedAt) {
            const timeline = document.getElementById('orderTimeline');
            timeline.innerHTML = '';

            const statuses = [{
                    status: 'pending',
                    label: 'Order Placed',
                    icon: 'shopping_cart'
                },
                {
                    status: 'processing',
                    label: 'Processing',
                    icon: 'settings'
                },
                {
                    status: 'shipped',
                    label: 'Shipped',
                    icon: 'local_shipping'
                },
                {
                    status: 'delivered',
                    label: 'Delivered',
                    icon: 'check_circle'
                }
            ];

            const statusIndex = statuses.findIndex(s => s.status === currentStatus);

            statuses.forEach((item, index) => {
                const isCompleted = index < statusIndex;
                const isActive = index === statusIndex;
                const isCancelled = currentStatus === 'cancelled';

                const div = document.createElement('div');
                div.className = `timeline-item relative pl-16 ${isActive ? 'active' : ''} ${isCompleted ? 'completed' : ''}`;

                let statusClass = 'bg-gray-200 border-gray-300';
                if (isCompleted) statusClass = 'bg-green-500 border-green-500';
                if (isActive) statusClass = 'bg-blue-500 border-blue-500';

                div.innerHTML = `
                    <div class="timeline-dot absolute left-0 top-0 w-10 h-10 rounded-full border-4 ${statusClass} flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-sm">${item.icon}</span>
                    </div>
                    <div class="pb-8">
                        <h4 class="font-semibold text-gray-900 mb-1">${item.label}</h4>
                        ${isActive ? `<p class="text-sm text-gray-600">${checkoutService.formatDate(updatedAt || createdAt)}</p>` : ''}
                        ${isCompleted ? `<p class="text-sm text-green-600 flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">check</span>
                            Completed
                        </p>` : ''}
                    </div>
                `;
                timeline.appendChild(div);
            });

            // Add cancelled status if applicable
            if (currentStatus === 'cancelled') {
                const div = document.createElement('div');
                div.className = 'timeline-item relative pl-16';
                div.innerHTML = `
                    <div class="timeline-dot absolute left-0 top-0 w-10 h-10 rounded-full border-4 bg-red-500 border-red-500 flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-sm">cancel</span>
                    </div>
                    <div class="pb-8">
                        <h4 class="font-semibold text-gray-900 mb-1">Order Cancelled</h4>
                        <p class="text-sm text-gray-600">${checkoutService.formatDate(updatedAt)}</p>
                    </div>
                `;
                timeline.appendChild(div);
            }
        }

        // Render order items
        function renderOrderItems(items) {
            const container = document.getElementById('orderItemsList');
            container.innerHTML = '';

            if (items.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-sm">No items</p>';
                return;
            }

            items.forEach(item => {
                const div = document.createElement('div');
                div.className = 'flex gap-4 p-4 bg-gray-50 rounded-lg';
                div.innerHTML = `
                    <div class="flex-shrink-0">
                        ${item.product_image ? `
                            <img src="${item.product_image}" alt="${item.product_name}" class="w-20 h-20 object-cover rounded-lg">
                        ` : `
                            <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                <span class="material-symbols-outlined text-gray-400">image</span>
                            </div>
                        `}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-gray-900 mb-1">${item.product_name || 'Product'}</h4>
                        <p class="text-sm text-gray-600">Quantity: ${item.quantity}</p>
                        <p class="text-sm text-gray-500">${cartService.formatCurrency(item.price)} each</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-900">${cartService.formatCurrency(item.price * item.quantity)}</p>
                    </div>
                `;
                container.appendChild(div);
            });
        }

        // Render shipping address
        function renderShippingAddress(address) {
            const container = document.getElementById('shippingAddress');

            if (!address) {
                container.innerHTML = '<p>-</p>';
                return;
            }

            container.innerHTML = `
                <p class="font-medium text-gray-900">${address.address_line1}</p>
                ${address.address_line2 ? `<p>${address.address_line2}</p>` : ''}
                <p>${address.city}, ${address.state} ${address.postal_code}</p>
                <p>${address.country}</p>
            `;
        }

        // Show not found state
        function showNotFound() {
            hideAllStates();
            document.getElementById('notFoundState').classList.remove('hidden');
        }

        // Reset search
        function resetSearch() {
            hideAllStates();
            document.getElementById('searchInput').value = '';
            document.getElementById('searchInput').focus();
        }

        // Hide all states
        function hideAllStates() {
            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('orderDetails').classList.add('hidden');
            document.getElementById('notFoundState').classList.add('hidden');
        }
    </script>
</body>

</html>