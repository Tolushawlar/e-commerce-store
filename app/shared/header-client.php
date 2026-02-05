<!DOCTYPE html>
<html lang="en" class="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Client Dashboard'; ?> | E-commerce Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        "primary": "#13ecb6",
                        "primary-dark": "#0dbf92",
                        "secondary": "#ffa07a",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Custom scrollbar for sidebar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
        }

        /* Sidebar transitions */
        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }

        @media (max-width: 1024px) {
            .sidebar-hidden {
                transform: translateX(-100%);
            }
        }
    </style>
</head>

<body class="bg-gray-50 dark:bg-gray-900 font-display antialiased">
    <!-- Sidebar Backdrop (Mobile) -->
    <div id="sidebarBackdrop" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 w-64 flex-shrink-0 flex flex-col justify-between bg-[#0b1614] text-white sidebar-transition z-50 h-full">
        <div class="flex flex-col h-full">
            <!-- User Profile / Brand -->
            <div class="p-6 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div class="relative w-10 h-10 rounded-full overflow-hidden border-2 border-primary/50">
                        <div class="w-full h-full bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-2xl">person</span>
                        </div>
                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-primary rounded-full border-2 border-[#0b1614]"></div>
                    </div>
                    <div class="flex flex-col">
                        <h1 class="text-sm font-bold text-white leading-tight" id="userName">Store Owner</h1>
                        <p class="text-xs text-white/60" id="userRole">Owner</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto custom-scrollbar">
                <div class="mb-6">
                    <p class="px-3 text-xs font-semibold text-white/40 uppercase tracking-wider mb-2">Main</p>
                    <a href="/client/dashboard.php" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/70 hover:bg-white/5 hover:text-white group transition-colors" data-page="dashboard">
                        <span class="material-symbols-outlined" style="font-size: 20px;">dashboard</span>
                        <span class="text-sm font-medium">Dashboard</span>
                    </a>
                    <a href="/client/stores.php" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/70 hover:bg-white/5 hover:text-white group transition-colors mt-1" data-page="stores">
                        <span class="material-symbols-outlined" style="font-size: 20px;">storefront</span>
                        <span class="text-sm font-medium">My Stores</span>
                    </a>
                    <a href="/client/products.php" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/70 hover:bg-white/5 hover:text-white group transition-colors mt-1" data-page="products">
                        <span class="material-symbols-outlined" style="font-size: 20px;">inventory_2</span>
                        <span class="text-sm font-medium">Products</span>
                    </a>
                    <a href="/client/orders.php" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/70 hover:bg-white/5 hover:text-white group transition-colors mt-1" data-page="orders">
                        <span class="material-symbols-outlined" style="font-size: 20px;">shopping_bag</span>
                        <span class="text-sm font-medium">Orders</span>
                    </a>
                </div>
                <div>
                    <p class="px-3 text-xs font-semibold text-white/40 uppercase tracking-wider mb-2">Management</p>
                    <a href="/client/categories.php" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/70 hover:bg-white/5 hover:text-white group transition-colors" data-page="categories">
                        <span class="material-symbols-outlined" style="font-size: 20px;">category</span>
                        <span class="text-sm font-medium">Categories</span>
                    </a>
                    <a href="/client/notifications.php" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/70 hover:bg-white/5 hover:text-white group transition-colors mt-1" data-page="notifications">
                        <span class="material-symbols-outlined" style="font-size: 20px;">notifications</span>
                        <span class="text-sm font-medium">Notifications</span>
                    </a>
                    <a href="/client/store-settings.php" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/70 hover:bg-white/5 hover:text-white group transition-colors mt-1" data-page="settings">
                        <span class="material-symbols-outlined" style="font-size: 20px;">settings</span>
                        <span class="text-sm font-medium">Settings</span>
                    </a>
                </div>
            </nav>

            <!-- Logout -->
            <div class="p-4 border-t border-white/10">
                <button onclick="auth.logout()" class="flex w-full items-center justify-center gap-2 rounded-lg py-2.5 bg-white/5 hover:bg-white/10 text-white/80 text-sm font-medium transition-colors">
                    <span class="material-symbols-outlined" style="font-size: 18px;">logout</span>
                    <span>Logout</span>
                </button>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main id="mainContent" class="lg:ml-64 min-h-screen transition-all duration-300">
        <!-- Header -->
        <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-20 transition-colors">
            <div class="px-4 lg:px-8 py-4 flex items-center justify-between">
                <!-- Mobile Menu Toggle -->
                <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 transition-colors">
                    <span class="material-symbols-outlined">menu</span>
                </button>

                <div class="flex-1 lg:flex-none">
                    <h1 class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
                    <?php if (isset($pageDescription)): ?>
                        <p class="text-xs lg:text-sm text-gray-500 dark:text-gray-400 mt-1"><?php echo $pageDescription; ?></p>
                    <?php endif; ?>
                </div>

                <!-- Notification Bell -->
                <div class="flex items-center gap-4">
                    <!-- Dark Mode Toggle -->
                    <button id="darkModeToggle" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 transition-colors">
                        <span class="material-symbols-outlined dark-mode-icon">dark_mode</span>
                    </button>

                    <!-- Notification Bell -->
                    <div id="notificationBell"></div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="p-4 lg:p-8">
            <script src="/assets/js/services/notification.service.js"></script>
            <script src="/assets/js/notification-bell.js"></script>
            <script>
                // Initialize notification bell
                document.addEventListener('DOMContentLoaded', function() {
                    new NotificationBell('notificationBell', {
                        pollInterval: 30000, // Poll every 30 seconds
                        maxVisible: 5,
                        onNotificationClick: (notification) => {
                            // Navigate to notification center or specific page based on type
                            if (notification.action_url) {
                                window.location.href = notification.action_url;
                            } else {
                                window.location.href = '/client/notifications.php';
                            }
                        }
                    });
                });

                // Sidebar toggle for mobile
                function toggleSidebar() {
                    const sidebar = document.getElementById('sidebar');
                    const backdrop = document.getElementById('sidebarBackdrop');

                    sidebar.classList.toggle('sidebar-hidden');
                    backdrop.classList.toggle('hidden');
                }

                // Set active navigation link
                document.addEventListener('DOMContentLoaded', function() {
                    const currentPath = window.location.pathname;
                    const navLinks = document.querySelectorAll('.nav-link');

                    navLinks.forEach(link => {
                        const href = link.getAttribute('href');
                        if (currentPath.includes(href)) {
                            link.classList.remove('text-white/70', 'hover:bg-white/5');
                            link.classList.add('bg-primary', 'text-[#0d1b18]');
                        }
                    });
                });
            </script>