<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="pageTitle">Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .cart-badge {
            display: none;
        }

        .cart-badge:not(.hidden) {
            display: inline-flex;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Store Header (Dynamic based on template) -->
    <div id="storeHeader"></div>

    <!-- Main Content -->
    <main id="storeContent" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Loading State -->
        <div id="loadingState" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
            <p class="text-gray-600">Loading store...</p>
        </div>

        <!-- Products Grid -->
        <div id="productsSection" class="hidden">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Products</h2>
                <div class="flex items-center gap-3">
                    <select id="categoryFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All Categories</option>
                    </select>
                    <select id="sortFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="name">Name</option>
                        <option value="price_asc">Price: Low to High</option>
                        <option value="price_desc">Price: High to Low</option>
                    </select>
                </div>
            </div>

            <div id="productsGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <!-- Products will be loaded here -->
            </div>

            <!-- Empty Products State -->
            <div id="noProducts" class="hidden text-center py-12">
                <span class="material-symbols-outlined text-gray-300 text-6xl mb-4">inventory_2</span>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No products available</h3>
                <p class="text-gray-600">This store hasn't added any products yet</p>
            </div>
        </div>
    </main>

    <!-- Cart Badge (Floating) -->
    <a href="/store/cart.php" id="floatingCartBtn" class="hidden fixed bottom-6 right-6 bg-blue-600 text-white rounded-full w-16 h-16 flex items-center justify-center shadow-lg hover:bg-blue-700 transition-all z-50">
        <span class="material-symbols-outlined">shopping_cart</span>
        <span class="cart-badge absolute -top-2 -right-2 bg-red-500 text-white text-xs w-6 h-6 flex items-center justify-center rounded-full font-bold">0</span>
    </a>

    <script src="/assets/js/api.js"></script>
    <script src="/assets/js/services/cart.js"></script>
    <script>
        const apiClient = new APIClient();
        const cartService = new CartService(apiClient);

        const urlParams = new URLSearchParams(window.location.search);
        const storeId = urlParams.get('id');

        let store = null;
        let products = [];
        let categories = [];

        document.addEventListener('DOMContentLoaded', async () => {
            if (!storeId) {
                window.location.href = '/';
                return;
            }

            await loadStore();
            updateCartBadge();
        });

        async function loadStore() {
            try {
                // Load store details
                const storeResponse = await apiClient.get(`/api/stores/${storeId}`);

                if (!storeResponse.success) {
                    throw new Error('Store not found');
                }

                store = storeResponse.data;

                // Update page title
                document.getElementById('pageTitle').textContent = store.name;
                document.title = store.name;

                // Render store header (based on template)
                renderStoreHeader();

                // Load products
                const productsResponse = await apiClient.get(`/api/stores/${storeId}/products`);
                if (productsResponse.success) {
                    products = productsResponse.data.products || [];
                }

                // Load categories
                const categoriesResponse = await apiClient.get(`/api/stores/${storeId}/categories`);
                if (categoriesResponse.success) {
                    categories = categoriesResponse.data.categories || [];
                    renderCategoryFilter();
                }

                document.getElementById('loadingState').classList.add('hidden');

                if (products.length > 0) {
                    renderProducts();
                    document.getElementById('productsSection').classList.remove('hidden');
                    document.getElementById('floatingCartBtn').classList.remove('hidden');
                } else {
                    document.getElementById('productsSection').classList.remove('hidden');
                    document.getElementById('noProducts').classList.remove('hidden');
                }

            } catch (error) {
                console.error('Error loading store:', error);
                alert('Store not found');
                window.location.href = '/';
            }
        }

        function renderStoreHeader() {
            const header = document.getElementById('storeHeader');
            const primaryColor = store.primary_color || '#2563eb';
            const accentColor = store.accent_color || '#10b981';

            header.innerHTML = `
                <header class="bg-white shadow-sm sticky top-0 z-40">
                    <div class="bg-gradient-to-r" style="background: linear-gradient(to right, ${primaryColor}, ${accentColor});">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex items-center justify-between text-white text-sm">
                            <span>Welcome to ${store.name} - Free shipping on orders over ₦10,000</span>
                            <div class="flex items-center gap-4">
                                <a href="/store/order-tracking.php?store_id=${storeId}" class="hover:underline">Track Order</a>
                                <a href="/" class="hover:underline">Browse Stores</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center justify-between h-16">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: ${primaryColor};">
                                    <span class="material-symbols-outlined text-white">storefront</span>
                                </div>
                                <div>
                                    <h1 class="text-xl font-bold text-gray-900">${store.name}</h1>
                                    ${store.description ? `<p class="text-xs text-gray-500">${store.description}</p>` : ''}
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-4">
                                <a href="/store/cart.php?store_id=${storeId}" class="relative text-gray-700 hover:text-gray-900">
                                    <span class="material-symbols-outlined">shopping_cart</span>
                                    <span class="cart-badge absolute -top-2 -right-2 bg-red-500 text-white text-xs w-5 h-5 flex items-center justify-center rounded-full font-bold">0</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </header>
            `;
        }

        function renderCategoryFilter() {
            const select = document.getElementById('categoryFilter');

            categories.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.id;
                option.textContent = cat.name;
                select.appendChild(option);
            });

            select.addEventListener('change', filterProducts);
        }

        function renderProducts() {
            const grid = document.getElementById('productsGrid');
            grid.innerHTML = '';

            const filteredProducts = getFilteredProducts();

            if (filteredProducts.length === 0) {
                document.getElementById('noProducts').classList.remove('hidden');
                return;
            }

            document.getElementById('noProducts').classList.add('hidden');

            filteredProducts.forEach(product => {
                const card = document.createElement('div');
                card.className = 'bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow';

                const inStock = product.stock_quantity > 0;

                card.innerHTML = `
                    <div class="relative">
                        ${product.image_url ? `
                            <img src="${product.image_url}" alt="${product.name}" class="w-full h-48 object-cover">
                        ` : `
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <span class="material-symbols-outlined text-gray-400 text-4xl">image</span>
                            </div>
                        `}
                        ${!inStock ? `
                            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                <span class="bg-red-500 text-white px-4 py-2 rounded-lg font-semibold">Out of Stock</span>
                            </div>
                        ` : ''}
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">${product.name}</h3>
                        ${product.description ? `<p class="text-sm text-gray-600 mb-3 line-clamp-2">${product.description}</p>` : ''}
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xl font-bold text-gray-900">${cartService.formatCurrency(product.price)}</span>
                            ${inStock ? `
                                <span class="text-xs text-green-600">${product.stock_quantity} in stock</span>
                            ` : ''}
                        </div>
                        <button 
                            onclick="addToCart(${product.id}, '${product.name}', ${product.price}, '${product.image_url || ''}', ${product.stock_quantity})"
                            ${!inStock ? 'disabled' : ''}
                            class="w-full py-2 px-4 rounded-lg font-medium transition-colors flex items-center justify-center gap-2 ${inStock ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-gray-300 text-gray-500 cursor-not-allowed'}">
                            <span class="material-symbols-outlined text-sm">add_shopping_cart</span>
                            ${inStock ? 'Add to Cart' : 'Out of Stock'}
                        </button>
                    </div>
                `;
                grid.appendChild(card);
            });
        }

        function getFilteredProducts() {
            let filtered = [...products];

            // Category filter
            const categoryId = document.getElementById('categoryFilter').value;
            if (categoryId) {
                filtered = filtered.filter(p => p.category_id == categoryId);
            }

            // Sort
            const sortBy = document.getElementById('sortFilter').value;
            if (sortBy === 'price_asc') {
                filtered.sort((a, b) => parseFloat(a.price) - parseFloat(b.price));
            } else if (sortBy === 'price_desc') {
                filtered.sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
            } else {
                filtered.sort((a, b) => a.name.localeCompare(b.name));
            }

            return filtered;
        }

        function filterProducts() {
            renderProducts();
        }

        document.getElementById('sortFilter').addEventListener('change', filterProducts);

        async function addToCart(productId, productName, price, imageUrl, stockQuantity) {
            try {
                const product = {
                    id: productId,
                    name: productName,
                    price: price,
                    image_url: imageUrl,
                    stock_quantity: stockQuantity
                };

                const isAuthenticated = !!localStorage.getItem('auth_token');
                await cartService.addToCart(storeId, product, 1, isAuthenticated);

                showNotification('Product added to cart!', 'success');
                updateCartBadge();
            } catch (error) {
                console.error('Error adding to cart:', error);
                showNotification(error.message || 'Failed to add to cart', 'error');
            }
        }

        function updateCartBadge() {
            const count = cartService.getLocalCartCount(storeId);
            document.querySelectorAll('.cart-badge').forEach(badge => {
                badge.textContent = count;
                badge.classList.toggle('hidden', count === 0);
            });
        }

        function showNotification(message, type = 'info') {
            // Simple alert for now
            const icons = {
                success: '✅',
                error: '❌',
                info: 'ℹ️'
            };
            alert(`${icons[type] || ''} ${message}`);
        }
    </script>
</body>

</html>