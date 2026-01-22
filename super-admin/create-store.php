<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Store | Super Admin</title>
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
    <div class="max-w-4xl mx-auto p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Create New Store</h1>
            <p class="text-gray-600">Set up a custom ecommerce store for your client</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Form -->
            <div class="bg-white rounded-2xl p-8 border border-gray-200">
                <form id="createStoreForm" class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Client</label>
                        <select name="client_id" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary">
                            <option value="">Select Client</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Store Name</label>
                        <input type="text" name="store_name" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary" placeholder="My Awesome Store">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Store URL</label>
                        <div class="flex">
                            <span class="px-4 py-3 bg-gray-100 border border-r-0 border-gray-200 rounded-l-xl text-gray-600">yoursite.com/</span>
                            <input type="text" name="store_slug" required class="flex-1 px-4 py-3 border border-gray-200 rounded-r-xl focus:ring-2 focus:ring-primary" placeholder="my-store">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Description</label>
                        <textarea name="description" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary h-24" placeholder="Brief description of the store"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Primary Color</label>
                            <input type="color" name="primary_color" value="#064E3B" class="w-full h-12 border border-gray-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Accent Color</label>
                            <input type="color" name="accent_color" value="#BEF264" class="w-full h-12 border border-gray-200 rounded-xl">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Template</label>
                        <select name="template_id" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary">
                            <option value="1">CampMart Style</option>
                            <option value="2">Minimal Clean</option>
                            <option value="3">Bold Modern</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90">
                        Create Store
                    </button>
                </form>
            </div>

            <!-- Live Preview -->
            <div class="bg-white rounded-2xl p-8 border border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Live Preview</h3>
                <div id="storePreview" class="border border-gray-200 rounded-xl overflow-hidden">
                    <div class="h-16 flex items-center px-4" style="background-color: #064E3B;">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white" style="background-color: #BEF264; color: #064E3B;">
                            <span class="material-symbols-outlined text-lg">shopping_bag</span>
                        </div>
                        <span class="ml-2 text-white font-bold" id="previewStoreName">Store Name</span>
                    </div>
                    <div class="p-6 text-center" style="background: linear-gradient(135deg, #064E3B, #065f46);">
                        <h1 class="text-2xl font-bold text-white mb-2">Welcome to <span id="previewStoreTitle">Your Store</span></h1>
                        <p class="text-white/80 text-sm" id="previewDescription">Store description will appear here</p>
                        <button class="mt-4 px-6 py-2 rounded-lg font-semibold" style="background-color: #BEF264; color: #064E3B;">Shop Now</button>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-100 rounded-lg p-4 text-center">
                                <div class="w-full h-20 bg-gray-200 rounded mb-2"></div>
                                <p class="text-sm font-semibold">Sample Product</p>
                                <p class="text-primary font-bold">₦12,500</p>
                            </div>
                            <div class="bg-gray-100 rounded-lg p-4 text-center">
                                <div class="w-full h-20 bg-gray-200 rounded mb-2"></div>
                                <p class="text-sm font-semibold">Sample Product</p>
                                <p class="text-primary font-bold">₦8,900</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load clients
        fetch('../api/clients.php')
            .then(response => response.json())
            .then(clients => {
                const select = document.querySelector('select[name="client_id"]');
                select.innerHTML = '<option value="">Select Client</option>';
                clients.forEach(client => {
                    const option = document.createElement('option');
                    option.value = client.id;
                    option.textContent = `${client.name} (${client.company_name || client.email})`;
                    select.appendChild(option);
                });
            });

        // Live preview updates
        document.querySelector('input[name="store_name"]').addEventListener('input', function(e) {
            document.getElementById('previewStoreName').textContent = e.target.value || 'Store Name';
            document.getElementById('previewStoreTitle').textContent = e.target.value || 'Your Store';
        });

        document.querySelector('textarea[name="description"]').addEventListener('input', function(e) {
            document.getElementById('previewDescription').textContent = e.target.value || 'Store description will appear here';
        });

        document.querySelector('input[name="primary_color"]').addEventListener('input', function(e) {
            const preview = document.getElementById('storePreview');
            preview.querySelector('.h-16').style.backgroundColor = e.target.value;
            preview.querySelector('.p-6').style.background = `linear-gradient(135deg, ${e.target.value}, ${e.target.value}dd)`;
        });

        document.querySelector('input[name="accent_color"]').addEventListener('input', function(e) {
            const preview = document.getElementById('storePreview');
            const icon = preview.querySelector('.w-8');
            const button = preview.querySelector('button');
            icon.style.backgroundColor = e.target.value;
            button.style.backgroundColor = e.target.value;
        });

        // Form submission
        document.getElementById('createStoreForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            fetch('../api/stores.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Store created successfully!');
                    window.location.href = `customize-store.php?id=${result.id}`;
                }
            });
        });
    </script>
</body>
</html>