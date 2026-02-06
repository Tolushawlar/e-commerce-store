<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | E-commerce Platform</title>
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
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Forgot Password?</h2>
                <p class="text-gray-600">Enter your email address and we'll send you a link to reset your password</p>
            </div>

            <!-- Forgot Password Form -->
            <div class="bg-white rounded-2xl border border-gray-200 p-8">
                <div id="formSection">
                    <form id="forgotPasswordForm" onsubmit="handleForgotPassword(event)">
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-900 mb-2">Email Address</label>
                            <input type="email" id="email" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="you@example.com">
                        </div>

                        <div id="errorMessage" class="hidden mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
                            <p class="text-sm font-semibold"></p>
                        </div>

                        <button type="submit" id="submitBtn"
                            class="w-full py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90 transition-colors">
                            Send Reset Link
                        </button>
                    </form>

                    <!-- Back to Login Link -->
                    <div class="mt-6 text-center">
                        <a href="/auth/login.php" class="text-primary font-semibold hover:underline inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">arrow_back</span>
                            Back to Login
                        </a>
                    </div>
                </div>

                <!-- Success Message (Hidden Initially) -->
                <div id="successSection" class="hidden">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="material-symbols-outlined text-green-600 text-3xl">check_circle</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Check Your Email</h3>
                        <p class="text-gray-600 mb-4">If an account exists with this email, a password reset link has been sent.</p>
                        
                        <!-- Reset Link Display (for development/testing) -->
                        <div id="resetLinkContainer" class="hidden mb-4 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                            <p class="text-xs font-semibold text-blue-900 mb-2">For Testing Purposes:</p>
                            <a id="resetLink" href="#" class="text-sm text-blue-600 hover:underline break-all"></a>
                            <p class="text-xs text-blue-700 mt-2">This link expires in 1 hour</p>
                        </div>

                        <button onclick="window.location.href='/auth/login.php'"
                            class="w-full py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90 transition-colors">
                            Back to Login
                        </button>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-6 text-sm text-gray-500">
                <p>&copy; 2026 E-commerce Platform. All rights reserved.</p>
            </div>
        </div>
    </div>

    <script src="/assets/js/core/api.js"></script>
    <script>
        async function handleForgotPassword(e) {
            e.preventDefault();

            const email = document.getElementById('email').value;
            const submitBtn = document.getElementById('submitBtn');
            const errorMessage = document.getElementById('errorMessage');
            const formSection = document.getElementById('formSection');
            const successSection = document.getElementById('successSection');

            // Hide error
            errorMessage.classList.add('hidden');

            // Show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="material-symbols-outlined animate-spin inline-block">refresh</span> Sending...';

            try {
                const response = await api.post('/api/auth/forgot-password', { email });

                // Hide form, show success
                formSection.classList.add('hidden');
                successSection.classList.remove('hidden');

                // If reset link is returned (for development), display it
                if (response.data && response.data.reset_link) {
                    const resetLinkContainer = document.getElementById('resetLinkContainer');
                    const resetLink = document.getElementById('resetLink');
                    
                    resetLink.href = response.data.reset_link;
                    resetLink.textContent = window.location.origin + response.data.reset_link;
                    resetLinkContainer.classList.remove('hidden');
                }

            } catch (error) {
                // Show error
                errorMessage.classList.remove('hidden');
                errorMessage.querySelector('p').textContent = error.message || 'Failed to send reset link';

                // Reset button
                submitBtn.disabled = false;
                submitBtn.textContent = 'Send Reset Link';
            }
        }

        // Check if already authenticated
        const token = localStorage.getItem('auth_token');
        if (token) {
            // Redirect to appropriate dashboard
            const authData = JSON.parse(atob(token.split('.')[1]));
            if (authData.type === 'super_admin') {
                window.location.href = '/admin/dashboard.php';
            } else {
                window.location.href = '/client/dashboard.php';
            }
        }
    </script>
</body>

</html>
