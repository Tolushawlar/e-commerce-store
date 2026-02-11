// Modern Scroll Animations - Intersection Observer
class ScrollAnimations {
    constructor() {
        this.observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };
        
        this.init();
    }
    
    init() {
        // Initialize Intersection Observer
        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible', 'revealed');
                    
                    // Optionally unobserve after animation
                    if (!entry.target.classList.contains('repeat-animation')) {
                        this.observer.unobserve(entry.target);
                    }
                }
            });
        }, this.observerOptions);
        
        // Observe all elements with scroll-animate class
        this.observeElements();
        
        // Initialize other animations
        this.initParallax();
        this.initMagneticButtons();
        this.initStaggerAnimations();
    }
    
    observeElements() {
        const elements = document.querySelectorAll('.scroll-animate, .reveal-on-scroll');
        elements.forEach(el => this.observer.observe(el));
    }
    
    initParallax() {
        const parallaxElements = document.querySelectorAll('.parallax');
        
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            
            parallaxElements.forEach(el => {
                const speed = el.dataset.speed || 0.5;
                const yPos = -(scrolled * speed);
                el.style.transform = `translateY(${yPos}px)`;
            });
        });
    }
    
    initMagneticButtons() {
        const magneticButtons = document.querySelectorAll('.magnetic-btn');
        
        magneticButtons.forEach(btn => {
            btn.addEventListener('mousemove', (e) => {
                const rect = btn.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;
                
                btn.style.transform = `translate(${x * 0.3}px, ${y * 0.3}px)`;
            });
            
            btn.addEventListener('mouseleave', () => {
                btn.style.transform = 'translate(0, 0)';
            });
        });
    }
    
    initStaggerAnimations() {
        const staggerContainers = document.querySelectorAll('.stagger-children');
        
        const staggerObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const children = entry.target.children;
                    Array.from(children).forEach((child, index) => {
                        setTimeout(() => {
                            child.style.opacity = '1';
                            child.style.transform = 'translateY(0)';
                        }, index * 100);
                    });
                    staggerObserver.unobserve(entry.target);
                }
            });
        }, this.observerOptions);
        
        staggerContainers.forEach(container => {
            staggerObserver.observe(container);
        });
    }
    
    // Add new elements to observer (useful for dynamic content)
    observe(element) {
        this.observer.observe(element);
    }
    
    // Remove element from observer
    unobserve(element) {
        this.observer.unobserve(element);
    }
}

// Initialize animations when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.scrollAnimations = new ScrollAnimations();
});

// Counter Animation
function animateCounter(element, target, duration = 2000) {
    let start = 0;
    const increment = target / (duration / 16);
    
    const timer = setInterval(() => {
        start += increment;
        if (start >= target) {
            element.textContent = Math.floor(target);
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(start);
        }
    }, 16);
}

// Text Reveal Animation
function revealText(element) {
    const text = element.textContent;
    element.textContent = '';
    element.style.opacity = '1';
    
    text.split('').forEach((char, index) => {
        setTimeout(() => {
            element.textContent += char;
        }, index * 50);
    });
}

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { ScrollAnimations, animateCounter, revealText };
}
