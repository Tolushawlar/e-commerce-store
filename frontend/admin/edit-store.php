<?php
$pageTitle = 'Edit Store';
$pageDescription = 'Update store configuration and customization';
include '../shared/header-admin.php';

$storeId = $_GET['id'] ?? null;
if (!$storeId) {
    header('Location: /admin/stores.php');
    exit;
}
?>

<div class="mb-6">
    <a href="/admin/stores.php" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900">
        <span class="material-symbols-outlined">arrow_back</span>
        Back to Stores
    </a>
</div>

<div id="loadingState" class="flex items-center justify-center p-12">
    <span class="material-symbols-outlined animate-spin text-4xl text-primary">refresh</span>
</div>

<div id="editForm" class="hidden grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Form -->
    <div class="bg-white rounded-2xl p-8 border border-gray-200">
        <form id="updateStoreForm" class="space-y-6">
            <input type="hidden" id="store_id" value="<?php echo htmlspecialchars($storeId); ?>">

            <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Client *</label>
                <select id="client_id" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary">
                    <option value="">Select Client</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Store Name *</label>
                <input type="text" id="store_name" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary"
                    placeholder="My Awesome Store">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Store Slug *</label>
                <input type="text" id="store_slug" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary"
                    placeholder="my-awesome-store">
                <p class="text-xs text-gray-500 mt-1">URL-friendly name (lowercase, no spaces)</p>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Domain</label>
                <input type="text" id="domain"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary"
                    placeholder="example.com">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Description</label>
                <textarea id="description"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary h-24"
                    placeholder="Brief description of the store"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Primary Color</label>
                    <input type="color" id="primary_color" value="#064E3B"
                        class="w-full h-12 border border-gray-200 rounded-xl cursor-pointer">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Accent Color</label>
                    <input type="color" id="accent_color" value="#BEF264"
                        class="w-full h-12 border border-gray-200 rounded-xl cursor-pointer">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Template</label>
                <select id="template_id"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary">
                    <option value="">Loading templates...</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Status *</label>
                <select id="status" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="suspended">Suspended</option>
                </select>
            </div>

            <div class="flex gap-3">
                <button type="submit" id="submitBtn"
                    class="flex-1 px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90">
                    Update Store
                </button>
                <button type="button" onclick="window.location.href='/admin/stores.php'"
                    class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200">
                    Cancel
                </button>
            </div>
        </form>
    </div>

    <!-- Live Preview -->
    <div class="bg-white rounded-2xl p-8 border border-gray-200">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Live Preview</h3>
        <div id="storePreview" class="border border-gray-200 rounded-xl overflow-hidden">
            <div id="previewHeader" class="h-16 flex items-center px-4" style="background-color: #064E3B;">
                <div id="previewIcon" class="w-8 h-8 rounded-lg flex items-center justify-center text-white"
                    style="background-color: #BEF264; color: #064E3B;">
                    <span class="material-symbols-outlined text-lg">shopping_bag</span>
                </div>
                <span class="ml-2 text-white font-bold" id="previewStoreName">Store Name</span>
            </div>
            <div id="previewHero" class="p-6 text-center" style="background: linear-gradient(135deg, #064E3B, #065f46);">
                <h1 class="text-2xl font-bold text-white mb-2">Welcome to <span id="previewStoreTitle">Your Store</span>
                </h1>
                <p class="text-white/80 text-sm" id="previewDescription">Store description will appear here</p>
                <button type="button" id="previewButton" class="mt-4 px-6 py-2 rounded-lg font-semibold"
                    style="background-color: #BEF264; color: #064E3B;">Shop Now</button>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-100 rounded-lg p-4 text-center">
                        <div class="w-full h-20 bg-gray-200 rounded mb-2"></div>
                        <p class="text-sm font-semibold">Sample Product</p>
                        <p id="previewPrice1" class="font-bold" style="color: #064E3B;">₦12,500</p>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-4 text-center">
                        <div class="w-full h-20 bg-gray-200 rounded mb-2"></div>
                        <p class="text-sm font-semibold">Sample Product</p>
                        <p id="previewPrice2" class="font-bold" style="color: #064E3B;">₦8,900</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../shared/footer-admin.php'; ?>

<script src="/assets/js/services/store.service.js"></script>
<script src="/assets/js/services/client.service.js"></script>
<script src="/assets/js/services/template.service.js"></script>

<script>
    const storeId = <?php echo json_encode($storeId); ?>;

    // Load store data and clients
    async function loadStore() {
        try {
            const [storeResponse, clientsResponse, templatesResponse] = await Promise.all([
                storeService.getById(storeId),
                clientService.getAll({
                    limit: 1000
                }),
                templateService.getAll({
                    limit: 100
                })
            ]);

            // Populate clients dropdown
            const clientSelect = document.getElementById('client_id');
            clientSelect.innerHTML = '<option value="">Select Client</option>';

            if (clientsResponse.success && clientsResponse.data.clients) {
                clientsResponse.data.clients.forEach(client => {
                    const option = document.createElement('option');
                    option.value = client.id;
                    option.textContent = `${client.name} ${client.company_name ? '(' + client.company_name + ')' : ''}`;
                    clientSelect.appendChild(option);
                });
            }

            // Populate templates dropdown
            const templateSelect = document.getElementById('template_id');
            templateSelect.innerHTML = '<option value="">Select Template</option>';

            if (templatesResponse.success && templatesResponse.data.templates) {
                templatesResponse.data.templates.forEach(template => {
                    const option = document.createElement('option');
                    option.value = template.id;
                    option.textContent = template.name;
                    templateSelect.appendChild(option);
                });
            }

            // Populate form with store data
            const store = storeResponse.data;
            document.getElementById('client_id').value = store.client_id;
            document.getElementById('store_name').value = store.store_name;
            document.getElementById('store_slug').value = store.store_slug;
            document.getElementById('domain').value = store.domain || '';
            document.getElementById('description').value = store.description || '';
            document.getElementById('primary_color').value = store.primary_color || '#064E3B';
            document.getElementById('accent_color').value = store.accent_color || '#BEF264';
            document.getElementById('template_id').value = store.template_id || 1;
            document.getElementById('status').value = store.status;

            // Update preview
            updatePreview();

            // Show form, hide loading
            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('editForm').classList.remove('hidden');

        } catch (error) {
            console.error('Error loading store:', error);
            utils.toast('Failed to load store data', 'error');
            setTimeout(() => {
                window.location.href = '/admin/stores.php';
            }, 2000);
        }
    }

    // Update preview
    function updatePreview() {
        const name = document.getElementById('store_name').value || 'Store Name';
        const description = document.getElementById('description').value || 'Store description will appear here';
        const primaryColor = document.getElementById('primary_color').value;
        const accentColor = document.getElementById('accent_color').value;

        // Update text content
        document.getElementById('previewStoreName').textContent = name;
        document.getElementById('previewStoreTitle').textContent = name;
        document.getElementById('previewDescription').textContent = description;

        // Update colors
        document.getElementById('previewHeader').style.backgroundColor = primaryColor;
        document.getElementById('previewHero').style.background = `linear-gradient(135deg, ${primaryColor}, ${primaryColor}dd)`;
        document.getElementById('previewIcon').style.backgroundColor = accentColor;
        document.getElementById('previewIcon').style.color = primaryColor;
        document.getElementById('previewButton').style.backgroundColor = accentColor;
        document.getElementById('previewButton').style.color = primaryColor;
        document.getElementById('previewPrice1').style.color = primaryColor;
        document.getElementById('previewPrice2').style.color = primaryColor;
    }

    // Live preview event listeners
    document.getElementById('store_name').addEventListener('input', updatePreview);
    document.getElementById('description').addEventListener('input', updatePreview);
    document.getElementById('primary_color').addEventListener('input', updatePreview);
    document.getElementById('accent_color').addEventListener('input', updatePreview);

    // Auto-generate slug from name
    document.getElementById('store_name').addEventListener('input', function(e) {
        const slug = e.target.value
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        document.getElementById('store_slug').value = slug;
    });

    // Form submission
    document.getElementById('updateStoreForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="material-symbols-outlined animate-spin">refresh</span> Updating...';

        const data = {
            client_id: parseInt(document.getElementById('client_id').value),
            store_name: document.getElementById('store_name').value,
            store_slug: document.getElementById('store_slug').value,
            status: document.getElementById('status').value,
            primary_color: document.getElementById('primary_color').value,
            accent_color: document.getElementById('accent_color').value,
            template_id: parseInt(document.getElementById('template_id').value)
        };

        const domain = document.getElementById('domain').value;
        const description = document.getElementById('description').value;

        if (domain) data.domain = domain;
        if (description) data.description = description;

        try {
            const response = await storeService.update(storeId, data);

            if (response.success) {
                utils.toast('Store updated successfully!', 'success');
                setTimeout(() => {
                    window.location.href = '/admin/stores.php';
                }, 1500);
            }
        } catch (error) {
            utils.toast(error.message, 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Update Store';
        }
    });

    // Initialize
    loadStore();
</script>