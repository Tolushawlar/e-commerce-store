<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | E-commerce Platform</title>
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
    <style>
        .password-strength-bar {
            height: 4px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
    </style>
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
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Reset Password</h2>
                <p class="text-gray-600">Enter your new password below</p>
            </div>

            <!-- Loading State -->
            <div id="loadingSection" class="bg-white rounded-2xl border border-gray-200 p-8 text-center">
                <div class="animate-spin w-12 h-12 border-4 border-primary border-t-transparent rounded-full mx-auto mb-4"></div>
                <p class="text-gray-600">Verifying reset token...</p>
            </div>

            <!-- Invalid Token Message -->
            <div id="invalidTokenSection" class="hidden bg-white rounded-2xl border border-gray-200 p-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-red-600 text-3xl">error</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Invalid or Expired Link</h3>
                    <p class="text-gray-600 mb-6">This password reset link is invalid or has expired. Please request a new one.</p>
                    
                    <button onclick="window.location.href='/auth/forgot-password.php'"
                        class="w-full py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90 transition-colors">
                        Request New Link
                    </button>
                </div>
            </div>

            <!-- Reset Password Form -->
            <div id="formSection" class="hidden bg-white rounded-2xl border border-gray-200 p-8">
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-blue-600 text-xl">info</span>
                        <div>
                            <p class="text-sm font-semibold text-blue-900 mb-1">Resetting password for:</p>
                            <p id="userEmail" class="text-sm text-blue-700"></p>
                        </div>
                    </div>
                </div>

                <form id="resetPasswordForm" onsubmit="handleResetPassword(event)">
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-900 mb-2">New Password</label>
                        <div class="relative">
                            <input type="password" id="password" required
                                oninput="checkPasswordStrength()"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="Enter new password (min. 8 characters)">
                            <button type="button" onclick="togglePasswordVisibility('password')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <span class="material-symbols-outlined" id="passwordIcon">visibility</span>
                            </button>
                        </div>
                        
                        <!-- Password Strength Indicator -->
                        <div class="mt-2">
                            <div class="flex gap-1 mb-1">
                                <div id="strength1" class="password-strength-bar flex-1 bg-gray-200"></div>
                                <div id="strength2" class="password-strength-bar flex-1 bg-gray-200"></div>
                                <div id="strength3" class="password-strength-bar flex-1 bg-gray-200"></div>
                                <div id="strength4" class="password-strength-bar flex-1 bg-gray-200"></div>
                            </div>
                            <p id="strengthText" class="text-xs text-gray-500">Password strength: Weak</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-900 mb-2">Confirm Password</label>
                        <div class="relative">
                            <input type="password" id="confirmPassword" required
                                oninput="checkPasswordMatch()"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="Re-enter new password">
                            <button type="button" onclick="togglePasswordVisibility('confirmPassword')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <span class="material-symbols-outlined" id="confirmPasswordIcon">visibility</span>
                            </button>
                        </div>
                        <p id="matchText" class="hidden text-xs mt-1"></p>
                    </div>

                    <div id="errorMessage" class="hidden mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
                        <p class="text-sm font-semibold"></p>
                    </div>

                    <button type="submit" id="submitBtn"
                        class="w-full py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90 transition-colors">
                        Reset Password
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

            <!-- Success Message -->
            <div id="successSection" class="hidden bg-white rounded-2xl border border-gray-200 p-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-green-600 text-3xl">check_circle</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Password Reset Successful!</h3>
                    <p class="text-gray-600 mb-6">Your password has been successfully reset. You can now login with your new password.</p>
                    
                    <button onclick="window.location.href='/auth/login.php'"
                        class="w-full py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90 transition-colors">
                        Go to Login
                    </button>
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
        let resetToken = null;

        // Get token from URL
        const urlParams = new URLSearchParams(window.location.search);
        resetToken = urlParams.get('token');

        if (!resetToken) {
            // No token provided
            document.getElementById('loadingSection').classList.add('hidden');
            document.getElementById('invalidTokenSection').classList.remove('hidden');
        } else {
            // Verify token
            verifyToken();
        }

        async function verifyToken() {
            try {
                const response = await api.get(`/api/auth/verify-reset-token/${resetToken}`);

                if (response.data && response.data.valid) {
                    // Token is valid, show form
                    document.getElementById('loadingSection').classList.add('hidden');
                    document.getElementById('formSection').classList.remove('hidden');
                    document.getElementById('userEmail').textContent = response.data.email;
                } else {
                    throw new Error('Invalid token');
                }
            } catch (error) {
                // Token is invalid or expired
                document.getElementById('loadingSection').classList.add('hidden');
                document.getElementById('invalidTokenSection').classList.remove('hidden');
            }
        }

        async function handleResetPassword(e) {
            e.preventDefault();

            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const submitBtn = document.getElementById('submitBtn');
            const errorMessage = document.getElementById('errorMessage');

            // Validate passwords match
            if (password !== confirmPassword) {
                errorMessage.classList.remove('hidden');
                errorMessage.querySelector('p').textContent = 'Passwords do not match';
                return;
            }

            // Validate password length
            if (password.length < 8) {
                errorMessage.classList.remove('hidden');
                errorMessage.querySelector('p').textContent = 'Password must be at least 8 characters long';
                return;
            }

            // Hide error
            errorMessage.classList.add('hidden');

            // Show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="material-symbols-outlined animate-spin inline-block">refresh</span> Resetting...';

            try {
                await api.post('/api/auth/reset-password', {
                    token: resetToken,
                    password: password,
                    confirm_password: confirmPassword
                });

                // Hide form, show success
                document.getElementById('formSection').classList.add('hidden');
                document.getElementById('successSection').classList.remove('hidden');

                // Redirect to login after 3 seconds
                setTimeout(() => {
                    window.location.href = '/auth/login.php';
                }, 3000);

            } catch (error) {
                // Show error
                errorMessage.classList.remove('hidden');
                errorMessage.querySelector('p').textContent = error.message || 'Failed to reset password';

                // Reset button
                submitBtn.disabled = false;
                submitBtn.textContent = 'Reset Password';
            }
        }

        function togglePasswordVisibility(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + 'Icon');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                field.type = 'password';
                icon.textContent = 'visibility';
            }
        }

        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strength = calculatePasswordStrength(password);
            
            // Reset all bars
            for (let i = 1; i <= 4; i++) {
                document.getElementById(`strength${i}`).className = 'password-strength-bar flex-1 bg-gray-200';
            }

            const strengthText = document.getElementById('strengthText');
            
            if (strength === 0) {
                strengthText.textContent = 'Password strength: Weak';
                strengthText.className = 'text-xs text-gray-500';
            } else if (strength === 1) {
                document.getElementById('strength1').className = 'password-strength-bar flex-1 bg-red-500';
                strengthText.textContent = 'Password strength: Weak';
                strengthText.className = 'text-xs text-red-600';
            } else if (strength === 2) {
                document.getElementById('strength1').className = 'password-strength-bar flex-1 bg-orange-500';
                document.getElementById('strength2').className = 'password-strength-bar flex-1 bg-orange-500';
                strengthText.textContent = 'Password strength: Fair';
                strengthText.className = 'text-xs text-orange-600';
            } else if (strength === 3) {
                document.getElementById('strength1').className = 'password-strength-bar flex-1 bg-yellow-500';
                document.getElementById('strength2').className = 'password-strength-bar flex-1 bg-yellow-500';
                document.getElementById('strength3').className = 'password-strength-bar flex-1 bg-yellow-500';
                strengthText.textContent = 'Password strength: Good';
                strengthText.className = 'text-xs text-yellow-600';
            } else {
                for (let i = 1; i <= 4; i++) {
                    document.getElementById(`strength${i}`).className = 'password-strength-bar flex-1 bg-green-500';
                }
                strengthText.textContent = 'Password strength: Strong';
                strengthText.className = 'text-xs text-green-600';
            }
        }

        function calculatePasswordStrength(password) {
            if (password.length === 0) return 0;
            
            let strength = 0;
            
            // Length
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            
            // Complexity
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z\d]/.test(password)) strength++;
            
            return Math.min(strength, 4);
        }

        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const matchText = document.getElementById('matchText');
            
            if (confirmPassword.length === 0) {
                matchText.classList.add('hidden');
                return;
            }
            
            matchText.classList.remove('hidden');
            
            if (password === confirmPassword) {
                matchText.textContent = '✓ Passwords match';
                matchText.className = 'text-xs text-green-600 mt-1';
            } else {
                matchText.textContent = '✗ Passwords do not match';
                matchText.className = 'text-xs text-red-600 mt-1';
            }
        }
    </script>
</body>

</html>
