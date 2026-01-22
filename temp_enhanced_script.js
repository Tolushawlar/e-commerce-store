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
            customCSS: '',
            sections: []
        };

        // Tab switching functionality
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active state from all tabs
            document.querySelectorAll('[id^="tab-"]').forEach(tab => {
                tab.classList.remove('text-primary', 'border-b-2', 'border-primary');
                tab.classList.add('text-gray-500');
            });
            
            // Show selected tab content
            document.getElementById(`content-${tabName}`).classList.remove('hidden');
            
            // Add active state to selected tab
            const activeTab = document.getElementById(`tab-${tabName}`);
            activeTab.classList.remove('text-gray-500');
            activeTab.classList.add('text-primary', 'border-b-2', 'border-primary');
        }

        // Section management
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
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Section Type</label>
                            <select class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                <option value="hero">Hero Banner</option>
                                <option value="featured_products">Featured Products</option>
                                <option value="categories">Categories</option>
                                <option value="testimonials">Testimonials</option>
                                <option value="newsletter">Newsletter</option>
                                <option value="custom">Custom Content</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Title</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Section title">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Content</label>
                            <textarea class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm h-20" placeholder="Section content"></textarea>
                        </div>
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Background Color</label>
                                <input type="color" class="w-full h-8 border border-gray-200 rounded">
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Text Color</label>
                                <input type="color" class="w-full h-8 border border-gray-200 rounded">
                            </div>
                        </div>
                        <label class="flex items-center">
                            <input type="checkbox" checked class="mr-2">
                            <span class="text-sm font-medium">Visible</span>
                        </label>
                    </div>
                </div>
            `;
            document.getElementById('sectionsContainer').insertAdjacentHTML('beforeend', sectionHTML);
        }

        function removeSection(sectionId) {
            document.querySelector(`[data-section-id="${sectionId}"]`).remove();
        }

        // Enhanced preview update
        function updatePreview() {
            const previewHTML = `
                <!DOCTYPE html>
                <html>
                <head>
                    <script src="https://cdn.tailwindcss.com"></script>
                    <link href="https://fonts.googleapis.com/css2?family=${storeData.fontFamily.replace(' ', '+')}:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
                    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
                    <style>
                        :root {
                            --primary: ${storeData.primaryColor};
                            --accent: ${storeData.accentColor};
                        }
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
                            <button class="px-8 py-3 font-bold text-lg btn-${storeData.buttonStyle}" style="background-color: var(--accent); color: var(--primary);">
                                Shop Now
                            </button>
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
                                            <button class="w-full mt-3 py-2 font-bold text-white btn-${storeData.buttonStyle}" style="background-color: var(--primary);">
                                                Add to Cart
                                            </button>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </section>

                    ${storeData.footerText ? `
                    <footer class="bg-gray-900 text-white py-8">
                        <div class="max-w-7xl mx-auto px-6 text-center">
                            <p>${storeData.footerText}</p>
                            <div class="flex justify-center gap-4 mt-4">
                                ${storeData.socialFacebook ? '<a href="' + storeData.socialFacebook + '" class="text-gray-400 hover:text-white">Facebook</a>' : ''}
                                ${storeData.socialInstagram ? '<a href="' + storeData.socialInstagram + '" class="text-gray-400 hover:text-white">Instagram</a>' : ''}
                                ${storeData.socialTwitter ? '<a href="' + storeData.socialTwitter + '" class="text-gray-400 hover:text-white">Twitter</a>' : ''}
                            </div>
                        </div>
                    </footer>
                    ` : ''}
                </body>
                </html>
            `;
            
            const iframe = document.getElementById('storePreview');
            iframe.srcdoc = previewHTML;
        }

        // Enhanced event listeners
        function setupEventListeners() {
            // Basic tab listeners
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

            // Design tab listeners
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

            document.getElementById('headerStyle').addEventListener('change', function(e) {
                storeData.headerStyle = e.target.value;
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

            // Feature checkboxes
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

            // Advanced tab listeners
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
        }

        // Load store data
        const urlParams = new URLSearchParams(window.location.search);
        const storeId = urlParams.get('id');
        
        if (storeId) {
            fetch(`../api/store-customization.php/${storeId}`)
                .then(response => response.json())
                .then(store => {
                    if (store) {
                        // Update storeData object
                        storeData.name = store.store_name || 'My Store';
                        storeData.tagline = store.tagline || 'Your premium marketplace';
                        storeData.description = store.description || 'Welcome to our amazing store';
                        storeData.primaryColor = store.primary_color || '#064E3B';
                        storeData.accentColor = store.accent_color || '#BEF264';
                        storeData.headerStyle = store.header_style || 'default';
                        storeData.productGrid = store.product_grid_columns || '4';
                        storeData.fontFamily = store.font_family || 'Plus Jakarta Sans';
                        storeData.buttonStyle = store.button_style || 'rounded';
                        storeData.showSearch = store.show_search !== false;
                        storeData.showCart = store.show_cart !== false;
                        storeData.showWishlist = store.show_wishlist === true;
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
                        document.getElementById('headerStyle').value = storeData.headerStyle;
                        document.getElementById('productGrid').value = storeData.productGrid;
                        document.getElementById('buttonStyle').value = storeData.buttonStyle;
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
                fetch(`../api/store-customization.php/${storeId}`, {
                    method: 'PUT',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        store_name: storeData.name,
                        store_slug: storeData.name.toLowerCase().replace(/\s+/g, '-'),
                        tagline: storeData.tagline,
                        description: storeData.description,
                        primary_color: storeData.primaryColor,
                        accent_color: storeData.accentColor,
                        header_style: storeData.headerStyle,
                        product_grid_columns: parseInt(storeData.productGrid),
                        font_family: storeData.fontFamily,
                        button_style: storeData.buttonStyle,
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
                saveChanges();
                alert('Store published successfully!');
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            setupEventListeners();
            updatePreview();
        });
    </script>
</body>
</html>