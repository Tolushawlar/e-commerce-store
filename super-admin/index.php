<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard | Ecommerce Platform</title>
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
                    <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-primary text-white">
                        <span class="material-symbols-outlined">dashboard</span>
                        <span class="font-semibold">Dashboard</span>
                    </a>
                    <a href="clients.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50">
                        <span class="material-symbols-outlined">people</span>
                        <span class="font-semibold">Clients</span>
                    </a>
                    <a href="stores.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50">
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
                <div class="mb-8">
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Platform Overview</h2>
                    <p class="text-gray-600">Monitor and manage your ecommerce platform</p>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-2xl border border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-blue-600">people</span>
                            </div>
                            <span class="text-sm font-bold text-green-600">+12%</span>
                        </div>
                        <h3 class="text-2xl font-extrabold text-gray-900 mb-1">247</h3>
                        <p class="text-gray-600 text-sm font-medium">Total Clients</p>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-green-600">storefront</span>
                            </div>
                            <span class="text-sm font-bold text-green-600">+8%</span>
                        </div>
                        <h3 class="text-2xl font-extrabold text-gray-900 mb-1">189</h3>
                        <p class="text-gray-600 text-sm font-medium">Active Stores</p>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-purple-600">shopping_cart</span>
                            </div>
                            <span class="text-sm font-bold text-green-600">+23%</span>
                        </div>
                        <h3 class="text-2xl font-extrabold text-gray-900 mb-1">1,429</h3>
                        <p class="text-gray-600 text-sm font-medium">Total Orders</p>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-yellow-600">payments</span>
                            </div>
                            <span class="text-sm font-bold text-green-600">+15%</span>
                        </div>
                        <h3 class="text-2xl font-extrabold text-gray-900 mb-1">₦2.4M</h3>
                        <p class="text-gray-600 text-sm font-medium">Revenue</p>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Recent Clients</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white font-bold">
                                        JD
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">John Doe</p>
                                        <p class="text-sm text-gray-600">Fashion Store</p>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-500">2 hours ago</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                                        SA
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">Sarah Adams</p>
                                        <p class="text-sm text-gray-600">Electronics Hub</p>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-500">5 hours ago</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Platform Health</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Server Status</span>
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">Online</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Database</span>
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">Healthy</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">API Response</span>
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">125ms</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>