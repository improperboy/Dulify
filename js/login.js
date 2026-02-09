// Dulify - Main JavaScript File
(function() {
    'use strict';

    // DOM Content Loaded Event
    document.addEventListener('DOMContentLoaded', function() {
        initializeApp();
    });

    // Initialize Application
    function initializeApp() {
        setupMobileMenu();
        setupFormValidation();
        setupScrollEffects();
        setupLoadingStates();
        setupAccessibility();
        setupAnimations();
        setupFormEnhancements();
    }

    // Mobile Menu Setup
    function setupMobileMenu() {
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const navLinks = document.querySelector('.nav-links');
        const authButtons = document.querySelector('.auth-buttons');

        if (!mobileMenuBtn) {
            // Create mobile menu button if it doesn't exist
            const header = document.querySelector('header .header-container');
            const nav = document.querySelector('nav');
            
            if (header && nav) {
                const menuBtn = document.createElement('button');
                menuBtn.className = 'mobile-menu-btn';
                menuBtn.setAttribute('aria-label', 'Toggle navigation menu');
                menuBtn.setAttribute('aria-expanded', 'false');
                menuBtn.innerHTML = '<span></span><span></span><span></span>';
                
                header.appendChild(menuBtn);
                
                menuBtn.addEventListener('click', toggleMobileMenu);
            }
        } else {
            mobileMenuBtn.addEventListener('click', toggleMobileMenu);
        }

        function toggleMobileMenu() {
            const btn = document.querySelector('.mobile-menu-btn');
            const isActive = btn.classList.contains('active');
            
            btn.classList.toggle('active');
            btn.setAttribute('aria-expanded', !isActive);
            
            if (navLinks) navLinks.classList.toggle('active');
            if (authButtons) authButtons.classList.toggle('active');
            
            // Prevent body scroll when menu is open
            document.body.style.overflow = isActive ? '' : 'hidden';
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            const btn = document.querySelector('.mobile-menu-btn');
            if (btn && btn.classList.contains('active') && 
                !e.target.closest('nav') && 
                !e.target.closest('.auth-buttons') &&
                !e.target.closest('.mobile-menu-btn')) {
                toggleMobileMenu();
            }
        });

        // Close mobile menu on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                const btn = document.querySelector('.mobile-menu-btn');
                if (btn && btn.classList.contains('active')) {
                    btn.classList.remove('active');
                    btn.setAttribute('aria-expanded', 'false');
                    if (navLinks) navLinks.classList.remove('active');
                    if (authButtons) authButtons.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }
        });
    }

    // Form Validation Setup
    function setupFormValidation() {
        const form = document.querySelector('form');
        const inputs = document.querySelectorAll('.form-control');

        if (!form) return;

        // Real-time validation
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });

            input.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    validateField(this);
                }
                updateFieldState(this);
            });

            input.addEventListener('focus', function() {
                this.closest('.form-group').classList.add('focused');
            });

            input.addEventListener('blur', function() {
                this.closest('.form-group').classList.remove('focused');
            });
        });

        // Form submission
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            inputs.forEach(input => {
                if (!validateField(input)) {
                    isValid = false;
                }
            });

            if (isValid) {
                showLoadingState(form);
            }
        });
    }

    // Field Validation
    function validateField(field) {
        const value = field.value.trim();
        const fieldName = field.name;
        let isValid = true;
        let errorMessage = '';

        // Clear previous errors
        clearFieldError(field);

        // Validation rules
        switch (fieldName) {
            case 'username':
                if (!value) {
                    errorMessage = 'Username is required';
                    isValid = false;
                } else if (value.length < 3) {
                    errorMessage = 'Username must be at least 3 characters';
                    isValid = false;
                } else if (!/^[a-zA-Z0-9_]+$/.test(value)) {
                    errorMessage = 'Username can only contain letters, numbers, and underscores';
                    isValid = false;
                }
                break;

            case 'password':
                if (!value) {
                    errorMessage = 'Password is required';
                    isValid = false;
                } else if (value.length < 6) {
                    errorMessage = 'Password must be at least 6 characters';
                    isValid = false;
                }
                break;

            case 'email':
                if (!value) {
                    errorMessage = 'Email is required';
                    isValid = false;
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    errorMessage = 'Please enter a valid email address';
                    isValid = false;
                }
                break;
        }

        if (!isValid) {
            showFieldError(field, errorMessage);
        }

        return isValid;
    }

    // Show Field Error
    function showFieldError(field, message) {
        field.classList.add('is-invalid');
        
        let errorSpan = field.parentNode.querySelector('.error-message');
        if (!errorSpan) {
            errorSpan = document.createElement('span');
            errorSpan.className = 'error-message';
            errorSpan.style.cssText = 'color: #ef4444; font-size: 14px; margin-top: 5px; display: block;';
            field.parentNode.appendChild(errorSpan);
        }
        
        errorSpan.textContent = message;
        errorSpan.style.animation = 'slideDown 0.3s ease';
    }

    // Clear Field Error
    function clearFieldError(field) {
        field.classList.remove('is-invalid');
        const errorSpan = field.parentNode.querySelector('.error-message');
        if (errorSpan) {
            errorSpan.remove();
        }
    }

    // Update Field State
    function updateFieldState(field) {
        const formGroup = field.closest('.form-group');
        if (field.value.trim()) {
            formGroup.classList.add('has-value');
        } else {
            formGroup.classList.remove('has-value');
        }
    }

    // Loading States
    function setupLoadingStates() {
        // Add loading spinner HTML to submit buttons
        const submitBtns = document.querySelectorAll('.form-submit');
        submitBtns.forEach(btn => {
            const spinner = document.createElement('span');
            spinner.className = 'loading-spinner';
            btn.appendChild(spinner);
        });
    }

    // Show Loading State
    function showLoadingState(form) {
        const submitBtn = form.querySelector('.form-submit');
        const spinner = submitBtn.querySelector('.loading-spinner');
        
        if (submitBtn && spinner) {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.8';
            spinner.style.display = 'inline-block';
            
            const originalText = submitBtn.textContent.replace('Loading...', '').trim();
            submitBtn.innerHTML = originalText + ' <span class="loading-spinner" style="display:inline-block;"></span>';
        }
    }

    // Scroll Effects
    function setupScrollEffects() {
        const header = document.querySelector('header');
        let lastScrollTop = 0;

        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            // Header background opacity
            if (scrollTop > 50) {
                header.style.background = 'rgba(255, 255, 255, 0.98)';
                header.style.backdropFilter = 'blur(20px)';
                header.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.1)';
            } else {
                header.style.background = 'rgba(255, 255, 255, 0.95)';
                header.style.backdropFilter = 'blur(10px)';
                header.style.boxShadow = 'none';
            }

            // Hide/show header on scroll
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                header.style.transform = 'translateY(-100%)';
            } else {
                header.style.transform = 'translateY(0)';
            }
            
            lastScrollTop = scrollTop;
        });
    }

    // Accessibility Setup
    function setupAccessibility() {
        // Keyboard navigation for custom elements
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                // Close mobile menu on Escape
                const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
                if (mobileMenuBtn && mobileMenuBtn.classList.contains('active')) {
                    mobileMenuBtn.click();
                }
            }
        });

        // Skip to main content link
        const skipLink = document.createElement('a');
        skipLink.href = '#main-content';
        skipLink.textContent = 'Skip to main content';
        skipLink.className = 'skip-link';
        skipLink.style.cssText = `
            position: absolute;
            top: -40px;
            left: 6px;
            background: var(--primary-color);
            color: white;
            padding: 8px;
            border-radius: 4px;
            text-decoration: none;
            z-index: 9999;
            transition: top 0.3s;
        `;
        
        skipLink.addEventListener('focus', function() {
            this.style.top = '6px';
        });
        
        skipLink.addEventListener('blur', function() {
            this.style.top = '-40px';
        });
        
        document.body.insertBefore(skipLink, document.body.firstChild);

        // Add main content ID if not exists
        const mainContent = document.querySelector('section') || document.querySelector('main');
        if (mainContent && !mainContent.id) {
            mainContent.id = 'main-content';
        }
    }

    // Animation Setup
    function setupAnimations() {
        // Intersection Observer for animations
        if ('IntersectionObserver' in window) {
            const animationObserver = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            // Observe elements for animation
            const animateElements = document.querySelectorAll('.form-container, .footer-column');
            animateElements.forEach(el => {
                el.classList.add('fade-in');
                animationObserver.observe(el);
            });
        }

        // Page load animations
        window.addEventListener('load', function() {
            document.body.classList.add('loaded');
            
            // Animate form container
            const formContainer = document.querySelector('.form-container');
            if (formContainer) {
                setTimeout(() => {
                    formContainer.classList.add('slide-up');
                }, 200);
            }
        });
    }

    // Form Enhancements
    function setupFormEnhancements() {
        // Password visibility toggle
        const passwordFields = document.querySelectorAll('input[type="password"]');
        passwordFields.forEach(field => {
            const wrapper = document.createElement('div');
            wrapper.style.position = 'relative';
            
            field.parentNode.insertBefore(wrapper, field);
            wrapper.appendChild(field);
            
            const toggleBtn = document.createElement('button');
            toggleBtn.type = 'button';
            toggleBtn.className = 'password-toggle';
            toggleBtn.innerHTML = '👁️';
            toggleBtn.setAttribute('aria-label', 'Toggle password visibility');
            toggleBtn.style.cssText = `
                position: absolute;
                right: 12px;
                top: 50%;
                transform: translateY(-50%);
                background: none;
                border: none;
                cursor: pointer;
                font-size: 16px;
                opacity: 0.6;
                transition: opacity 0.3s ease;
            `;
            
            toggleBtn.addEventListener('click', function() {
                const isPassword = field.type === 'password';
                field.type = isPassword ? 'text' : 'password';
                this.innerHTML = isPassword ? '🙈' : '👁️';
                this.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
            });
            
            toggleBtn.addEventListener('mouseenter', function() {
                this.style.opacity = '1';
            });
            
            toggleBtn.addEventListener('mouseleave', function() {
                this.style.opacity = '0.6';
            });
            
            field.style.paddingRight = '45px';
            wrapper.appendChild(toggleBtn);
        });

        // Auto-focus first input
        const firstInput = document.querySelector('.form-control');
        if (firstInput && !firstInput.value) {
            setTimeout(() => {
                firstInput.focus();
            }, 500);
        }

        // Form input animations
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            // Add floating label effect
            const label = input.previousElementSibling;
            if (label && label.tagName === 'LABEL') {
                label.style.transition = 'all 0.3s ease';
                
                function updateLabelState() {
                    if (input.value || input === document.activeElement) {
                        label.style.transform = 'translateY(-25px) scale(0.8)';
                        label.style.color = 'var(--primary-color)';
                    } else {
                        label.style.transform = 'translateY(0) scale(1)';
                        label.style.color = 'var(--text-secondary)';
                    }
                }
                
                input.addEventListener('focus', updateLabelState);
                input.addEventListener('blur', updateLabelState);
                input.addEventListener('input', updateLabelState);
                
                // Initial state
                updateLabelState();
            }
        });

        // Form submission with better UX
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('.form-submit');
                if (submitBtn) {
                    // Add success animation class
                    submitBtn.classList.add('submitting');
                    
                    // Disable double submission
                    if (submitBtn.disabled) {
                        e.preventDefault();
                        return false;
                    }
                }
            });
        });
    }

    // Utility Functions
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // Performance Monitoring
    function setupPerformanceMonitoring() {
        // Monitor page load performance
        window.addEventListener('load', function() {
            if ('performance' in window) {
                setTimeout(() => {
                    const perfData = performance.getEntriesByType('navigation')[0];
                    if (perfData) {
                        console.log('Page Load Time:', perfData.loadEventEnd - perfData.fetchStart, 'ms');
                    }
                }, 0);
            }
        });
    }

    // Error Handling
    function setupErrorHandling() {
        window.addEventListener('error', function(e) {
            console.error('JavaScript Error:', e.error);
            
            // Show user-friendly error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-error';
            errorDiv.textContent = 'Something went wrong. Please refresh the page and try again.';
            errorDiv.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                max-width: 300px;
                animation: slideDown 0.3s ease;
            `;
            
            document.body.appendChild(errorDiv);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (errorDiv.parentNode) {
                    errorDiv.remove();
                }
            }, 5000);
        });
    }

    // Theme Switching (if needed in future)
    function setupThemeToggle() {
        // Check for saved theme preference or default to light
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        
        // Create theme toggle button (hidden by default)
        const themeToggle = document.createElement('button');
        themeToggle.className = 'theme-toggle';
        themeToggle.setAttribute('aria-label', 'Toggle dark mode');
        themeToggle.style.display = 'none'; // Hidden for now
        
        themeToggle.addEventListener('click', function() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        });
    }

    // Initialize performance monitoring and error handling
    setupPerformanceMonitoring();
    setupErrorHandling();
    setupThemeToggle();

    // Smooth scroll polyfill for older browsers
    function setupSmoothScroll() {
        const links = document.querySelectorAll('a[href^="#"]');
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href === '#') return;
                
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    // Call smooth scroll setup
    setupSmoothScroll();

    // Service Worker Registration (for future PWA features)
    function setupServiceWorker() {
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                // Uncomment when service worker is available
                // navigator.serviceWorker.register('/sw.js')
                //     .then(function(registration) {
                //         console.log('SW registered: ', registration);
                //     })
                //     .catch(function(registrationError) {
                //         console.log('SW registration failed: ', registrationError);
                //     });
            });
        }
    }

    // Lazy loading for images (if any)
    function setupLazyLoading() {
        if ('IntersectionObserver' in window) {
            const lazyImages = document.querySelectorAll('img[data-src]');
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            lazyImages.forEach(img => imageObserver.observe(img));
        }
    }

    // Initialize additional features
    setupLazyLoading();

    // Export functions for global use if needed
    window.DulifyApp = {
        validateField: validateField,
        showLoadingState: showLoadingState,
        debounce: debounce,
        throttle: throttle
    };

})();

// Additional CSS-in-JS for dynamic styling
(function() {
    const style = document.createElement('style');
    style.textContent = `
        .form-control:focus + .password-toggle {
            opacity: 1 !important;
        }
        
        .submitting {
            pointer-events: none;
            opacity: 0.8;
        }
        
        .animate-in {
            animation: fadeInUp 0.6s ease forwards;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .loaded .form-container {
            animation: slideUpBounce 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }
        
        @keyframes slideUpBounce {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .error-message {
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        /* Improved mobile menu animations */
        @media (max-width: 768px) {
            .nav-links {
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .nav-links.active {
                animation: slideInFromTop 0.3s ease forwards;
            }
            
            @keyframes slideInFromTop {
                from {
                    transform: translateY(-100vh);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }
        }
    `;
    document.head.appendChild(style);
})();