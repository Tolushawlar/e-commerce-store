<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce Platform - Discover Amazing Stores</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 text-white rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined font-bold">storefront</span>
                    </div>
                    <h1 class="text-xl font-bold text-gray-900">E-commerce Platform</h1>
                </div>
                <div class="flex items-center gap-4">
                    <a href="/auth/login.php" class="text-gray-600 hover:text-gray-900 text-sm font-medium">Login</a>
                    <a href="/auth/register.php" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        Sign Up
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Discover Amazing Online Stores</h2>
            <p class="text-xl text-gray-600 mb-8">Browse thousands of products from various stores</p>
            <div class="max-w-2xl mx-auto">
                <div class="flex gap-2">
                    <input type="text" id="searchInput" placeholder="Search stores or products..."
                        class="flex-1 px-6 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <button onclick="searchStores()" class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                        Search
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div id="loadingState" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
            <p class="text-gray-600">Loading stores...</p>
        </div>

        <!-- Stores Grid -->
        <div id="storesGrid" class="hidden">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">Featured Stores</h3>
            <div id="storesList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Stores will be loaded here -->
            </div>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="hidden text-center py-12">
            <span class="material-symbols-outlined text-gray-300 text-6xl mb-4">store</span>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No stores found</h3>
            <p class="text-gray-600">Check back later for new stores</p>
        </div>
    </main>

    <script src="/assets/js/api.js"></script>
    <script>
        const apiClient = new APIClient();

        document.addEventListener('DOMContentLoaded', () => {
            loadStores();
        });

        async function loadStores() {
            try {
                document.getElementById('loadingState').classList.remove('hidden');

                const response = await apiClient.get('/api/stores?status=active');

                document.getElementById('loadingState').classList.add('hidden');

                if (response.success && response.data.stores && response.data.stores.length > 0) {
                    renderStores(response.data.stores);
                    document.getElementById('storesGrid').classList.remove('hidden');
                } else {
                    document.getElementById('emptyState').classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error loading stores:', error);
                document.getElementById('loadingState').classList.add('hidden');
                document.getElementById('emptyState').classList.remove('hidden');
            }
        }

        function renderStores(stores) {
            const container = document.getElementById('storesList');
            container.innerHTML = '';

            stores.forEach(store => {
                const card = document.createElement('div');
                card.className = 'bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow cursor-pointer';
                card.onclick = () => window.location.href = `/store/view.php?id=${store.id}`;

                card.innerHTML = `
                    <div class="h-48 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-6xl">storefront</span>
                    </div>
                    <div class="p-6">
                        <h4 class="text-xl font-bold text-gray-900 mb-2">${store.name}</h4>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">${store.description || 'Discover amazing products'}</p>
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <span class="material-symbols-outlined text-sm mr-1">check_circle</span>
                                Active
                            </span>
                            <button class="text-blue-600 hover:text-blue-700 font-medium text-sm flex items-center gap-1">
                                Visit Store
                                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </button>
                        </div>
                    </div>
                `;
                container.appendChild(card);
            });
        }

        function searchStores() {
            const query = document.getElementById('searchInput').value;
            // Implement search functionality
            console.log('Searching for:', query);
        }
    </script>
</body>

</html>