<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stores Management | Super Admin</title>
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
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-72 bg-white border-r border-gray-200 fixed h-full">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 bg-primary text-accent rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl font-bold">admin_panel_settings</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-extrabold text-primary">Super Admin</h1>
                        <p class="text-xs text-gray-500">Platform Control</p>
                    </div>
                </div>
                
                <nav class="space-y-2">
                    <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50">
                        <span class="material-symbols-outlined">dashboard</span>
                        <span class="font-semibold">Dashboard</span>
                    </a>
                    <a href="clients.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50">
                        <span class="material-symbols-outlined">people</span>
                        <span class="font-semibold">Clients</span>
                    </a>
                    <a href="stores.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-primary text-white">
                        <span class="material-symbols-outlined">storefront</span>
                        <span class="font-semibold">Stores</span>
                    </a>
                    <a href="templates.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50">
                        <span class="material-symbols-outlined">palette</span>
                        <span class="font-semibold">Templates</span>
                    </a>
                    <a href="analytics.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50">
                        <span class="material-symbols-outlined">analytics</span>
                        <span class="font-semibold">Analytics</span>
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-72 p-8">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Stores Management</h2>
                        <p class="text-gray-600">Manage all client stores and their configurations</p>
                    </div>
                    <a href="create-store.php" class="px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90 flex items-center gap-2">
                        <span class="material-symbols-outlined">add</span>
                        Create New Store
                    </a>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-2xl border border-gray-200">
                        <h3 class="text-2xl font-extrabold text-gray-900 mb-1">189</h3>
                        <p class="text-gray-600 text-sm font-medium">Total Stores</p>
                    </div>
                    <div class="bg-white p-6 rounded-2xl border border-gray-200">
                        <h3 class="text-2xl font-extrabold text-green-600 mb-1">156</h3>
                        <p class="text-gray-600 text-sm font-medium">Active Stores</p>
                    </div>
                    <div class="bg-white p-6 rounded-2xl border border-gray-200">
                        <h3 class="text-2xl font-extrabold text-yellow-600 mb-1">23</h3>
                        <p class="text-gray-600 text-sm font-medium">Pending Setup</p>
                    </div>
                    <div class="bg-white p-6 rounded-2xl border border-gray-200">
                        <h3 class="text-2xl font-extrabold text-purple-600 mb-1">10</h3>
                        <p class="text-gray-600 text-sm font-medium">Suspended</p>
                    </div>
                </div>

                <!-- Stores Table -->
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xl font-bold text-gray-900">All Stores</h3>
                            <div class="flex gap-4">
                                <input type="text" placeholder="Search stores..." class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <select class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option>All Status</option>
                                    <option>Active</option>
                                    <option>Inactive</option>
                                    <option>Suspended</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">Store</th>
                                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">Client</th>
                                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">URL</th>
                                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">Status</th>
                                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">Created</th>
                                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200" id="stores-table">
                                <!-- Stores will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function loadStores() {
            fetch('../api/stores.php')
                .then(response => response.json())
                .then(stores => {
                    const tbody = document.getElementById('stores-table');
                    tbody.innerHTML = stores.map(store => `
                        <tr>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center text-white">
                                        <span class="material-symbols-outlined">storefront</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">${store.store_name}</p>
                                        <p class="text-sm text-gray-600">${store.description || 'No description'}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-900">${store.client_name}</td>
                            <td class="px-6 py-4">
                                <a href="../stores/${store.store_slug}/" class="text-primary hover:underline" target="_blank">${store.store_slug}</a>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase ${getStatusColor(store.status)}">
                                    ${store.status}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">${new Date(store.created_at).toLocaleDateString()}</td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <button onclick="editStore(${store.id})" class="p-2 text-gray-400 hover:text-primary" title="Edit">
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                    </button>
                                    <button onclick="viewStore('${store.store_slug}')" class="p-2 text-gray-400 hover:text-blue-500" title="View">
                                        <span class="material-symbols-outlined text-sm">visibility</span>
                                    </button>
                                    <button onclick="deleteStore(${store.id})" class="p-2 text-gray-400 hover:text-red-500" title="Delete">
                                        <span class="material-symbols-outlined text-sm">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `).join('');
                })
                .catch(error => {
                    console.error('Error loading stores:', error);
                });
        }

        function getStatusColor(status) {
            switch(status) {
                case 'active': return 'bg-green-100 text-green-800';
                case 'inactive': return 'bg-gray-100 text-gray-800';
                case 'suspended': return 'bg-red-100 text-red-800';
                default: return 'bg-gray-100 text-gray-800';
            }
        }

        function editStore(id) {
            window.location.href = `customize-store.php?id=${id}`;
        }

        function viewStore(slug) {
            window.open(`../stores/${slug}/`, '_blank');
        }

        function deleteStore(id) {
            if (confirm('Are you sure you want to delete this store?')) {
                fetch(`../api/stores.php/${id}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert('Store deleted successfully!');
                        loadStores();
                    } else {
                        alert('Error deleting store');
                    }
                });
            }
        }

        loadStores();
    </script>
</body>
</html>