/**
 * 3D Effects JavaScript
 * Adds interactive 3D mouse tracking and advanced animations
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // 3D Tilt Effect for Cards
    const cards = document.querySelectorAll('.role-card, .quick-link, .admin-link');
    
    cards.forEach(card => {
        card.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 10;
            const rotateY = (centerX - x) / 10;
            
            this.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateZ(20px)`;
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateZ(0)';
        });
    });
    
    // Parallax Effect on Scroll
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('[data-parallax]');
        
        parallaxElements.forEach(element => {
            const speed = element.getAttribute('data-parallax') || 0.5;
            element.style.transform = `translateY(${scrolled * speed}px)`;
        });
    });
    
    // Animate elements on scroll into view
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('stagger-3d');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe all cards
    document.querySelectorAll('.role-card, .quick-link, .admin-link').forEach(el => {
        observer.observe(el);
    });
    
    // Floating animation for logo
    const logo = document.querySelector('.logo-section h1');
    if (logo) {
        logo.addEventListener('mouseenter', function() {
            this.style.animation = 'float-3d 3s ease-in-out infinite';
        });
    }
    
    // Add glow effect on hover
    const glowElements = document.querySelectorAll('.quick-link');
    glowElements.forEach(element => {
        element.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            this.style.setProperty('--mouse-x', x + 'px');
            this.style.setProperty('--mouse-y', y + 'px');
        });
    });
    
    // Ripple effect on click
    document.querySelectorAll('.quick-link, .role-card').forEach(element => {
        element.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    });
    
    // Add CSS for ripple effect
    if (!document.querySelector('style[data-ripple]')) {
        const style = document.createElement('style');
        style.setAttribute('data-ripple', 'true');
        style.textContent = `
            .ripple {
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.6);
                transform: scale(0);
                animation: ripple-animation 0.6s ease-out;
                pointer-events: none;
            }
            
            @keyframes ripple-animation {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    // Cursor glow effect
    const cursorGlow = document.createElement('div');
    cursorGlow.style.cssText = `
        position: fixed;
        width: 30px;
        height: 30px;
        border: 2px solid rgba(255, 182, 0, 0.5);
        border-radius: 50%;
        pointer-events: none;
        z-index: 9999;
        display: none;
        box-shadow: 0 0 20px rgba(255, 182, 0, 0.3);
    `;
    document.body.appendChild(cursorGlow);
    
    document.addEventListener('mousemove', function(e) {
        cursorGlow.style.left = (e.clientX - 15) + 'px';
        cursorGlow.style.top = (e.clientY - 15) + 'px';
        cursorGlow.style.display = 'block';
    });
    
    document.addEventListener('mouseleave', function() {
        cursorGlow.style.display = 'none';
    });
    
    // Smooth scroll behavior
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Add depth effect to sections
    const sections = document.querySelectorAll('.products-preview, .quick-links-container, .admin-links-container');
    sections.forEach((section, index) => {
        section.style.animation = `slideInUp 0.8s ease-out ${index * 0.1}s both`;
    });
    
    // Add CSS for slide in animation
    if (!document.querySelector('style[data-slide-in]')) {
        const style = document.createElement('style');
        style.setAttribute('data-slide-in', 'true');
        style.textContent = `
            @keyframes slideInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px) rotateX(-10deg);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) rotateX(0);
                }
            }
        `;
        document.head.appendChild(style);
    }
});

// Parallax scroll effect
window.addEventListener('scroll', function() {
    const scrolled = window.pageYOffset;
    const parallaxBg = document.querySelector('.logo-section');
    
    if (parallaxBg) {
        parallaxBg.style.backgroundPosition = `0 ${scrolled * 0.5}px`;
    }
});
