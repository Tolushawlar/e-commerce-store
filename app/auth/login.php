<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | E-commerce Platform</title>
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
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-primary text-accent rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl font-bold">storefront</span>
                    </div>
                    <h1 class="text-3xl font-extrabold text-primary">E-commerce Platform</h1>
                </div>
                <p class="text-gray-600">Sign in to your account</p>
            </div>

            <!-- Tab Switcher -->
            <div class="flex gap-2 mb-6 bg-white p-1 rounded-xl border border-gray-200">
                <button id="adminTab" onclick="switchTab('admin')" class="flex-1 py-3 px-4 rounded-lg font-semibold bg-primary text-white">
                    Super Admin
                </button>
                <button id="clientTab" onclick="switchTab('client')" class="flex-1 py-3 px-4 rounded-lg font-semibold text-gray-600 hover:bg-gray-50">
                    Client
                </button>
            </div>

            <!-- Login Form -->
            <div class="bg-white rounded-2xl border border-gray-200 p-8">
                <!-- Session Expired Message -->
                <div id="expiredMessage" class="hidden mb-4 p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-600">schedule</span>
                        <p class="text-sm font-semibold"></p>
                    </div>
                </div>

                <form id="loginForm" onsubmit="handleLogin(event)">
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-900 mb-2">Email</label>
                        <input type="email" id="email" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                            placeholder="you@example.com">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-900 mb-2">Password</label>
                        <input type="password" id="password" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                            placeholder="••••••••">
                    </div>

                    <div id="errorMessage" class="hidden mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
                        <p class="text-sm font-semibold"></p>
                    </div>

                    <button type="submit" id="loginBtn"
                        class="w-full py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90 transition-colors">
                        Sign In
                    </button>
                </form>

                <!-- Client Registration Link -->
                <div id="registerLink" class="hidden mt-6 text-center">
                    <p class="text-gray-600">Don't have an account?
                        <a href="/auth/register.php" class="text-primary font-semibold hover:underline">Register here</a>
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-6 text-sm text-gray-500">
                <p>&copy; 2026 E-commerce Platform. All rights reserved.</p>
            </div>
        </div>
    </div>

    <script src="/assets/js/core/api.js"></script>
    <script src="/assets/js/core/auth.js"></script>
    <script>
        let loginType = 'admin';

        function switchTab(type) {
            loginType = type;

            const adminTab = document.getElementById('adminTab');
            const clientTab = document.getElementById('clientTab');
            const registerLink = document.getElementById('registerLink');

            if (type === 'admin') {
                adminTab.className = 'flex-1 py-3 px-4 rounded-lg font-semibold bg-primary text-white';
                clientTab.className = 'flex-1 py-3 px-4 rounded-lg font-semibold text-gray-600 hover:bg-gray-50';
                registerLink.classList.add('hidden');
            } else {
                adminTab.className = 'flex-1 py-3 px-4 rounded-lg font-semibold text-gray-600 hover:bg-gray-50';
                clientTab.className = 'flex-1 py-3 px-4 rounded-lg font-semibold bg-primary text-white';
                registerLink.classList.remove('hidden');
            }
        }

        async function handleLogin(e) {
            e.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const loginBtn = document.getElementById('loginBtn');
            const errorMessage = document.getElementById('errorMessage');

            // Hide error
            errorMessage.classList.add('hidden');

            // Show loading
            loginBtn.disabled = true;
            loginBtn.innerHTML = '<span class="material-symbols-outlined animate-spin">refresh</span> Signing in...';

            try {
                let response;

                if (loginType === 'admin') {
                    response = await auth.adminLogin(email, password);
                    window.location.href = '/admin/dashboard.php';
                } else {
                    response = await auth.clientLogin(email, password);
                    window.location.href = '/client/dashboard.php';
                }

            } catch (error) {
                // Show error
                errorMessage.classList.remove('hidden');
                errorMessage.querySelector('p').textContent = error.message || 'Login failed';

                // Reset button
                loginBtn.disabled = false;
                loginBtn.textContent = 'Sign In';
            }
        }

        // Check if already authenticated
        if (auth.isAuthenticated()) {
            if (auth.isAdmin()) {
                window.location.href = '/admin/dashboard.php';
            } else {
                window.location.href = '/client/dashboard.php';
            }
        }

        // Check for session expiration message
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('expired') === '1') {
            const expiredMessage = document.getElementById('expiredMessage');
            const message = urlParams.get('message') || 'Your session has expired. Please login again.';

            expiredMessage.classList.remove('hidden');
            expiredMessage.querySelector('p').textContent = message;

            // Auto-hide after 5 seconds
            setTimeout(() => {
                expiredMessage.classList.add('hidden');
            }, 5000);
        }
    </script>
</body>

</html>