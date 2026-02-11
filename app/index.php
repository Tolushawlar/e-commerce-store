<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>StoreFlow - Launch Your Online Store</title>
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
                        "primary": "#10b77f",
                        "primary-dark": "#0e9f6e",
                        "lime-accent": "#BEF264",
                        "background-light": "#f6f8f7",
                        "background-dark": "#10221c",
                        "surface-light": "#ffffff",
                        "surface-dark": "#162e26",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.5rem",
                        "lg": "1rem",
                        "xl": "1.5rem",
                        "2xl": "2rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <link href="/assets/css/animations.css" rel="stylesheet" />
    <script src="/assets/js/components/nav-scroll.js" defer></script>
    <script src="/assets/js/components/faq-accordion.js" defer></script>
    <script src="/assets/js/components/scroll-animations.js" defer></script>
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, rgba(6, 78, 59, 0.85) 0%, rgba(6, 95, 70, 0.9) 100%);
        }

        .mesh-gradient {
            background: radial-gradient(at 0% 0%, rgba(16, 183, 127, 0.3) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(190, 242, 100, 0.15) 0px, transparent 50%);
        }

        .dot-pattern {
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 24px 24px;
        }

        .dot-pattern-light {
            background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 32px 32px;
        }

        .perspective-1000 {
            perspective: 1000px;
        }

        .rotate-y-12 {
            transform: rotateY(-12deg) rotateX(5deg);
        }

        .scroll-indicator {
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-10px);
            }

            60% {
                transform: translateY(-5px);
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-float-delayed {
            animation: float 7s ease-in-out infinite 2s;
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-15px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .curved-line path {
            stroke-dasharray: 10;
            animation: dash 30s linear infinite;
        }

        @keyframes dash {
            to {
                stroke-dashoffset: -1000;
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .delay-100 {
            animation-delay: 100ms;
        }

        .delay-200 {
            animation-delay: 200ms;
        }

        .delay-300 {
            animation-delay: 300ms;
        }

        details>summary {
            list-style: none;
        }

        details>summary::-webkit-details-marker {
            display: none;
        }

        details[open] summary~* {
            animation: sweep .3s ease-in-out;
        }

        @keyframes sweep {
            0% {
                opacity: 0;
                margin-top: -10px
            }

            100% {
                opacity: 1;
                margin-top: 0px
            }
        }

        details>summary .icon-plus {
            display: block;
        }

        details>summary .icon-minus {
            display: none;
        }

        details[open]>summary .icon-plus {
            display: none;
        }

        details[open]>summary .icon-minus {
            display: block;
        }
    </style>
</head>

<body class="font-display bg-background-light dark:bg-background-dark text-gray-800 dark:text-gray-100 antialiased overflow-x-hidden">
    <nav id="mainNav" class="fixed w-full z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex-shrink-0 flex items-center gap-2 cursor-pointer">
                    <div class="w-8 h-8 rounded bg-primary flex items-center justify-center text-white font-bold text-xl">S</div>
                    <span id="navLogo" class="font-bold text-xl tracking-tight text-white">StoreFlow</span>
                </div>
                <div class="hidden md:flex space-x-8 items-center">
                    <a class="nav-link text-white/90 hover:text-lime-accent transition-colors font-medium" href="#features">Features</a>
                    <a class="nav-link text-white/90 hover:text-lime-accent transition-colors font-medium" href="#templates">Templates</a>
                    <a class="nav-link text-white/90 hover:text-lime-accent transition-colors font-medium" href="#how-it-works">How it Works</a>
                    <a class="nav-link text-white/90 hover:text-lime-accent transition-colors font-medium" href="#pricing">Pricing</a>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    <a id="navLogin" class="text-white/90 hover:text-lime-accent font-medium" href="/auth/login.php">Log in</a>
                    <a class="bg-primary hover:bg-primary-dark text-white px-5 py-2.5 rounded-lg font-medium transition-all shadow-lg shadow-primary/30" href="#">Get Started</a>
                </div>
                <div class="md:hidden flex items-center">
                    <button id="mobileMenuBtn" class="text-white hover:text-lime-accent focus:outline-none">
                        <span class="material-icons text-3xl">menu</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden">
        <!-- Full-screen background video -->
        <div class="absolute inset-0 z-0">
            <video autoplay loop muted playsinline class="absolute w-full h-full object-cover">
                <source src="https://res.cloudinary.com/dcxknkwjn/video/upload/v1770807735/0_Online_Shopping_Tablet_3840x2160_a0cqp3.mp4" type="video/mp4" />
            </video>
            <!-- Subtle dark overlay for text readability -->
            <div class="absolute inset-0 bg-black/40"></div>
        </div>
        
        <!-- Content -->
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full text-center py-32">
            <div>
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 backdrop-blur-md mb-8 hover:bg-white/15 transition-colors cursor-default animate-fade-in">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-lime-accent opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-lime-accent"></span>
                    </span>
                    <span class="text-lime-accent font-semibold text-sm tracking-wide uppercase">AI-Powered</span>
                    <span class="text-white/80 text-sm border-l border-white/20 pl-2 ml-1">Smart product descriptions</span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl lg:text-7xl xl:text-8xl font-bold text-white leading-[1.1] mb-8 tracking-tight animate-fade-in-up delay-200">
                    Launch Your Online Store in <span class="text-lime-accent">Minutes</span>, Not Months
                </h1>
                
                <p class="text-xl sm:text-2xl text-white/90 mb-12 max-w-3xl mx-auto font-normal leading-relaxed animate-fade-in-up delay-400">
                    The enterprise-grade platform for serious merchants. Leverage AI to manage inventory, payments, and shipping in one premium dashboard.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-5 justify-center mb-12 stagger-children">
                    <a class="group relative bg-lime-accent hover:bg-[#a3e635] text-teal-900 text-lg px-10 py-5 rounded-xl font-bold transition-all shadow-[0_0_30px_rgba(190,242,100,0.4)] hover:shadow-[0_0_40px_rgba(190,242,100,0.6)] transform hover:-translate-y-1 flex items-center justify-center gap-2 btn-ripple magnetic-btn hover-glow" href="#">
                        Get Started
                        <span class="material-icons text-xl transition-transform group-hover:translate-x-1">arrow_forward</span>
                    </a>
                    <a class="flex items-center justify-center gap-3 text-white border-2 border-white/40 hover:bg-white/15 hover:border-white/60 text-lg px-10 py-5 rounded-xl font-medium transition-all backdrop-blur-sm" href="#">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/>
                        </svg>
                        Watch Demo
                    </a>
                </div>
                
                <div class="flex flex-col sm:flex-row items-center justify-center gap-x-10 gap-y-3 text-base font-medium text-white/90 animate-fade-in delay-600">
                    <div class="flex items-center gap-2">
                        <div class="bg-lime-accent/20 rounded-full p-1">
                            <span class="material-icons text-lime-accent text-base">check</span>
                        </div>
                        <span>No credit card required</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="bg-lime-accent/20 rounded-full p-1">
                            <span class="material-icons text-lime-accent text-base">check</span>
                        </div>
                        <span>14-day free trial</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="bg-lime-accent/20 rounded-full p-1">
                            <span class="material-icons text-lime-accent text-base">check</span>
                        </div>
                        <span>Cancel anytime</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-20 flex flex-col items-center gap-2 cursor-pointer opacity-80 hover:opacity-100 transition-opacity">
            <span class="text-white/60 text-xs uppercase tracking-widest font-medium">Scroll to explore</span>
            <div class="w-6 h-9 border-2 border-white/30 rounded-full flex justify-center pt-2">
                <div class="w-1 h-2 bg-lime-accent rounded-full scroll-indicator"></div>
            </div>
        </div>
    </section>
    <section class="bg-white border-t border-[rgba(6,78,59,0.1)] py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 scroll-animate">
                <h3 class="text-gray-500 text-sm font-semibold uppercase tracking-[0.2em] mb-12">Trusted by 500+ Businesses Worldwide</h3>
                <div class="flex flex-wrap justify-center items-center gap-x-12 gap-y-10">
                    <div class="group cursor-pointer">
                        <div class="flex items-center gap-2 grayscale opacity-40 group-hover:grayscale-0 group-hover:opacity-100 group-hover:scale-110 transition-all duration-300 w-[140px] justify-center">
                            <span class="material-icons text-3xl text-indigo-600">bolt</span>
                            <span class="text-xl font-bold text-gray-800">FlashPay</span>
                        </div>
                    </div>
                    <div class="group cursor-pointer">
                        <div class="flex items-center gap-2 grayscale opacity-40 group-hover:grayscale-0 group-hover:opacity-100 group-hover:scale-110 transition-all duration-300 w-[140px] justify-center">
                            <span class="material-icons text-3xl text-sky-600">diamond</span>
                            <span class="text-xl font-bold text-gray-800">Luxe</span>
                        </div>
                    </div>
                    <div class="group cursor-pointer">
                        <div class="flex items-center gap-2 grayscale opacity-40 group-hover:grayscale-0 group-hover:opacity-100 group-hover:scale-110 transition-all duration-300 w-[140px] justify-center">
                            <span class="material-icons text-3xl text-orange-600">rocket_launch</span>
                            <span class="text-xl font-bold text-gray-800">Boost</span>
                        </div>
                    </div>
                    <div class="group cursor-pointer">
                        <div class="flex items-center gap-2 grayscale opacity-40 group-hover:grayscale-0 group-hover:opacity-100 group-hover:scale-110 transition-all duration-300 w-[140px] justify-center">
                            <span class="material-icons text-3xl text-pink-600">pets</span>
                            <span class="text-xl font-bold text-gray-800">Petals</span>
                        </div>
                    </div>
                    <div class="group cursor-pointer">
                        <div class="flex items-center gap-2 grayscale opacity-40 group-hover:grayscale-0 group-hover:opacity-100 group-hover:scale-110 transition-all duration-300 w-[140px] justify-center">
                            <span class="material-icons text-3xl text-green-600">eco</span>
                            <span class="text-xl font-bold text-gray-800">Organic</span>
                        </div>
                    </div>
                    <div class="group cursor-pointer">
                        <div class="flex items-center gap-2 grayscale opacity-40 group-hover:grayscale-0 group-hover:opacity-100 group-hover:scale-110 transition-all duration-300 w-[140px] justify-center">
                            <span class="material-icons text-3xl text-blue-600">local_shipping</span>
                            <span class="text-xl font-bold text-gray-800">Swift</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 divide-x-0 md:divide-x divide-gray-100 pt-10 border-t border-gray-100 scroll-animate">
                <div class="text-center px-4 group">
                    <div class="flex justify-center mb-2">
                        <span class="material-icons text-lime-accent text-lg group-hover:scale-110 transition-transform icon-bounce">store</span>
                    </div>
                    <div class="text-4xl md:text-[48px] font-extrabold text-[#10b77f] leading-none mb-2 tracking-tight">500+</div>
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">Active Stores</div>
                </div>
                <div class="text-center px-4 group">
                    <div class="flex justify-center mb-2">
                        <span class="material-icons text-lime-accent text-lg group-hover:scale-110 transition-transform icon-bounce">inventory_2</span>
                    </div>
                    <div class="text-4xl md:text-[48px] font-extrabold text-[#10b77f] leading-none mb-2 tracking-tight">50K+</div>
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">Products Listed</div>
                </div>
                <div class="text-center px-4 group">
                    <div class="flex justify-center mb-2">
                        <span class="material-icons text-lime-accent text-lg group-hover:scale-110 transition-transform">payments</span>
                    </div>
                    <div class="text-4xl md:text-[48px] font-extrabold text-[#10b77f] leading-none mb-2 tracking-tight">₦2.5B+</div>
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">Revenue Processed</div>
                </div>
                <div class="text-center px-4 group">
                    <div class="flex justify-center mb-2">
                        <span class="material-icons text-lime-accent text-lg group-hover:scale-110 transition-transform">verified_user</span>
                    </div>
                    <div class="text-4xl md:text-[48px] font-extrabold text-[#10b77f] leading-none mb-2 tracking-tight">99.9%</div>
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">Platform Uptime</div>
                </div>
            </div>
        </div>
    </section>
    <section class="py-24 bg-[#F8FAFC] relative overflow-hidden" id="features">
        <div class="absolute inset-0 dot-pattern opacity-60 pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-20 scroll-animate">
                <span class="inline-block py-1 px-3 rounded-full bg-[#BEF264]/10 text-[#BEF264] font-bold text-sm tracking-widest uppercase mb-4 border border-[#BEF264]/20">Features</span>
                <h2 class="text-4xl md:text-[48px] font-bold text-[#064E3B] leading-tight mb-6">Everything you need to run your online business</h2>
                <p class="text-xl text-gray-600 leading-relaxed font-light">
                    We've packed StoreFlow with powerful enterprise-grade tools to help you manage every aspect of your e-commerce business without the complexity.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-[16px] p-8 shadow-sm border border-gray-100 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] hover:-translate-y-1 transition-all duration-300 group h-full flex flex-col reveal-on-scroll delay-100 hover-lift">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center mb-6 shadow-lg shadow-emerald-200 group-hover:scale-110 transition-transform duration-300">
                        <span class="material-icons text-white text-3xl">storefront</span>
                    </div>
                    <h3 class="text-[22px] font-bold text-gray-900 mb-3 group-hover:text-[#064E3B] transition-colors">Multi-Store Management</h3>
                    <p class="text-gray-600 leading-relaxed mb-6 flex-grow">
                        Control multiple brands or regional stores from a single dashboard. Centralize inventory and sync updates instantly.
                    </p>
                    <ul class="space-y-3 mt-auto border-t border-gray-50 pt-5">
                        <li class="flex items-start gap-3 text-sm font-medium text-gray-700">
                            <span class="material-icons text-[#BEF264] text-lg bg-green-50 rounded-full">check_circle</span>
                            <span>Unified inventory sync</span>
                        </li>
                        <li class="flex items-start gap-3 text-sm font-medium text-gray-700">
                            <span class="material-icons text-[#BEF264] text-lg bg-green-50 rounded-full">check_circle</span>
                            <span>Role-based access</span>
                        </li>
                        <li class="flex items-start gap-3 text-sm font-medium text-gray-700">
                            <span class="material-icons text-[#BEF264] text-lg bg-green-50 rounded-full">check_circle</span>
                            <span>Cross-store analytics</span>
                        </li>
                    </ul>
                </div>
                <div class="bg-white rounded-[16px] p-8 shadow-sm border border-gray-100 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] hover:-translate-y-1 transition-all duration-300 group h-full flex flex-col reveal-on-scroll delay-200 hover-lift">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center mb-6 shadow-lg shadow-purple-200 group-hover:scale-110 transition-transform duration-300">
                        <span class="material-icons text-white text-3xl">inventory_2</span>
                    </div>
                    <h3 class="text-[22px] font-bold text-gray-900 mb-3 group-hover:text-purple-900 transition-colors">Product Management</h3>
                    <p class="text-gray-600 leading-relaxed mb-6 flex-grow">
                        Add unlimited variants, manage digital products, and automate stock alerts. Bulk edit thousands of SKUs in seconds.
                    </p>
                    <ul class="space-y-3 mt-auto border-t border-gray-50 pt-5">
                        <li class="flex items-start gap-3 text-sm font-medium text-gray-700">
                            <span class="material-icons text-[#BEF264] text-lg bg-green-50 rounded-full">check_circle</span>
                            <span>Bulk CSV import/export</span>
                        </li>
                        <li class="flex items-start gap-3 text-sm font-medium text-gray-700">
                            <span class="material-icons text-[#BEF264] text-lg bg-green-50 rounded-full">check_circle</span>
                            <span>Digital product delivery</span>
                        </li>
                        <li class="flex items-start gap-3 text-sm font-medium text-gray-700">
                            <span class="material-icons text-[#BEF264] text-lg bg-green-50 rounded-full">check_circle</span>
                            <span>Smart collections</span>
                        </li>
                    </ul>
                </div>
                <div class="bg-white rounded-[16px] p-8 shadow-sm border border-gray-100 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] hover:-translate-y-1 transition-all duration-300 group h-full flex flex-col reveal-on-scroll delay-300 hover-lift">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center mb-6 shadow-lg shadow-blue-200 group-hover:scale-110 transition-transform duration-300">
                        <span class="material-icons text-white text-3xl">credit_card</span>
                    </div>
                    <h3 class="text-[22px] font-bold text-gray-900 mb-3 group-hover:text-blue-900 transition-colors">Payment Integration</h3>
                    <p class="text-gray-600 leading-relaxed mb-6 flex-grow">
                        Accept payments globally with 100+ gateways. Built-in fraud protection and automatic tax calculations included.
                    </p>
                    <ul class="space-y-3 mt-auto border-t border-gray-50 pt-5">
                        <li class="flex items-start gap-3 text-sm font-medium text-gray-700">
                            <span class="material-icons text-[#BEF264] text-lg bg-green-50 rounded-full">check_circle</span>
                            <span>100+ Payment Gateways</span>
                        </li>
                        <li class="flex items-start gap-3 text-sm font-medium text-gray-700">
                            <span class="material-icons text-[#BEF264] text-lg bg-green-50 rounded-full">check_circle</span>
                            <span>Auto-tax calculation</span>
                        </li>
                        <li class="flex items-start gap-3 text-sm font-medium text-gray-700">
                            <span class="material-icons text-[#BEF264] text-lg bg-green-50 rounded-full">check_circle</span>
                            <span>Multi-currency support</span>
                        </li>
                    </ul>
                </div>
                <div class="bg-white rounded-[16px] p-8 shadow-sm border border-gray-100 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] hover:-translate-y-1 transition-all duration-300 group h-full flex flex-col reveal-on-scroll delay-100 hover-lift">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center mb-6 shadow-lg shadow-orange-200 group-hover:scale-110 transition-transform duration-300">
                        <span class="material-icons text-white text-3xl">shopping_cart</span>
                    </div>
                    <h3 class="text-[22px] font-bold text-gray-900 mb-3 group-hover:text-orange-900 transition-colors">Order Management</h3>
                    <p class="text-gray-600 leading-relaxed mb-6 flex-grow">
                        Streamline fulfillment with automated workflows. Print labels, track shipments, and manage returns in one place.
                    </p>
                    <ul class="space-y-3 mt-auto border-t border-gray-50 pt-5">
                        <li class="flex items-start gap-3 text-sm font-medium text-gray-700">
                            <span class="material-icons text-[#BEF264] text-lg bg-green-50 rounded-full">check_circle</span>
                            <span>Automated fulfillment</span>
                        </li>
                        <li class="flex items-start gap-3 text-sm font-medium text-gray-700">
                            <span class="material-icons text-[#BEF264] text-lg bg-green-50 rounded-full">check_circle</span>
                            <span>Branded tracking page</span>
                        </li>
                        <li class="flex items-start gap-3 text-sm font-medium text-gray-700">
                            <span class="material-icons text-[#BEF264] text-lg bg-green-50 rounded-full">check_circle</span>
                            <span>Easy returns portal</span>
                        </li>
                    </ul>
                </div>
                <div class="bg-white rounded-[16px] p-8 shadow-sm border border-gray-100 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] hover:-translate-y-1 transition-all duration-300 group h-full flex flex-col reveal-on-scroll delay-200 hover-lift">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-pink-400 to-pink-600 flex items-center justify-center mb-6 shadow-lg shadow-pink-200 group-hover:scale-110 transition-transform duration-300">
                        <span class="material-icons text-white text-3xl">bar_chart</span>
                    </div>
                    <h3 class="text-[22px] font-bold text-gray-900 mb-3 group-hover:text-pink-900 transition-colors">Analytics Dashboard</h3>
                    <p class="text-gray-600 leading-relaxed mb-6 flex-grow">
                        Make data-driven decisions with real-time reporting. Track sales, visitor behavior, and conversion rates effortlessly.
                    </p>
                    <ul class="space-y-3 mt-auto border-t border-gray-50 pt-5">
                        <li class="flex items-start gap-3 text-sm font-medium text-gray-700">
                            <span class="material-icons text-[#BEF264] text-lg bg-green-50 rounded-full">check_circle</span>
                            <span>Real-time sales view</span>
                        </li>
                        <li class="flex items-start gap-3 text-sm font-medium text-gray-700">
                            <span class="material-icons text-[#BEF264] text-lg bg-green-50 rounded-full">check_circle</span>
                            <span>Customer cohorts</span>
                        </li>
                        <li class="flex items-start gap-3 text-sm font-medium text-gray-700">
                            <span class="material-icons text-[#BEF264] text-lg bg-green-50 rounded-full">check_circle</span>
                            <span>Custom report builder</span>
                        </li>
                    </ul>
                </div>
                <div class="bg-white rounded-[16px] p-8 shadow-sm border border-gray-100 hover:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)] hover:-translate-y-1 transition-all duration-300 group h-full flex flex-col reveal-on-scroll delay-300 hover-lift">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center mb-6 shadow-lg shadow-teal-200 group-hover:scale-110 transition-transform duration-300">
                        <span class="material-icons text-white text-3xl">groups</span>
                    </div>
                    <h3 class="text-[22px] font-bold text-gray-900 mb-3 group-hover:text-teal-900 transition-colors">Customer Portal</h3>
                    <p class="text-gray-600 leading-relaxed mb-6 flex-grow">
                        Build loyalty with a dedicated customer accounts area. Allow self-service for order tracking and reordering.
                    </p>
                    <ul class="space-y-3 mt-auto border-t border-gray-50 pt-5">
                        <li class="flex items-start gap-3 text-sm font-medium text-gray-700">
                            <span class="material-icons text-[#BEF264] text-lg bg-green-50 rounded-full">check_circle</span>
                            <span>Order history access</span>
                        </li>
                        <li class="flex items-start gap-3 text-sm font-medium text-gray-700">
                            <span class="material-icons text-[#BEF264] text-lg bg-green-50 rounded-full">check_circle</span>
                            <span>Saved addresses</span>
                        </li>
                        <li class="flex items-start gap-3 text-sm font-medium text-gray-700">
                            <span class="material-icons text-[#BEF264] text-lg bg-green-50 rounded-full">check_circle</span>
                            <span>Wishlist management</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section class="py-24 bg-gradient-to-b from-[#F8FAFC] to-white relative overflow-hidden" id="templates">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16 scroll-animate">
                <span class="inline-block py-1 px-3 rounded-full bg-[#BEF264]/10 text-[#BEF264] font-bold text-sm tracking-widest uppercase mb-4 border border-[#BEF264]/20">Store Templates</span>
                <h2 class="text-4xl md:text-[48px] font-bold text-[#064E3B] leading-tight mb-6">Beautiful stores, ready in one click</h2>
                <p class="text-xl text-gray-600 leading-relaxed font-light">
                    Choose from over 100 professionally designed templates. Fully customizable, mobile-ready, and optimized for conversion.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="bg-white rounded-[20px] shadow-md border border-gray-200 transition-all duration-300 group overflow-hidden reveal-on-scroll delay-100 flex flex-col card-hover">
                    <div class="relative h-[400px] overflow-hidden bg-gray-100">
                        <img alt="Bold Modern Template" class="w-full h-full object-cover object-top transition-transform duration-700 group-hover:scale-105 hover-scale" loading="lazy" src="https://res.cloudinary.com/dcxknkwjn/image/upload/v1770637344/Screenshot_2026-02-09_123901_xnr5yp.png" />
                        <div class="absolute top-6 right-6 z-20">
                            <span class="bg-[#064E3B] text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg shimmer">Most Popular</span>
                        </div>
                        <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center gap-4 z-10">
                            <button class="bg-white text-gray-900 font-bold py-3 px-8 rounded-full hover:bg-gray-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                                Preview Template
                            </button>
                            <button class="bg-[#BEF264] text-[#064E3B] font-bold py-3 px-8 rounded-full hover:bg-[#b0e64c] transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 delay-75 shadow-lg">
                                Use This Template
                            </button>
                        </div>
                    </div>
                    <div class="p-8 flex-grow flex flex-col">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">Bold Modern</h3>
                                <p class="text-gray-500 text-sm font-medium">Perfect for fashion &amp; lifestyle brands</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 mb-6">
                            <div class="w-6 h-6 rounded-full bg-[#111827] border border-gray-200"></div>
                            <div class="w-6 h-6 rounded-full bg-[#F3F4F6] border border-gray-200"></div>
                            <div class="w-6 h-6 rounded-full bg-[#BEF264] border border-gray-200"></div>
                            <div class="w-6 h-6 rounded-full bg-[#E5E7EB] border border-gray-200"></div>
                            <div class="w-6 h-6 rounded-full bg-white border border-gray-200"></div>
                        </div>
                        <div class="grid grid-cols-3 gap-4 mb-8 pt-6 border-t border-gray-100">
                            <div class="flex flex-col items-center text-center gap-2">
                                <span class="material-symbols-outlined text-gray-400">smartphone</span>
                                <span class="text-xs font-semibold text-gray-600">Mobile Ready</span>
                            </div>
                            <div class="flex flex-col items-center text-center gap-2">
                                <span class="material-symbols-outlined text-gray-400">speed</span>
                                <span class="text-xs font-semibold text-gray-600">Fast Load</span>
                            </div>
                            <div class="flex flex-col items-center text-center gap-2">
                                <span class="material-symbols-outlined text-gray-400">google_my_business</span>
                                <span class="text-xs font-semibold text-gray-600">SEO Optimized</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-auto">
                            <a class="text-[#064E3B] font-semibold hover:text-[#BEF264] transition-colors flex items-center gap-1 group/link" href="#">
                                View Demo <span class="material-icons text-sm transition-transform group-hover/link:translate-x-1">arrow_forward</span>
                            </a>
                            <a class="text-[#064E3B] font-semibold hover:text-[#BEF264] transition-colors flex items-center gap-1 group/link" href="#">
                                Customize <span class="material-icons text-sm transition-transform group-hover/link:translate-x-1">edit</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-[20px] shadow-md border border-gray-200 hover:shadow-2xl transition-all duration-300 group overflow-hidden fade-in-up delay-200 flex flex-col">
                    <div class="relative h-[400px] overflow-hidden bg-gray-100">
                        <img alt="Classic E-commerce Template" class="w-full h-full object-cover object-top transition-transform duration-700 group-hover:scale-105" loading="lazy" src="https://res.cloudinary.com/dcxknkwjn/image/upload/v1770645616/e6f69931-c0e4-4bc2-98ad-70ddcb53e9b8.png" />
                        <div class="absolute top-6 right-6 z-20">
                            <span class="bg-[#3B82F6] text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">New Arrival</span>
                        </div>
                        <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center gap-4 z-10">
                            <button class="bg-white text-gray-900 font-bold py-3 px-8 rounded-full hover:bg-gray-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                                Preview Template
                            </button>
                            <button class="bg-[#BEF264] text-[#064E3B] font-bold py-3 px-8 rounded-full hover:bg-[#b0e64c] transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 delay-75 shadow-lg">
                                Use This Template
                            </button>
                        </div>
                    </div>
                    <div class="p-8 flex-grow flex flex-col">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">Classic E-commerce</h3>
                                <p class="text-gray-500 text-sm font-medium">Ideal for large inventories &amp; electronics</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 mb-6">
                            <div class="w-6 h-6 rounded-full bg-[#1e3a8a] border border-gray-200"></div>
                            <div class="w-6 h-6 rounded-full bg-[#60a5fa] border border-gray-200"></div>
                            <div class="w-6 h-6 rounded-full bg-[#f3f4f6] border border-gray-200"></div>
                            <div class="w-6 h-6 rounded-full bg-[#9ca3af] border border-gray-200"></div>
                            <div class="w-6 h-6 rounded-full bg-white border border-gray-200"></div>
                        </div>
                        <div class="grid grid-cols-3 gap-4 mb-8 pt-6 border-t border-gray-100">
                            <div class="flex flex-col items-center text-center gap-2">
                                <span class="material-symbols-outlined text-gray-400">shopping_cart</span>
                                <span class="text-xs font-semibold text-gray-600">Quick Buy</span>
                            </div>
                            <div class="flex flex-col items-center text-center gap-2">
                                <span class="material-symbols-outlined text-gray-400">filter_alt</span>
                                <span class="text-xs font-semibold text-gray-600">Adv. Filter</span>
                            </div>
                            <div class="flex flex-col items-center text-center gap-2">
                                <span class="material-symbols-outlined text-gray-400">reviews</span>
                                <span class="text-xs font-semibold text-gray-600">Review System</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-auto">
                            <a class="text-[#064E3B] font-semibold hover:text-[#BEF264] transition-colors flex items-center gap-1 group/link" href="#">
                                View Demo <span class="material-icons text-sm transition-transform group-hover/link:translate-x-1">arrow_forward</span>
                            </a>
                            <a class="text-[#064E3B] font-semibold hover:text-[#BEF264] transition-colors flex items-center gap-1 group/link" href="#">
                                Customize <span class="material-icons text-sm transition-transform group-hover/link:translate-x-1">edit</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-[20px] shadow-md border border-gray-200 hover:shadow-2xl transition-all duration-300 group overflow-hidden fade-in-up delay-300 flex flex-col">
                    <div class="relative h-[400px] overflow-hidden bg-gray-100">
                        <img alt="Minimal Clean Template" class="w-full h-full object-cover object-top transition-transform duration-700 group-hover:scale-105" loading="lazy" src="https://res.cloudinary.com/dcxknkwjn/image/upload/v1770647899/791118a9-7ee4-4e1e-acd8-07ed442c6ebb.png" />
                        <div class="absolute top-6 right-6 z-20">
                            <span class="bg-[#064E3B] text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">Most Popular</span>
                        </div>
                        <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center gap-4 z-10">
                            <button class="bg-white text-gray-900 font-bold py-3 px-8 rounded-full hover:bg-gray-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                                Preview Template
                            </button>
                            <button class="bg-[#BEF264] text-[#064E3B] font-bold py-3 px-8 rounded-full hover:bg-[#b0e64c] transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 delay-75 shadow-lg">
                                Use This Template
                            </button>
                        </div>
                    </div>
                    <div class="p-8 flex-grow flex flex-col">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">Minimal Clean</h3>
                                <p class="text-gray-500 text-sm font-medium">Focused on product photography &amp; art</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 mb-6">
                            <div class="w-6 h-6 rounded-full bg-[#f8fafc] border border-gray-200"></div>
                            <div class="w-6 h-6 rounded-full bg-[#e2e8f0] border border-gray-200"></div>
                            <div class="w-6 h-6 rounded-full bg-[#94a3b8] border border-gray-200"></div>
                            <div class="w-6 h-6 rounded-full bg-[#475569] border border-gray-200"></div>
                            <div class="w-6 h-6 rounded-full bg-[#0f172a] border border-gray-200"></div>
                        </div>
                        <div class="grid grid-cols-3 gap-4 mb-8 pt-6 border-t border-gray-100">
                            <div class="flex flex-col items-center text-center gap-2">
                                <span class="material-symbols-outlined text-gray-400">grid_view</span>
                                <span class="text-xs font-semibold text-gray-600">Masonry Grid</span>
                            </div>
                            <div class="flex flex-col items-center text-center gap-2">
                                <span class="material-symbols-outlined text-gray-400">zoom_in</span>
                                <span class="text-xs font-semibold text-gray-600">Image Zoom</span>
                            </div>
                            <div class="flex flex-col items-center text-center gap-2">
                                <span class="material-symbols-outlined text-gray-400">font_download</span>
                                <span class="text-xs font-semibold text-gray-600">Custom Fonts</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-auto">
                            <a class="text-[#064E3B] font-semibold hover:text-[#BEF264] transition-colors flex items-center gap-1 group/link" href="#">
                                View Demo <span class="material-icons text-sm transition-transform group-hover/link:translate-x-1">arrow_forward</span>
                            </a>
                            <a class="text-[#064E3B] font-semibold hover:text-[#BEF264] transition-colors flex items-center gap-1 group/link" href="#">
                                Customize <span class="material-icons text-sm transition-transform group-hover/link:translate-x-1">edit</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-[20px] shadow-md border border-gray-200 hover:shadow-2xl transition-all duration-300 group overflow-hidden fade-in-up delay-300 flex flex-col">
                    <div class="relative h-[400px] overflow-hidden bg-gray-100">
                        <img alt="Premium Luxury Template" class="w-full h-full object-cover object-top transition-transform duration-700 group-hover:scale-105" loading="lazy" src="https://res.cloudinary.com/dcxknkwjn/image/upload/v1770651535/d804e93e-c6ee-4d2b-b08f-67c5f1e0e0be.png" />
                        <div class="absolute top-6 right-6 z-20">
                            <span class="bg-[#3B82F6] text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">New Arrival</span>
                        </div>
                        <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center gap-4 z-10">
                            <button class="bg-white text-gray-900 font-bold py-3 px-8 rounded-full hover:bg-gray-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                                Preview Template
                            </button>
                            <button class="bg-[#BEF264] text-[#064E3B] font-bold py-3 px-8 rounded-full hover:bg-[#b0e64c] transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 delay-75 shadow-lg">
                                Use This Template
                            </button>
                        </div>
                    </div>
                    <div class="p-8 flex-grow flex flex-col">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">Premium Luxury</h3>
                                <p class="text-gray-500 text-sm font-medium">Elegant design for high-end goods</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 mb-6">
                            <div class="w-6 h-6 rounded-full bg-[#27272a] border border-gray-200"></div>
                            <div class="w-6 h-6 rounded-full bg-[#d4af37] border border-gray-200"></div>
                            <div class="w-6 h-6 rounded-full bg-[#fce7f3] border border-gray-200"></div>
                            <div class="w-6 h-6 rounded-full bg-[#52525b] border border-gray-200"></div>
                            <div class="w-6 h-6 rounded-full bg-white border border-gray-200"></div>
                        </div>
                        <div class="grid grid-cols-3 gap-4 mb-8 pt-6 border-t border-gray-100">
                            <div class="flex flex-col items-center text-center gap-2">
                                <span class="material-symbols-outlined text-gray-400">diamond</span>
                                <span class="text-xs font-semibold text-gray-600">Premium UI</span>
                            </div>
                            <div class="flex flex-col items-center text-center gap-2">
                                <span class="material-symbols-outlined text-gray-400">animation</span>
                                <span class="text-xs font-semibold text-gray-600">Animations</span>
                            </div>
                            <div class="flex flex-col items-center text-center gap-2">
                                <span class="material-symbols-outlined text-gray-400">verified</span>
                                <span class="text-xs font-semibold text-gray-600">Trust Badges</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-auto">
                            <a class="text-[#064E3B] font-semibold hover:text-[#BEF264] transition-colors flex items-center gap-1 group/link" href="#">
                                View Demo <span class="material-icons text-sm transition-transform group-hover/link:translate-x-1">arrow_forward</span>
                            </a>
                            <a class="text-[#064E3B] font-semibold hover:text-[#BEF264] transition-colors flex items-center gap-1 group/link" href="#">
                                Customize <span class="material-icons text-sm transition-transform group-hover/link:translate-x-1">edit</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-16 text-center">
                <a class="inline-flex items-center justify-center gap-2 text-gray-900 font-bold hover:text-primary transition-colors text-lg border-b-2 border-transparent hover:border-primary pb-1" href="#">
                    View all templates <span class="material-icons text-lg">arrow_forward</span>
                </a>
            </div>
        </div>
    </section>
    <section class="py-24 bg-white relative overflow-hidden" id="how-it-works">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-20 relative">
                <span class="inline-block py-1 px-3 rounded-full bg-[#BEF264]/10 text-[#BEF264] font-bold text-sm tracking-widest uppercase mb-4 border border-[#BEF264]/20">How It Works</span>
                <h2 class="text-4xl md:text-[48px] font-bold text-[#064E3B] leading-tight mb-6">Start selling in 3 simple steps</h2>
                <p class="text-xl text-gray-600 leading-relaxed font-light">
                    Launch your business with our intuitive onboarding process designed for speed.
                </p>
                <div class="absolute -right-4 top-1/2 transform -translate-y-1/2 hidden lg:flex items-center gap-2 bg-[#BEF264] px-4 py-2 rounded-full shadow-lg border-2 border-white rotate-3 hover:rotate-0 transition-transform duration-300">
                    <span class="text-2xl">⚡</span>
                    <span class="text-[#064E3B] font-bold text-sm">Average setup time: 8 minutes</span>
                </div>
            </div>
            <div class="relative">
                <div class="absolute top-0 bottom-0 left-1/2 w-0.5 bg-dashed border-l-2 border-gray-200 border-dashed hidden lg:block -translate-x-1/2 h-[90%] z-0"></div>
                <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-24 items-center mb-24 lg:mb-32">
                    <div class="order-2 lg:order-1 relative">
                        <div class="absolute -left-12 -top-12 text-[180px] font-bold text-gray-50 opacity-50 select-none z-[-1] leading-none">01</div>
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-14 h-14 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-200">
                                <span class="material-icons text-white text-2xl">person_add_alt</span>
                            </div>
                            <h3 class="text-3xl font-bold text-gray-900">Sign Up Free</h3>
                        </div>
                        <p class="text-lg text-gray-600 leading-relaxed mb-8">
                            Get started in seconds without a credit card. Our smart wizard guides you through setting up your account, currency, and business details.
                        </p>
                        <ul class="space-y-4">
                            <li class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 flex items-center justify-center mt-0.5">
                                    <span class="material-icons text-green-600 text-sm">check</span>
                                </div>
                                <span class="text-gray-700 font-medium">No credit card required</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 flex items-center justify-center mt-0.5">
                                    <span class="material-icons text-green-600 text-sm">check</span>
                                </div>
                                <span class="text-gray-700 font-medium">Instant account verification</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 flex items-center justify-center mt-0.5">
                                    <span class="material-icons text-green-600 text-sm">check</span>
                                </div>
                                <span class="text-gray-700 font-medium">14-day premium trial included</span>
                            </li>
                        </ul>
                    </div>
                    <div class="order-1 lg:order-2 perspective-1000 group">
                        <div class="relative transform transition-transform duration-700 lg:rotate-y-12 group-hover:rotate-0">
                            <div class="absolute -inset-4 bg-gradient-to-r from-emerald-100 to-green-50 rounded-[2rem] transform rotate-2 opacity-50 blur-xl"></div>
                            <div class="relative bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden">
                                <div class="bg-gray-50 border-b border-gray-100 px-4 py-3 flex items-center gap-2">
                                    <div class="flex gap-1.5">
                                        <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                        <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                        <div class="w-3 h-3 rounded-full bg-green-400"></div>
                                    </div>
                                    <div class="flex-1 ml-4 bg-white border border-gray-200 rounded-md h-6 w-full max-w-sm mx-auto shadow-sm"></div>
                                </div>
                                <img alt="Sign up form interface" class="w-full h-auto object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuB-w65YZiSeHJF5iQ-0iZxqa7SecIVD4S-JfqBYNTIgstuCQCHXUjtHHgDfHxoM7RLZWlcvmptTu33_nQLe-symO-Y1xZ_dw-5D2soFgtBpRMZIOR3I_Ya3xbBOPSxZIyo6odnkHv40gYhTkOFYgEf11JSk9kefFI7saNWKOiKOFJK2yhaPX6QaM9ww-cm6ccZhJIRzdIVwsn679QiTD_TiHQ3lUjqTQfBrBWqY2efqNzdjrVaDtADI95bEEBroOXNzR5fE4MqYJ4s" />
                            </div>
                            <svg class="curved-line absolute -bottom-32 -left-20 w-48 h-48 hidden lg:block text-gray-300 pointer-events-none z-[-1]" fill="none" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                                <path d="M180,20 C180,100 20,100 20,180" stroke="currentColor" stroke-dasharray="8 8" stroke-linecap="round" stroke-width="2"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-24 items-center mb-24 lg:mb-32">
                    <div class="order-1 perspective-1000 group">
                        <div class="relative transform transition-transform duration-700 lg:-rotate-y-12 group-hover:rotate-0">
                            <div class="absolute -inset-4 bg-gradient-to-r from-purple-100 to-indigo-50 rounded-[2rem] transform -rotate-2 opacity-50 blur-xl"></div>
                            <div class="relative bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden">
                                <div class="bg-gray-50 border-b border-gray-100 px-4 py-3 flex items-center gap-2">
                                    <div class="flex gap-1.5">
                                        <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                        <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                        <div class="w-3 h-3 rounded-full bg-green-400"></div>
                                    </div>
                                    <div class="flex-1 ml-4 bg-white border border-gray-200 rounded-md h-6 w-full max-w-sm mx-auto shadow-sm"></div>
                                </div>
                                <img alt="Template selection interface" class="w-full h-auto object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCiLK-rPm1w05Y_jZ0D6z2Hqwx8l-j9oM7hkpYAEXIm3s3IOZfsyvdcI3QC4SthvYJ5U_9f-tMj5iSAPC2QnfmiBU3gNBLxaqIH1SVoWAljVP8NDQZvasVEuGPkZo2zNqAWD7-JsWxuE2rvZKgOA6Kvir89J6g-G8LIPHsqqoLIL9hF2swfr-cmJU9IE5wgrYNnzfS1YN8fn5o-cMxkTUt21knIXMcL-rT7vpkmivnxMMVUPrvkklSZyZUJqCmOJOLdYEgP625_J9E" />
                            </div>
                        </div>
                    </div>
                    <div class="order-2 text-left relative">
                        <div class="absolute -right-12 -top-12 text-[180px] font-bold text-gray-50 opacity-50 select-none z-[-1] leading-none">02</div>
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-14 h-14 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center shadow-lg shadow-purple-200">
                                <span class="material-icons text-white text-2xl">brush</span>
                            </div>
                            <h3 class="text-3xl font-bold text-gray-900">Customize Store</h3>
                        </div>
                        <p class="text-lg text-gray-600 leading-relaxed mb-8">
                            Select from 50+ professional themes and make it yours. Drag-and-drop builder lets you change colors, fonts, and layouts without coding.
                        </p>
                        <ul class="space-y-4">
                            <li class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center mt-0.5">
                                    <span class="material-icons text-purple-600 text-sm">check</span>
                                </div>
                                <span class="text-gray-700 font-medium">50+ mobile-responsive themes</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center mt-0.5">
                                    <span class="material-icons text-purple-600 text-sm">check</span>
                                </div>
                                <span class="text-gray-700 font-medium">Drag-and-drop visual editor</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center mt-0.5">
                                    <span class="material-icons text-purple-600 text-sm">check</span>
                                </div>
                                <span class="text-gray-700 font-medium">Custom domain connection</span>
                            </li>
                        </ul>
                        <svg class="curved-line absolute -bottom-32 -left-20 w-48 h-48 hidden lg:block text-gray-300 pointer-events-none z-[-1] transform scale-x-[-1]" fill="none" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                            <path d="M180,20 C180,100 20,100 20,180" stroke="currentColor" stroke-dasharray="8 8" stroke-linecap="round" stroke-width="2"></path>
                        </svg>
                    </div>
                </div>
                <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-24 items-center">
                    <div class="order-2 lg:order-1 relative">
                        <div class="absolute -left-12 -top-12 text-[180px] font-bold text-gray-50 opacity-50 select-none z-[-1] leading-none">03</div>
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-14 h-14 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center shadow-lg shadow-orange-200">
                                <span class="material-icons text-white text-2xl">rocket_launch</span>
                            </div>
                            <h3 class="text-3xl font-bold text-gray-900">Start Selling</h3>
                        </div>
                        <p class="text-lg text-gray-600 leading-relaxed mb-8">
                            Launch your store to the world. Accept payments securely, track orders in real-time, and watch your business grow from the dashboard.
                        </p>
                        <ul class="space-y-4">
                            <li class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-orange-100 flex items-center justify-center mt-0.5">
                                    <span class="material-icons text-orange-600 text-sm">check</span>
                                </div>
                                <span class="text-gray-700 font-medium">One-click launch</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-orange-100 flex items-center justify-center mt-0.5">
                                    <span class="material-icons text-orange-600 text-sm">check</span>
                                </div>
                                <span class="text-gray-700 font-medium">Global payment processing</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-orange-100 flex items-center justify-center mt-0.5">
                                    <span class="material-icons text-orange-600 text-sm">check</span>
                                </div>
                                <span class="text-gray-700 font-medium">Real-time analytics app</span>
                            </li>
                        </ul>
                    </div>
                    <div class="order-1 lg:order-2 perspective-1000 group">
                        <div class="relative transform transition-transform duration-700 lg:rotate-y-12 group-hover:rotate-0">
                            <div class="absolute -inset-4 bg-gradient-to-r from-orange-100 to-amber-50 rounded-[2rem] transform rotate-2 opacity-50 blur-xl"></div>
                            <div class="relative">
                                <div class="relative bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden z-10">
                                    <div class="bg-gray-50 border-b border-gray-100 px-4 py-3 flex items-center gap-2">
                                        <div class="flex gap-1.5">
                                            <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                            <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                            <div class="w-3 h-3 rounded-full bg-green-400"></div>
                                        </div>
                                        <div class="flex-1 ml-4 bg-white border border-gray-200 rounded-md h-6 w-full max-w-sm mx-auto shadow-sm"></div>
                                    </div>
                                    <img alt="Live storefront desktop" class="w-full h-auto object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDkLjrbhJt0vz0TY0PEtWjf3hggTIxGxSmOsgswFIpjq1iW5LXImONT-TEpWMHt19lMzpszREEIT2zSjO0q-jWej2Xoc2xWL-OJ3G23NclwLcIzJqmJcpvi86vJZZUZo4QQpUxV3qg9hVX_I41M32QTOwN57cdNhzD5KmsfgrnRFS47xi4giSpJ8qU-Hp4RiSgv9rbVEtgmaD5dqX3Vc39X9CYeiZeoBSjmlFG5zi7bZ0otiNSbVzG1zc9sutykKrDC_7mscqwt92o" />
                                </div>
                                <div class="absolute -bottom-8 -right-8 w-1/3 bg-gray-900 rounded-[2rem] border-4 border-gray-800 shadow-[0_20px_50px_-12px_rgba(0,0,0,0.5)] z-20 transform translate-y-4 animate-float">
                                    <div class="h-full w-full bg-white rounded-[1.7rem] overflow-hidden relative">
                                        <div class="absolute top-0 inset-x-0 h-6 bg-gray-100 z-10 flex justify-center items-center">
                                            <div class="w-16 h-4 bg-gray-800 rounded-b-lg"></div>
                                        </div>
                                        <img alt="Live storefront mobile" class="w-full h-full object-cover pt-6" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDkLjrbhJt0vz0TY0PEtWjf3hggTIxGxSmOsgswFIpjq1iW5LXImONT-TEpWMHt19lMzpszREEIT2zSjO0q-jWej2Xoc2xWL-OJ3G23NclwLcIzJqmJcpvi86vJZZUZo4QQpUxV3qg9hVX_I41M32QTOwN57cdNhzD5KmsfgrnRFS47xi4giSpJ8qU-Hp4RiSgv9rbVEtgmaD5dqX3Vc39X9CYeiZeoBSjmlFG5zi7bZ0otiNSbVzG1zc9sutykKrDC_7mscqwt92o" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-24 text-center">
                <a class="inline-flex items-center justify-center gap-3 bg-[#BEF264] hover:bg-[#a9d94f] text-[#064E3B] text-lg font-bold px-10 py-5 rounded-xl transition-all shadow-[0_10px_30px_-10px_rgba(190,242,100,0.6)] hover:shadow-[0_20px_40px_-10px_rgba(190,242,100,0.7)] hover:-translate-y-1" href="#">
                    Start Your Free Trial
                    <span class="material-icons">arrow_forward</span>
                </a>
            </div>
        </div>
    </section>
    <section class="py-24 bg-white relative overflow-hidden" id="pricing">
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute -top-20 -right-20 w-96 h-96 bg-emerald-50 rounded-full blur-3xl opacity-50"></div>
            <div class="absolute bottom-0 left-0 w-full h-1/2 bg-gradient-to-t from-gray-50 to-transparent"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16 scroll-animate">
                <span class="inline-block py-1 px-3 rounded-full bg-[#BEF264]/10 text-[#BEF264] font-bold text-sm tracking-widest uppercase mb-4 border border-[#BEF264]/20">Pricing</span>
                <h2 class="text-4xl md:text-[48px] font-bold text-[#064E3B] leading-tight mb-6">Simple, transparent pricing</h2>
                <p class="text-xl text-gray-600 leading-relaxed font-light">
                    Choose the perfect plan for your business growth. No hidden fees.
                </p>
                <div class="mt-10 flex justify-center items-center gap-4">
                    <span class="text-gray-600 font-medium text-lg">Monthly</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input class="sr-only peer" type="checkbox" value="" />
                        <div class="w-16 h-8 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-[#10b77f] shadow-inner"></div>
                    </label>
                    <span class="text-gray-900 font-bold text-lg flex items-center gap-2">
                        Yearly
                        <span class="bg-[#BEF264] text-[#064E3B] text-xs px-2 py-0.5 rounded-full font-bold uppercase tracking-wide">Save 20%</span>
                    </span>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start max-w-6xl mx-auto">
                <div class="bg-white rounded-2xl shadow-lg border-2 border-emerald-50 p-8 transition-all duration-300 relative group h-full flex flex-col reveal-on-scroll delay-100 hover-lift">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Starter</h3>
                        <p class="text-gray-500 text-sm">Perfect for new businesses.</p>
                    </div>
                    <div class="mb-8">
                        <div class="flex items-baseline">
                            <span class="text-5xl font-extrabold text-[#064E3B]">₦5000</span>
                            <span class="text-gray-500 font-medium ml-2">/month</span>
                        </div>
                    </div>
                    <a class="block w-full py-4 px-6 text-center rounded-xl border-2 border-[#10b77f] text-[#10b77f] font-bold hover:bg-emerald-50 transition-colors mb-8" href="#">
                        Start Free
                    </a>
                    <ul class="space-y-4 flex-1">
                        <li class="flex items-start gap-3 text-gray-600">
                            <span class="material-icons text-[#10b77f] text-sm mt-1">check_circle</span>
                            <span>1 Online Store</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-600">
                            <span class="material-icons text-[#10b77f] text-sm mt-1">check_circle</span>
                            <span>50 Products</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-600">
                            <span class="material-icons text-[#10b77f] text-sm mt-1">check_circle</span>
                            <span>Email Support</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-400">
                            <span class="material-icons text-gray-300 text-sm mt-1">remove_circle_outline</span>
                            <span>Advanced Analytics</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-400">
                            <span class="material-icons text-gray-300 text-sm mt-1">remove_circle_outline</span>
                            <span>Custom Domain</span>
                        </li>
                    </ul>
                </div>
                <div class="bg-gradient-to-b from-[#064E3B] to-[#065F46] rounded-2xl shadow-2xl p-8 transform md:scale-105 md:-translate-y-2 relative border border-[#BEF264]/30 flex flex-col h-full z-10 reveal-on-scroll delay-200 border-glow hover-lift">
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2">
                        <span class="bg-[#BEF264] text-[#064E3B] text-xs font-bold px-4 py-1.5 rounded-full shadow-lg tracking-wider uppercase shimmer">Most Popular</span>
                    </div>
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-white mb-2">Professional</h3>
                        <p class="text-emerald-100/70 text-sm">For growing businesses.</p>
                    </div>
                    <div class="mb-8">
                        <div class="flex items-baseline">
                            <span class="text-5xl font-extrabold text-white">₦15,000</span>
                            <span class="text-emerald-100/70 font-medium ml-2">/month</span>
                        </div>
                    </div>
                    <a class="block w-full py-4 px-6 text-center rounded-xl bg-[#BEF264] text-[#064E3B] font-bold hover:bg-[#a3e635] transition-colors mb-8 shadow-lg shadow-[#BEF264]/20" href="#">
                        Start Free Trial
                    </a>
                    <ul class="space-y-4 flex-1">
                        <li class="flex items-start gap-3 text-white">
                            <span class="material-icons text-[#BEF264] text-sm mt-1">check_circle</span>
                            <span class="font-medium">Everything in Starter, plus:</span>
                        </li>
                        <li class="flex items-start gap-3 text-emerald-50">
                            <span class="material-icons text-[#BEF264] text-sm mt-1">check_circle</span>
                            <span>Unlimited Stores</span>
                        </li>
                        <li class="flex items-start gap-3 text-emerald-50">
                            <span class="material-icons text-[#BEF264] text-sm mt-1">check_circle</span>
                            <span>5,000 Products</span>
                        </li>
                        <li class="flex items-start gap-3 text-emerald-50">
                            <span class="material-icons text-[#BEF264] text-sm mt-1">check_circle</span>
                            <span>Advanced Analytics</span>
                        </li>
                        <li class="flex items-start gap-3 text-emerald-50">
                            <span class="material-icons text-[#BEF264] text-sm mt-1">check_circle</span>
                            <span>Priority Support</span>
                        </li>
                        <li class="flex items-start gap-3 text-emerald-50">
                            <span class="material-icons text-[#BEF264] text-sm mt-1">check_circle</span>
                            <span>0% Transaction Fees</span>
                        </li>
                    </ul>
                </div>
                <div class="bg-white rounded-2xl shadow-lg border-2 border-emerald-50 p-8 transition-all duration-300 relative group h-full flex flex-col reveal-on-scroll delay-300 hover-lift">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Enterprise</h3>
                        <p class="text-gray-500 text-sm">For large scale operations.</p>
                    </div>
                    <div class="mb-8">
                        <div class="flex items-baseline">
                            <span class="text-5xl font-extrabold text-[#064E3B]">Custom</span>
                        </div>
                    </div>
                    <a class="block w-full py-4 px-6 text-center rounded-xl border-2 border-gray-200 text-gray-700 font-bold hover:border-[#064E3B] hover:text-[#064E3B] transition-colors mb-8" href="#">
                        Contact Sales
                    </a>
                    <ul class="space-y-4 flex-1">
                        <li class="flex items-start gap-3 text-gray-600">
                            <span class="material-icons text-[#10b77f] text-sm mt-1">verified</span>
                            <span class="font-bold text-gray-800">Everything in Pro, plus:</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-600">
                            <span class="material-icons text-[#10b77f] text-sm mt-1">check_circle</span>
                            <span>Unlimited Products</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-600">
                            <span class="material-icons text-[#10b77f] text-sm mt-1">check_circle</span>
                            <span>Dedicated Account Manager</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-600">
                            <span class="material-icons text-[#10b77f] text-sm mt-1">check_circle</span>
                            <span>Custom API Integration</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-600">
                            <span class="material-icons text-[#10b77f] text-sm mt-1">check_circle</span>
                            <span>SLA &amp; 99.99% Uptime</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-600">
                            <span class="material-icons text-[#10b77f] text-sm mt-1">check_circle</span>
                            <span>24/7 Phone Support</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="mt-16 text-center border-t border-gray-100 pt-10">
                <div class="flex flex-col md:flex-row justify-center items-center gap-6 mb-6 opacity-70">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-gray-500">lock</span>
                        <span class="text-sm font-medium text-gray-600">Secure Payments</span>
                    </div>
                    <div class="h-4 w-px bg-gray-300 hidden md:block"></div>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-gray-500">encrypted</span>
                        <span class="text-sm font-medium text-gray-600">SSL Encrypted</span>
                    </div>
                    <div class="h-4 w-px bg-gray-300 hidden md:block"></div>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-gray-500">verified_user</span>
                        <span class="text-sm font-medium text-gray-600">GDPR Compliant</span>
                    </div>
                </div>
                <p class="text-gray-500 text-sm">
                    Have questions? Check out our <a class="text-[#10b77f] font-semibold hover:underline" href="#">Pricing FAQ</a>.
                </p>
            </div>
        </div>
    </section>
    <section class="py-24 bg-[#F8FAFC] relative overflow-hidden" id="testimonials">
        <div class="absolute inset-0 dot-pattern opacity-40 pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <span class="inline-block py-1 px-3 rounded-full bg-[#BEF264]/10 text-[#BEF264] font-bold text-sm tracking-widest uppercase mb-4 border border-[#BEF264]/20">Testimonials</span>
                <h2 class="text-4xl md:text-[48px] font-bold text-[#064E3B] leading-tight mb-6">Loved by businesses worldwide</h2>
                <p class="text-xl text-gray-600 leading-relaxed font-light">
                    Discover how thousands of entrepreneurs are scaling their revenue and simplifying their operations with StoreFlow.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-[20px] p-8 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 relative group flex flex-col h-full">
                    <div class="absolute top-8 right-8 text-9xl leading-none text-emerald-50 font-serif opacity-50 select-none group-hover:text-emerald-100 transition-colors pointer-events-none">”</div>
                    <div class="flex gap-1 mb-6 text-yellow-400">
                        <span class="material-icons">star</span>
                        <span class="material-icons">star</span>
                        <span class="material-icons">star</span>
                        <span class="material-icons">star</span>
                        <span class="material-icons">star</span>
                    </div>
                    <blockquote class="text-lg text-gray-700 italic leading-relaxed mb-6 flex-grow relative z-10">
                        "Switching to StoreFlow was the best decision for our fashion brand. The inventory management is seamless, and our <span class="text-gray-900 font-semibold not-italic">conversion rate doubled</span> within the first month. It just works."
                    </blockquote>
                    <div class="mb-8">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#BEF264]/20 text-[#064E3B] text-sm font-bold border border-[#BEF264]/30">
                            <span class="material-icons text-sm">trending_up</span>
                            200% Revenue Growth
                        </span>
                    </div>
                    <div class="flex items-center gap-4 pt-6 border-t border-gray-50 mt-auto">
                        <img alt="Sarah J." class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-md" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBFNnXM3AY5GoUbZiPPbQrygjgV9thB6AnxekfzKREsstm5qyEL1HBYr5HjHfWxtBu8uUbTtcWTRZE2KlM0GmmxJmBgaAj7SC2mmVmYttkn0Un4LdVfrHzAAb_xJdCHZwKV7svGvzl8qATyVAlUgP5_CMuz6FLhh75YKOBR9W5E91YgVMz4UsAo-iFO_FKZRF1kD4NJA81JpMWQM8qp1lO0_nCcQT_0Nft0cOeItN1mQYX6K7WlePHSIcAM8V_lwu0kkGhUL0cZUfc" />
                        <div>
                            <div class="font-bold text-gray-900 flex items-center gap-1">
                                Sarah Jenkins
                                <span class="material-icons text-blue-500 text-[16px]">verified</span>
                            </div>
                            <div class="text-sm text-gray-500">Founder, Velvet &amp; Vine</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-[20px] p-8 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 relative group flex flex-col h-full lg:translate-y-8">
                    <div class="absolute top-8 right-8 text-9xl leading-none text-emerald-50 font-serif opacity-50 select-none group-hover:text-emerald-100 transition-colors pointer-events-none">”</div>
                    <div class="flex gap-1 mb-6 text-yellow-400">
                        <span class="material-icons">star</span>
                        <span class="material-icons">star</span>
                        <span class="material-icons">star</span>
                        <span class="material-icons">star</span>
                        <span class="material-icons">star</span>
                    </div>
                    <blockquote class="text-lg text-gray-700 italic leading-relaxed mb-6 flex-grow relative z-10">
                        "I used to spend hours managing orders across different platforms. StoreFlow centralized everything. The <span class="text-gray-900 font-semibold not-italic">automated workflows</span> saved me about 20 hours a week. Incredible tool."
                    </blockquote>
                    <div class="mb-8">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#BEF264]/20 text-[#064E3B] text-sm font-bold border border-[#BEF264]/30">
                            <span class="material-icons text-sm">schedule</span>
                            20hrs Saved/Week
                        </span>
                    </div>
                    <div class="flex items-center gap-4 pt-6 border-t border-gray-50 mt-auto">
                        <img alt="Michael C." class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-md" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA4mHl0SakTrxaeQvAWwoz0wMek-y58s3Uq3BQDWUsKhyh87vhaU5BPRKXidIGn9IxcB79mMtVdg87H2joqfiHYa0ew7NF5M3PfK74ENeslXy3x9UTffl_uMwhwZ0hotFDEp-vrDu6CJ5dJ5l3nq7kIdbuJhkhnUfzXoN0vdRSPL5FWcN7ypJrLanSrY65GQ6wrr-MpQeqAZ-rdGBRxKjovIB-zHUVbnJlDAtmgM5uFbk8b-Kf2nEC9uVWlnR6xJ4Kr12QeiTMV6Lg" />
                        <div>
                            <div class="font-bold text-gray-900 flex items-center gap-1">
                                Michael Chen
                                <span class="material-icons text-blue-500 text-[16px]">verified</span>
                            </div>
                            <div class="text-sm text-gray-500">CEO, TechGear Pro</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-[20px] p-8 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 relative group flex flex-col h-full">
                    <div class="absolute top-8 right-8 text-9xl leading-none text-emerald-50 font-serif opacity-50 select-none group-hover:text-emerald-100 transition-colors pointer-events-none">”</div>
                    <div class="flex gap-1 mb-6 text-yellow-400">
                        <span class="material-icons">star</span>
                        <span class="material-icons">star</span>
                        <span class="material-icons">star</span>
                        <span class="material-icons">star</span>
                        <span class="material-icons">star</span>
                    </div>
                    <blockquote class="text-lg text-gray-700 italic leading-relaxed mb-6 flex-grow relative z-10">
                        "We needed a solution that could scale with our rapid growth. StoreFlow's enterprise features gave us the <span class="text-gray-900 font-semibold not-italic">stability and analytics</span> we needed to expand into international markets seamlessly."
                    </blockquote>
                    <div class="mb-8">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#BEF264]/20 text-[#064E3B] text-sm font-bold border border-[#BEF264]/30">
                            <span class="material-icons text-sm">public</span>
                            Expanded to 12 Countries
                        </span>
                    </div>
                    <div class="flex items-center gap-4 pt-6 border-t border-gray-50 mt-auto">
                        <img alt="Elena R." class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-md" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC997ZKmxU34JujePguCcrEYMQ3LeKCWWq2GrrU_WTlLBXtbnVu_f1-iPCF52E5eZgsI3k2AF6g8GOQ8cT0oK8jB_OG1GtDPB6C-uCzY_dEWQRkS2IqIxcujB9GI3dPuP4iCyyIphgW3wyod89Ipxc67s_M8pJKWYDhYyjUMh39hsS3gLXyNKDZeUQdGCWYrw2trqyfi7fhpMAE527bJqxRgtEGY8sZeH2FOlHapyFOEHjWmJyeNKMvFhaiUl1K64zxCB0MHFcmRO4" />
                        <div>
                            <div class="font-bold text-gray-900 flex items-center gap-1">
                                Elena Rodriguez
                                <span class="material-icons text-blue-500 text-[16px]">verified</span>
                            </div>
                            <div class="text-sm text-gray-500">Director, Organic Living</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-20 text-center lg:mt-24">
                <p class="text-gray-500 font-medium mb-4">Join 500+ businesses succeeding with our platform</p>
                <a class="inline-flex items-center text-[#064E3B] font-bold text-lg hover:text-[#BEF264] transition-colors group" href="#">
                    Read More Success Stories
                    <span class="material-icons ml-2 transition-transform group-hover:translate-x-1">arrow_forward</span>
                </a>
            </div>
        </div>
    </section>
    <section class="py-24 bg-gradient-to-b from-gray-50 to-white relative overflow-hidden" id="faq">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Header -->
            <div class="text-center max-w-3xl mx-auto mb-16 scroll-animate">
                <span class="inline-block py-1 px-3 rounded-full bg-[#BEF264]/10 text-[#BEF264] font-bold text-sm tracking-widest uppercase mb-4 border border-[#BEF264]/20">FAQ</span>
                <h2 class="text-4xl md:text-[48px] font-bold text-[#064E3B] leading-tight mb-6">Frequently asked questions</h2>
                <p class="text-xl text-gray-600 leading-relaxed font-light">
                    Everything you need to know about StoreFlow features, pricing, and getting started.
                </p>
            </div>
            
            <!-- FAQ Card -->
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden scroll-animate">
                    <!-- Card Header -->
                    <div class="px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-2xl font-bold text-[#064E3B]">Common Questions</h3>
                        <p class="text-gray-600 mt-1">Quick answers to questions you may have</p>
                    </div>
                    
                    <!-- Accordion Items -->
                    <div class="divide-y divide-gray-200">
                        <!-- FAQ Item 1 -->
                        <div class="faq-item">
                            <button class="faq-trigger w-full px-8 py-5 flex justify-between items-center text-left hover:bg-gray-50 transition-colors group">
                                <span class="text-lg font-semibold text-gray-900 pr-8 group-hover:text-[#064E3B]">Do I need technical skills to build my store?</span>
                                <svg class="faq-icon w-5 h-5 text-gray-500 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="faq-content max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                                <div class="px-8 pb-5 pt-2">
                                    <p class="text-gray-600 leading-relaxed">Not at all! StoreFlow is designed with a user-friendly, no-code interface. You can build, customize, and launch your store using our intuitive drag-and-drop editor. Our themes are pre-configured to look great out of the box.</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- FAQ Item 2 -->
                        <div class="faq-item">
                            <button class="faq-trigger w-full px-8 py-5 flex justify-between items-center text-left hover:bg-gray-50 transition-colors group">
                                <span class="text-lg font-semibold text-gray-900 pr-8 group-hover:text-[#064E3B]">Can I manage multiple stores from one account?</span>
                                <svg class="faq-icon w-5 h-5 text-gray-500 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="faq-content max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                                <div class="px-8 pb-5 pt-2">
                                    <p class="text-gray-600 leading-relaxed">Yes, absolutely. Our multi-tenant architecture allows you to create and manage unlimited storefronts under a single account. This is perfect for businesses operating in different regions or managing multiple brands. You can switch between stores seamlessly.</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- FAQ Item 3 -->
                        <div class="faq-item">
                            <button class="faq-trigger w-full px-8 py-5 flex justify-between items-center text-left hover:bg-gray-50 transition-colors group">
                                <span class="text-lg font-semibold text-gray-900 pr-8 group-hover:text-[#064E3B]">How do payments work with Paystack?</span>
                                <svg class="faq-icon w-5 h-5 text-gray-500 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="faq-content max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                                <div class="px-8 pb-5 pt-2">
                                    <p class="text-gray-600 leading-relaxed">StoreFlow integrates natively with Paystack, allowing you to accept payments via card, bank transfer, and mobile money instantly. Setup takes just a few clicks. We also support 100+ other payment gateways globally so you can sell anywhere.</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- FAQ Item 4 -->
                        <div class="faq-item">
                            <button class="faq-trigger w-full px-8 py-5 flex justify-between items-center text-left hover:bg-gray-50 transition-colors group">
                                <span class="text-lg font-semibold text-gray-900 pr-8 group-hover:text-[#064E3B]">Can I use my own custom domain name?</span>
                                <svg class="faq-icon w-5 h-5 text-gray-500 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="faq-content max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                                <div class="px-8 pb-5 pt-2">
                                    <p class="text-gray-600 leading-relaxed">Yes! All paid plans allow you to connect a custom domain (e.g., yourstore.com). If you don't have one yet, you can purchase one directly through your StoreFlow dashboard, or use our free .storeflow.shop subdomain to get started instantly.</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- FAQ Item 5 -->
                        <div class="faq-item">
                            <button class="faq-trigger w-full px-8 py-5 flex justify-between items-center text-left hover:bg-gray-50 transition-colors group">
                                <span class="text-lg font-semibold text-gray-900 pr-8 group-hover:text-[#064E3B]">Are there any hidden transaction fees?</span>
                                <svg class="faq-icon w-5 h-5 text-gray-500 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="faq-content max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                                <div class="px-8 pb-5 pt-2">
                                    <p class="text-gray-600 leading-relaxed">We believe in transparent pricing. On our Professional and Enterprise plans, StoreFlow charges 0% transaction fees. You only pay the standard processing fees charged by your payment provider (like Stripe or Paystack). The Starter plan has a small 2% fee per transaction.</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- FAQ Item 6 -->
                        <div class="faq-item">
                            <button class="faq-trigger w-full px-8 py-5 flex justify-between items-center text-left hover:bg-gray-50 transition-colors group">
                                <span class="text-lg font-semibold text-gray-900 pr-8 group-hover:text-[#064E3B]">Is it easy to migrate from another platform?</span>
                                <svg class="faq-icon w-5 h-5 text-gray-500 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="faq-content max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                                <div class="px-8 pb-5 pt-2">
                                    <p class="text-gray-600 leading-relaxed">Migration is simple with our one-click import tools. You can easily import products, customers, and order history via CSV or directly from platforms like Shopify, WooCommerce, or Magento. Our support team is also available to assist with complex migrations.</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- FAQ Item 7 -->
                        <div class="faq-item">
                            <button class="faq-trigger w-full px-8 py-5 flex justify-between items-center text-left hover:bg-gray-50 transition-colors group">
                                <span class="text-lg font-semibold text-gray-900 pr-8 group-hover:text-[#064E3B]">What kind of customer support do you offer?</span>
                                <svg class="faq-icon w-5 h-5 text-gray-500 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="faq-content max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                                <div class="px-8 pb-5 pt-2">
                                    <p class="text-gray-600 leading-relaxed">We offer 24/7 email support for all plans. Professional plan users get priority support with faster response times. Enterprise customers receive a dedicated account manager and 24/7 phone support to ensure your business never stops running.</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- FAQ Item 8 -->
                        <div class="faq-item">
                            <button class="faq-trigger w-full px-8 py-5 flex justify-between items-center text-left hover:bg-gray-50 transition-colors group">
                                <span class="text-lg font-semibold text-gray-900 pr-8 group-hover:text-[#064E3B]">Can I cancel my subscription at any time?</span>
                                <svg class="faq-icon w-5 h-5 text-gray-500 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="faq-content max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                                <div class="px-8 pb-5 pt-2">
                                    <p class="text-gray-600 leading-relaxed">Yes, there are no long-term contracts. You can cancel your monthly subscription at any time from your dashboard. If you're on a yearly plan, you can cancel and your store will remain active until the end of your billing cycle.</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- FAQ Item 9 -->
                        <div class="faq-item">
                            <button class="faq-trigger w-full px-8 py-5 flex justify-between items-center text-left hover:bg-gray-50 transition-colors group">
                                <span class="text-lg font-semibold text-gray-900 pr-8 group-hover:text-[#064E3B]">Is my data secure and compliant?</span>
                                <svg class="faq-icon w-5 h-5 text-gray-500 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="faq-content max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                                <div class="px-8 pb-5 pt-2">
                                    <p class="text-gray-600 leading-relaxed">Security is our top priority. StoreFlow is Level 1 PCI DSS compliant, ensuring secure payment processing. We also provide free SSL certificates for every store and are fully GDPR compliant to protect you and your customers' data.</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- FAQ Item 10 -->
                        <div class="faq-item">
                            <button class="faq-trigger w-full px-8 py-5 flex justify-between items-center text-left hover:bg-gray-50 transition-colors group">
                                <span class="text-lg font-semibold text-gray-900 pr-8 group-hover:text-[#064E3B]">What is included in the free trial?</span>
                                <svg class="faq-icon w-5 h-5 text-gray-500 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="faq-content max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                                <div class="px-8 pb-5 pt-2">
                                    <p class="text-gray-600 leading-relaxed">Our 14-day free trial gives you full access to all features on the Professional plan. You can build your store, test the checkout process, explore themes, and see analytics. No credit card is required to start your trial.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact CTA -->
            <div class="mt-16 bg-gradient-to-r from-[#BEF264]/10 to-[#10b77f]/10 rounded-2xl p-8 max-w-4xl mx-auto border border-[#BEF264]/30 shadow-sm">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center shadow-md">
                            <svg class="w-7 h-7 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-[#064E3B]">Still have questions?</h3>
                            <p class="text-gray-600">Our support team is here to help you succeed.</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <button class="bg-[#064E3B] hover:bg-[#065F46] text-white px-6 py-3 rounded-lg font-semibold transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            Chat with us
                        </button>
                        <button class="bg-white border-2 border-[#064E3B] text-[#064E3B] hover:bg-gray-50 px-6 py-3 rounded-lg font-semibold transition-all">
                            Email support
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="py-24 bg-gradient-to-br from-[#064E3B] to-[#047857] relative overflow-hidden">
        <div class="absolute inset-0 mesh-gradient opacity-60"></div>
        <div class="absolute inset-0 dot-pattern-light opacity-30 pointer-events-none"></div>
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-[#BEF264] opacity-10 blur-[100px] rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-[#10b77f] opacity-20 blur-[120px] rounded-full pointer-events-none"></div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#BEF264]/10 border border-[#BEF264]/20 backdrop-blur-sm mb-8 hover:bg-[#BEF264]/20 transition-all cursor-default shadow-lg shadow-[#BEF264]/5">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#BEF264] opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#BEF264]"></span>
                </span>
                <span class="text-[#BEF264] font-bold text-sm tracking-widest uppercase">Start Today</span>
            </div>
            <h2 class="text-4xl md:text-[56px] font-bold text-white leading-[1.1] mb-6 tracking-tight drop-shadow-sm">
                Ready to launch your online store?
            </h2>
            <p class="text-xl text-emerald-50 mb-10 max-w-2xl mx-auto font-light leading-relaxed">
                Join thousands of merchants growing their business with StoreFlow. Start your 14-day free trial today—no credit card required.
            </p>
            <div class="flex flex-col sm:flex-row gap-5 justify-center items-center mb-12">
                <a class="group relative bg-[#BEF264] hover:bg-[#a3e635] text-[#064E3B] text-lg px-8 py-4 rounded-xl font-bold transition-all shadow-[0_0_25px_rgba(190,242,100,0.4)] hover:shadow-[0_0_40px_rgba(190,242,100,0.6)] transform hover:-translate-y-1 flex items-center justify-center gap-2 w-full sm:w-auto" href="#">
                    Start Your Free Trial
                    <span class="material-icons text-xl transition-transform group-hover:translate-x-1">arrow_forward</span>
                </a>
                <a class="w-full sm:w-auto flex items-center justify-center gap-2 text-white border border-white/30 hover:bg-white/10 hover:border-white/50 text-lg px-8 py-4 rounded-xl font-medium transition-all backdrop-blur-sm group" href="#">
                    <span class="material-symbols-outlined group-hover:scale-110 transition-transform">calendar_month</span>
                    Schedule a Demo
                </a>
            </div>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-6 sm:gap-10 text-sm font-medium text-emerald-50/90 mb-14 border-b border-white/10 pb-12">
                <div class="flex items-center gap-2">
                    <div class="bg-[#BEF264]/20 rounded-full p-0.5 ring-1 ring-[#BEF264]/40">
                        <span class="material-icons text-[#BEF264] text-sm">check</span>
                    </div>
                    <span class="tracking-wide">No credit card required</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="bg-[#BEF264]/20 rounded-full p-0.5 ring-1 ring-[#BEF264]/40">
                        <span class="material-icons text-[#BEF264] text-sm">check</span>
                    </div>
                    <span class="tracking-wide">Setup in 10 minutes</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="bg-[#BEF264]/20 rounded-full p-0.5 ring-1 ring-[#BEF264]/40">
                        <span class="material-icons text-[#BEF264] text-sm">check</span>
                    </div>
                    <span class="tracking-wide">Cancel anytime</span>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 text-center sm:divide-x divide-white/10">
                <div>
                    <div class="text-2xl font-bold text-white mb-1">500+</div>
                    <div class="text-emerald-200/80 text-xs uppercase tracking-wider font-medium">Active Stores</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-white mb-1">50K+</div>
                    <div class="text-emerald-200/80 text-xs uppercase tracking-wider font-medium">Products</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-white mb-1">99.9%</div>
                    <div class="text-emerald-200/80 text-xs uppercase tracking-wider font-medium">Uptime</div>
                </div>
            </div>
        </div>
    </section>
    <!-- Comprehensive Footer -->
<footer class="bg-[#0F172A] border-t border-[rgba(190,242,100,0.1)] pt-20 pb-8 relative">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Top Row: Branding & Newsletter -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-12 mb-12">
            
            <!-- Left Column - Brand Section (40%) -->
            <div class="lg:col-span-2">
                <!-- Logo -->
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-10 h-10 rounded bg-primary flex items-center justify-center text-white font-bold text-xl">S</div>
                    <span class="font-bold text-2xl tracking-tight text-white">StoreFlow</span>
                </div>
                
                <!-- Tagline -->
                <p class="text-[16px] text-white/70 leading-relaxed max-w-[320px] mb-8">
                    Empowering businesses to sell online
                </p>
                
                <!-- Social Media Links -->
                <div class="flex items-center gap-3">
                    <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white/70 hover:bg-[rgba(190,242,100,0.2)] hover:text-[#BEF264] transition-all duration-300" aria-label="Twitter">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
                        </svg>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white/70 hover:bg-[rgba(190,242,100,0.2)] hover:text-[#BEF264] transition-all duration-300" aria-label="LinkedIn">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/>
                            <circle cx="4" cy="4" r="2"/>
                        </svg>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white/70 hover:bg-[rgba(190,242,100,0.2)] hover:text-[#BEF264] transition-all duration-300" aria-label="Instagram">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <rect width="20" height="20" x="2" y="2" rx="5" ry="5"/>
                            <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zm1.5-4.87h.01"/>
                        </svg>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white/70 hover:bg-[rgba(190,242,100,0.2)] hover:text-[#BEF264] transition-all duration-300" aria-label="YouTube">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M22.54 6.42a2.78 2.78 0 00-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 00-1.94 2A29 29 0 001 11.75a29 29 0 00.46 5.33A2.78 2.78 0 003.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 001.94-2 29 29 0 00.46-5.25 29 29 0 00-.46-5.33z"/>
                            <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/>
                        </svg>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white/70 hover:bg-[rgba(190,242,100,0.2)] hover:text-[#BEF264] transition-all duration-300" aria-label="GitHub">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.477 2 2 6.477 2 12c0 4.42 2.865 8.17 6.839 9.49.5.092.682-.217.682-.482 0-.237-.008-.866-.013-1.7-2.782.603-3.369-1.34-3.369-1.34-.454-1.156-1.11-1.463-1.11-1.463-.908-.62.069-.608.069-.608 1.003.07 1.531 1.03 1.531 1.03.892 1.529 2.341 1.087 2.91.831.092-.646.35-1.086.636-1.336-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.029-2.683-.103-.253-.446-1.27.098-2.647 0 0 .84-.269 2.75 1.025A9.578 9.578 0 0112 6.836c.85.004 1.705.114 2.504.336 1.909-1.294 2.747-1.025 2.747-1.025.546 1.377.203 2.394.1 2.647.64.699 1.028 1.592 1.028 2.683 0 3.842-2.339 4.687-4.566 4.935.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48C19.138 20.167 22 16.418 22 12c0-5.523-4.477-10-10-10z"/>
                        </svg>
                    </a>
                </div>
            </div>
            
            <!-- Right Column - Newsletter Signup (60%) -->
            <div class="lg:col-span-3">
                <h3 class="text-[18px] font-semibold text-white mb-3">Stay updated</h3>
                <p class="text-[14px] text-white/60 mb-5">
                    Get the latest features and updates delivered to your inbox
                </p>
                
                <!-- Email Form -->
                <form class="flex flex-col sm:flex-row gap-3 mb-3">
                    <input 
                        type="email" 
                        placeholder="Enter your email" 
                        class="flex-1 bg-white/10 border border-white/20 text-white placeholder-white/40 px-5 py-3.5 rounded-[10px] text-[15px] focus:outline-none focus:border-[#BEF264] transition-colors"
                        required
                    />
                    <button 
                        type="submit" 
                        class="bg-[#BEF264] text-[#064E3B] px-7 py-3.5 rounded-[10px] text-[15px] font-semibold hover:scale-105 transition-transform duration-200"
                    >
                        Subscribe
                    </button>
                </form>
                
                <!-- Privacy Note -->
                <p class="text-[12px] text-white/50">
                    We respect your privacy. Unsubscribe anytime.
                </p>
            </div>
        </div>
        
        <!-- Divider -->
        <div class="h-px bg-white/10 mb-12"></div>
        
        <!-- Links Section - 4 Columns -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 lg:gap-12 mb-10">
            
            <!-- Column 1 - PRODUCT -->
            <div>
                <h4 class="text-[14px] font-semibold uppercase text-white tracking-wider mb-5">Product</h4>
                <ul class="space-y-0">
                    <li><a href="#" class="block text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">Features</a></li>
                    <li><a href="#" class="block text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">Pricing</a></li>
                    <li><a href="#" class="block text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">Templates</a></li>
                    <li><a href="#" class="block text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">Integrations</a></li>
                    <li><a href="#" class="block text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">API Documentation</a></li>
                    <li><a href="#" class="block text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">Changelog</a></li>
                    <li><a href="#" class="block text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">Roadmap</a></li>
                </ul>
            </div>
            
            <!-- Column 2 - COMPANY -->
            <div>
                <h4 class="text-[14px] font-semibold uppercase text-white tracking-wider mb-5">Company</h4>
                <ul class="space-y-0">
                    <li><a href="#" class="block text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">About Us</a></li>
                    <li><a href="#" class="block text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">Our Story</a></li>
                    <li>
                        <a href="#" class="inline-flex items-center gap-2 text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">
                            Careers
                            <span class="bg-red-500/20 text-[#FCA5A5] px-2 py-0.5 rounded-lg text-[10px] font-bold">We're hiring!</span>
                        </a>
                    </li>
                    <li><a href="#" class="block text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">Press Kit</a></li>
                    <li><a href="#" class="block text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">Contact Us</a></li>
                    <li><a href="#" class="block text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">Partners</a></li>
                    <li><a href="#" class="block text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">Affiliates</a></li>
                </ul>
            </div>
            
            <!-- Column 3 - RESOURCES -->
            <div>
                <h4 class="text-[14px] font-semibold uppercase text-white tracking-wider mb-5">Resources</h4>
                <ul class="space-y-0">
                    <li><a href="#" class="block text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">Help Center</a></li>
                    <li><a href="#" class="block text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">Documentation</a></li>
                    <li><a href="#" class="block text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">Video Tutorials</a></li>
                    <li><a href="#" class="block text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">Blog</a></li>
                    <li><a href="#" class="block text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">Case Studies</a></li>
                    <li><a href="#" class="block text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">Community Forum</a></li>
                    <li>
                        <a href="#" class="inline-flex items-center gap-2 text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">
                            System Status
                            <span class="w-2 h-2 rounded-full bg-[#22C55E] shadow-[0_0_8px_rgba(34,197,94,0.4)]"></span>
                        </a>
                    </li>
                    <li><a href="#" class="block text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">Developer API</a></li>
                </ul>
            </div>
            
            <!-- Column 4 - LEGAL -->
            <div>
                <h4 class="text-[14px] font-semibold uppercase text-white tracking-wider mb-5">Legal</h4>
                <ul class="space-y-0">
                    <li><a href="#" class="block text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">Privacy Policy</a></li>
                    <li><a href="#" class="block text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">Terms of Service</a></li>
                    <li><a href="#" class="block text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">Cookie Policy</a></li>
                    <li><a href="#" class="block text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">GDPR Compliance</a></li>
                    <li><a href="#" class="block text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">Security</a></li>
                    <li><a href="#" class="block text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">Acceptable Use</a></li>
                    <li><a href="#" class="block text-[14px] text-white/70 hover:text-[#BEF264] transition-colors duration-200 leading-[2.2]">SLA</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Bottom Divider -->
        <div class="h-px bg-white/10 mb-8"></div>
        
        <!-- Bottom Bar -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-6">
            
            <!-- Left - Copyright -->
            <p class="text-[14px] text-white/50 order-2 md:order-1">
                © 2026 StoreFlow. All rights reserved.
            </p>
            
            <!-- Center - Compliance Badges -->
            <div class="flex items-center gap-5 opacity-50 order-1 md:order-2">
                <div class="flex items-center gap-1.5 text-white/70 text-[11px] font-semibold">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>
                    </svg>
                    SSL Secure
                </div>
                <div class="flex items-center gap-1.5 text-white/70 text-[11px] font-semibold">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
                    </svg>
                    PCI Compliant
                </div>
                <div class="flex items-center gap-1.5 text-white/70 text-[11px] font-semibold">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                    GDPR Ready
                </div>
            </div>
            
            <!-- Right - Language Selector -->
            <div class="order-3">
                <button class="flex items-center gap-2 bg-white/10 border border-white/20 text-white/70 px-4 py-2 rounded-lg text-[13px] hover:bg-white/15 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    English (NG)
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Back to Top Button -->
    <button 
        id="backToTop" 
        class="fixed bottom-8 right-8 w-12 h-12 rounded-full bg-[rgba(190,242,100,0.2)] text-[#BEF264] flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 hover:bg-[rgba(190,242,100,0.3)] z-50"
        aria-label="Back to top"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
        </svg>
    </button>
</footer>

<!-- Footer JavaScript -->
<script>
    // Back to Top Button
    const backToTopBtn = document.getElementById('backToTop');
    
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 500) {
            backToTopBtn.classList.remove('opacity-0', 'pointer-events-none');
        } else {
            backToTopBtn.classList.add('opacity-0', 'pointer-events-none');
        }
    });
    
    backToTopBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
</script>
</body>

</html>