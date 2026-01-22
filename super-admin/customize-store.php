<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Store Customization | Super Admin</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "primary": "#064E3B",
                        "accent": "#BEF264",
                        "surface": "#F8FAFC",
                    },
                    fontFamily: {
                        "display": ["Plus Jakarta Sans", "sans-serif"]
                    },
                },
            },
        }
    </script>
</head>
<body class="bg-surface font-display">
    <div class="flex h-screen">
        <!-- Enhanced Customization Panel -->
        <div class="w-96 bg-white border-r border-gray-200 overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Advanced Store Customization</h2>
                    <button onclick="saveChanges()" class="px-4 py-2 bg-primary text-white rounded-lg font-semibold text-sm">
                        Save
                    </button>
                </div>

                <!-- Tabs -->
                <div class="flex mb-6 border-b border-gray-200">
                    <button onclick="switchTab('basic')" id="tab-basic" class="px-4 py-2 font-semibold text-primary border-b-2 border-primary">
                        Basic
                    </button>
                    <button onclick="switchTab('design')" id="tab-design" class="px-4 py-2 font-semibold text-gray-500">
                        Design
                    </button>
                    <button onclick="switchTab('sections')" id="tab-sections" class="px-4 py-2 font-semibold text-gray-500">
                        Sections
                    </button>
                    <button onclick="switchTab('advanced')" id="tab-advanced" class="px-4 py-2 font-semibold text-gray-500">
                        Advanced
                    </button>
                </div>

                <!-- Basic Tab Content -->
                <div id="content-basic" class="tab-content">
                    <!-- Store Info -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Store Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Store Name</label>
                                <input type="text" id="storeName" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary" value="My Store">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Tagline</label>
                                <input type="text" id="storeTagline" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary" value="Your premium marketplace">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                                <textarea id="storeDescription" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary h-20">Welcome to our amazing store</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Logo & Images -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Logo & Images</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Store Logo</label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                                    <input type="file" id="logoUpload" accept="image/*" class="hidden">
                                    <button onclick="document.getElementById('logoUpload').click()" class="text-primary font-semibold">
                                        Upload Logo
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Hero Background</label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                                    <input type="file" id="heroUpload" accept="image/*" class="hidden">
                                    <button onclick="document.getElementById('heroUpload').click()" class="text-primary font-semibold">
                                        Upload Background
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Design Tab Content -->
                <div id="content-design" class="tab-content hidden">
                    <!-- Colors -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Brand Colors</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Primary Color</label>
                                <div class="flex gap-2">
                                    <input type="color" id="primaryColor" value="#064E3B" class="w-12 h-10 border border-gray-200 rounded">
                                    <input type="text" id="primaryColorHex" value="#064E3B" class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Accent Color</label>
                                <div class="flex gap-2">
                                    <input type="color" id="accentColor" value="#BEF264" class="w-12 h-10 border border-gray-200 rounded">
                                    <input type="text" id="accentColorHex" value="#BEF264" class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Typography -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Typography</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Font Family</label>
                                <select id="fontFamily" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                                    <option value="Plus Jakarta Sans">Plus Jakarta Sans</option>
                                    <option value="Inter">Inter</option>
                                    <option value="Poppins">Poppins</option>
                                    <option value="Roboto">Roboto</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Layout -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Layout Options</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Header Style</label>
                                <select id="headerStyle" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                                    <option value="default">Default</option>
                                    <option value="centered">Centered</option>
                                    <option value="minimal">Minimal</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Product Grid</label>
                                <select id="productGrid" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                                    <option value="3">3 Columns</option>
                                    <option value="4" selected>4 Columns</option>
                                    <option value="5">5 Columns</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Button Style</label>
                                <select id="buttonStyle" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                                    <option value="rounded">Rounded</option>
                                    <option value="square">Square</option>
                                    <option value="pill">Pill</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Features -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Store Features</h3>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" id="showSearch" checked class="mr-3">
                                <span class="text-sm font-medium">Show Search Bar</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" id="showCart" checked class="mr-3">
                                <span class="text-sm font-medium">Show Shopping Cart</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" id="showWishlist" class="mr-3">
                                <span class="text-sm font-medium">Show Wishlist</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Sections Tab Content -->
                <div id="content-sections" class="tab-content hidden">
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Page Sections</h3>
                        <button onclick="addSection()" class="px-4 py-2 bg-primary text-white rounded-lg font-semibold text-sm">
                            Add Section
                        </button>
                    </div>
                    <div id="sectionsContainer" class="space-y-4">
                        <!-- Sections will be dynamically added here -->
                    </div>
                </div>

                <!-- Advanced Tab Content -->
                <div id="content-advanced" class="tab-content hidden">
                    <!-- Social Media -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Social Media</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Facebook URL</label>
                                <input type="url" id="socialFacebook" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Instagram URL</label>
                                <input type="url" id="socialInstagram" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Twitter URL</label>
                                <input type="url" id="socialTwitter" class="w-full px-3 py-2 border border-gray-200 rounded-lg">
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Footer</h3>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Footer Text</label>
                            <textarea id="footerText" class="w-full px-3 py-2 border border-gray-200 rounded-lg h-20" placeholder="© 2024 Your Store. All rights reserved."></textarea>
                        </div>
                    </div>

                    <!-- Custom CSS -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Custom CSS</h3>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Additional Styles</label>
                            <textarea id="customCSS" class="w-full px-3 py-2 border border-gray-200 rounded-lg h-32 font-mono text-sm" placeholder="/* Add your custom CSS here */"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="space-y-2">
                    <button onclick="previewStore()" class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200">
                        Preview Store
                    </button>
                    <button onclick="publishStore()" class="w-full px-4 py-2 bg-primary text-white rounded-lg font-semibold hover:bg-primary/90">
                        Publish Store
                    </button>
                </div>
            </div>
        </div>

        <!-- Live Preview -->
        <div class="flex-1 bg-gray-100">
            <div class="h-full overflow-auto">
                <iframe id="storePreview" src="about:blank" class="w-full h-full border-0"></iframe>
            </div>
        </div>
    </div>

    <script>
        let storeData = {
            name: 'My Store',
            tagline: 'Your premium marketplace',
            description: 'Welcome to our amazing store',
            primaryColor: '#064E3B',
            accentColor: '#BEF264',
            headerStyle: 'default',
            productGrid: '4',
            fontFamily: 'Plus Jakarta Sans',
            buttonStyle: 'rounded',
            showSearch: true,
            showCart: true,
            showWishlist: false,
            socialFacebook: '',
            socialInstagram: '',
            socialTwitter: '',
            footerText: '',
            customCSS: ''
        };

        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
            document.querySelectorAll('[id^="tab-"]').forEach(tab => {
                tab.classList.remove('text-primary', 'border-b-2', 'border-primary');
                tab.classList.add('text-gray-500');
            });
            document.getElementById(`content-${tabName}`).classList.remove('hidden');
            const activeTab = document.getElementById(`tab-${tabName}`);
            activeTab.classList.remove('text-gray-500');
            activeTab.classList.add('text-primary', 'border-b-2', 'border-primary');
        }

        function addSection() {
            const sectionId = Date.now();
            const sectionHTML = `
                <div class="border border-gray-200 rounded-lg p-4" data-section-id="${sectionId}">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-bold text-gray-800">New Section</h4>
                        <button onclick="removeSection(${sectionId})" class="text-red-500 hover:text-red-700">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </div>
                    <div class="space-y-3">
                        <select class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            <option value="hero">Hero Banner</option>
                            <option value="featured_products">Featured Products</option>
                            <option value="categories">Categories</option>
                        </select>
                        <input type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Section title">
                        <textarea class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm h-20" placeholder="Section content"></textarea>
                    </div>
                </div>
            `;
            document.getElementById('sectionsContainer').insertAdjacentHTML('beforeend', sectionHTML);
        }

        function removeSection(sectionId) {
            document.querySelector(`[data-section-id="${sectionId}"]`).remove();
        }

        function updatePreview() {
            const previewHTML = `
                <!DOCTYPE html>
                <html>
                <head>
                    <script src="https://cdn.tailwindcss.com"></script>
                    <link href="https://fonts.googleapis.com/css2?family=${storeData.fontFamily.replace(' ', '+')}:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
                    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
                    <style>
                        :root { --primary: ${storeData.primaryColor}; --accent: ${storeData.accentColor}; }
                        body { font-family: '${storeData.fontFamily}', sans-serif; }
                        .btn-${storeData.buttonStyle} {
                            ${storeData.buttonStyle === 'rounded' ? 'border-radius: 0.5rem;' : ''}
                            ${storeData.buttonStyle === 'square' ? 'border-radius: 0;' : ''}
                            ${storeData.buttonStyle === 'pill' ? 'border-radius: 9999px;' : ''}
                        }
                        ${storeData.customCSS}
                    </style>
                </head>
                <body class="bg-white">
                    <header class="sticky top-0 z-50 bg-white border-b border-gray-200">
                        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white" style="background-color: var(--primary);">
                                    <span class="material-symbols-outlined text-lg">shopping_bag</span>
                                </div>
                                <span class="text-xl font-bold" style="color: var(--primary);">${storeData.name}</span>
                            </div>
                            <div class="flex items-center gap-4">
                                ${storeData.showSearch ? '<button class="p-2 text-gray-600 hover:text-gray-900"><span class="material-symbols-outlined">search</span></button>' : ''}
                                ${storeData.showWishlist ? '<button class="p-2 text-gray-600 hover:text-gray-900"><span class="material-symbols-outlined">favorite</span></button>' : ''}
                                ${storeData.showCart ? '<button class="p-2 text-gray-600 hover:text-gray-900"><span class="material-symbols-outlined">shopping_cart</span></button>' : ''}
                            </div>
                        </div>
                    </header>
                    <section class="py-20 text-center text-white" style="background: linear-gradient(135deg, var(--primary), var(--primary)dd);">
                        <div class="max-w-4xl mx-auto px-6">
                            <h1 class="text-5xl font-bold mb-4">Welcome to ${storeData.name}</h1>
                            <p class="text-xl mb-8 opacity-90">${storeData.tagline}</p>
                            <p class="text-lg mb-8 opacity-80">${storeData.description}</p>
                            <button class="px-8 py-3 font-bold text-lg btn-${storeData.buttonStyle}" style="background-color: var(--accent); color: var(--primary);">Shop Now</button>
                        </div>
                    </section>
                    <section class="py-16">
                        <div class="max-w-7xl mx-auto px-6">
                            <h2 class="text-3xl font-bold mb-8" style="color: var(--primary);">Featured Products</h2>
                            <div class="grid grid-cols-${storeData.productGrid} gap-6">
                                ${Array(parseInt(storeData.productGrid)).fill().map((_, i) => `
                                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
                                        <div class="aspect-square bg-gray-200"></div>
                                        <div class="p-4">
                                            <h3 class="font-bold text-gray-800 mb-2">Sample Product ${i + 1}</h3>
                                            <p class="font-bold" style="color: var(--primary);">₦${(Math.random() * 50000 + 5000).toFixed(0)}</p>
                                            <button class="w-full mt-3 py-2 font-bold text-white btn-${storeData.buttonStyle}" style="background-color: var(--primary);">Add to Cart</button>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </section>
                    ${storeData.footerText ? `<footer class="bg-gray-900 text-white py-8"><div class="max-w-7xl mx-auto px-6 text-center"><p>${storeData.footerText}</p></div></footer>` : ''}
                </body>
                </html>
            `;
            document.getElementById('storePreview').srcdoc = previewHTML;
        }

        // Event listeners for real-time updates
        document.getElementById('storeName').addEventListener('input', function(e) {
            storeData.name = e.target.value;
            updatePreview();
        });

        document.getElementById('storeTagline').addEventListener('input', function(e) {
            storeData.tagline = e.target.value;
            updatePreview();
        });

        document.getElementById('storeDescription').addEventListener('input', function(e) {
            storeData.description = e.target.value;
            updatePreview();
        });

        document.getElementById('primaryColor').addEventListener('input', function(e) {
            storeData.primaryColor = e.target.value;
            document.getElementById('primaryColorHex').value = e.target.value;
            updatePreview();
        });

        document.getElementById('accentColor').addEventListener('input', function(e) {
            storeData.accentColor = e.target.value;
            document.getElementById('accentColorHex').value = e.target.value;
            updatePreview();
        });

        document.getElementById('fontFamily').addEventListener('change', function(e) {
            storeData.fontFamily = e.target.value;
            updatePreview();
        });

        document.getElementById('productGrid').addEventListener('change', function(e) {
            storeData.productGrid = e.target.value;
            updatePreview();
        });

        document.getElementById('buttonStyle').addEventListener('change', function(e) {
            storeData.buttonStyle = e.target.value;
            updatePreview();
        });

        document.getElementById('showSearch').addEventListener('change', function(e) {
            storeData.showSearch = e.target.checked;
            updatePreview();
        });

        document.getElementById('showCart').addEventListener('change', function(e) {
            storeData.showCart = e.target.checked;
            updatePreview();
        });

        document.getElementById('showWishlist').addEventListener('change', function(e) {
            storeData.showWishlist = e.target.checked;
            updatePreview();
        });

        document.getElementById('socialFacebook').addEventListener('input', function(e) {
            storeData.socialFacebook = e.target.value;
            updatePreview();
        });

        document.getElementById('socialInstagram').addEventListener('input', function(e) {
            storeData.socialInstagram = e.target.value;
            updatePreview();
        });

        document.getElementById('socialTwitter').addEventListener('input', function(e) {
            storeData.socialTwitter = e.target.value;
            updatePreview();
        });

        document.getElementById('footerText').addEventListener('input', function(e) {
            storeData.footerText = e.target.value;
            updatePreview();
        });

        document.getElementById('customCSS').addEventListener('input', function(e) {
            storeData.customCSS = e.target.value;
            updatePreview();
        });

        // Load store data if ID provided
        const urlParams = new URLSearchParams(window.location.search);
        const storeId = urlParams.get('id');
        
        if (storeId) {
            fetch('../api/store-customization.php/' + storeId)
                .then(response => response.json())
                .then(store => {
                    if (store && store.store_name) {
                        storeData.name = store.store_name;
                        storeData.tagline = store.tagline || 'Your premium marketplace';
                        storeData.description = store.description || 'Welcome to our amazing store';
                        storeData.primaryColor = store.primary_color || '#064E3B';
                        storeData.accentColor = store.accent_color || '#BEF264';
                        storeData.fontFamily = store.font_family || 'Plus Jakarta Sans';
                        storeData.buttonStyle = store.button_style || 'rounded';
                        storeData.productGrid = store.product_grid_columns || '4';
                        storeData.showSearch = store.show_search !== 0;
                        storeData.showCart = store.show_cart !== 0;
                        storeData.showWishlist = store.show_wishlist === 1;
                        storeData.socialFacebook = store.social_facebook || '';
                        storeData.socialInstagram = store.social_instagram || '';
                        storeData.socialTwitter = store.social_twitter || '';
                        storeData.footerText = store.footer_text || '';
                        storeData.customCSS = store.custom_css || '';
                        
                        // Update form fields
                        document.getElementById('storeName').value = storeData.name;
                        document.getElementById('storeTagline').value = storeData.tagline;
                        document.getElementById('storeDescription').value = storeData.description;
                        document.getElementById('primaryColor').value = storeData.primaryColor;
                        document.getElementById('primaryColorHex').value = storeData.primaryColor;
                        document.getElementById('accentColor').value = storeData.accentColor;
                        document.getElementById('accentColorHex').value = storeData.accentColor;
                        document.getElementById('fontFamily').value = storeData.fontFamily;
                        document.getElementById('buttonStyle').value = storeData.buttonStyle;
                        document.getElementById('productGrid').value = storeData.productGrid;
                        document.getElementById('showSearch').checked = storeData.showSearch;
                        document.getElementById('showCart').checked = storeData.showCart;
                        document.getElementById('showWishlist').checked = storeData.showWishlist;
                        document.getElementById('socialFacebook').value = storeData.socialFacebook;
                        document.getElementById('socialInstagram').value = storeData.socialInstagram;
                        document.getElementById('socialTwitter').value = storeData.socialTwitter;
                        document.getElementById('footerText').value = storeData.footerText;
                        document.getElementById('customCSS').value = storeData.customCSS;
                        
                        updatePreview();
                    }
                })
                .catch(error => {
                    console.error('Error loading store:', error);
                });
        }

        function saveChanges() {
            if (storeId) {
                fetch('../api/store-customization.php/' + storeId, {
                    method: 'PUT',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        store_name: storeData.name,
                        store_slug: storeData.name.toLowerCase().replace(/\s+/g, '-'),
                        tagline: storeData.tagline,
                        description: storeData.description,
                        primary_color: storeData.primaryColor,
                        accent_color: storeData.accentColor,
                        font_family: storeData.fontFamily,
                        button_style: storeData.buttonStyle,
                        product_grid_columns: parseInt(storeData.productGrid),
                        show_search: storeData.showSearch,
                        show_cart: storeData.showCart,
                        show_wishlist: storeData.showWishlist,
                        social_facebook: storeData.socialFacebook,
                        social_instagram: storeData.socialInstagram,
                        social_twitter: storeData.socialTwitter,
                        footer_text: storeData.footerText,
                        custom_css: storeData.customCSS,
                        status: 'active'
                    })
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert('Changes saved successfully!');
                    } else {
                        alert('Error saving changes: ' + (result.error || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error saving changes');
                });
            }
        }

        function previewStore() {
            if (storeId) {
                window.open(`../stores/store-${storeId}/`, '_blank');
            } else {
                alert('Please save the store first');
            }
        }

        function publishStore() {
            if (confirm('Are you sure you want to publish this store?')) {
                saveChanges(); // Save first, then publish
                alert('Store published successfully!');
            }
        }

        // Initialize preview
        document.addEventListener('DOMContentLoaded', function() {
            updatePreview();
        });
        updatePreview();
    </script>
</body>
</html>