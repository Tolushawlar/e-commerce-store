<?php
$pageTitle = 'Customize Store';
$pageDescription = 'Advanced store customization and branding';
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

<div id="customizeContent" class="hidden">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900" id="storeName">Store Customization</h1>
            <p class="text-gray-600" id="storeSlug"></p>
        </div>
        <div class="flex gap-3">
            <button onclick="previewStore()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 font-semibold">
                <span class="material-symbols-outlined inline-block align-middle">visibility</span>
                Preview
            </button>
            <button onclick="saveCustomization()" id="saveBtn" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 font-semibold">
                <span class="material-symbols-outlined inline-block align-middle">save</span>
                Save Changes
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Customization Panel -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Tabs -->
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="border-b border-gray-200 px-4">
                    <nav class="flex -mb-px space-x-8">
                        <button onclick="switchTab('basic')" id="tab-basic" class="py-4 px-1 border-b-2 border-primary text-primary font-medium">
                            Basic
                        </button>
                        <button onclick="switchTab('design')" id="tab-design" class="py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium">
                            Design
                        </button>
                        <button onclick="switchTab('advanced')" id="tab-advanced" class="py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium">
                            Advanced
                        </button>
                    </nav>
                </div>

                <!-- Basic Tab -->
                <div id="content-basic" class="tab-content p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Store Name *</label>
                        <input type="text" id="store_name" required
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary"
                            oninput="updatePreview()">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Tagline</label>
                        <input type="text" id="tagline"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary"
                            placeholder="Your premium marketplace"
                            oninput="updatePreview()">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Description</label>
                        <textarea id="description"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary h-24"
                            placeholder="Welcome to our amazing store"
                            oninput="updatePreview()"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Logo URL</label>
                        <input type="url" id="logo_url"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary"
                            placeholder="https://example.com/logo.png">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Hero Background URL</label>
                        <input type="url" id="hero_background_url"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary"
                            placeholder="https://example.com/hero.jpg">
                    </div>
                </div>

                <!-- Design Tab -->
                <div id="content-design" class="tab-content hidden p-6 space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Primary Color</label>
                            <div class="flex gap-2">
                                <input type="color" id="primary_color" value="#064E3B"
                                    class="h-10 w-16 border border-gray-200 rounded cursor-pointer"
                                    oninput="updatePreview()">
                                <input type="text" id="primary_color_hex" value="#064E3B"
                                    class="flex-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary"
                                    oninput="syncColorInput('primary')">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Accent Color</label>
                            <div class="flex gap-2">
                                <input type="color" id="accent_color" value="#BEF264"
                                    class="h-10 w-16 border border-gray-200 rounded cursor-pointer"
                                    oninput="updatePreview()">
                                <input type="text" id="accent_color_hex" value="#BEF264"
                                    class="flex-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary"
                                    oninput="syncColorInput('accent')">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Font Family</label>
                        <select id="font_family"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary"
                            onchange="updatePreview()">
                            <option value="Plus Jakarta Sans">Plus Jakarta Sans</option>
                            <option value="Inter">Inter</option>
                            <option value="Poppins">Poppins</option>
                            <option value="Roboto">Roboto</option>
                            <option value="Open Sans">Open Sans</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Header Style</label>
                        <select id="header_style"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary"
                            onchange="updatePreview()">
                            <option value="default">Default</option>
                            <option value="centered">Centered</option>
                            <option value="minimal">Minimal</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Button Style</label>
                        <select id="button_style"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary"
                            onchange="updatePreview()">
                            <option value="rounded">Rounded</option>
                            <option value="square">Square</option>
                            <option value="pill">Pill</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Product Grid Columns</label>
                        <select id="product_grid_columns"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary"
                            onchange="updatePreview()">
                            <option value="2">2 Columns</option>
                            <option value="3">3 Columns</option>
                            <option value="4">4 Columns</option>
                            <option value="5">5 Columns</option>
                            <option value="6">6 Columns</option>
                        </select>
                    </div>

                    <div class="space-y-3">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" id="show_search" class="rounded text-primary focus:ring-primary" onchange="updatePreview()">
                            <span class="text-sm font-medium text-gray-700">Show Search</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" id="show_cart" class="rounded text-primary focus:ring-primary" onchange="updatePreview()">
                            <span class="text-sm font-medium text-gray-700">Show Cart</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" id="show_wishlist" class="rounded text-primary focus:ring-primary" onchange="updatePreview()">
                            <span class="text-sm font-medium text-gray-700">Show Wishlist</span>
                        </label>
                    </div>
                </div>

                <!-- Advanced Tab -->
                <div id="content-advanced" class="tab-content hidden p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Footer Text</label>
                        <textarea id="footer_text"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary h-20"
                            placeholder="© 2024 Your Store. All rights reserved."></textarea>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-sm font-bold text-gray-900">Social Media Links</label>
                        <input type="url" id="social_facebook"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary"
                            placeholder="Facebook URL">
                        <input type="url" id="social_instagram"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary"
                            placeholder="Instagram URL">
                        <input type="url" id="social_twitter"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary"
                            placeholder="Twitter URL">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Custom CSS</label>
                        <textarea id="custom_css"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary font-mono text-sm h-32"
                            placeholder="/* Add your custom CSS here */"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Advanced: Add custom CSS to further customize your store</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Live Preview -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200 sticky top-6">
                <div class="p-4 border-b flex items-center justify-between">
                    <h3 class="font-bold text-gray-900">Live Preview</h3>
                    <span class="text-xs text-gray-500">Real-time preview</span>
                </div>
                <div class="p-4">
                    <div id="storePreview" class="border border-gray-200 rounded-lg overflow-hidden" style="height: calc(100vh - 250px);">
                        <!-- Preview content will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../shared/footer-admin.php'; ?>

<script src="/assets/js/services/store.service.js"></script>

<script>
    const storeId = <?php echo json_encode($storeId); ?>;
    let storeData = {};

    // Load store data
    async function loadStore() {
        try {
            const response = await storeService.getById(storeId);
            storeData = response.data;

            // Populate form fields
            document.getElementById('store_name').value = storeData.store_name || '';
            document.getElementById('tagline').value = storeData.tagline || '';
            document.getElementById('description').value = storeData.description || '';
            document.getElementById('logo_url').value = storeData.logo_url || '';
            document.getElementById('hero_background_url').value = storeData.hero_background_url || '';

            document.getElementById('primary_color').value = storeData.primary_color || '#064E3B';
            document.getElementById('primary_color_hex').value = storeData.primary_color || '#064E3B';
            document.getElementById('accent_color').value = storeData.accent_color || '#BEF264';
            document.getElementById('accent_color_hex').value = storeData.accent_color || '#BEF264';

            document.getElementById('font_family').value = storeData.font_family || 'Plus Jakarta Sans';
            document.getElementById('header_style').value = storeData.header_style || 'default';
            document.getElementById('button_style').value = storeData.button_style || 'rounded';
            document.getElementById('product_grid_columns').value = storeData.product_grid_columns || 4;

            document.getElementById('show_search').checked = storeData.show_search !== false;
            document.getElementById('show_cart').checked = storeData.show_cart !== false;
            document.getElementById('show_wishlist').checked = storeData.show_wishlist === true;

            document.getElementById('footer_text').value = storeData.footer_text || '';
            document.getElementById('social_facebook').value = storeData.social_facebook || '';
            document.getElementById('social_instagram').value = storeData.social_instagram || '';
            document.getElementById('social_twitter').value = storeData.social_twitter || '';
            document.getElementById('custom_css').value = storeData.custom_css || '';

            // Update header
            document.getElementById('storeName').textContent = `Customize: ${storeData.store_name}`;
            document.getElementById('storeSlug').textContent = storeData.store_slug;

            // Show content, hide loading
            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('customizeContent').classList.remove('hidden');

            // Update preview
            updatePreview();

        } catch (error) {
            console.error('Error loading store:', error);
            utils.toast('Failed to load store data', 'error');
            setTimeout(() => {
                window.location.href = '/admin/stores.php';
            }, 2000);
        }
    }

    // Switch tabs
    function switchTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
        document.querySelectorAll('[id^="tab-"]').forEach(tab => {
            tab.classList.remove('text-primary', 'border-primary');
            tab.classList.add('text-gray-500', 'border-transparent');
        });
        document.getElementById(`content-${tabName}`).classList.remove('hidden');
        const activeTab = document.getElementById(`tab-${tabName}`);
        activeTab.classList.remove('text-gray-500', 'border-transparent');
        activeTab.classList.add('text-primary', 'border-primary');
    }

    // Sync color inputs
    function syncColorInput(type) {
        const hexInput = document.getElementById(`${type}_color_hex`);
        const colorPicker = document.getElementById(`${type}_color`);
        colorPicker.value = hexInput.value;
        updatePreview();
    }

    // Update preview
    function updatePreview() {
        const name = document.getElementById('store_name').value || 'Store Name';
        const tagline = document.getElementById('tagline').value || 'Your premium marketplace';
        const description = document.getElementById('description').value || 'Welcome to our amazing store';
        const primaryColor = document.getElementById('primary_color').value;
        const accentColor = document.getElementById('accent_color').value;
        const fontFamily = document.getElementById('font_family').value;
        const buttonStyle = document.getElementById('button_style').value;
        const gridColumns = document.getElementById('product_grid_columns').value;
        const showSearch = document.getElementById('show_search').checked;
        const showCart = document.getElementById('show_cart').checked;
        const showWishlist = document.getElementById('show_wishlist').checked;

        // Sync hex inputs
        document.getElementById('primary_color_hex').value = primaryColor;
        document.getElementById('accent_color_hex').value = accentColor;

        let buttonClass = 'rounded-lg';
        if (buttonStyle === 'square') buttonClass = 'rounded-none';
        if (buttonStyle === 'pill') buttonClass = 'rounded-full';

        const previewHTML = `
            <!DOCTYPE html>
            <html>
            <head>
                <script src="https://cdn.tailwindcss.com"><\/script>
                <link href="https://fonts.googleapis.com/css2?family=${fontFamily.replace(' ', '+')}:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
                <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
                <style>
                    body { font-family: '${fontFamily}', sans-serif; }
                </style>
            </head>
            <body class="bg-white">
                <header class="sticky top-0 z-50 bg-white border-b border-gray-200">
                    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 ${buttonClass} flex items-center justify-center text-white" style="background-color: ${primaryColor};">
                                <span class="material-symbols-outlined text-lg">shopping_bag</span>
                            </div>
                            <span class="text-xl font-bold" style="color: ${primaryColor};">${name}</span>
                        </div>
                        <div class="flex items-center gap-4">
                            ${showSearch ? '<button class="p-2 text-gray-600"><span class="material-symbols-outlined">search</span></button>' : ''}
                            ${showWishlist ? '<button class="p-2 text-gray-600"><span class="material-symbols-outlined">favorite</span></button>' : ''}
                            ${showCart ? '<button class="p-2 text-gray-600"><span class="material-symbols-outlined">shopping_cart</span></button>' : ''}
                        </div>
                    </div>
                </header>

                <section class="py-20 text-center text-white" style="background: linear-gradient(135deg, ${primaryColor}, ${primaryColor}dd);">
                    <div class="max-w-4xl mx-auto px-6">
                        <h1 class="text-5xl font-bold mb-4">Welcome to ${name}</h1>
                        <p class="text-xl mb-8 opacity-90">${tagline}</p>
                        <p class="text-lg mb-8 opacity-80">${description}</p>
                        <button class="px-8 py-3 ${buttonClass} font-bold text-lg" style="background-color: ${accentColor}; color: ${primaryColor};">
                            Shop Now
                        </button>
                    </div>
                </section>

                <section class="py-16">
                    <div class="max-w-7xl mx-auto px-6">
                        <h2 class="text-3xl font-bold mb-8" style="color: ${primaryColor};">Featured Products</h2>
                        <div class="grid grid-cols-${gridColumns} gap-6">
                            ${generateSampleProducts(parseInt(gridColumns), buttonClass, primaryColor)}
                        </div>
                    </div>
                </section>
            </body>
            </html>
        `;

        document.getElementById('storePreview').innerHTML = `<iframe srcdoc="${previewHTML.replace(/"/g, '&quot;')}" class="w-full h-full border-0"></iframe>`;
    }

    // Generate sample products
    function generateSampleProducts(count, buttonClass, primaryColor) {
        let products = '';
        for (let i = 1; i <= count; i++) {
            const price = Math.floor(Math.random() * 45000) + 5000;
            products += `
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="aspect-square bg-gray-200"></div>
                    <div class="p-4">
                        <h3 class="font-bold text-gray-800 mb-2">Product ${i}</h3>
                        <p class="font-bold" style="color: ${primaryColor};">₦${price.toLocaleString()}</p>
                        <button class="w-full mt-3 py-2 ${buttonClass} font-bold text-white" style="background-color: ${primaryColor};">
                            Add to Cart
                        </button>
                    </div>
                </div>
            `;
        }
        return products;
    }

    // Save customization
    async function saveCustomization() {
        const saveBtn = document.getElementById('saveBtn');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="material-symbols-outlined animate-spin inline-block align-middle">refresh</span> Saving...';

        const data = {
            store_name: document.getElementById('store_name').value,
            tagline: document.getElementById('tagline').value,
            description: document.getElementById('description').value,
            logo_url: document.getElementById('logo_url').value,
            hero_background_url: document.getElementById('hero_background_url').value,
            primary_color: document.getElementById('primary_color').value,
            accent_color: document.getElementById('accent_color').value,
            font_family: document.getElementById('font_family').value,
            header_style: document.getElementById('header_style').value,
            button_style: document.getElementById('button_style').value,
            product_grid_columns: parseInt(document.getElementById('product_grid_columns').value),
            show_search: document.getElementById('show_search').checked,
            show_cart: document.getElementById('show_cart').checked,
            show_wishlist: document.getElementById('show_wishlist').checked,
            footer_text: document.getElementById('footer_text').value,
            social_facebook: document.getElementById('social_facebook').value,
            social_instagram: document.getElementById('social_instagram').value,
            social_twitter: document.getElementById('social_twitter').value,
            custom_css: document.getElementById('custom_css').value
        };

        try {
            const response = await storeService.update(storeId, data);
            if (response.success) {
                utils.toast('Store customization saved successfully!', 'success');
            }
        } catch (error) {
            utils.toast(error.message || 'Failed to save customization', 'error');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<span class="material-symbols-outlined inline-block align-middle">save</span> Save Changes';
        }
    }

    // Preview store
    function previewStore() {
        const previewWindow = window.open('', '_blank');
        const previewHTML = document.getElementById('storePreview').querySelector('iframe').srcdoc;
        previewWindow.document.write(previewHTML);
        previewWindow.document.close();
    }

    // Initialize
    loadStore();
</script>