<?php
$pageTitle = 'Products Manager';
$pageDescription = '';
include '../shared/header-client.php';
?>

<!-- Page Heading & Actions -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">Products</h1>
        <p class="text-gray-600 dark:text-gray-400 text-sm">Manage your inventory, prices, and product visibility across your store.</p>
    </div>
    <div class="flex gap-3">
        <button class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm text-gray-900 dark:text-white">
            <span class="material-symbols-outlined" style="font-size: 18px;">upload_file</span>
            Import CSV
        </button>
        <button onclick="openAddProductModal()" id="addProductBtn"
            class="flex items-center gap-2 px-4 py-2 bg-primary hover:bg-primary-dark text-[#0d1b18] rounded-lg text-sm font-bold shadow-glow transition-all">
            <span class="material-symbols-outlined" style="font-size: 18px;">add</span>
            Add Product
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

<!-- Filters Section -->
<div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 mb-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div class="col-span-1 md:col-span-2">
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Search Products</label>
            <div class="flex items-center bg-gray-50 dark:bg-gray-900/50 rounded-lg px-3 py-2 border border-gray-200 dark:border-gray-700 focus-within:border-primary/50 transition-colors">
                <span class="material-symbols-outlined text-gray-400" style="font-size: 20px;">search</span>
                <input type="text" id="searchInput" placeholder="Search by name, SKU..." oninput="handleSearch()"
                    class="bg-transparent border-none text-sm w-full focus:ring-0 text-gray-900 dark:text-white placeholder-gray-400 ml-2" />
            </div>
        </div>
        <div class="col-span-1">
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Store</label>
            <select id="storeFilter" class="w-full bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm py-2 px-3 text-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                <option value="">Loading stores...</option>
            </select>
        </div>
        <div class="col-span-1">
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Status</label>
            <select id="statusFilter" onchange="loadProducts()" class="w-full bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg text-sm py-2 px-3 text-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                <option value="">All Status</option>
                <option value="active">In Stock</option>
                <option value="out_of_stock">Out of Stock</option>
            </select>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8" id="statsCards">
    <!-- Will be populated by JavaScript -->
</div>

<!-- Products Table -->
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-900/30">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-gray-600 dark:text-gray-400">inventory_2</span>
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Product Inventory</h3>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-500 dark:text-gray-400" id="productCount">0 products</span>
        </div>
    </div>
    <div id="productsTable">
        <div class="flex items-center justify-center p-12">
            <span class="material-symbols-outlined animate-spin text-4xl text-primary">refresh</span>
        </div>
    </div>
</div>

<!-- Pagination -->
<div id="pagination" class="mt-6"></div>

<!-- Add/Edit Product Modal -->
<div id="productModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-auto">
        <div class="p-6 border-b flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900" id="modalTitle">Add Product</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="productForm" onsubmit="handleSubmit(event)" class="p-6">
            <input type="hidden" id="productId">

            <div class="space-y-4">
                <!-- Store Selection (for add only) -->
                <div id="storeSelectContainer">
                    <label class="block text-sm font-bold text-gray-900 mb-2">Store *</label>
                    <select id="product_store_id" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary">
                        <option value="">Select Store</option>
                    </select>
                </div>

                <!-- Product Name -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Product Name *</label>
                    <input type="text" id="name" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary"
                        placeholder="Premium Wireless Headphones">
                </div>

                <!-- SKU -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">SKU</label>
                    <input type="text" id="sku"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary"
                        placeholder="WH-1000XM4">
                    <p class="text-xs text-gray-500 mt-1">Stock Keeping Unit (unique identifier)</p>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Description *</label>
                    <textarea id="description" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary h-24"
                        placeholder="Detailed product description..."></textarea>
                </div>

                <!-- Price and Stock -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Price (₦) *</label>
                        <input type="number" id="price" required step="0.01" min="0"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary"
                            placeholder="25000">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Stock Quantity *</label>
                        <input type="number" id="stock_quantity" required min="0"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary"
                            placeholder="100">
                    </div>
                </div>

                <!-- Category and Weight -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Category</label>
                        <select id="category_id"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary">
                            <option value="">Select Category (Optional)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Weight (kg)</label>
                        <input type="number" id="weight" step="0.01" min="0"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary"
                            placeholder="0.5">
                    </div>
                </div>

                <!-- Images -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Product Images</label>

                    <!-- Upload Button -->
                    <div class="flex items-center gap-3 mb-3">
                        <button type="button" onclick="triggerImageUpload()"
                            class="flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-100 font-semibold">
                            <span class="material-symbols-outlined text-sm">upload</span>
                            Upload Images
                        </button>
                        <input type="file" id="imageFiles" accept="image/*" multiple class="hidden" onchange="handleImageUpload(event)">
                        <span id="uploadStatus" class="text-sm text-gray-500"></span>
                    </div>

                    <!-- Image Preview Grid -->
                    <div id="imagePreviewGrid" class="grid grid-cols-4 gap-3 mb-2"></div>

                    <p class="text-xs text-gray-500">Upload up to 5 product images. First image will be the main product image.</p>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Status *</label>
                    <select id="status" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-3 justify-end mt-6 pt-6 border-t">
                <button type="button" onclick="closeModal()"
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 font-semibold">
                    Cancel
                </button>
                <button type="submit" id="submitBtn"
                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 font-semibold">
                    Save Product
                </button>
            </div>
        </form>
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
</style>

<script src="/assets/js/services/store.service.js"></script>
<script src="/assets/js/services/product.service.js"></script>
<script src="/assets/js/services/category.service.js"></script>
<script src="/assets/js/services/image.service.js"></script>

<script>
    let currentPage = 1;
    let currentSearch = '';
    let currentStatus = '';
    let selectedStoreId = null;
    let userStores = [];
    let categories = [];
    let uploadedImages = []; // Track uploaded images with URLs and public_ids

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

    // Load user stores
    async function loadStores() {
        try {
            const user = auth.getUser();
            const response = await storeService.getAll({
                client_id: user.id,
                limit: 100
            });
            userStores = response.data?.stores || [];

            // Populate store filter
            const storeFilter = document.getElementById('storeFilter');
            const productStoreSelect = document.getElementById('product_store_id');

            let options = '<option value="">All Stores</option>';
            userStores.forEach(store => {
                options += `<option value="${store.id}">${store.store_name}</option>`;
            });

            storeFilter.innerHTML = options;
            productStoreSelect.innerHTML = options.replace('All Stores', 'Select Store');

            // Check for store_id in URL
            const urlParams = new URLSearchParams(window.location.search);
            const storeIdFromUrl = urlParams.get('store_id');

            if (storeIdFromUrl) {
                selectedStoreId = storeIdFromUrl;
                storeFilter.value = storeIdFromUrl;
            } else if (userStores.length === 1) {
                selectedStoreId = userStores[0].id;
                storeFilter.value = selectedStoreId;
            }

            // Enable/disable add button based on store selection
            updateAddButtonState();

        } catch (error) {
            console.error('Error loading stores:', error);
            utils.toast('Failed to load stores', 'error');
        }
    }

    // Load categories for selected store
    async function loadCategories(storeId) {
        if (!storeId) {
            const categorySelect = document.getElementById('category_id');
            categorySelect.innerHTML = '<option value="">Select Category (Optional)</option>';
            categories = [];
            return;
        }

        try {
            const response = await categoryService.getAll({
                store_id: storeId,
                status: 'active'
            });
            categories = response.data?.categories || [];

            const categorySelect = document.getElementById('category_id');
            let options = '<option value="">Select Category (Optional)</option>';

            categories.forEach(cat => {
                options += `<option value="${cat.id}">${cat.name}</option>`;
            });

            categorySelect.innerHTML = options;
        } catch (error) {
            console.error('Error loading categories:', error);
        }
    }

    // Store filter change handler
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('storeFilter').addEventListener('change', function() {
            selectedStoreId = this.value || null;
            updateAddButtonState();
            loadProducts(1);
            loadCategories(selectedStoreId);
        });
    });

    // Update add button state
    function updateAddButtonState() {
        const addBtn = document.getElementById('addProductBtn');
        if (selectedStoreId) {
            addBtn.disabled = false;
            addBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            addBtn.disabled = true;
            addBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    // Load products
    async function loadProducts(page = 1) {
        if (!selectedStoreId) {
            document.getElementById('productsTable').innerHTML =
                components.emptyState('Please select a store to view products', 'inventory_2');
            document.getElementById('pagination').innerHTML = '';
            return;
        }

        try {
            currentPage = page;
            const params = {
                store_id: selectedStoreId,
                page: currentPage,
                limit: 20
            };

            if (currentSearch) params.search = currentSearch;
            if (currentStatus) params.status = currentStatus;

            const response = await productService.getAll(params);
            const products = response.data?.products || [];
            const pagination = response.data?.pagination || {};

            displayProducts(products);
            displayPagination(pagination);

        } catch (error) {
            console.error('Error loading products:', error);
            document.getElementById('productsTable').innerHTML =
                components.errorState('Failed to load products');
        }
    }

    // Display products table
    function displayProducts(products) {
        const container = document.getElementById('productsTable');
        const countElement = document.getElementById('productCount');

        countElement.textContent = `${products.length} product${products.length !== 1 ? 's' : ''}`;

        // Calculate stats
        const totalProducts = products.length;
        const activeProducts = products.filter(p => p.status === 'active' && p.stock_quantity > 0).length;
        const lowStock = products.filter(p => p.stock_quantity < 10 && p.stock_quantity > 0).length;
        const outOfStock = products.filter(p => p.stock_quantity === 0).length;
        const totalValue = products.reduce((sum, p) => sum + (p.price * p.stock_quantity), 0);

        // Find top performer by sales
        const topPerformer = products.reduce((top, p) => {
            const sales = p.sales_count || 0;
            return (!top || sales > (top.sales_count || 0)) ? p : top;
        }, null);

        // Update stats cards
        updateStatsCards({
            totalProducts,
            activeProducts,
            lowStock,
            outOfStock,
            totalValue,
            topPerformer: topPerformer ? topPerformer.name : 'N/A'
        });

        if (products.length === 0) {
            container.innerHTML = `
                <div class="text-center py-12">
                    <span class="material-symbols-outlined text-6xl text-gray-300 dark:text-gray-600">inventory_2</span>
                    <h4 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">${currentSearch ? 'No products found' : 'No products yet'}</h4>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">${currentSearch ? 'Try adjusting your search or filters' : 'Add your first product to get started'}</p>
                </div>
            `;
            return;
        }

        let html = '<div class="overflow-x-auto"><table class="w-full">';

        // Header
        html += `
            <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">SKU</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Sales</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
        `;

        // Body
        html += '<tbody class="divide-y divide-gray-200 dark:divide-gray-700">';
        products.forEach(product => {
            const stockClass = product.stock_quantity === 0 ? 'text-red-600 dark:text-red-400' :
                product.stock_quantity < 10 ? 'text-orange-600 dark:text-orange-400' : 'text-gray-900 dark:text-white';

            const statusBadge = product.stock_quantity === 0 ?
                '<span class="px-2 py-1 text-xs font-medium rounded bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Out of Stock</span>' :
                product.stock_quantity < 10 ?
                '<span class="px-2 py-1 text-xs font-medium rounded bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400">Low Stock</span>' :
                '<span class="px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">In Stock</span>';

            html += `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center overflow-hidden flex-shrink-0">
                                ${product.images && product.images.length > 0 ? 
                                    `<img src="${product.images.find(img => img.is_primary)?.image_url || product.images[0].image_url}" alt="${product.name}" class="w-full h-full object-cover">` :
                                    product.image_url ? 
                                    `<img src="${product.image_url.split(',')[0]}" alt="${product.name}" class="w-full h-full object-cover">` :
                                    '<span class="material-symbols-outlined text-gray-400">inventory_2</span>'
                                }
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-gray-900 dark:text-white truncate">${product.name}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">${product.description ? product.description.substring(0, 40) + '...' : 'No description'}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">${product.sku || '-'}</td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">${product.category_name || 'Uncategorized'}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">₦${Number(product.price).toLocaleString('en-NG')}</td>
                    <td class="px-6 py-4 text-sm font-semibold ${stockClass}">${product.stock_quantity}</td>
                    <td class="px-6 py-4">${statusBadge}</td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-bold text-gray-900 dark:text-white">${product.sales_count || 0}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="editProduct(${product.id})" 
                                class="p-2 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors" title="Edit">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </button>
                            <button onclick="deleteProduct(${product.id}, '${product.name.replace(/'/g, "\\'")}')" 
                                class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors" title="Delete">
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
        html += '</tbody></table></div>';

        container.innerHTML = html;
    }

    // Update stats cards
    function updateStatsCards(stats) {
        const container = document.getElementById('statsCards');

        container.innerHTML = `
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden group hover:shadow-lg transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900 dark:to-green-800 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-green-600 dark:text-green-400">inventory_2</span>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">${stats.totalProducts}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Products</p>
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-green-100 dark:bg-green-900 rounded-full opacity-20 group-hover:scale-110 transition-transform"></div>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden hover:shadow-lg transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900 dark:to-blue-800 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">check_circle</span>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">${stats.activeProducts}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">In Stock</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden hover:shadow-lg transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900 dark:to-orange-800 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-orange-600 dark:text-orange-400">warning</span>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">${stats.lowStock}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Low Stock</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden hover:shadow-lg transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900 dark:to-purple-800 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-purple-600 dark:text-purple-400">payments</span>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">₦${stats.totalValue.toLocaleString('en-NG')}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Value</p>
            </div>
        `;
    }

    // Display pagination
    function displayPagination(pagination) {
        const container = document.getElementById('pagination');

        if (!pagination || pagination.pages <= 1) {
            container.innerHTML = '';
            return;
        }

        const {
            page,
            pages
        } = pagination;
        let html = '<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-6 py-4 flex items-center justify-between shadow-sm">';

        // Previous
        html += `
            <button onclick="loadProducts(${page - 1})" ${page === 1 ? 'disabled' : ''}
                class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-gray-600 dark:text-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                <span class="material-symbols-outlined" style="font-size: 18px;">chevron_left</span>
                Previous
            </button>
        `;

        // Pages
        html += '<div class="flex items-center gap-2">';
        for (let i = 1; i <= pages; i++) {
            if (i === 1 || i === pages || (i >= page - 2 && i <= page + 2)) {
                const isActive = i === page;
                html += `
                    <button onclick="loadProducts(${i})"
                        class="px-3 py-2 rounded-lg text-sm font-medium transition-colors ${
                            isActive 
                                ? 'bg-primary text-[#0d1b18]' 
                                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
                        }">
                        ${i}
                    </button>
                `;
            } else if (i === page - 3 || i === page + 3) {
                html += '<span class="text-gray-400">...</span>';
            }
        }
        html += '</div>';

        // Next
        html += `
            <button onclick="loadProducts(${page + 1})" ${page === pages ? 'disabled' : ''}
                class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-gray-600 dark:text-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                Next
                <span class="material-symbols-outlined" style="font-size: 18px;">chevron_right</span>
            </button>
        `;

        html += '</div>';
        container.innerHTML = html;
    }

    // Search handler
    const handleSearch = utils.debounce(function() {
        currentSearch = document.getElementById('searchInput').value;
        loadProducts(1);
    }, 300);

    // Status filter
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('statusFilter').addEventListener('change', function() {
            currentStatus = this.value;
            loadProducts(1);
        });
    });

    // Open add product modal
    function openAddProductModal() {
        if (!selectedStoreId) {
            utils.toast('Please select a store first', 'warning');
            return;
        }

        document.getElementById('modalTitle').textContent = 'Add Product';
        document.getElementById('productForm').reset();
        document.getElementById('productId').value = '';
        document.getElementById('product_store_id').value = selectedStoreId;
        document.getElementById('storeSelectContainer').style.display = 'block';
        uploadedImages = []; // Clear uploaded images
        displayImagePreviews();
        loadCategories(selectedStoreId);
        document.getElementById('productModal').classList.remove('hidden');
    }

    // Edit product
    async function editProduct(id) {
        try {
            const response = await productService.getById(id);
            const product = response.data;

            document.getElementById('modalTitle').textContent = 'Edit Product';
            document.getElementById('productId').value = product.id;
            document.getElementById('product_store_id').value = product.store_id;
            document.getElementById('name').value = product.name;
            document.getElementById('sku').value = product.sku || '';
            document.getElementById('description').value = product.description;
            document.getElementById('price').value = product.price;
            document.getElementById('stock_quantity').value = product.stock_quantity || 0;
            document.getElementById('status').value = product.status;

            // Load existing images
            uploadedImages = [];
            if (product.images && Array.isArray(product.images)) {
                // New format: images array from product_images table
                uploadedImages = product.images.map(img => ({
                    url: img.image_url,
                    public_id: img.public_id || null
                }));
            } else if (product.image_url) {
                // Legacy format: comma-separated string (backward compatibility)
                const imageUrls = product.image_url.split(',')
                    .map(url => url.trim())
                    .filter(url => url.length > 0);
                uploadedImages = imageUrls.map(url => ({
                    url: url,
                    public_id: null
                }));
            }
            displayImagePreviews();
            document.getElementById('status').value = product.status;

            // Load categories and set selected
            await loadCategories(product.store_id);
            document.getElementById('category_id').value = product.category_id || '';

            document.getElementById('storeSelectContainer').style.display = 'none';
            document.getElementById('productModal').classList.remove('hidden');

        } catch (error) {
            console.error('Error loading product:', error);
            utils.toast('Failed to load product details', 'error');
        }
    }

    // Handle form submit
    async function handleSubmit(event) {
        event.preventDefault();

        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Saving...';

        const productId = document.getElementById('productId').value;
        const categoryId = document.getElementById('category_id').value;

        // Prepare images array from uploadedImages
        const images = uploadedImages.map(img => img.url);

        const data = {
            store_id: document.getElementById('product_store_id').value,
            name: document.getElementById('name').value,
            sku: document.getElementById('sku').value || null,
            description: document.getElementById('description').value,
            price: parseFloat(document.getElementById('price').value),
            stock_quantity: parseInt(document.getElementById('stock_quantity').value),
            category_id: categoryId ? parseInt(categoryId) : null,
            weight: parseFloat(document.getElementById('weight').value) || null,
            images: images, // Send as array instead of comma-separated string
            status: document.getElementById('status').value
        };

        try {
            if (productId) {
                await productService.update(productId, data);
                utils.toast('Product updated successfully!', 'success');
            } else {
                await productService.create(data);
                utils.toast('Product created successfully!', 'success');
            }

            closeModal();
            loadProducts(currentPage);

        } catch (error) {
            console.error('Error saving product:', error);
            utils.toast(error.message || 'Failed to save product', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Save Product';
        }
    }

    // Delete product
    async function deleteProduct(id, name) {
        if (!confirm(`Are you sure you want to delete "${name}"?`)) return;

        try {
            await productService.delete(id);
            utils.toast('Product deleted successfully!', 'success');
            loadProducts(currentPage);
        } catch (error) {
            console.error('Error deleting product:', error);
            utils.toast('Failed to delete product', 'error');
        }
    }

    // Trigger image upload
    function triggerImageUpload() {
        if (uploadedImages.length >= 5) {
            utils.toast('Maximum 5 images allowed', 'warning');
            return;
        }
        document.getElementById('imageFiles').click();
    }

    // Handle image upload
    async function handleImageUpload(event) {
        const files = event.target.files;
        if (!files || files.length === 0) return;

        const remainingSlots = 5 - uploadedImages.length;
        if (files.length > remainingSlots) {
            utils.toast(`You can only upload ${remainingSlots} more image(s)`, 'warning');
            return;
        }

        const uploadStatus = document.getElementById('uploadStatus');
        const totalFiles = files.length;
        let uploadedCount = 0;
        let failedCount = 0;

        // Create progress container
        uploadStatus.innerHTML = `
            <div class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-blue-600 font-medium">Uploading ${totalFiles} image(s)...</span>
                    <span class="text-gray-600"><span id="uploadProgress">0</span>/${totalFiles}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="uploadProgressBar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
                <div id="uploadDetails" class="space-y-1 text-xs"></div>
            </div>
        `;

        const progressSpan = document.getElementById('uploadProgress');
        const progressBar = document.getElementById('uploadProgressBar');
        const detailsDiv = document.getElementById('uploadDetails');

        try {
            // Upload images sequentially with detailed feedback
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const fileIndex = i + 1;

                // Add file to details
                const fileDiv = document.createElement('div');
                fileDiv.id = `file-${i}`;
                fileDiv.className = 'flex items-center gap-2';
                fileDiv.innerHTML = `
                    <span class="material-symbols-outlined text-sm text-blue-600 animate-spin">progress_activity</span>
                    <span class="text-gray-600 truncate flex-1">${file.name}</span>
                    <span class="text-gray-500">Uploading...</span>
                `;
                detailsDiv.appendChild(fileDiv);

                try {
                    // Validate file
                    const validation = imageService.validateImage(file);
                    if (!validation.valid) {
                        fileDiv.innerHTML = `
                            <span class="material-symbols-outlined text-sm text-red-600">error</span>
                            <span class="text-gray-600 truncate flex-1">${file.name}</span>
                            <span class="text-red-600">Invalid</span>
                        `;
                        failedCount++;
                        continue;
                    }

                    // Upload single image
                    const result = await imageService.uploadImage(file, {
                        folder: `products/store-${selectedStoreId}`
                    });

                    // Handle response structure
                    const imageData = result.data || result;

                    uploadedImages.push({
                        url: imageData.url,
                        public_id: imageData.public_id
                    });

                    // Update file status
                    fileDiv.innerHTML = `
                        <span class="material-symbols-outlined text-sm text-green-600">check_circle</span>
                        <span class="text-gray-600 truncate flex-1">${file.name}</span>
                        <span class="text-green-600">Done</span>
                    `;

                    uploadedCount++;

                } catch (error) {
                    console.error(`Upload error for ${file.name}:`, error);
                    fileDiv.innerHTML = `
                        <span class="material-symbols-outlined text-sm text-red-600">error</span>
                        <span class="text-gray-600 truncate flex-1">${file.name}</span>
                        <span class="text-red-600">Failed</span>
                    `;
                    failedCount++;
                }

                // Update progress
                const progress = ((uploadedCount + failedCount) / totalFiles) * 100;
                progressSpan.textContent = uploadedCount + failedCount;
                progressBar.style.width = `${progress}%`;

                // Update progress bar color based on status
                if (failedCount > 0) {
                    progressBar.classList.remove('bg-blue-600');
                    progressBar.classList.add('bg-yellow-600');
                }
            }

            // Display previews
            displayImagePreviews();

            // Show final status
            setTimeout(() => {
                if (failedCount === 0) {
                    uploadStatus.innerHTML = `<span class="text-green-600 font-medium">✓ ${uploadedCount} image(s) uploaded successfully!</span>`;
                    progressBar.classList.remove('bg-yellow-600');
                    progressBar.classList.add('bg-green-600');
                } else {
                    uploadStatus.innerHTML = `<span class="text-yellow-600 font-medium">⚠ ${uploadedCount} uploaded, ${failedCount} failed</span>`;
                }

                setTimeout(() => {
                    uploadStatus.innerHTML = '';
                }, 3000);
            }, 1000);

            // Reset file input
            event.target.value = '';

        } catch (error) {
            console.error('Upload error:', error);
            uploadStatus.innerHTML = '<span class="text-red-600">Upload failed</span>';
            utils.toast('Failed to upload images: ' + error.message, 'error');
        }
    }

    // Display image previews
    function displayImagePreviews() {
        const grid = document.getElementById('imagePreviewGrid');

        if (uploadedImages.length === 0) {
            grid.innerHTML = '';
            return;
        }

        let html = '';
        uploadedImages.forEach((img, index) => {
            html += `
                <div class="relative group border-2 ${index === 0 ? 'border-blue-500' : 'border-gray-200'} rounded-lg overflow-hidden">
                    <img src="${img.url}" alt="Product image ${index + 1}" 
                         class="w-full h-24 object-cover">
                    ${index === 0 ? '<div class="absolute top-0 left-0 bg-blue-500 text-white text-xs px-2 py-1">Main</div>' : ''}
                    <button type="button" onclick="removeImage(${index})"
                        class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <span class="material-symbols-outlined text-sm">close</span>
                    </button>
                    ${index > 0 ? `
                        <button type="button" onclick="setMainImage(${index})"
                            class="absolute bottom-1 right-1 bg-blue-500 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity">
                            Set Main
                        </button>
                    ` : ''}
                </div>
            `;
        });

        grid.innerHTML = html;
    }

    // Remove image
    async function removeImage(index) {
        const image = uploadedImages[index];

        // Try to delete from Cloudinary if we have public_id
        if (image.public_id) {
            try {
                await imageService.deleteImage(image.public_id);
            } catch (error) {
                console.error('Failed to delete image from Cloudinary:', error);
                // Continue anyway as we still want to remove it from the list
            }
        }

        uploadedImages.splice(index, 1);
        displayImagePreviews();
    }

    // Set main image
    function setMainImage(index) {
        const image = uploadedImages.splice(index, 1)[0];
        uploadedImages.unshift(image);
        displayImagePreviews();
    }

    // Close modal
    function closeModal() {
        document.getElementById('productModal').classList.add('hidden');
        document.getElementById('productForm').reset();
        uploadedImages = [];
        displayImagePreviews();
    }

    // Initialize
    async function init() {
        initDarkMode();
        await loadStores();
        loadProducts();
    }

    init();
</script>