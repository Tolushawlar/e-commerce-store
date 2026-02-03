<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        @keyframes checkmark {
            0% {
                stroke-dashoffset: 100;
            }

            100% {
                stroke-dashoffset: 0;
            }
        }

        .checkmark-circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            animation: checkmark 0.6s ease-in-out 0.3s forwards;
        }

        .checkmark-check {
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            animation: checkmark 0.3s ease-in-out 0.9s forwards;
        }
    </style>
</head>

<body class="bg-gray-50">
    <main class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-2xl w-full">
            <!-- Loading State -->
            <div id="loadingState" class="bg-white rounded-xl shadow-lg p-12 text-center">
                <div class="inline-block animate-spin rounded-full h-16 w-16 border-b-4 border-green-600 mb-4"></div>
                <p class="text-gray-600">Loading order details...</p>
            </div>

            <!-- Success State -->
            <div id="successState" class="hidden bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Success Header -->
                <div class="bg-gradient-to-r from-green-500 to-green-600 p-12 text-center">
                    <!-- Animated Checkmark -->
                    <div class="mb-6">
                        <svg class="inline-block" width="120" height="120" viewBox="0 0 52 52">
                            <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none" stroke="white" stroke-width="2" />
                            <path class="checkmark-check" fill="none" stroke="white" stroke-width="2" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-white mb-2">Order Confirmed!</h1>
                    <p class="text-green-100">Thank you for your purchase</p>
                </div>

                <!-- Order Details -->
                <div class="p-8">
                    <!-- Order Number -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-6 text-center">
                        <p class="text-sm text-gray-600 mb-1">Order Number</p>
                        <p class="text-3xl font-bold text-gray-900" id="orderNumber">#0000</p>
                    </div>

                    <!-- Order Info Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Email Confirmation -->
                        <div class="flex items-start gap-3">
                            <div class="bg-blue-100 rounded-full p-3">
                                <span class="material-symbols-outlined text-blue-600">email</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Confirmation Email</p>
                                <p class="text-sm text-gray-600" id="customerEmail">email@example.com</p>
                            </div>
                        </div>

                        <!-- Order Total -->
                        <div class="flex items-start gap-3">
                            <div class="bg-green-100 rounded-full p-3">
                                <span class="material-symbols-outlined text-green-600">payments</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Order Total</p>
                                <p class="text-sm text-gray-600" id="orderTotal">₦0</p>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="flex items-start gap-3">
                            <div class="bg-purple-100 rounded-full p-3">
                                <span class="material-symbols-outlined text-purple-600">credit_card</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Payment Method</p>
                                <p class="text-sm text-gray-600 capitalize" id="paymentMethod">-</p>
                            </div>
                        </div>

                        <!-- Delivery Address -->
                        <div class="flex items-start gap-3">
                            <div class="bg-yellow-100 rounded-full p-3">
                                <span class="material-symbols-outlined text-yellow-600">location_on</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Delivery To</p>
                                <p class="text-sm text-gray-600" id="deliveryAddress">-</p>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="border-t border-gray-200 pt-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h3>
                        <div id="orderItemsList" class="space-y-4">
                            <!-- Items will be loaded here -->
                        </div>
                    </div>

                    <!-- What's Next -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                        <h3 class="font-semibold text-blue-900 mb-3 flex items-center gap-2">
                            <span class="material-symbols-outlined">info</span>
                            What happens next?
                        </h3>
                        <ol class="space-y-2 text-sm text-blue-800">
                            <li class="flex items-start gap-2">
                                <span class="font-bold">1.</span>
                                <span>You'll receive an order confirmation email shortly</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="font-bold">2.</span>
                                <span>We'll notify you when your order is being prepared</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="font-bold">3.</span>
                                <span>Track your order status using the tracking link below</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="font-bold">4.</span>
                                <span>Your order will be delivered to your address</span>
                            </li>
                        </ol>
                    </div>

                    <!-- Action Buttons -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a id="trackOrderBtn" href="#" class="flex items-center justify-center gap-2 bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            <span class="material-symbols-outlined text-sm">location_searching</span>
                            <span>Track Order</span>
                        </a>
                        <a href="/" class="flex items-center justify-center gap-2 border border-gray-300 text-gray-700 py-3 px-6 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                            <span class="material-symbols-outlined text-sm">storefront</span>
                            <span>Continue Shopping</span>
                        </a>
                    </div>

                    <!-- Support -->
                    <div class="mt-6 text-center text-sm text-gray-600">
                        <p>Need help? <a href="/contact" class="text-blue-600 hover:text-blue-700 font-medium">Contact Support</a></p>
                    </div>
                </div>

                <!-- Social Share (Optional) -->
                <div class="hidden bg-gray-50 border-t border-gray-200 p-6 text-center">
                    <p class="text-sm text-gray-600 mb-3">Share your purchase</p>
                    <div class="flex justify-center gap-3">
                        <button class="w-10 h-10 rounded-full bg-blue-600 text-white hover:bg-blue-700 transition-colors flex items-center justify-center">
                            <span class="material-symbols-outlined text-sm">share</span>
                        </button>
                        <button class="w-10 h-10 rounded-full bg-green-600 text-white hover:bg-green-700 transition-colors flex items-center justify-center">
                            <span class="material-symbols-outlined text-sm">chat</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Error State -->
            <div id="errorState" class="hidden bg-white rounded-xl shadow-lg p-12 text-center">
                <span class="material-symbols-outlined text-red-500 text-6xl mb-4">error</span>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Order Not Found</h2>
                <p class="text-gray-600 mb-6">We couldn't find the order you're looking for</p>
                <a href="/" class="inline-flex items-center gap-2 bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    <span class="material-symbols-outlined text-sm">home</span>
                    <span>Go to Home</span>
                </a>
            </div>
        </div>
    </main>

    <script src="/assets/js/api.js"></script>
    <script src="/assets/js/services/cart.js"></script>
    <script src="/assets/js/services/checkout.js"></script>
    <script>
        const apiClient = new APIClient();
        const cartService = new CartService(apiClient);
        const checkoutService = new CheckoutService(apiClient);

        const urlParams = new URLSearchParams(window.location.search);
        const orderId = urlParams.get('order_id');
        const storeId = urlParams.get('store_id') || 1;

        // Initialize
        document.addEventListener('DOMContentLoaded', async () => {
            if (!orderId) {
                showError();
                return;
            }

            await loadOrderDetails();
        });

        // Load order details
        async function loadOrderDetails() {
            try {
                const response = await checkoutService.getOrder(storeId, orderId);

                if (response.success && response.data) {
                    renderOrderDetails(response.data);
                    document.getElementById('loadingState').classList.add('hidden');
                    document.getElementById('successState').classList.remove('hidden');

                    // Update cart badge
                    cartService.updateCartBadge(storeId);
                } else {
                    showError();
                }
            } catch (error) {
                console.error('Error loading order:', error);
                showError();
            }
        }

        // Render order details
        function renderOrderDetails(order) {
            // Order number
            document.getElementById('orderNumber').textContent = `#${order.id}`;

            // Customer email
            document.getElementById('customerEmail').textContent = order.customer_email || '-';

            // Order total
            document.getElementById('orderTotal').textContent = cartService.formatCurrency(order.total_amount);

            // Payment method
            document.getElementById('paymentMethod').textContent = checkoutService.getPaymentMethodName(order.payment_method);

            // Delivery address
            if (order.shipping_address) {
                const addr = order.shipping_address;
                const addressText = `${addr.city}, ${addr.state}`;
                document.getElementById('deliveryAddress').textContent = addressText;
            }

            // Track order button
            document.getElementById('trackOrderBtn').href = `/store/order-tracking.php?order_id=${order.id}&store_id=${storeId}`;

            // Order items
            renderOrderItems(order.items || []);
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
                div.className = 'flex gap-4 items-center';
                div.innerHTML = `
                    <div class="flex-shrink-0">
                        ${item.product_image ? `
                            <img src="${item.product_image}" alt="${item.product_name}" class="w-16 h-16 object-cover rounded-lg">
                        ` : `
                            <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                <span class="material-symbols-outlined text-gray-400">image</span>
                            </div>
                        `}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-medium text-gray-900">${item.product_name || 'Product'}</h4>
                        <p class="text-sm text-gray-600">Quantity: ${item.quantity}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-medium text-gray-900">${cartService.formatCurrency(item.price * item.quantity)}</p>
                        <p class="text-xs text-gray-500">${cartService.formatCurrency(item.price)} each</p>
                    </div>
                `;
                container.appendChild(div);
            });
        }

        // Show error state
        function showError() {
            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('successState').classList.add('hidden');
            document.getElementById('errorState').classList.remove('hidden');
        }
    </script>
</body>

</html>