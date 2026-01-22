<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Templates Management | Super Admin</title>
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
                    <a href="stores.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50">
                        <span class="material-symbols-outlined">storefront</span>
                        <span class="font-semibold">Stores</span>
                    </a>
                    <a href="templates.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-primary text-white">
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
                        <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Store Templates</h2>
                        <p class="text-gray-600">Manage store design templates and themes</p>
                    </div>
                    <button class="px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90 flex items-center gap-2">
                        <span class="material-symbols-outlined">add</span>
                        Add Template
                    </button>
                </div>

                <!-- Templates Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- CampMart Template -->
                    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="aspect-video bg-gradient-to-br from-primary to-primary/80 relative">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <div class="w-12 h-12 bg-accent rounded-lg mx-auto mb-3 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-primary text-2xl">shopping_bag</span>
                                    </div>
                                    <h3 class="text-xl font-bold">Store Name</h3>
                                    <p class="text-sm opacity-80">Premium Marketplace</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">CampMart Style</h3>
                                    <p class="text-gray-600 text-sm">Modern marketplace design</p>
                                </div>
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">Active</span>
                            </div>
                            <p class="text-gray-600 text-sm mb-6">Perfect for campus commerce and student marketplaces. Features clean design with modern aesthetics.</p>
                            <div class="flex gap-3">
                                <button class="flex-1 px-4 py-2 bg-primary text-white rounded-lg font-semibold hover:bg-primary/90">
                                    Use Template
                                </button>
                                <button class="px-4 py-2 border border-gray-200 rounded-lg font-semibold hover:bg-gray-50">
                                    Preview
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Minimal Template -->
                    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="aspect-video bg-gradient-to-br from-gray-800 to-gray-600 relative">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <div class="w-12 h-12 bg-white rounded-lg mx-auto mb-3 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-gray-800 text-2xl">storefront</span>
                                    </div>
                                    <h3 class="text-xl font-bold">Minimal Store</h3>
                                    <p class="text-sm opacity-80">Clean & Simple</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">Minimal Clean</h3>
                                    <p class="text-gray-600 text-sm">Simple and elegant design</p>
                                </div>
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-bold">Available</span>
                            </div>
                            <p class="text-gray-600 text-sm mb-6">Minimalist approach with focus on products. Great for professional and corporate stores.</p>
                            <div class="flex gap-3">
                                <button class="flex-1 px-4 py-2 bg-primary text-white rounded-lg font-semibold hover:bg-primary/90">
                                    Use Template
                                </button>
                                <button class="px-4 py-2 border border-gray-200 rounded-lg font-semibold hover:bg-gray-50">
                                    Preview
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Bold Template -->
                    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="aspect-video bg-gradient-to-br from-purple-600 to-pink-600 relative">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <div class="w-12 h-12 bg-yellow-400 rounded-lg mx-auto mb-3 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-purple-800 text-2xl">local_mall</span>
                                    </div>
                                    <h3 class="text-xl font-bold">Bold Store</h3>
                                    <p class="text-sm opacity-80">Eye-catching Design</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">Bold Modern</h3>
                                    <p class="text-gray-600 text-sm">Vibrant and dynamic layout</p>
                                </div>
                                <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-bold">Coming Soon</span>
                            </div>
                            <p class="text-gray-600 text-sm mb-6">Bold colors and modern typography. Perfect for fashion, lifestyle, and creative businesses.</p>
                            <div class="flex gap-3">
                                <button class="flex-1 px-4 py-2 bg-gray-200 text-gray-500 rounded-lg font-semibold cursor-not-allowed">
                                    Coming Soon
                                </button>
                                <button class="px-4 py-2 border border-gray-200 rounded-lg font-semibold hover:bg-gray-50">
                                    Preview
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Template Stats -->
                <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-2xl border border-gray-200">
                        <h3 class="text-2xl font-extrabold text-gray-900 mb-1">3</h3>
                        <p class="text-gray-600 text-sm font-medium">Total Templates</p>
                    </div>
                    <div class="bg-white p-6 rounded-2xl border border-gray-200">
                        <h3 class="text-2xl font-extrabold text-green-600 mb-1">2</h3>
                        <p class="text-gray-600 text-sm font-medium">Active Templates</p>
                    </div>
                    <div class="bg-white p-6 rounded-2xl border border-gray-200">
                        <h3 class="text-2xl font-extrabold text-blue-600 mb-1">156</h3>
                        <p class="text-gray-600 text-sm font-medium">Stores Using Templates</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>