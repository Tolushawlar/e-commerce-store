<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients Management | Super Admin</title>
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
                    <a href="clients.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-primary text-white">
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
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Clients Management</h2>
                        <p class="text-gray-600">Manage all platform clients and their subscriptions</p>
                    </div>
                    <button onclick="openAddClientModal()" class="px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90 flex items-center gap-2">
                        <span class="material-symbols-outlined">add</span>
                        Add New Client
                    </button>
                </div>

                <!-- Clients Table -->
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">Client</th>
                                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">Company</th>
                                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">Plan</th>
                                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">Status</th>
                                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-900">Joined</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200" id="clients-table">
                                <!-- Clients will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Client Modal -->
    <div id="addClientModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">Add New Client</h3>
                <button onclick="closeAddClientModal()" class="text-gray-400 hover:text-gray-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form id="addClientForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Full Name</label>
                    <input type="text" name="name" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Email</label>
                    <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Company Name</label>
                    <input type="text" name="company_name" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Phone</label>
                    <input type="tel" name="phone" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Subscription Plan</label>
                    <select name="subscription_plan" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="basic">Basic</option>
                        <option value="pro">Pro</option>
                        <option value="enterprise">Enterprise</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">Password</label>
                    <input type="password" name="password" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="closeAddClientModal()" class="flex-1 px-6 py-3 border border-gray-200 rounded-xl font-semibold text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90">
                        Add Client
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddClientModal() {
            document.getElementById('addClientModal').classList.remove('hidden');
            document.getElementById('addClientModal').classList.add('flex');
        }

        function closeAddClientModal() {
            document.getElementById('addClientModal').classList.add('hidden');
            document.getElementById('addClientModal').classList.remove('flex');
        }

        function loadClients() {
            fetch('../api/clients.php')
                .then(response => response.json())
                .then(clients => {
                    const tbody = document.getElementById('clients-table');
                    tbody.innerHTML = clients.map(client => `
                        <tr>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white font-bold">
                                        ${client.name.charAt(0)}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">${client.name}</p>
                                        <p class="text-sm text-gray-600">${client.email}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-900">${client.company_name || '-'}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-blue-100 text-blue-800">
                                    ${client.subscription_plan}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-green-100 text-green-800">
                                    ${client.status}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">${new Date(client.created_at).toLocaleDateString()}</td>
                        </tr>
                    `).join('');
                })
                .catch(error => {
                    console.error('Error loading clients:', error);
                });
        }

        document.getElementById('addClientForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            fetch('../api/clients.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Client added successfully!');
                    closeAddClientModal();
                    loadClients();
                    this.reset();
                } else {
                    alert('Error: ' + (result.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding client');
            });
        });

        loadClients();
    </script>
</body>
</html>