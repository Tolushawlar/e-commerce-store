<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Admin Dashboard'; ?> | E-commerce Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 w-64 bg-white border-r border-gray-200 z-30">
        <div class="flex flex-col h-full">
            <!-- Logo -->
            <div class="p-6 border-b">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary text-accent rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined font-bold">storefront</span>
                    </div>
                    <div>
                        <h1 class="font-extrabold text-primary">E-commerce</h1>
                        <p class="text-xs text-gray-500">Admin Panel</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-4 overflow-y-auto">
                <a href="/admin/dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-primary/10 text-gray-700 hover:text-primary mb-1">
                    <span class="material-symbols-outlined">dashboard</span>
                    <span class="font-semibold">Dashboard</span>
                </a>
                <a href="/admin/clients.php" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-primary/10 text-gray-700 hover:text-primary mb-1">
                    <span class="material-symbols-outlined">group</span>
                    <span class="font-semibold">Clients</span>
                </a>
                <a href="/admin/stores.php" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-primary/10 text-gray-700 hover:text-primary mb-1">
                    <span class="material-symbols-outlined">store</span>
                    <span class="font-semibold">Stores</span>
                </a>
                <a href="/admin/products.php" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-primary/10 text-gray-700 hover:text-primary mb-1">
                    <span class="material-symbols-outlined">inventory_2</span>
                    <span class="font-semibold">Products</span>
                </a>
                <a href="/admin/orders.php" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-primary/10 text-gray-700 hover:text-primary mb-1">
                    <span class="material-symbols-outlined">shopping_cart</span>
                    <span class="font-semibold">Orders</span>
                </a>
                <a href="/admin/templates.php" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-primary/10 text-gray-700 hover:text-primary mb-1">
                    <span class="material-symbols-outlined">web</span>
                    <span class="font-semibold">Templates</span>
                </a>
            </nav>

            <!-- User Section -->
            <div class="p-4 border-t">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined">person</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900" id="userName">Admin</p>
                        <p class="text-xs text-gray-500">Super Admin</p>
                    </div>
                </div>
                <button onclick="auth.logout()" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 text-sm font-semibold">
                    <span class="material-symbols-outlined text-sm">logout</span>
                    Logout
                </button>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="ml-64 min-h-screen">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 sticky top-0 z-20">
            <div class="px-8 py-4 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
                    <?php if (isset($pageDescription)): ?>
                        <p class="text-sm text-gray-500 mt-1"><?php echo $pageDescription; ?></p>
                    <?php endif; ?>
                </div>
                <div class="flex items-center gap-3">
                    <a href="http://localhost:8000/docs.html" target="_blank" class="px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100 rounded-lg">
                        API Docs
                    </a>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="p-8">