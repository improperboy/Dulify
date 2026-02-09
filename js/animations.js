// Dulify - Animations and Interactive Features

document.addEventListener('DOMContentLoaded', function() {
    // Navbar scroll effect
    const navbar = document.querySelector('.navbar');
    
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });
    }

    // Initialize animations for elements with animation classes
    const animateElements = document.querySelectorAll(
        '.animate-fadeInUp, .animate-fadeIn, .animate-slideInRight, .animate-slideInLeft'
    );
    
    // Intersection Observer for scroll animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                if (entry.target.classList.contains('animate-fadeInUp')) {
                    entry.target.style.animation = 'fadeInUp 0.8s forwards';
                } else if (entry.target.classList.contains('animate-fadeIn')) {
                    entry.target.style.animation = 'fadeIn 0.8s forwards';
                } else if (entry.target.classList.contains('animate-slideInRight')) {
                    entry.target.style.animation = 'slideInRight 0.8s forwards';
                } else if (entry.target.classList.contains('animate-slideInLeft')) {
                    entry.target.style.animation = 'slideInLeft 0.8s forwards';
                }
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1
    });
    
    animateElements.forEach(element => {
        observer.observe(element);
    });

    // Service cards hover effect enhancement
    const serviceCards = document.querySelectorAll('.service-card');
    serviceCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.querySelector('.service-img img')?.classList.add('scale-effect');
        });
        
        card.addEventListener('mouseleave', function() {
            this.querySelector('.service-img img')?.classList.remove('scale-effect');
        });
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Counter animation for stats section
    const counterElements = document.querySelectorAll('.counter');
    
    function startCounting(element) {
        const target = parseInt(element.getAttribute('data-count'));
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;
        
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current);
            }
        }, 16);
    }

    if (counterElements.length > 0) {
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    startCounting(entry.target);
                    counterObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        counterElements.forEach(counter => {
            counterObserver.observe(counter);
        });
    }

    // Testimonial carousel (if present)
    const testimonialContainer = document.querySelector('.testimonial-carousel');
    if (testimonialContainer) {
        let currentSlide = 0;
        const slides = testimonialContainer.querySelectorAll('.testimonial-item');
        const totalSlides = slides.length;
        
        if (totalSlides > 1) {
            // Create navigation dots
            const dotsContainer = document.createElement('div');
            dotsContainer.className = 'testimonial-dots';
            
            for (let i = 0; i < totalSlides; i++) {
                const dot = document.createElement('span');
                dot.className = 'testimonial-dot';
                if (i === 0) dot.classList.add('active');
                dot.addEventListener('click', () => goToSlide(i));
                dotsContainer.appendChild(dot);
            }
            
            testimonialContainer.appendChild(dotsContainer);
            
            // Auto-play functionality
            setInterval(() => {
                currentSlide = (currentSlide + 1) % totalSlides;
                goToSlide(currentSlide);
            }, 5000);
            
            function goToSlide(index) {
                slides.forEach((slide, i) => {
                    slide.style.transform = `translateX(${100 * (i - index)}%)`;
                });
                
                const dots = dotsContainer.querySelectorAll('.testimonial-dot');
                dots.forEach((dot, i) => {
                    dot.classList.toggle('active', i === index);
                });
                
                currentSlide = index;
            }
            
            // Initialize position
            slides.forEach((slide, i) => {
                slide.style.transform = `translateX(${100 * i}%)`;
            });
        }
    }

    // Mobile menu toggle
    const menuToggle = document.querySelector('.navbar-toggler');
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            document.body.classList.toggle('menu-open');
        });
    }

    // Form validation enhancement
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value.trim() !== '') {
                    this.classList.add('has-value');
                } else {
                    this.classList.remove('has-value');
                }
            });
        });
    });
});

// Add SVG wave pattern for modern design aesthetic
function createWavePattern() {
    const waveSvgs = document.querySelectorAll('.wave-pattern');
    
    waveSvgs.forEach(wave => {
        // Create random wave pattern
        const waveColors = ['rgba(67, 97, 238, 0.05)', 'rgba(76, 201, 240, 0.05)', 'rgba(247, 37, 133, 0.03)'];
        const numWaves = 3;
        
        let svgContent = `<svg viewBox="0 0 1200 120" preserveAspectRatio="none">`;
        
        for (let i = 0; i < numWaves; i++) {
            const amplitude = 15 + (i * 5);
            const yOffset = 40 + (i * 20);
            
            svgContent += `<path fill="${waveColors[i]}" d="M0,${yOffset} C300,${yOffset - amplitude} 600,${yOffset + amplitude} 1200,${yOffset} L1200,120 L0,120 Z"></path>`;
        }
        
        svgContent += `</svg>`;
        wave.innerHTML = svgContent;
    });
}

// Call once DOM is loaded
document.addEventListener('DOMContentLoaded', createWavePattern);
