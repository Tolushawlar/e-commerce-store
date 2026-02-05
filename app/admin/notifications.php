<?php
$pageTitle = 'Notifications';
$pageDescription = 'System notifications and alerts';
include '../shared/header-admin.php';
?>

<!-- Page Heading -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
        <p class="text-gray-600 mt-1">Manage system notifications and alerts</p>
    </div>
    <div class="flex items-center gap-3">
        <!-- Filter by Type -->
        <select id="typeFilter" onchange="filterByType(this.value)"
            class="px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-primary transition-all">
            <option value="">All Types</option>
            <option value="order">Orders</option>
            <option value="product">Products</option>
            <option value="system">System</option>
            <option value="store">Store</option>
            <option value="payment">Payment</option>
        </select>

        <!-- Mark All Read -->
        <button onclick="markAllAsRead()"
            class="px-4 py-2 bg-primary hover:bg-primary/90 text-white font-semibold rounded-lg transition-colors">
            Mark All Read
        </button>
    </div>
</div>

<!-- Notification Preferences Link -->
<div class="mb-6">
    <a href="#" onclick="showPreferences(); return false;"
        class="inline-flex items-center gap-2 text-primary hover:text-primary/80 transition-colors">
        <span class="material-symbols-outlined" style="font-size: 20px;">settings</span>
        <span class="font-medium">Notification Preferences</span>
    </a>
</div>

<!-- Notifications List -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <!-- Loading State -->
    <div id="loadingState" class="p-8 text-center">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        <p class="text-gray-600 mt-4">Loading notifications...</p>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="hidden p-12 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
            <span class="material-symbols-outlined text-gray-400 text-4xl">notifications_off</span>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">No notifications yet</h3>
        <p class="text-gray-600">You're all caught up! New notifications will appear here.</p>
    </div>

    <!-- Notifications Container -->
    <div id="notificationsList" class="divide-y divide-gray-200 hidden"></div>
</div>

<!-- Preferences Modal -->
<div id="preferencesModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full max-h-[80vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Notification Preferences</h2>
            <button onclick="hidePreferences()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-gray-600">close</span>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-6">
            <!-- Email Notifications Toggle -->
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-gray-900">Email Notifications</h3>
                    <p class="text-sm text-gray-600 mt-1">Receive notifications via email</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="emailEnabled" class="sr-only peer" onchange="updatePreferences()">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                </label>
            </div>

            <!-- Notification Types -->
            <div>
                <h3 class="font-semibold text-gray-900 mb-4">Notification Types</h3>
                <div class="space-y-3">
                    <!-- Order Notifications -->
                    <div class="flex items-center justify-between">
                        <label class="text-sm text-gray-700">Order Updates</label>
                        <input type="checkbox" id="notifyOrders" class="w-5 h-5 text-primary bg-gray-100 border-gray-300 rounded focus:ring-primary" onchange="updatePreferences()">
                    </div>

                    <!-- Product Notifications -->
                    <div class="flex items-center justify-between">
                        <label class="text-sm text-gray-700">Product Alerts</label>
                        <input type="checkbox" id="notifyProducts" class="w-5 h-5 text-primary bg-gray-100 border-gray-300 rounded focus:ring-primary" onchange="updatePreferences()">
                    </div>

                    <!-- System Notifications -->
                    <div class="flex items-center justify-between">
                        <label class="text-sm text-gray-700">System Announcements</label>
                        <input type="checkbox" id="notifySystem" class="w-5 h-5 text-primary bg-gray-100 border-gray-300 rounded focus:ring-primary" onchange="updatePreferences()">
                    </div>

                    <!-- Store Notifications -->
                    <div class="flex items-center justify-between">
                        <label class="text-sm text-gray-700">Store Updates</label>
                        <input type="checkbox" id="notifyStore" class="w-5 h-5 text-primary bg-gray-100 border-gray-300 rounded focus:ring-primary" onchange="updatePreferences()">
                    </div>

                    <!-- Payment Notifications -->
                    <div class="flex items-center justify-between">
                        <label class="text-sm text-gray-700">Payment Notifications</label>
                        <input type="checkbox" id="notifyPayment" class="w-5 h-5 text-primary bg-gray-100 border-gray-300 rounded focus:ring-primary" onchange="updatePreferences()">
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200">
            <button onclick="hidePreferences()" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                Cancel
            </button>
            <button onclick="savePreferences()" class="px-4 py-2 bg-primary hover:bg-primary/90 text-white font-semibold rounded-lg transition-colors">
                Save Preferences
            </button>
        </div>
    </div>
</div>

<script src="/assets/js/components/toast.js"></script>
<script>
    // notificationService is already available from header
    let allNotifications = [];
    let currentFilter = '';

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        loadNotifications();
        loadPreferences();
    });

    // Load notifications
    async function loadNotifications() {
        try {
            showLoading();
            const response = await notificationService.getNotifications();
            allNotifications = response.success ? response.data : [];
            displayNotifications(allNotifications);
        } catch (error) {
            console.error('Failed to load notifications:', error);
            showError('Failed to load notifications');
        }
    }

    // Display notifications
    function displayNotifications(notifications) {
        const container = document.getElementById('notificationsList');
        const emptyState = document.getElementById('emptyState');
        const loadingState = document.getElementById('loadingState');

        loadingState.classList.add('hidden');

        if (!notifications || notifications.length === 0) {
            container.classList.add('hidden');
            emptyState.classList.remove('hidden');
            return;
        }

        emptyState.classList.add('hidden');
        container.classList.remove('hidden');

        container.innerHTML = notifications.map(notification => {
            const isUnread = !notification.read_at;
            const iconMap = {
                order: 'shopping_bag',
                product: 'inventory_2',
                system: 'info',
                store: 'storefront',
                payment: 'payments'
            };
            const colorMap = {
                order: 'bg-blue-100 text-blue-600',
                product: 'bg-green-100 text-green-600',
                system: 'bg-gray-100 text-gray-600',
                store: 'bg-purple-100 text-purple-600',
                payment: 'bg-yellow-100 text-yellow-600'
            };

            return `
                <div class="flex items-start gap-4 p-4 hover:bg-gray-50 transition-colors ${isUnread ? 'bg-primary/5' : ''}" 
                     onclick="handleNotificationClick('${notification.id}', '${notification.action_url || ''}')">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-full ${colorMap[notification.type] || colorMap.system} flex items-center justify-center">
                            <span class="material-symbols-outlined">${iconMap[notification.type] || 'notifications'}</span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <h3 class="font-semibold text-gray-900 ${isUnread ? 'font-bold' : ''}">${notification.title}</h3>
                            ${isUnread ? '<div class="w-2 h-2 bg-primary rounded-full flex-shrink-0"></div>' : ''}
                        </div>
                        <p class="text-sm text-gray-600 mb-2">${notification.message}</p>
                        <div class="flex items-center gap-4 text-xs text-gray-500">
                            <span>${formatDate(notification.created_at)}</span>
                            <span class="capitalize">${notification.type}</span>
                            ${notification.priority !== 'normal' ? `<span class="px-2 py-0.5 rounded-full bg-red-100 text-red-600 font-medium">${notification.priority}</span>` : ''}
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <button onclick="deleteNotification('${notification.id}', event)" 
                                class="p-2 hover:bg-gray-200 rounded-lg transition-colors"
                                title="Delete notification">
                            <span class="material-symbols-outlined text-gray-400">delete</span>
                        </button>
                    </div>
                </div>
            `;
        }).join('');
    }

    // Handle notification click
    async function handleNotificationClick(notificationId, actionUrl) {
        try {
            await notificationService.markAsRead(notificationId);
            if (actionUrl) {
                window.location.href = actionUrl;
            }
        } catch (error) {
            console.error('Failed to mark notification as read:', error);
        }
    }

    // Delete notification
    async function deleteNotification(notificationId, event) {
        event.stopPropagation();

        if (!confirm('Are you sure you want to delete this notification?')) {
            return;
        }

        try {
            await notificationService.deleteNotification(notificationId);
            allNotifications = allNotifications.filter(n => n.id !== notificationId);
            displayNotifications(currentFilter ? allNotifications.filter(n => n.type === currentFilter) : allNotifications);
        } catch (error) {
            console.error('Failed to delete notification:', error);
            toast.error('Failed to delete notification');
        }
    }

    // Mark all as read
    async function markAllAsRead() {
        try {
            await notificationService.markAllAsRead();
            allNotifications = allNotifications.map(n => ({
                ...n,
                read_at: new Date().toISOString()
            }));
            displayNotifications(currentFilter ? allNotifications.filter(n => n.type === currentFilter) : allNotifications);
        } catch (error) {
            console.error('Failed to mark all as read:', error);
            toast.error('Failed to mark all notifications as read');
        }
    }

    // Filter by type
    function filterByType(type) {
        currentFilter = type;
        const filtered = type ? allNotifications.filter(n => n.type === type) : allNotifications;
        displayNotifications(filtered);
    }

    // Load preferences
    async function loadPreferences() {
        try {
            const response = await notificationService.getPreferences();
            if (response.success && response.data) {
                const prefs = response.data;
                // Map preferences array to checkboxes
                prefs.forEach(pref => {
                    switch (pref.notification_type) {
                        case 'order':
                            document.getElementById('notifyOrders').checked = pref.in_app_enabled;
                            break;
                        case 'product':
                            document.getElementById('notifyProducts').checked = pref.in_app_enabled;
                            break;
                        case 'system':
                            document.getElementById('notifySystem').checked = pref.in_app_enabled;
                            break;
                        case 'store':
                            document.getElementById('notifyStore').checked = pref.in_app_enabled;
                            break;
                        case 'payment':
                            document.getElementById('notifyPayment').checked = pref.in_app_enabled;
                            break;
                    }
                });
            }
        } catch (error) {
            console.error('Failed to load preferences:', error);
        }
    }

    // Save preferences
    async function savePreferences() {
        try {
            const types = [{
                    type: 'order',
                    enabled: document.getElementById('notifyOrders').checked
                },
                {
                    type: 'product',
                    enabled: document.getElementById('notifyProducts').checked
                },
                {
                    type: 'system',
                    enabled: document.getElementById('notifySystem').checked
                },
                {
                    type: 'store',
                    enabled: document.getElementById('notifyStore').checked
                },
                {
                    type: 'payment',
                    enabled: document.getElementById('notifyPayment').checked
                }
            ];

            // Update each preference
            for (const pref of types) {
                await notificationService.updatePreference(pref.type, {
                    in_app_enabled: pref.enabled,
                    email_enabled: pref.enabled
                });
            }

            hidePreferences();
            toast.success('Preferences saved successfully!');
        } catch (error) {
            console.error('Failed to save preferences:', error);
            toast.error('Failed to save preferences');
        }
    }

    // Update preferences (auto-save)
    async function updatePreferences() {
        // Auto-save when toggled (optional)
    }

    // Show/hide preferences modal
    function showPreferences() {
        document.getElementById('preferencesModal').classList.remove('hidden');
    }

    function hidePreferences() {
        document.getElementById('preferencesModal').classList.add('hidden');
    }

    // Utility functions
    function showLoading() {
        document.getElementById('loadingState').classList.remove('hidden');
        document.getElementById('notificationsList').classList.add('hidden');
        document.getElementById('emptyState').classList.add('hidden');
    }

    function showError(message) {
        document.getElementById('loadingState').classList.add('hidden');
        toast.error(message);
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);

        if (diffMins < 1) return 'Just now';
        if (diffMins < 60) return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
        if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
        if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;

        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
    }
</script>

<?php include '../shared/footer-admin.php'; ?>