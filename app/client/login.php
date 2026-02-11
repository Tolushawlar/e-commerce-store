<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>StoreFlow - Client Login</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary-emerald": "#064E3B",
                        "secondary-emerald": "#047857",
                        "lime-accent": "#BEF264",
                        "field-bg": "#F8FAFC",
                    },
                    fontFamily: {
                        "display": ["Plus Jakarta Sans", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.5rem",
                        "lg": "1rem",
                        "xl": "1.5rem",
                        "2xl": "2rem"
                    },
                },
            },
        }
    </script>
    <style type="text/tailwindcss">
        .mesh-overlay {
            background-image: radial-gradient(at 0% 0%, rgba(190, 242, 100, 0.15) 0px, transparent 50%),
                              radial-gradient(at 100% 100%, rgba(16, 183, 127, 0.2) 0px, transparent 50%);
        }
        .dot-grid {
            background-image: radial-gradient(rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 30px 30px;
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(-1deg); }
            50% { transform: translateY(-20px) rotate(1deg); }
        }
        .perspective-lg {
            perspective: 2000px;
        }
        .dashboard-3d {
            transform: rotateY(-15deg) rotateX(10deg);
            box-shadow: -20px 40px 80px rgba(0,0,0,0.4);
        }
    </style>
</head>

<body class="font-display antialiased bg-white overflow-hidden">
    <div class="min-h-screen flex">
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-primary-emerald to-secondary-emerald relative overflow-hidden flex-col p-16 justify-between">
            <div class="absolute inset-0 mesh-overlay pointer-events-none"></div>
            <div class="absolute inset-0 dot-grid pointer-events-none"></div>
            <div class="absolute top-[10%] right-[10%] w-32 h-32 bg-lime-accent/10 rounded-full blur-2xl animate-pulse"></div>
            <div class="absolute bottom-[20%] left-[5%] w-48 h-48 bg-emerald-400/10 rounded-full blur-3xl"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-12">
                    <div class="w-10 h-10 rounded bg-white flex items-center justify-center text-primary-emerald font-bold text-2xl shadow-lg">S</div>
                    <span class="font-bold text-2xl tracking-tight text-white">StoreFlow</span>
                </div>
                <h1 class="text-[42px] font-extrabold text-white leading-tight mb-8 max-w-md">
                    Shop with confidence
                </h1>
                <ul class="space-y-6">
                    <li class="flex items-center gap-4 group">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-lime-accent/20 border border-lime-accent/30 flex items-center justify-center transition-transform group-hover:scale-110">
                            <span class="material-icons text-lime-accent text-xl">done</span>
                        </div>
                        <span class="text-emerald-50 text-lg font-medium">Secure payment processing</span>
                    </li>
                    <li class="flex items-center gap-4 group">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-lime-accent/20 border border-lime-accent/30 flex items-center justify-center transition-transform group-hover:scale-110">
                            <span class="material-icons text-lime-accent text-xl">done</span>
                        </div>
                        <span class="text-emerald-50 text-lg font-medium">Fast & reliable delivery</span>
                    </li>
                    <li class="flex items-center gap-4 group">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-lime-accent/20 border border-lime-accent/30 flex items-center justify-center transition-transform group-hover:scale-110">
                            <span class="material-icons text-lime-accent text-xl">done</span>
                        </div>
                        <span class="text-emerald-50 text-lg font-medium">24/7 customer support</span>
                    </li>
                </ul>
            </div>
            <div class="relative z-10 mt-auto perspective-lg">
                <div class="dashboard-3d animate-float bg-[#0f172a] rounded-2xl border-4 border-white/10 p-2 w-[120%] -ml-[10%] transform origin-bottom">
                    <img alt="Dashboard Preview" class="rounded-xl w-full h-auto opacity-90 shadow-2xl" src="https://lh3.googleusercontent.com/aida-public/AB6AXuB1wdAydPQzHTVz9wmSbJQPk6AnpDrNMCQXa1HzUUHNhVgNoWrrEez4SogxLBDEDRl-gXoPNCw4U0kIDj7p2wpY9VaSvU5T_ZyuDBZJkXmX0PZtdxZtruNMlc5BnPGrYsiBynP-RPDLEmIEzTBpn9i5p0hnOtng1KA7yDIFl0Vrr9w0lFOtbwrm1yYXQtTpAX0st946j9DxkvkWe5sX7UdAEt6pLZFODNqaeb5fEi5-He216xBhKbuPZN3x07lUzyKczMPLbsKvC9E" />
                    <div class="absolute -top-4 -right-4 p-4 rounded-xl bg-white/95 backdrop-blur shadow-xl border border-white/20 w-40">
                        <div class="text-[10px] text-gray-500 uppercase tracking-widest font-bold mb-1">Total Sales</div>
                        <div class="text-xl font-bold text-primary-emerald">₦12.5M</div>
                        <div class="w-full bg-gray-100 h-1 mt-2 rounded-full overflow-hidden">
                            <div class="bg-lime-accent h-full w-[80%]"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full lg:w-1/2 flex flex-col bg-white relative min-h-screen overflow-y-auto">
            <div class="w-full max-w-md mx-auto pt-16 pb-12 px-6 sm:px-8 lg:px-12">
                <div class="mb-8 text-center">
                    <h2 class="text-[32px] font-bold text-primary-emerald mb-2">Welcome back</h2>
                    <p class="text-gray-500 font-medium">Sign in to your client account</p>
                </div>

                <!-- Session Expired Message -->
                <div id="expiredMessage" class="hidden mb-4 p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-600">schedule</span>
                        <p class="text-sm font-semibold"></p>
                    </div>
                </div>

                <form class="space-y-5" id="loginForm" onsubmit="handleLogin(event)">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Email Address</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="material-symbols-outlined text-gray-400 group-focus-within:text-secondary-emerald transition-colors">mail</span>
                            </div>
                            <input class="block w-full pl-11 pr-4 py-4 bg-field-bg border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-secondary-emerald/20 focus:border-secondary-emerald transition-all text-gray-900 placeholder:text-gray-400" id="email" placeholder="name@company.com" type="email" required />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Password</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="material-symbols-outlined text-gray-400 group-focus-within:text-secondary-emerald transition-colors">lock</span>
                            </div>
                            <input class="block w-full pl-11 pr-12 py-4 bg-field-bg border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-secondary-emerald/20 focus:border-secondary-emerald transition-all text-gray-900 placeholder:text-gray-400" id="password" placeholder="••••••••" type="password" required />
                            <button id="togglePassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600" type="button">
                                <span class="material-symbols-outlined">visibility</span>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input id="rememberMe" class="w-5 h-5 rounded border-gray-300 text-secondary-emerald focus:ring-secondary-emerald/20 cursor-pointer" type="checkbox" />
                            <span class="text-sm font-medium text-gray-600 group-hover:text-gray-900 transition-colors">Remember me</span>
                        </label>
                        <a class="text-sm font-bold text-secondary-emerald hover:text-primary-emerald transition-colors" href="../auth/forgot-password.php">Forgot password?</a>
                    </div>

                    <div id="errorMessage" class="hidden mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
                        <p class="text-sm font-semibold"></p>
                    </div>

                    <button type="submit" id="loginBtn" class="w-full py-4 px-6 bg-primary-emerald hover:bg-secondary-emerald text-white rounded-xl font-bold text-lg transition-all shadow-xl shadow-secondary-emerald/20 hover:shadow-secondary-emerald/30 transform hover:-translate-y-0.5 active:translate-y-0">
                        Sign In to Dashboard
                    </button>
                </form>
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-100"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-400 font-medium uppercase tracking-widest text-[10px]">Or continue with</span>
                    </div>
                </div>
                <button class="w-full py-4 px-6 border-2 border-gray-100 bg-white hover:bg-gray-50 text-gray-700 rounded-xl font-bold flex items-center justify-center gap-3 transition-all hover:border-gray-200">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"></path>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"></path>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.66l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"></path>
                    </svg>
                    Sign in with Google
                </button>
                <div class="mt-8 text-center">
                    <p class="text-gray-500 font-medium">
                        Don't have an account?
                        <a class="text-secondary-emerald font-bold hover:underline" href="/auth/register.php">Sign up for free</a>
                    </p>
                </div>
                <div class="mt-12 text-center text-[11px] text-gray-400 font-medium uppercase tracking-widest">
                    © 2026 StoreFlow • Secure Enterprise Infrastructure
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/core/api.js"></script>
    <script src="/assets/js/core/auth.js"></script>
    <script>
        // Password visibility toggle
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const toggleIcon = togglePassword.querySelector('.material-symbols-outlined');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            toggleIcon.textContent = type === 'password' ? 'visibility' : 'visibility_off';
        });

        // Remember me functionality
        const rememberMeCheckbox = document.getElementById('rememberMe');
        const emailInput = document.getElementById('email');

        // Load saved email if remember me was checked
        window.addEventListener('DOMContentLoaded', function() {
            const savedEmail = localStorage.getItem('rememberedEmail');
            if (savedEmail) {
                emailInput.value = savedEmail;
                rememberMeCheckbox.checked = true;
            }
        });

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

                response = await auth.clientLogin(email, password);
                
                // Handle remember me
                if (rememberMeCheckbox.checked) {
                    localStorage.setItem('rememberedEmail', email);
                } else {
                    localStorage.removeItem('rememberedEmail');
                }
                
                window.location.href = '/client/dashboard.php';

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
            window.location.href = '/client/dashboard.php';
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