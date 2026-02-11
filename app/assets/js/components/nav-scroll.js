// Navigation Scroll Effect
const mainNav = document.getElementById('mainNav');
const navLogo = document.getElementById('navLogo');
const navLogin = document.getElementById('navLogin');
const navLinks = document.querySelectorAll('.nav-link');
const mobileMenuBtn = document.getElementById('mobileMenuBtn');

window.addEventListener('scroll', () => {
    if (window.pageYOffset > 50) {
        // Scrolled - add solid background
        mainNav.classList.add('bg-surface-light/95', 'dark:bg-surface-dark/95', 'backdrop-blur-md', 'border-b', 'border-gray-200', 'dark:border-white/10', 'shadow-sm');
        navLogo.classList.remove('text-white');
        navLogo.classList.add('text-gray-900', 'dark:text-white');
        navLogin.classList.remove('text-white/90', 'hover:text-lime-accent');
        navLogin.classList.add('text-gray-700', 'dark:text-gray-200', 'hover:text-primary');
        mobileMenuBtn.classList.remove('text-white', 'hover:text-lime-accent');
        mobileMenuBtn.classList.add('text-gray-600', 'dark:text-gray-300', 'hover:text-primary');
        navLinks.forEach(link => {
            link.classList.remove('text-white/90', 'hover:text-lime-accent');
            link.classList.add('text-gray-600', 'dark:text-gray-300', 'hover:text-primary', 'dark:hover:text-primary');
        });
    } else {
        // At top - transparent background
        mainNav.classList.remove('bg-surface-light/95', 'dark:bg-surface-dark/95', 'backdrop-blur-md', 'border-b', 'border-gray-200', 'dark:border-white/10', 'shadow-sm');
        navLogo.classList.add('text-white');
        navLogo.classList.remove('text-gray-900', 'dark:text-white');
        navLogin.classList.add('text-white/90', 'hover:text-lime-accent');
        navLogin.classList.remove('text-gray-700', 'dark:text-gray-200', 'hover:text-primary');
        mobileMenuBtn.classList.add('text-white', 'hover:text-lime-accent');
        mobileMenuBtn.classList.remove('text-gray-600', 'dark:text-gray-300', 'hover:text-primary');
        navLinks.forEach(link => {
            link.classList.add('text-white/90', 'hover:text-lime-accent');
            link.classList.remove('text-gray-600', 'dark:text-gray-300', 'hover:text-primary', 'dark:hover:text-primary');
        });
    }
});
