<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Dashboard | Ecommerce Platform</title>
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
                        <span class="material-symbols-outlined text-xl font-bold">storefront</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-extrabold text-primary">My Store</h1>
                        <p class="text-xs text-gray-500">Dashboard</p>
                    </div>
                </div>
                
                <nav class="space-y-2">
                    <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-primary text-white">
                        <span class="material-symbols-outlined">dashboard</span>
                        <span class="font-semibold">Overview</span>
                    </a>
                    <a href="products.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50">
                        <span class="material-symbols-outlined">inventory</span>
                        <span class="font-semibold">Products</span>
                    </a>
                    <a href="orders.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50">
                        <span class="material-symbols-outlined">shopping_cart</span>
                        <span class="font-semibold">Orders</span>
                    </a>
                    <a href="customers.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50">
                        <span class="material-symbols-outlined">people</span>
                        <span class="font-semibold">Customers</span>
                    </a>
                    <a href="analytics.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50">
                        <span class="material-symbols-outlined">analytics</span>
                        <span class="font-semibold">Analytics</span>
                    </a>
                    <a href="settings.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50">
                        <span class="material-symbols-outlined">settings</span>
                        <span class="font-semibold">Settings</span>
                    </a>
                </nav>

                <div class="mt-8 p-4 bg-primary/5 rounded-2xl">
                    <p class="text-xs text-primary font-bold uppercase tracking-wider mb-2">Store Status</p>
                    <div class="flex justify-between items-end">
                        <p class="text-xl font-black text-primary">Active</p>
                        <span class="text-xs font-bold text-gray-500">24/7 Online</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-72 p-8">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Store Dashboard</h2>
                        <p class="text-gray-600">Manage your online store and track performance</p>
                    </div>
                    <div class="flex gap-4">
                        <button class="px-6 py-3 bg-white border border-gray-200 rounded-xl font-semibold text-gray-700 hover:bg-gray-50">
                            View Store
                        </button>
                        <button class="px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90">
                            Add Product
                        </button>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-2xl border border-gray-200 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-blue-600">inventory</span>
                            </div>
                            <span class="text-sm font-bold text-green-600">+5</span>
                        </div>
                        <h3 class="text-2xl font-extrabold text-gray-900 mb-1">42</h3>
                        <p class="text-gray-600 text-sm font-medium">Total Products</p>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-gray-200 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-green-600">shopping_cart</span>
                            </div>
                            <span class="text-sm font-bold text-green-600">+12</span>
                        </div>
                        <h3 class="text-2xl font-extrabold text-gray-900 mb-1">128</h3>
                        <p class="text-gray-600 text-sm font-medium">Orders This Month</p>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-gray-200 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-purple-600">payments</span>
                            </div>
                            <span class="text-sm font-bold text-green-600">+18%</span>
                        </div>
                        <h3 class="text-2xl font-extrabold text-gray-900 mb-1">₦245K</h3>
                        <p class="text-gray-600 text-sm font-medium">Revenue</p>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-gray-200 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-yellow-600">people</span>
                            </div>
                            <span class="text-sm font-bold text-green-600">+7</span>
                        </div>
                        <h3 class="text-2xl font-extrabold text-gray-900 mb-1">89</h3>
                        <p class="text-gray-600 text-sm font-medium">Customers</p>
                    </div>
                </div>

                <!-- Recent Orders & Top Products -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900">Recent Orders</h3>
                            <a href="orders.php" class="text-primary font-semibold text-sm hover:underline">View All</a>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div>
                                    <p class="font-semibold text-gray-900">#ORD-001</p>
                                    <p class="text-sm text-gray-600">John Doe • 2 items</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-primary">₦12,500</p>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Paid</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div>
                                    <p class="font-semibold text-gray-900">#ORD-002</p>
                                    <p class="text-sm text-gray-600">Sarah Adams • 1 item</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-primary">₦8,200</p>
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Pending</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900">Top Products</h3>
                            <a href="products.php" class="text-primary font-semibold text-sm hover:underline">Manage</a>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gray-200 rounded-lg"></div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">Vintage Denim Jacket</p>
                                    <p class="text-sm text-gray-600">24 sold this month</p>
                                </div>
                                <p class="font-bold text-primary">₦12,500</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gray-200 rounded-lg"></div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">Campus Sneakers</p>
                                    <p class="text-sm text-gray-600">18 sold this month</p>
                                </div>
                                <p class="font-bold text-primary">₦8,900</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>