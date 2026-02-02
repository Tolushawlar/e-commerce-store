<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-4">
                    <button onclick="window.history.back()" class="text-gray-600 hover:text-gray-900">
                        <span class="material-symbols-outlined">arrow_back</span>
                    </button>
                    <h1 class="text-xl font-bold text-gray-900">Shopping Cart</h1>
                </div>
                <div class="flex items-center gap-4">
                    <span id="cartCount" class="text-sm text-gray-600">0 items</span>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items (Left Column) -->
            <div class="lg:col-span-2">
                <!-- Loading State -->
                <div id="loadingState" class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                    <p class="mt-4 text-gray-500">Loading cart...</p>
                </div>

                <!-- Empty Cart State -->
                <div id="emptyState" class="hidden bg-white rounded-xl shadow-sm p-12 text-center">
                    <span class="material-symbols-outlined text-gray-300 text-6xl mb-4">shopping_cart</span>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Your cart is empty</h2>
                    <p class="text-gray-500 mb-6">Looks like you haven't added anything to your cart yet</p>
                    <a href="/" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <span class="material-symbols-outlined text-sm">arrow_back</span>
                        Continue Shopping
                    </a>
                </div>

                <!-- Cart Items List -->
                <div id="cartItems" class="hidden bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Cart Items</h2>
                    </div>
                    <div id="cartItemsList" class="divide-y divide-gray-200">
                        <!-- Items will be rendered here -->
                    </div>
                    <div class="p-6 bg-gray-50 border-t border-gray-200">
                        <button onclick="clearCart()" class="text-red-600 hover:text-red-700 text-sm font-medium flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">delete</span>
                            Clear Cart
                        </button>
                    </div>
                </div>
            </div>

            <!-- Order Summary (Right Column) -->
            <div class="lg:col-span-1">
                <div id="orderSummary" class="hidden bg-white rounded-xl shadow-sm sticky top-24">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Order Summary</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Subtotal -->
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal (<span id="summaryItemCount">0</span> items)</span>
                            <span id="summarySubtotal" class="font-medium">₦0</span>
                        </div>

                        <!-- Shipping -->
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span id="summaryShipping" class="font-medium">₦0</span>
                        </div>

                        <!-- Shipping Info -->
                        <div id="shippingInfo" class="hidden bg-green-50 border border-green-200 rounded-lg p-3">
                            <div class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-green-600 text-sm">local_shipping</span>
                                <p class="text-xs text-green-700">
                                    <strong>Free shipping</strong> on orders above ₦10,000
                                </p>
                            </div>
                        </div>

                        <!-- Tax -->
                        <div class="hidden flex justify-between text-gray-600">
                            <span>Tax</span>
                            <span id="summaryTax" class="font-medium">₦0</span>
                        </div>

                        <!-- Divider -->
                        <div class="border-t border-gray-200"></div>

                        <!-- Total -->
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total</span>
                            <span id="summaryTotal" class="text-blue-600">₦0</span>
                        </div>

                        <!-- Checkout Button -->
                        <button onclick="proceedToCheckout()" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center justify-center gap-2">
                            <span>Proceed to Checkout</span>
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </button>

                        <!-- Continue Shopping -->
                        <a href="/" class="block w-full text-center border border-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                            Continue Shopping
                        </a>

                        <!-- Trust Badges -->
                        <div class="pt-4 border-t border-gray-200 space-y-2">
                            <div class="flex items-center gap-2 text-xs text-gray-600">
                                <span class="material-symbols-outlined text-green-600 text-sm">verified</span>
                                <span>Secure checkout</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-600">
                                <span class="material-symbols-outlined text-blue-600 text-sm">local_shipping</span>
                                <span>Fast delivery</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-600">
                                <span class="material-symbols-outlined text-purple-600 text-sm">currency_exchange</span>
                                <span>Easy returns</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommended Products -->
        <div id="recommendedSection" class="hidden mt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">You may also like</h2>
            <div id="recommendedProducts" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                <!-- Recommended products will be loaded here -->
            </div>
        </div>
    </main>

    <!-- Notification Toast -->
    <div id="notification" class="hidden fixed top-4 right-4 bg-white rounded-lg shadow-lg p-4 z-50 max-w-sm">
        <div class="flex items-start gap-3">
            <span id="notificationIcon" class="material-symbols-outlined text-2xl"></span>
            <div class="flex-1">
                <p id="notificationMessage" class="text-sm font-medium text-gray-900"></p>
            </div>
            <button onclick="hideNotification()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
    </div>

    <script src="/assets/js/api.js"></script>
    <script src="/assets/js/services/cart.js"></script>
    <script>
        const apiClient = new APIClient();
        const cartService = new CartService(apiClient);

        // Get store ID from URL or config
        const storeId = new URLSearchParams(window.location.search).get('store_id') || 1;
        let cartItems = [];
        let isAuthenticated = false;

        // Initialize
        document.addEventListener('DOMContentLoaded', async () => {
            await loadCart();
        });

        // Load cart
        async function loadCart() {
            try {
                document.getElementById('loadingState').classList.remove('hidden');
                document.getElementById('emptyState').classList.add('hidden');
                document.getElementById('cartItems').classList.add('hidden');

                // Check if user is authenticated
                isAuthenticated = !!localStorage.getItem('auth_token');

                if (isAuthenticated) {
                    // Load from API
                    const response = await cartService.getCart(storeId);
                    if (response.success) {
                        cartItems = response.data.items || [];
                    }
                } else {
                    // Load from localStorage
                    cartItems = cartService.getLocalCart(storeId);
                }

                document.getElementById('loadingState').classList.add('hidden');

                if (cartItems.length === 0) {
                    document.getElementById('emptyState').classList.remove('hidden');
                    document.getElementById('orderSummary').classList.add('hidden');
                } else {
                    renderCart();
                    updateSummary();
                    document.getElementById('cartItems').classList.remove('hidden');
                    document.getElementById('orderSummary').classList.remove('hidden');
                }

                updateCartCount();
            } catch (error) {
                console.error('Error loading cart:', error);
                document.getElementById('loadingState').classList.add('hidden');
                showNotification('Failed to load cart', 'error');
            }
        }

        // Render cart items
        function renderCart() {
            const container = document.getElementById('cartItemsList');
            container.innerHTML = '';

            cartItems.forEach(item => {
                const itemElement = document.createElement('div');
                itemElement.className = 'p-6 hover:bg-gray-50 transition-colors';
                itemElement.innerHTML = `
                    <div class="flex gap-4">
                        <!-- Product Image -->
                        <div class="flex-shrink-0">
                            ${item.image_url ? `
                                <img src="${item.image_url}" alt="${item.product_name}" class="w-24 h-24 object-cover rounded-lg">
                            ` : `
                                <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <span class="material-symbols-outlined text-gray-400 text-3xl">image</span>
                                </div>
                            `}
                        </div>

                        <!-- Product Details -->
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1 truncate">${item.product_name}</h3>
                            <p class="text-sm text-gray-500 mb-2">
                                ${cartService.formatCurrency(item.price)} each
                            </p>
                            ${item.stock_quantity !== undefined ? `
                                <p class="text-xs ${item.stock_quantity > 0 ? 'text-green-600' : 'text-red-600'}">
                                    ${item.stock_quantity > 0 ? `${item.stock_quantity} in stock` : 'Out of stock'}
                                </p>
                            ` : ''}
                        </div>

                        <!-- Quantity & Actions -->
                        <div class="flex flex-col items-end justify-between">
                            <button onclick="removeItem(${item.id || item.product_id})" class="text-gray-400 hover:text-red-600 transition-colors">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                            
                            <div class="flex items-center gap-3">
                                <!-- Quantity Controls -->
                                <div class="flex items-center border border-gray-300 rounded-lg">
                                    <button onclick="updateQuantity(${item.id || item.product_id}, ${item.quantity - 1})" 
                                            class="px-3 py-2 hover:bg-gray-100 transition-colors ${item.quantity <= 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                                            ${item.quantity <= 1 ? 'disabled' : ''}>
                                        <span class="material-symbols-outlined text-sm">remove</span>
                                    </button>
                                    <input type="number" 
                                           value="${item.quantity}" 
                                           min="1"
                                           max="${item.stock_quantity || 999}"
                                           onchange="updateQuantity(${item.id || item.product_id}, parseInt(this.value))"
                                           class="w-16 text-center border-x border-gray-300 py-2 focus:outline-none">
                                    <button onclick="updateQuantity(${item.id || item.product_id}, ${item.quantity + 1})" 
                                            class="px-3 py-2 hover:bg-gray-100 transition-colors"
                                            ${item.stock_quantity && item.quantity >= item.stock_quantity ? 'disabled' : ''}>
                                        <span class="material-symbols-outlined text-sm">add</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Item Total -->
                            <p class="text-lg font-bold text-gray-900 mt-2">
                                ${cartService.formatCurrency(item.price * item.quantity)}
                            </p>
                        </div>
                    </div>
                `;
                container.appendChild(itemElement);
            });
        }

        // Update quantity
        async function updateQuantity(itemId, newQuantity) {
            if (newQuantity < 1) return;

            try {
                if (isAuthenticated) {
                    // Update via API
                    const response = await cartService.updateQuantity(storeId, itemId, newQuantity);
                    if (response.success) {
                        cartItems = response.data.items || [];
                        showNotification('Cart updated', 'success');
                    }
                } else {
                    // Update localStorage
                    const itemIndex = cartItems.findIndex(item => item.product_id === itemId);
                    if (itemIndex >= 0) {
                        const item = cartItems[itemIndex];

                        // Check stock
                        if (item.stock_quantity && newQuantity > item.stock_quantity) {
                            showNotification(`Only ${item.stock_quantity} available`, 'error');
                            return;
                        }

                        cartItems[itemIndex].quantity = newQuantity;
                        cartService.saveLocalCart(storeId, cartItems);
                        showNotification('Cart updated', 'success');
                    }
                }

                renderCart();
                updateSummary();
                updateCartCount();
            } catch (error) {
                console.error('Error updating quantity:', error);
                showNotification(error.message || 'Failed to update quantity', 'error');
            }
        }

        // Remove item
        async function removeItem(itemId) {
            if (!confirm('Remove this item from cart?')) return;

            try {
                if (isAuthenticated) {
                    // Remove via API
                    const response = await cartService.removeItem(storeId, itemId);
                    if (response.success) {
                        cartItems = response.data.items || [];
                        showNotification('Item removed', 'success');
                    }
                } else {
                    // Remove from localStorage
                    cartItems = cartItems.filter(item => item.product_id !== itemId);
                    cartService.saveLocalCart(storeId, cartItems);
                    showNotification('Item removed', 'success');
                }

                if (cartItems.length === 0) {
                    document.getElementById('cartItems').classList.add('hidden');
                    document.getElementById('orderSummary').classList.add('hidden');
                    document.getElementById('emptyState').classList.remove('hidden');
                } else {
                    renderCart();
                    updateSummary();
                }

                updateCartCount();
            } catch (error) {
                console.error('Error removing item:', error);
                showNotification('Failed to remove item', 'error');
            }
        }

        // Clear cart
        async function clearCart() {
            if (!confirm('Clear all items from cart?')) return;

            try {
                if (isAuthenticated) {
                    // Clear via API
                    const response = await cartService.clearCart(storeId);
                    if (response.success) {
                        cartItems = [];
                        showNotification('Cart cleared', 'success');
                    }
                } else {
                    // Clear localStorage
                    cartItems = [];
                    cartService.saveLocalCart(storeId, cartItems);
                    showNotification('Cart cleared', 'success');
                }

                document.getElementById('cartItems').classList.add('hidden');
                document.getElementById('orderSummary').classList.add('hidden');
                document.getElementById('emptyState').classList.remove('hidden');
                updateCartCount();
            } catch (error) {
                console.error('Error clearing cart:', error);
                showNotification('Failed to clear cart', 'error');
            }
        }

        // Update summary
        function updateSummary() {
            const totals = cartService.calculateTotals(cartItems);

            document.getElementById('summaryItemCount').textContent = totals.itemCount;
            document.getElementById('summarySubtotal').textContent = cartService.formatCurrency(totals.subtotal);
            document.getElementById('summaryShipping').textContent = totals.shipping === 0 ? 'FREE' : cartService.formatCurrency(totals.shipping);
            document.getElementById('summaryTotal').textContent = cartService.formatCurrency(totals.total);

            // Show/hide free shipping info
            const shippingInfo = document.getElementById('shippingInfo');
            if (totals.subtotal >= 10000) {
                shippingInfo.classList.remove('hidden');
            } else if (totals.subtotal >= 5000) {
                shippingInfo.classList.remove('hidden');
                shippingInfo.innerHTML = `
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-blue-600 text-sm">info</span>
                        <p class="text-xs text-blue-700">
                            Add <strong>${cartService.formatCurrency(10000 - totals.subtotal)}</strong> more for free shipping
                        </p>
                    </div>
                `;
            } else {
                shippingInfo.classList.add('hidden');
            }
        }

        // Update cart count
        function updateCartCount() {
            const count = cartItems.reduce((sum, item) => sum + parseInt(item.quantity), 0);
            document.getElementById('cartCount').textContent = `${count} ${count === 1 ? 'item' : 'items'}`;
            cartService.updateCartBadge(storeId);
        }

        // Proceed to checkout
        function proceedToCheckout() {
            if (cartItems.length === 0) {
                showNotification('Your cart is empty', 'error');
                return;
            }

            // Check stock availability
            const outOfStock = cartItems.filter(item => {
                return item.stock_quantity !== undefined && item.stock_quantity < item.quantity;
            });

            if (outOfStock.length > 0) {
                showNotification('Some items are out of stock. Please update quantities.', 'error');
                return;
            }

            // Redirect to checkout
            window.location.href = `/store/checkout.php?store_id=${storeId}`;
        }

        // Notification
        function showNotification(message, type = 'info') {
            const notification = document.getElementById('notification');
            const icon = document.getElementById('notificationIcon');
            const messageEl = document.getElementById('notificationMessage');

            const icons = {
                success: 'check_circle',
                error: 'error',
                warning: 'warning',
                info: 'info'
            };

            const colors = {
                success: 'text-green-600',
                error: 'text-red-600',
                warning: 'text-yellow-600',
                info: 'text-blue-600'
            };

            icon.textContent = icons[type] || icons.info;
            icon.className = `material-symbols-outlined text-2xl ${colors[type] || colors.info}`;
            messageEl.textContent = message;

            notification.classList.remove('hidden');

            setTimeout(() => {
                notification.classList.add('hidden');
            }, 5000);
        }

        function hideNotification() {
            document.getElementById('notification').classList.add('hidden');
        }
    </script>
</body>

</html>