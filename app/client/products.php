<?php
$pageTitle = 'Products';
$pageDescription = 'Manage your store products';
include '../shared/header-client.php';
?>

<!-- Header Actions -->
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-4">
        <!-- Store Filter -->
        <select id="storeFilter" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary">
            <option value="">Loading stores...</option>
        </select>

        <!-- Search -->
        <input type="text" id="searchInput" placeholder="Search products..."
            class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
            oninput="handleSearch()">

        <!-- Status Filter -->
        <select id="statusFilter" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary"
            onchange="loadProducts()">
            <option value="">All Status</option>
            <option value="active">In Stock</option>
            <option value="out_of_stock">Out of Stock</option>
        </select>
    </div>

    <button onclick="openAddProductModal()" id="addProductBtn"
        class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 font-semibold">
        <span class="material-symbols-outlined">add</span>
        Add Product
    </button>
</div>

<!-- Products Table -->
<div class="bg-white rounded-xl border border-gray-200">
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
                        <input type="text" id="category"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary"
                            placeholder="Electronics">
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
                    <label class="block text-sm font-bold text-gray-900 mb-2">Product Images (URLs)</label>
                    <input type="url" id="image_url"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary mb-2"
                        placeholder="https://example.com/image.jpg">
                    <p class="text-xs text-gray-500">Enter image URLs (comma-separated for multiple)</p>
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

<script src="/assets/js/services/store.service.js"></script>
<script src="/assets/js/services/product.service.js"></script>

<script>
    let currentPage = 1;
    let currentSearch = '';
    let currentStatus = '';
    let selectedStoreId = null;
    let userStores = [];

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

    // Store filter change handler
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('storeFilter').addEventListener('change', function() {
            selectedStoreId = this.value || null;
            updateAddButtonState();
            loadProducts(1);
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

        if (products.length === 0) {
            container.innerHTML = components.emptyState(
                currentSearch ? 'No products found' : 'No products yet. Add your first product!',
                'inventory_2'
            );
            return;
        }

        let html = '<table class="min-w-full divide-y divide-gray-200">';

        // Header
        html += `
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
        `;

        // Body
        html += '<tbody class="bg-white divide-y divide-gray-200">';
        products.forEach(product => {
            const stockStatus = product.stock_quantity === 0 ? 'out_of_stock' : 'active';
            const stockClass = product.stock_quantity === 0 ? 'text-red-600' :
                product.stock_quantity < 10 ? 'text-orange-600' : 'text-gray-900';

            html += `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                                ${product.image_url ? 
                                    `<img src="${product.image_url.split(',')[0]}" alt="${product.name}" class="w-full h-full object-cover">` :
                                    '<span class="material-symbols-outlined text-gray-400">inventory_2</span>'
                                }
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">${product.name}</p>
                                <p class="text-sm text-gray-500">${utils.truncate(product.description, 40)}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">${product.sku || '-'}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">₦${Number(product.price).toLocaleString('en-NG')}</td>
                    <td class="px-6 py-4 text-sm font-semibold ${stockClass}">${product.stock_quantity}</td>
                    <td class="px-6 py-4">${components.statusBadge(product.status)}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="editProduct(${product.id})" 
                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Edit">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </button>
                            <button onclick="deleteProduct(${product.id}, '${product.name.replace(/'/g, "\\'")}')" 
                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Delete">
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
        html += '</tbody></table>';

        container.innerHTML = html;
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
        let html = '<div class="flex items-center justify-center gap-2">';

        // Previous
        html += `
            <button onclick="loadProducts(${page - 1})" ${page === 1 ? 'disabled' : ''}
                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed font-semibold">
                <span class="material-symbols-outlined text-sm">chevron_left</span>
            </button>
        `;

        // Pages
        for (let i = 1; i <= pages; i++) {
            if (i === 1 || i === pages || (i >= page - 2 && i <= page + 2)) {
                html += `
                    <button onclick="loadProducts(${i})"
                        class="w-10 h-10 rounded-lg font-semibold ${i === page ? 'bg-primary text-white' : 'border border-gray-300 hover:bg-gray-50'}">
                        ${i}
                    </button>
                `;
            } else if (i === page - 3 || i === page + 3) {
                html += '<span class="px-2">...</span>';
            }
        }

        // Next
        html += `
            <button onclick="loadProducts(${page + 1})" ${page === pages ? 'disabled' : ''}
                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed font-semibold">
                <span class="material-symbols-outlined text-sm">chevron_right</span>
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
            document.getElementById('stock_quantity').value = product.stock_quantity;
            document.getElementById('category').value = product.category || '';
            document.getElementById('weight').value = product.weight || '';
            document.getElementById('image_url').value = product.image_url || '';
            document.getElementById('status').value = product.status;

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
        const data = {
            store_id: document.getElementById('product_store_id').value,
            name: document.getElementById('name').value,
            sku: document.getElementById('sku').value || null,
            description: document.getElementById('description').value,
            price: parseFloat(document.getElementById('price').value),
            stock_quantity: parseInt(document.getElementById('stock_quantity').value),
            category: document.getElementById('category').value || null,
            weight: parseFloat(document.getElementById('weight').value) || null,
            image_url: document.getElementById('image_url').value || null,
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

    // Close modal
    function closeModal() {
        document.getElementById('productModal').classList.add('hidden');
        document.getElementById('productForm').reset();
    }

    // Initialize
    async function init() {
        await loadStores();
        loadProducts();
    }

    init();
</script>