<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .step-active {
            @apply bg-blue-600 text-white;
        }

        .step-completed {
            @apply bg-green-600 text-white;
        }

        .step-inactive {
            @apply bg-gray-200 text-gray-600;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-4">
                    <a href="/store/cart.php" class="text-gray-600 hover:text-gray-900">
                        <span class="material-symbols-outlined">arrow_back</span>
                    </a>
                    <h1 class="text-xl font-bold text-gray-900">Checkout</h1>
                </div>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-green-600 text-sm">lock</span>
                    <span class="text-sm text-gray-600">Secure Checkout</span>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Progress Steps -->
        <div class="mb-8">
            <div class="flex items-center justify-center">
                <div class="flex items-center gap-4">
                    <!-- Step 1 -->
                    <div class="flex items-center gap-2">
                        <div id="step1Indicator" class="w-10 h-10 rounded-full flex items-center justify-center font-semibold step-active">
                            1
                        </div>
                        <span class="text-sm font-medium text-gray-700">Contact</span>
                    </div>
                    <div class="w-16 h-0.5 bg-gray-300"></div>

                    <!-- Step 2 -->
                    <div class="flex items-center gap-2">
                        <div id="step2Indicator" class="w-10 h-10 rounded-full flex items-center justify-center font-semibold step-inactive">
                            2
                        </div>
                        <span class="text-sm font-medium text-gray-700">Shipping</span>
                    </div>
                    <div class="w-16 h-0.5 bg-gray-300"></div>

                    <!-- Step 3 -->
                    <div class="flex items-center gap-2">
                        <div id="step3Indicator" class="w-10 h-10 rounded-full flex items-center justify-center font-semibold step-inactive">
                            3
                        </div>
                        <span class="text-sm font-medium text-gray-700">Payment</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Checkout Form (Left Column) -->
            <div class="lg:col-span-2">
                <form id="checkoutForm" class="space-y-6">
                    <!-- Step 1: Contact Information -->
                    <div id="step1" class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Contact Information</h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="customerName" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="John Doe">
                                <p class="text-red-500 text-xs mt-1 hidden" id="errorCustomerName"></p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Email Address <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" id="customerEmail" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="john@example.com">
                                    <p class="text-red-500 text-xs mt-1 hidden" id="errorCustomerEmail"></p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Phone Number <span class="text-red-500">*</span>
                                    </label>
                                    <input type="tel" id="customerPhone" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="+234 800 000 0000">
                                    <p class="text-red-500 text-xs mt-1 hidden" id="errorCustomerPhone"></p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="button" onclick="nextStep(2)" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                Continue to Shipping
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Shipping Address -->
                    <div id="step2" class="hidden bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Shipping Address</h2>

                        <!-- Saved Addresses (for authenticated users) -->
                        <div id="savedAddresses" class="hidden mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Select Saved Address</label>
                            <div id="addressList" class="space-y-2 mb-4">
                                <!-- Saved addresses will be loaded here -->
                            </div>
                            <button type="button" onclick="showNewAddressForm()" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                + Add New Address
                            </button>
                        </div>

                        <!-- New Address Form -->
                        <div id="newAddressForm" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Street Address <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="addressLine1" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="123 Main Street">
                                <p class="text-red-500 text-xs mt-1 hidden" id="errorAddressLine1"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Apartment, suite, etc. (optional)
                                </label>
                                <input type="text" id="addressLine2"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Apartment 4B">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        City <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="city" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Lagos">
                                    <p class="text-red-500 text-xs mt-1 hidden" id="errorCity"></p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        State <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="state" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Lagos">
                                    <p class="text-red-500 text-xs mt-1 hidden" id="errorState"></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Country <span class="text-red-500">*</span>
                                    </label>
                                    <select id="country" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="Nigeria">Nigeria</option>
                                        <option value="Ghana">Ghana</option>
                                        <option value="Kenya">Kenya</option>
                                        <option value="South Africa">South Africa</option>
                                    </select>
                                    <p class="text-red-500 text-xs mt-1 hidden" id="errorCountry"></p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Postal Code <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="postalCode" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="100001">
                                    <p class="text-red-500 text-xs mt-1 hidden" id="errorPostalCode"></p>
                                </div>
                            </div>

                            <div class="hidden">
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" id="saveAddress" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-gray-700">Save this address for future orders</span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-6 flex gap-3">
                            <button type="button" onclick="prevStep(1)" class="flex-1 border border-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                                Back
                            </button>
                            <button type="button" onclick="nextStep(3)" class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                Continue to Payment
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Payment & Review -->
                    <div id="step3" class="hidden bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Payment Method</h2>

                        <div class="space-y-4">
                            <!-- Payment Methods -->
                            <div class="space-y-3">
                                <label class="flex items-start gap-3 p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-colors">
                                    <input type="radio" name="paymentMethod" value="card" checked
                                        class="mt-1 text-blue-600 focus:ring-blue-500">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-blue-600">credit_card</span>
                                            <span class="font-medium text-gray-900">Credit/Debit Card</span>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">Pay securely with your card</p>
                                    </div>
                                </label>

                                <label class="flex items-start gap-3 p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-colors">
                                    <input type="radio" name="paymentMethod" value="bank_transfer"
                                        class="mt-1 text-blue-600 focus:ring-blue-500">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-green-600">account_balance</span>
                                            <span class="font-medium text-gray-900">Bank Transfer</span>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">Direct bank transfer</p>
                                    </div>
                                </label>

                                <label class="flex items-start gap-3 p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-colors">
                                    <input type="radio" name="paymentMethod" value="cash_on_delivery"
                                        class="mt-1 text-blue-600 focus:ring-blue-500">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-yellow-600">payments</span>
                                            <span class="font-medium text-gray-900">Cash on Delivery</span>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">Pay when you receive your order</p>
                                    </div>
                                </label>
                            </div>

                            <!-- Order Notes -->
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Order Notes (optional)
                                </label>
                                <textarea id="orderNotes" rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Any special instructions for your order..."></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex gap-3">
                            <button type="button" onclick="prevStep(2)" class="flex-1 border border-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                                Back
                            </button>
                            <button type="submit" id="placeOrderBtn" class="flex-1 bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition-colors font-medium flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-sm">shopping_bag</span>
                                <span>Place Order</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Order Summary (Right Column) -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm sticky top-24">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Order Summary</h2>
                    </div>

                    <!-- Cart Items -->
                    <div id="orderItems" class="p-6 max-h-96 overflow-y-auto border-b border-gray-200">
                        <!-- Items will be loaded here -->
                    </div>

                    <!-- Totals -->
                    <div class="p-6 space-y-3">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span id="checkoutSubtotal" class="font-medium">₦0</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span id="checkoutShipping" class="font-medium">₦0</span>
                        </div>
                        <div class="border-t border-gray-200 pt-3 flex justify-between text-lg font-bold">
                            <span>Total</span>
                            <span id="checkoutTotal" class="text-blue-600">₦0</span>
                        </div>
                    </div>

                    <!-- Security Badges -->
                    <div class="p-6 bg-gray-50 border-t border-gray-200 space-y-2">
                        <div class="flex items-center gap-2 text-xs text-gray-600">
                            <span class="material-symbols-outlined text-green-600 text-sm">verified_user</span>
                            <span>SSL Encrypted Checkout</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-600">
                            <span class="material-symbols-outlined text-blue-600 text-sm">shield</span>
                            <span>Your information is protected</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Processing Modal -->
    <div id="processingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-md w-full p-8 text-center">
            <div class="inline-block animate-spin rounded-full h-16 w-16 border-b-4 border-blue-600 mb-4"></div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Processing Your Order</h3>
            <p class="text-gray-600">Please wait while we process your order...</p>
        </div>
    </div>

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
    <script src="/assets/js/services/checkout.js"></script>
    <script>
        const apiClient = new APIClient();
        const cartService = new CartService(apiClient);
        const checkoutService = new CheckoutService(apiClient);

        const storeId = new URLSearchParams(window.location.search).get('store_id') || 1;
        let cartItems = [];
        let isAuthenticated = false;
        let currentStep = 1;
        let savedAddresses = [];

        // Initialize
        document.addEventListener('DOMContentLoaded', async () => {
            isAuthenticated = !!localStorage.getItem('auth_token');
            await loadCheckoutData();
            setupFormValidation();
        });

        // Load checkout data
        async function loadCheckoutData() {
            try {
                // Load cart
                if (isAuthenticated) {
                    const cartResponse = await cartService.getCart(storeId);
                    if (cartResponse.success) {
                        cartItems = cartResponse.data.items || [];
                    }

                    // Load saved addresses
                    const addressResponse = await checkoutService.getAddresses();
                    if (addressResponse.success) {
                        savedAddresses = addressResponse.data.addresses || [];
                        if (savedAddresses.length > 0) {
                            document.getElementById('savedAddresses').classList.remove('hidden');
                            renderSavedAddresses();
                        }
                    }
                } else {
                    cartItems = cartService.getLocalCart(storeId);
                }

                if (cartItems.length === 0) {
                    window.location.href = '/store/cart.php';
                    return;
                }

                renderOrderSummary();
                loadSavedProgress();
            } catch (error) {
                console.error('Error loading checkout data:', error);
                showNotification('Failed to load checkout data', 'error');
            }
        }

        // Render saved addresses
        function renderSavedAddresses() {
            const container = document.getElementById('addressList');
            container.innerHTML = '';

            savedAddresses.forEach(address => {
                const div = document.createElement('label');
                div.className = 'flex items-start gap-3 p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-colors';
                div.innerHTML = `
                    <input type="radio" name="savedAddress" value="${address.id}" 
                           ${address.is_default ? 'checked' : ''}
                           onchange="selectSavedAddress(${address.id})"
                           class="mt-1 text-blue-600 focus:ring-blue-500">
                    <div class="flex-1 text-sm">
                        <div class="font-medium text-gray-900">
                            ${address.address_line1}${address.address_line2 ? ', ' + address.address_line2 : ''}
                        </div>
                        <div class="text-gray-600">
                            ${address.city}, ${address.state} ${address.postal_code}
                        </div>
                        <div class="text-gray-600">${address.country}</div>
                        ${address.is_default ? '<span class="inline-block mt-1 px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded">Default</span>' : ''}
                    </div>
                `;
                container.appendChild(div);
            });
        }

        // Select saved address
        function selectSavedAddress(addressId) {
            document.getElementById('newAddressForm').classList.add('hidden');
        }

        // Show new address form
        function showNewAddressForm() {
            document.querySelectorAll('input[name="savedAddress"]').forEach(radio => radio.checked = false);
            document.getElementById('newAddressForm').classList.remove('hidden');
        }

        // Render order summary
        function renderOrderSummary() {
            const container = document.getElementById('orderItems');
            container.innerHTML = '';

            cartItems.forEach(item => {
                const div = document.createElement('div');
                div.className = 'flex gap-3 mb-4 last:mb-0';
                div.innerHTML = `
                    <div class="relative flex-shrink-0">
                        ${item.image_url ? `
                            <img src="${item.image_url}" alt="${item.product_name}" class="w-16 h-16 object-cover rounded-lg">
                        ` : `
                            <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                <span class="material-symbols-outlined text-gray-400">image</span>
                            </div>
                        `}
                        <span class="absolute -top-2 -right-2 bg-gray-900 text-white text-xs w-6 h-6 rounded-full flex items-center justify-center">
                            ${item.quantity}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-medium text-gray-900 truncate">${item.product_name}</h4>
                        <p class="text-xs text-gray-500">Qty: ${item.quantity}</p>
                    </div>
                    <div class="text-sm font-medium text-gray-900">
                        ${cartService.formatCurrency(item.price * item.quantity)}
                    </div>
                `;
                container.appendChild(div);
            });

            updateCheckoutTotals();
        }

        // Update totals
        function updateCheckoutTotals() {
            const totals = cartService.calculateTotals(cartItems);

            document.getElementById('checkoutSubtotal').textContent = cartService.formatCurrency(totals.subtotal);
            document.getElementById('checkoutShipping').textContent = totals.shipping === 0 ? 'FREE' : cartService.formatCurrency(totals.shipping);
            document.getElementById('checkoutTotal').textContent = cartService.formatCurrency(totals.total);
        }

        // Step navigation
        function nextStep(step) {
            if (!validateCurrentStep()) {
                return;
            }

            // Hide current step
            document.getElementById(`step${currentStep}`).classList.add('hidden');
            document.getElementById(`step${currentStep}Indicator`).classList.remove('step-active');
            document.getElementById(`step${currentStep}Indicator`).classList.add('step-completed');

            // Show next step
            currentStep = step;
            document.getElementById(`step${step}`).classList.remove('hidden');
            document.getElementById(`step${step}Indicator`).classList.add('step-active');

            // Save progress
            saveProgress();

            // Scroll to top
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function prevStep(step) {
            // Hide current step
            document.getElementById(`step${currentStep}`).classList.add('hidden');
            document.getElementById(`step${currentStep}Indicator`).classList.remove('step-active');
            document.getElementById(`step${currentStep}Indicator`).classList.add('step-inactive');

            // Show previous step
            currentStep = step;
            document.getElementById(`step${step}`).classList.remove('hidden');
            document.getElementById(`step${step}Indicator`).classList.remove('step-completed');
            document.getElementById(`step${step}Indicator`).classList.add('step-active');

            // Scroll to top
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Validate current step
        function validateCurrentStep() {
            clearErrors();

            if (currentStep === 1) {
                return validateContactInfo();
            } else if (currentStep === 2) {
                return validateShippingAddress();
            }
            return true;
        }

        // Validate contact info
        function validateContactInfo() {
            let isValid = true;
            const name = document.getElementById('customerName').value.trim();
            const email = document.getElementById('customerEmail').value.trim();
            const phone = document.getElementById('customerPhone').value.trim();

            if (!name) {
                showError('errorCustomerName', 'Full name is required');
                isValid = false;
            }

            if (!email) {
                showError('errorCustomerEmail', 'Email is required');
                isValid = false;
            } else if (!checkoutService.isValidEmail(email)) {
                showError('errorCustomerEmail', 'Invalid email format');
                isValid = false;
            }

            if (!phone) {
                showError('errorCustomerPhone', 'Phone number is required');
                isValid = false;
            }

            return isValid;
        }

        // Validate shipping address
        function validateShippingAddress() {
            // Check if saved address is selected
            const savedAddressSelected = document.querySelector('input[name="savedAddress"]:checked');
            if (savedAddressSelected) {
                return true;
            }

            // Validate new address form
            let isValid = true;
            const fields = ['addressLine1', 'city', 'state', 'country', 'postalCode'];

            fields.forEach(field => {
                const value = document.getElementById(field).value.trim();
                if (!value) {
                    showError(`error${field.charAt(0).toUpperCase() + field.slice(1)}`, 'This field is required');
                    isValid = false;
                }
            });

            return isValid;
        }

        // Form submission
        document.getElementById('checkoutForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            await placeOrder();
        });

        // Place order
        async function placeOrder() {
            if (!validateCurrentStep()) {
                return;
            }

            try {
                document.getElementById('processingModal').classList.remove('hidden');
                document.getElementById('placeOrderBtn').disabled = true;

                // Prepare order data
                const orderData = {
                    customer_name: document.getElementById('customerName').value.trim(),
                    customer_email: document.getElementById('customerEmail').value.trim(),
                    customer_phone: document.getElementById('customerPhone').value.trim(),
                    payment_method: document.querySelector('input[name="paymentMethod"]:checked').value,
                    order_notes: document.getElementById('orderNotes').value.trim(),
                    items: cartItems.map(item => ({
                        product_id: item.product_id,
                        quantity: item.quantity,
                        price: item.price
                    }))
                };

                // Add shipping address
                const savedAddressId = document.querySelector('input[name="savedAddress"]:checked')?.value;
                if (savedAddressId) {
                    orderData.shipping_address_id = parseInt(savedAddressId);
                } else {
                    orderData.shipping_address = {
                        address_line1: document.getElementById('addressLine1').value.trim(),
                        address_line2: document.getElementById('addressLine2').value.trim(),
                        city: document.getElementById('city').value.trim(),
                        state: document.getElementById('state').value.trim(),
                        country: document.getElementById('country').value,
                        postal_code: document.getElementById('postalCode').value.trim()
                    };
                }

                // Place order
                const response = await checkoutService.placeOrder(storeId, orderData);

                if (response.success) {
                    // Clear cart
                    if (isAuthenticated) {
                        await cartService.clearCart(storeId);
                    } else {
                        cartService.saveLocalCart(storeId, []);
                    }

                    // Clear checkout progress
                    checkoutService.clearCheckoutProgress(storeId);

                    // Redirect to success page
                    window.location.href = `/store/order-success.php?order_id=${response.data.order_id}`;
                }
            } catch (error) {
                console.error('Error placing order:', error);
                document.getElementById('processingModal').classList.add('hidden');
                document.getElementById('placeOrderBtn').disabled = false;
                showNotification(error.message || 'Failed to place order. Please try again.', 'error');
            }
        }

        // Save progress
        function saveProgress() {
            const progress = {
                step: currentStep,
                customerName: document.getElementById('customerName').value,
                customerEmail: document.getElementById('customerEmail').value,
                customerPhone: document.getElementById('customerPhone').value,
                addressLine1: document.getElementById('addressLine1').value,
                addressLine2: document.getElementById('addressLine2').value,
                city: document.getElementById('city').value,
                state: document.getElementById('state').value,
                country: document.getElementById('country').value,
                postalCode: document.getElementById('postalCode').value
            };
            checkoutService.saveCheckoutProgress(storeId, progress);
        }

        // Load saved progress
        function loadSavedProgress() {
            const progress = checkoutService.getCheckoutProgress(storeId);
            if (progress) {
                document.getElementById('customerName').value = progress.customerName || '';
                document.getElementById('customerEmail').value = progress.customerEmail || '';
                document.getElementById('customerPhone').value = progress.customerPhone || '';
                document.getElementById('addressLine1').value = progress.addressLine1 || '';
                document.getElementById('addressLine2').value = progress.addressLine2 || '';
                document.getElementById('city').value = progress.city || '';
                document.getElementById('state').value = progress.state || '';
                document.getElementById('country').value = progress.country || 'Nigeria';
                document.getElementById('postalCode').value = progress.postalCode || '';
            }
        }

        // Setup form validation
        function setupFormValidation() {
            // Auto-save on input
            ['customerName', 'customerEmail', 'customerPhone', 'addressLine1', 'addressLine2', 'city', 'state', 'country', 'postalCode'].forEach(field => {
                document.getElementById(field)?.addEventListener('input', () => {
                    saveProgress();
                });
            });
        }

        // Error handling
        function showError(elementId, message) {
            const errorEl = document.getElementById(elementId);
            if (errorEl) {
                errorEl.textContent = message;
                errorEl.classList.remove('hidden');
            }
        }

        function clearErrors() {
            document.querySelectorAll('[id^="error"]').forEach(el => {
                el.classList.add('hidden');
                el.textContent = '';
            });
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