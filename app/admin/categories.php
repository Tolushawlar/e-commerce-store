<?php
$pageTitle = 'Categories';
$pageDescription = 'Manage product categories';
include '../shared/header-admin.php';
?>

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Categories</h1>
        <p class="text-gray-600">Organize your products with categories</p>
    </div>
    <button onclick="openCreateModal()" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 font-semibold">
        <span class="material-symbols-outlined inline-block align-middle">add</span>
        Add Category
    </button>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl p-4 border border-gray-200 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Store</label>
            <select id="filterStore" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary">
                <option value="">All Stores</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select id="filterStatus" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input type="text" id="filterSearch" placeholder="Search categories..."
                class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary">
        </div>
        <div class="flex items-end">
            <button onclick="loadCategories()" class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-semibold">
                <span class="material-symbols-outlined inline-block align-middle">filter_list</span>
                Apply Filters
            </button>
        </div>
    </div>
</div>

<!-- Categories List -->
<div class="bg-white rounded-xl border border-gray-200">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Store</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Parent</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Products</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase">Order</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-900 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody id="categoriesTable" class="divide-y divide-gray-200">
                <!-- Rows will be inserted here -->
            </tbody>
        </table>
    </div>
    <div id="emptyState" class="hidden p-12 text-center">
        <span class="material-symbols-outlined text-6xl text-gray-300">category</span>
        <h3 class="mt-4 text-lg font-medium text-gray-900">No categories found</h3>
        <p class="mt-2 text-gray-500">Get started by creating your first category</p>
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="categoryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900" id="modalTitle">Add Category</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form id="categoryForm" class="space-y-6">
            <input type="hidden" id="categoryId">

            <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Store *</label>
                <select id="storeId" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary">
                    <option value="">Select Store</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Name *</label>
                    <input type="text" id="categoryName" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary"
                        placeholder="Electronics">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Slug</label>
                    <input type="text" id="categorySlug"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary"
                        placeholder="electronics">
                    <p class="text-xs text-gray-500 mt-1">Auto-generated if empty</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Description</label>
                <textarea id="categoryDescription"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary h-24"
                    placeholder="Electronic devices and accessories"></textarea>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Icon</label>
                    <div class="relative">
                        <input type="text" id="categoryIcon" readonly
                            class="w-full px-4 py-3 pr-10 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary cursor-pointer"
                            placeholder="Click to select"
                            onclick="openIconPicker()">
                        <span class="material-symbols-outlined absolute right-3 top-3 text-gray-400 pointer-events-none" id="selectedIconPreview"></span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Click to browse icons</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Color</label>
                    <input type="color" id="categoryColor" value="#064E3B"
                        class="w-full h-12 border border-gray-200 rounded-xl cursor-pointer">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Display Order</label>
                    <input type="number" id="categoryOrder" value="0"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Parent Category</label>
                    <select id="parentId" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary">
                        <option value="">None (Top Level)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Status</label>
                    <select id="categoryStatus" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" id="submitBtn"
                    class="flex-1 px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90">
                    Save Category
                </button>
                <button type="button" onclick="closeModal()"
                    class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Icon Picker Modal -->
<div id="iconPickerModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-8 max-w-3xl w-full mx-4 max-h-[80vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Select Icon</h2>
            <button onclick="closeIconPicker()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="mb-6">
            <input type="text" id="iconSearch" placeholder="Search icons..."
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary"
                oninput="filterIcons()">
        </div>

        <div id="iconGrid" class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-10 gap-3">
            <!-- Icons will be inserted here -->
        </div>
    </div>
</div>

<?php include '../shared/footer-admin.php'; ?>

<script src="/assets/js/services/category.service.js"></script>
<script src="/assets/js/services/store.service.js"></script>

<script>
    let categories = [];
    let stores = [];

    // Popular category icons
    const categoryIcons = [
        'category', 'shopping_bag', 'shopping_cart', 'storefront', 'inventory_2',
        'devices', 'phone_android', 'laptop', 'computer', 'headphones',
        'watch', 'camera', 'photo_camera', 'videocam', 'tv',
        'checkroom', 'dry_cleaning', 'woman', 'man', 'face',
        'chair', 'bed', 'weekend', 'kitchen', 'dining',
        'home', 'apartment', 'house', 'cottage', 'villa',
        'restaurant', 'local_cafe', 'lunch_dining', 'fastfood', 'cake',
        'sports_soccer', 'fitness_center', 'sports_basketball', 'sports_tennis', 'pool',
        'menu_book', 'auto_stories', 'library_books', 'school', 'edit_note',
        'toys', 'sports_esports', 'videogame_asset', 'casino', 'celebration',
        'pets', 'cruelty_free', 'eco', 'local_florist', 'forest',
        'child_care', 'baby_changing_station', 'stroller', 'toys_fan', 'child_friendly',
        'build', 'handyman', 'construction', 'hardware', 'plumbing',
        'palette', 'brush', 'draw', 'color_lens', 'format_paint',
        'health_and_safety', 'medical_services', 'medication', 'pharmacy', 'vaccines',
        'directions_car', 'two_wheeler', 'directions_bike', 'electric_car', 'local_shipping',
        'diamond', 'favorite', 'star', 'loyalty', 'card_giftcard',
        'music_note', 'headset', 'piano', 'guitar', 'mic',
        'luggage', 'beach_access', 'hiking', 'landscape', 'terrain',
        'payments', 'account_balance', 'savings', 'credit_card', 'wallet'
    ];

    let allIcons = [...categoryIcons];

    // Load stores for dropdowns
    async function loadStores() {
        try {
            const response = await storeService.getAll({
                limit: 1000
            });
            if (response.success) {
                stores = response.data.stores;
                populateStoreDropdowns();
                // Auto-load categories on page load
                loadCategories();
            }
        } catch (error) {
            console.error('Error loading stores:', error);
        }
    }

    function populateStoreDropdowns() {
        const filterStore = document.getElementById('filterStore');
        const storeId = document.getElementById('storeId');

        stores.forEach(store => {
            const option1 = document.createElement('option');
            option1.value = store.id;
            option1.textContent = store.store_name;
            filterStore.appendChild(option1);

            const option2 = document.createElement('option');
            option2.value = store.id;
            option2.textContent = store.store_name;
            storeId.appendChild(option2);
        });
    }

    // Load categories
    async function loadCategories() {
        try {
            const storeId = document.getElementById('filterStore').value;

            const filters = {};

            if (storeId) {
                filters.store_id = storeId;
            }

            const status = document.getElementById('filterStatus').value;
            const search = document.getElementById('filterSearch').value;

            if (status) filters.status = status;
            if (search) filters.search = search;

            const response = await categoryService.getAll(filters);

            if (response.success) {
                categories = response.data.categories;
                renderCategories();
            }
        } catch (error) {
            console.error('Error loading categories:', error);
            utils.toast('Failed to load categories', 'error');
        }
    }

    // Render categories table
    function renderCategories() {
        const tbody = document.getElementById('categoriesTable');
        const emptyState = document.getElementById('emptyState');

        if (categories.length === 0) {
            tbody.innerHTML = '';
            emptyState.classList.remove('hidden');
            return;
        }

        emptyState.classList.add('hidden');

        tbody.innerHTML = categories.map(cat => `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        ${cat.icon ? `<span class="material-symbols-outlined" style="color: ${cat.color}">${cat.icon}</span>` : ''}
                        <div>
                            <div class="font-semibold text-gray-900">${cat.name}</div>
                            <div class="text-sm text-gray-500">${cat.slug}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">${cat.store_name || getStoreName(cat.store_id)}</td>
                <td class="px-6 py-4 text-sm text-gray-600">${cat.parent_name || '-'}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                        ${cat.product_count || 0}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 ${cat.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'} rounded-full text-xs font-semibold">
                        ${cat.status}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">${cat.display_order}</td>
                <td class="px-6 py-4 text-right">
                    <button onclick="editCategory(${cat.id})" class="text-primary hover:text-primary/80 mr-3">
                        <span class="material-symbols-outlined">edit</span>
                    </button>
                    <button onclick="deleteCategory(${cat.id})" class="text-red-600 hover:text-red-800">
                        <span class="material-symbols-outlined">delete</span>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    function getStoreName(storeId) {
        const store = stores.find(s => s.id === storeId);
        return store ? store.store_name : 'Unknown';
    }

    // Open create modal
    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Add Category';
        document.getElementById('categoryForm').reset();
        document.getElementById('categoryId').value = '';
        document.getElementById('categoryColor').value = '#064E3B';
        document.getElementById('categoryIcon').value = '';
        document.getElementById('selectedIconPreview').textContent = '';
        loadParentCategories();
        document.getElementById('categoryModal').classList.remove('hidden');
    }

    // Edit category
    async function editCategory(id) {
        try {
            const response = await categoryService.getById(id);
            if (response.success) {
                const cat = response.data;
                document.getElementById('modalTitle').textContent = 'Edit Category';
                document.getElementById('categoryId').value = cat.id;
                document.getElementById('storeId').value = cat.store_id;
                document.getElementById('categoryName').value = cat.name;
                document.getElementById('categorySlug').value = cat.slug;
                document.getElementById('categoryDescription').value = cat.description || '';
                document.getElementById('categoryIcon').value = cat.icon || '';
                document.getElementById('selectedIconPreview').textContent = cat.icon || '';
                document.getElementById('categoryColor').value = cat.color || '#064E3B';
                document.getElementById('categoryOrder').value = cat.display_order || 0;
                document.getElementById('categoryStatus').value = cat.status;

                await loadParentCategories(cat.store_id, cat.id);
                document.getElementById('parentId').value = cat.parent_id || '';

                document.getElementById('categoryModal').classList.remove('hidden');
            }
        } catch (error) {
            utils.toast('Failed to load category', 'error');
        }
    }

    // Load parent categories
    async function loadParentCategories(storeId = null, excludeId = null) {
        const select = document.getElementById('parentId');
        select.innerHTML = '<option value="">None (Top Level)</option>';

        if (!storeId) {
            storeId = document.getElementById('storeId').value;
        }

        if (!storeId) return;

        try {
            const response = await categoryService.getAll({
                store_id: storeId,
                status: 'active'
            });
            if (response.success) {
                response.data.categories
                    .filter(cat => cat.id != excludeId)
                    .forEach(cat => {
                        const option = document.createElement('option');
                        option.value = cat.id;
                        option.textContent = cat.name;
                        select.appendChild(option);
                    });
            }
        } catch (error) {
            console.error('Error loading parent categories:', error);
        }
    }

    // Auto-generate slug
    document.getElementById('categoryName').addEventListener('input', function(e) {
        if (!document.getElementById('categoryId').value) {
            const slug = e.target.value
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
            document.getElementById('categorySlug').value = slug;
        }
    });

    // Form submission
    document.getElementById('categoryForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="material-symbols-outlined animate-spin">refresh</span> Saving...';

        const categoryId = document.getElementById('categoryId').value;
        const data = {
            store_id: parseInt(document.getElementById('storeId').value),
            name: document.getElementById('categoryName').value,
            slug: document.getElementById('categorySlug').value,
            description: document.getElementById('categoryDescription').value,
            icon: document.getElementById('categoryIcon').value,
            color: document.getElementById('categoryColor').value,
            parent_id: document.getElementById('parentId').value ? parseInt(document.getElementById('parentId').value) : null,
            display_order: parseInt(document.getElementById('categoryOrder').value),
            status: document.getElementById('categoryStatus').value
        };

        try {
            let response;
            if (categoryId) {
                response = await categoryService.update(categoryId, data);
            } else {
                response = await categoryService.create(data);
            }

            if (response.success) {
                utils.toast(categoryId ? 'Category updated successfully' : 'Category created successfully', 'success');
                closeModal();
                loadCategories();
            }
        } catch (error) {
            utils.toast(error.message || 'Failed to save category', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Save Category';
        }
    });

    // Icon Picker Functions
    function openIconPicker() {
        renderIconGrid(allIcons);
        document.getElementById('iconPickerModal').classList.remove('hidden');
        document.getElementById('iconSearch').value = '';
        document.getElementById('iconSearch').focus();
    }

    function closeIconPicker() {
        document.getElementById('iconPickerModal').classList.add('hidden');
    }

    function renderIconGrid(icons) {
        const grid = document.getElementById('iconGrid');
        grid.innerHTML = icons.map(icon => `
            <button type="button" 
                onclick="selectIcon('${icon}')"
                class="flex flex-col items-center justify-center p-3 border border-gray-200 rounded-lg hover:bg-primary/10 hover:border-primary transition-all group"
                title="${icon}">
                <span class="material-symbols-outlined text-2xl text-gray-700 group-hover:text-primary">${icon}</span>
                <span class="text-xs text-gray-500 mt-1 truncate w-full text-center">${icon.substring(0, 8)}</span>
            </button>
        `).join('');
    }

    function selectIcon(iconName) {
        document.getElementById('categoryIcon').value = iconName;
        document.getElementById('selectedIconPreview').textContent = iconName;
        closeIconPicker();
    }

    function filterIcons() {
        const search = document.getElementById('iconSearch').value.toLowerCase();
        if (!search) {
            renderIconGrid(allIcons);
            return;
        }
        const filtered = allIcons.filter(icon => icon.toLowerCase().includes(search));
        renderIconGrid(filtered);
    }

    // Delete category
    async function deleteCategory(id) {
        if (!confirm('Are you sure you want to delete this category? Products will be unlinked.')) {
            return;
        }

        try {
            const response = await categoryService.delete(id);
            if (response.success) {
                utils.toast('Category deleted successfully', 'success');
                loadCategories();
            }
        } catch (error) {
            utils.toast(error.message || 'Failed to delete category', 'error');
        }
    }

    // Close modal
    function closeModal() {
        document.getElementById('categoryModal').classList.add('hidden');
    }

    // Store change handler
    document.getElementById('storeId').addEventListener('change', function() {
        loadParentCategories(this.value);
    });

    // Initialize
    loadStores();
</script>