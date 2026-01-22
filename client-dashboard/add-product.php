<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product | Store Dashboard</title>
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
                    <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50">
                        <span class="material-symbols-outlined">dashboard</span>
                        <span class="font-semibold">Overview</span>
                    </a>
                    <a href="products.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-primary text-white">
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
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-72 p-8">
            <div class="max-w-6xl mx-auto">
                <!-- Header -->
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Add New Product</h2>
                        <p class="text-gray-600">Create a new product listing for your store</p>
                    </div>
                    <div class="flex gap-4">
                        <button class="px-6 py-3 bg-white border border-gray-200 rounded-xl font-semibold text-gray-700 hover:bg-gray-50">
                            Discard
                        </button>
                        <button class="px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90">
                            Publish Product
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Form Column -->
                    <div class="lg:col-span-7 space-y-8">
                        <!-- Basic Information -->
                        <div class="bg-white rounded-2xl border border-gray-200 p-8">
                            <h3 class="text-xl font-bold text-primary mb-6 flex items-center gap-2">
                                <span class="material-symbols-outlined">info</span>
                                Basic Information
                            </h3>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-2">Product Title</label>
                                    <input type="text" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g. Vintage Denim Jacket - Limited Edition">
                                </div>
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-900 mb-2">Price (₦)</label>
                                        <div class="relative">
                                            <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-primary">₦</span>
                                            <input type="number" class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="0.00">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-900 mb-2">Category</label>
                                        <select class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent">
                                            <option>Fashion & Apparel</option>
                                            <option>Electronics</option>
                                            <option>Textbooks & Study</option>
                                            <option>Services</option>
                                            <option>Furniture</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-2">Description</label>
                                    <textarea class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent min-h-[120px] resize-none" placeholder="Describe your product condition, features, and why people should buy it..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Product Photos -->
                        <div class="bg-white rounded-2xl border border-gray-200 p-8">
                            <h3 class="text-xl font-bold text-primary mb-6 flex items-center gap-2">
                                <span class="material-symbols-outlined">image</span>
                                Product Photos
                            </h3>
                            <div class="border-2 border-dashed border-gray-300 rounded-2xl p-10 text-center hover:border-primary transition-colors">
                                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <span class="material-symbols-outlined text-primary text-3xl">upload_file</span>
                                </div>
                                <p class="text-lg font-bold text-gray-900 mb-2">Drag and drop images here</p>
                                <p class="text-gray-600 mb-6">PNG, JPG or WEBP (Max 5MB each)</p>
                                <button class="px-6 py-3 bg-white border border-gray-200 rounded-xl font-semibold hover:bg-gray-50">
                                    Browse Files
                                </button>
                            </div>
                            <div class="grid grid-cols-4 gap-4 mt-6">
                                <div class="aspect-square bg-gray-100 rounded-xl border-2 border-dashed border-gray-300 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-gray-400">add</span>
                                </div>
                                <div class="aspect-square bg-gray-100 rounded-xl border-2 border-dashed border-gray-300 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-gray-400">add</span>
                                </div>
                                <div class="aspect-square bg-gray-100 rounded-xl border-2 border-dashed border-gray-300 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-gray-400">add</span>
                                </div>
                                <div class="aspect-square bg-gray-100 rounded-xl border-2 border-dashed border-gray-300 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-gray-400">add</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Live Preview Column -->
                    <div class="lg:col-span-5">
                        <div class="sticky top-8">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-black text-primary uppercase tracking-widest">Live Preview</h3>
                                <span class="flex items-center gap-2 text-xs font-bold text-gray-500">
                                    <span class="w-2 h-2 rounded-full bg-accent animate-pulse"></span>
                                    Syncing Real-time
                                </span>
                            </div>
                            
                            <!-- Product Card Preview -->
                            <div class="bg-white rounded-3xl overflow-hidden shadow-xl border border-gray-200">
                                <div class="aspect-[4/5] bg-gray-200 relative">
                                    <div class="absolute top-4 left-4 flex flex-col gap-2">
                                        <span class="bg-primary/90 text-white text-xs font-bold py-1 px-3 rounded-full uppercase tracking-wider">Fashion</span>
                                        <span class="bg-accent text-primary text-xs font-black py-1 px-3 rounded-full uppercase tracking-wider">New</span>
                                    </div>
                                    <button class="absolute top-4 right-4 w-10 h-10 bg-white/20 backdrop-blur-lg rounded-full flex items-center justify-center text-white">
                                        <span class="material-symbols-outlined">favorite</span>
                                    </button>
                                </div>
                                <div class="p-8">
                                    <div class="flex justify-between items-start gap-4 mb-4">
                                        <h4 class="text-2xl font-bold leading-tight">Product Title</h4>
                                        <p class="text-2xl font-black text-primary whitespace-nowrap">₦0</p>
                                    </div>
                                    <p class="text-gray-600 text-sm leading-relaxed mb-6">
                                        Product description will appear here as you type...
                                    </p>
                                    <div class="flex items-center gap-4 border-t border-gray-200 pt-6">
                                        <div class="w-10 h-10 bg-primary rounded-full"></div>
                                        <div>
                                            <p class="text-xs font-bold text-gray-900">Your Store</p>
                                            <p class="text-xs text-gray-600 flex items-center gap-1">
                                                <span class="material-symbols-outlined text-xs text-accent">star</span>
                                                4.9 (24 sales)
                                            </p>
                                        </div>
                                        <button class="ml-auto flex items-center gap-2 bg-primary/5 hover:bg-primary/10 text-primary px-4 py-2 rounded-xl text-xs font-black transition-colors">
                                            VIEW PROFILE
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Selling Tip -->
                            <div class="mt-6 p-4 bg-accent/10 border border-accent/30 rounded-2xl flex items-start gap-3">
                                <span class="material-symbols-outlined text-primary">lightbulb</span>
                                <div>
                                    <p class="text-sm font-bold text-primary">Selling Tip</p>
                                    <p class="text-xs text-primary/70 leading-normal">Items with 3 or more high-quality photos sell 40% faster on our platform.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>