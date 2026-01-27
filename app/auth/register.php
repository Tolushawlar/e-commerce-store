<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | E-commerce Platform</title>
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
    <div class="min-h-screen flex items-center justify-center p-4 py-12">
        <div class="max-w-md w-full">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-primary text-accent rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl font-bold">storefront</span>
                    </div>
                    <h1 class="text-3xl font-extrabold text-primary">E-commerce Platform</h1>
                </div>
                <p class="text-gray-600">Create your client account</p>
            </div>

            <!-- Registration Form -->
            <div class="bg-white rounded-2xl border border-gray-200 p-8">
                <form id="registerForm" onsubmit="handleRegister(event)">
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-900 mb-2">Full Name</label>
                        <input type="text" id="name" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                            placeholder="John Doe">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-900 mb-2">Email</label>
                        <input type="email" id="email" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                            placeholder="you@example.com">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-900 mb-2">Company Name (Optional)</label>
                        <input type="text" id="company_name"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                            placeholder="Acme Inc">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-900 mb-2">Phone (Optional)</label>
                        <input type="tel" id="phone"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                            placeholder="+1234567890">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-900 mb-2">Password</label>
                        <input type="password" id="password" required minlength="8"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                            placeholder="••••••••">
                        <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-900 mb-2">Subscription Plan</label>
                        <select id="subscription_plan" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Select a plan</option>
                            <option value="basic">Basic - $29/month</option>
                            <option value="standard">Standard - $79/month</option>
                            <option value="premium">Premium - $199/month</option>
                        </select>
                    </div>

                    <div id="errorMessage" class="hidden mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
                        <p class="text-sm font-semibold"></p>
                    </div>

                    <div id="successMessage" class="hidden mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl">
                        <p class="text-sm font-semibold"></p>
                    </div>

                    <button type="submit" id="registerBtn"
                        class="w-full py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90 transition-colors">
                        Create Account
                    </button>
                </form>

                <!-- Login Link -->
                <div class="mt-6 text-center">
                    <p class="text-gray-600">Already have an account?
                        <a href="/auth/login.php" class="text-primary font-semibold hover:underline">Sign in</a>
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
        async function handleRegister(e) {
            e.preventDefault();

            const registerBtn = document.getElementById('registerBtn');
            const errorMessage = document.getElementById('errorMessage');
            const successMessage = document.getElementById('successMessage');

            // Hide messages
            errorMessage.classList.add('hidden');
            successMessage.classList.add('hidden');

            // Show loading
            registerBtn.disabled = true;
            registerBtn.innerHTML = '<span class="material-symbols-outlined animate-spin">refresh</span> Creating account...';

            const data = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                subscription_plan: document.getElementById('subscription_plan').value,
            };

            // Optional fields
            const companyName = document.getElementById('company_name').value;
            const phone = document.getElementById('phone').value;

            if (companyName) data.company_name = companyName;
            if (phone) data.phone = phone;

            try {
                const response = await auth.register(data);

                // Show success message
                successMessage.classList.remove('hidden');
                successMessage.querySelector('p').textContent = 'Account created successfully! Redirecting to login...';

                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = '/auth/login.php';
                }, 2000);

            } catch (error) {
                // Show error
                errorMessage.classList.remove('hidden');
                errorMessage.querySelector('p').textContent = error.message || 'Registration failed';

                // Reset button
                registerBtn.disabled = false;
                registerBtn.textContent = 'Create Account';
            }
        }

        // Redirect if already authenticated
        if (auth.isAuthenticated()) {
            if (auth.isAdmin()) {
                window.location.href = '/admin/dashboard.php';
            } else {
                window.location.href = '/client/dashboard.php';
            }
        }
    </script>
</body>

</html>