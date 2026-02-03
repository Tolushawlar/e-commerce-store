<?php
$pageTitle = 'Store Settings';
$pageDescription = 'Configure your store display settings';
include '../shared/header-client.php';
?>

<!-- Header Actions -->
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-4">
        <!-- Store Filter -->
        <select id="storeFilter" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary">
            <option value="">Loading stores...</option>
        </select>
    </div>
</div>

<!-- Settings Form -->
<div id="settingsContainer" class="hidden">
    <div class="bg-white rounded-xl border border-gray-200 p-8 max-w-3xl">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Display Settings</h2>

        <form id="settingsForm" class="space-y-6">
            <input type="hidden" id="storeId">

            <!-- Category Grouping -->
            <div class="border-b border-gray-200 pb-6">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Group Products by Categories</h3>
                        <p class="text-gray-600 text-sm">Display products in organized sections based on their categories. When disabled, all products will be shown in a single grid.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer ml-4">
                        <input type="checkbox" id="groupByCategory" class="sr-only peer">
                        <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-primary"></div>
                    </label>
                </div>
            </div>

            <!-- Show Category Images -->
            <div class="border-b border-gray-200 pb-6">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Show Category Icons</h3>
                        <p class="text-gray-600 text-sm">Display category icons and descriptions in category sections. Only applicable when products are grouped by categories.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer ml-4">
                        <input type="checkbox" id="showCategoryImages" class="sr-only peer">
                        <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-primary"></div>
                    </label>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex justify-end pt-4">
                <button type="submit" id="saveBtn"
                    class="px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90 flex items-center gap-2">
                    <span class="material-symbols-outlined">save</span>
                    Save Settings
                </button>
            </div>
        </form>

        <!-- Regenerate Store -->
        <div class="mt-8 p-6 bg-amber-50 border border-amber-200 rounded-xl">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-amber-600 mt-1">info</span>
                <div class="flex-1">
                    <h4 class="font-semibold text-amber-900 mb-2">Apply Changes</h4>
                    <p class="text-amber-800 text-sm mb-4">After saving settings, you need to regenerate your store for the changes to take effect on your live store.</p>
                    <button onclick="regenerateStore()" id="regenerateBtn"
                        class="px-4 py-2 bg-amber-600 text-white rounded-lg font-semibold hover:bg-amber-700 flex items-center gap-2">
                        <span class="material-symbols-outlined">refresh</span>
                        Regenerate Store
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Empty State -->
<div id="emptyState" class="bg-white rounded-xl border border-gray-200 p-12 text-center">
    <span class="material-symbols-outlined text-6xl text-gray-300">store</span>
    <h3 class="mt-4 text-lg font-medium text-gray-900">Select a Store</h3>
    <p class="mt-2 text-gray-500">Choose a store to configure its display settings</p>
</div>

<?php include '../shared/footer-client.php'; ?>

<script src="/assets/js/services/store.service.js"></script>

<script>
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

            const storeFilter = document.getElementById('storeFilter');
            let options = '<option value="">Select a store</option>';
            userStores.forEach(store => {
                options += `<option value="${store.id}">${store.store_name}</option>`;
            });

            storeFilter.innerHTML = options;

            // Auto-select if only one store
            if (userStores.length === 1) {
                storeFilter.value = userStores[0].id;
                loadStoreSettings(userStores[0].id);
            }
        } catch (error) {
            console.error('Error loading stores:', error);
            utils.toast('Failed to load stores', 'error');
        }
    }

    // Store filter change handler
    document.getElementById('storeFilter').addEventListener('change', function() {
        const storeId = this.value;
        if (storeId) {
            loadStoreSettings(storeId);
        } else {
            document.getElementById('settingsContainer').classList.add('hidden');
            document.getElementById('emptyState').classList.remove('hidden');
        }
    });

    // Load store settings
    async function loadStoreSettings(storeId) {
        try {
            const response = await storeService.getById(storeId);
            const store = response.data;

            document.getElementById('storeId').value = store.id;
            document.getElementById('groupByCategory').checked = store.group_by_category || false;
            document.getElementById('showCategoryImages').checked = store.show_category_images !== false;

            document.getElementById('settingsContainer').classList.remove('hidden');
            document.getElementById('emptyState').classList.add('hidden');
        } catch (error) {
            console.error('Error loading store settings:', error);
            utils.toast('Failed to load store settings', 'error');
        }
    }

    // Save settings
    document.getElementById('settingsForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const saveBtn = document.getElementById('saveBtn');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="material-symbols-outlined animate-spin">refresh</span> Saving...';

        const storeId = document.getElementById('storeId').value;
        const data = {
            group_by_category: document.getElementById('groupByCategory').checked,
            show_category_images: document.getElementById('showCategoryImages').checked
        };

        try {
            const response = await storeService.update(storeId, data);
            if (response.success) {
                utils.toast('Settings saved successfully!', 'success');
            }
        } catch (error) {
            console.error('Error saving settings:', error);
            utils.toast(error.message || 'Failed to save settings', 'error');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<span class="material-symbols-outlined">save</span> Save Settings';
        }
    });

    // Regenerate store
    async function regenerateStore() {
        const storeId = document.getElementById('storeId').value;
        if (!storeId) return;

        const regenerateBtn = document.getElementById('regenerateBtn');
        regenerateBtn.disabled = true;
        regenerateBtn.innerHTML = '<span class="material-symbols-outlined animate-spin">refresh</span> Regenerating...';

        try {
            const response = await api.post(`/api/stores/${storeId}/generate`);

            if (response.success) {
                utils.toast('Store regenerated successfully!', 'success');
            } else {
                throw new Error(response.message || 'Failed to regenerate store');
            }
        } catch (error) {
            console.error('Error regenerating store:', error);
            utils.toast(error.message || 'Failed to regenerate store', 'error');
        } finally {
            regenerateBtn.disabled = false;
            regenerateBtn.innerHTML = '<span class="material-symbols-outlined">refresh</span> Regenerate Store';
        }
    }

    // Initialize
    loadStores();
</script>