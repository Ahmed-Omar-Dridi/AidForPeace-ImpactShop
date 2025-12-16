/**
 * Donation Widget - Interactive donation features
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Animate donation cards on scroll
    const donationCards = document.querySelectorAll('.donation-card');
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'slideInUp 0.6s ease-out forwards';
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    
    donationCards.forEach(card => observer.observe(card));
    
    // Add ripple effect to donation cards
    donationCards.forEach(card => {
        card.addEventListener('click', function(e) {
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
    
    // Animate impact numbers
    const impactNumbers = document.querySelectorAll('.impact-number');
    impactNumbers.forEach(number => {
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateNumber(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        observer.observe(number);
    });
    
    function animateNumber(element) {
        const finalValue = parseInt(element.textContent.replace(/\D/g, ''));
        const duration = 2000;
        const steps = 60;
        const stepValue = finalValue / steps;
        let currentStep = 0;
        
        const interval = setInterval(() => {
            currentStep++;
            const currentValue = Math.floor(stepValue * currentStep);
            element.textContent = currentValue.toLocaleString() + '+';
            
            if (currentStep >= steps) {
                element.textContent = finalValue.toLocaleString() + '+';
                clearInterval(interval);
            }
        }, duration / steps);
    }
    
    // Testimonial carousel
    const testimonialCards = document.querySelectorAll('.testimonial-card');
    if (testimonialCards.length > 0) {
        let currentTestimonial = 0;
        
        setInterval(() => {
            testimonialCards.forEach(card => {
                card.style.opacity = '0.5';
                card.style.transform = 'scale(0.95)';
            });
            
            testimonialCards[currentTestimonial].style.opacity = '1';
            testimonialCards[currentTestimonial].style.transform = 'scale(1)';
            
            currentTestimonial = (currentTestimonial + 1) % testimonialCards.length;
        }, 5000);
    }
    
    // Form validation
    const donationForm = document.getElementById('donationForm');
    if (donationForm) {
        donationForm.addEventListener('submit', function(e) {
            const amount = document.getElementById('donationAmount').value;
            
            if (!amount || amount <= 0) {
                e.preventDefault();
                alert('Veuillez entrer un montant valide');
                return;
            }
            
            // Add success animation
            const btn = this.querySelector('.donate-btn');
            btn.innerHTML = '<i class="fas fa-check"></i> Donation en cours...';
            btn.disabled = true;
        });
    }
    
    // Donation amount input validation
    const amountInput = document.getElementById('donationAmount');
    if (amountInput) {
        amountInput.addEventListener('input', function() {
            if (this.value < 0) this.value = 0;
        });
    }
    
    // Custom amount input
    const customAmountInput = document.getElementById('customAmount');
    if (customAmountInput) {
        customAmountInput.addEventListener('input', function() {
            if (this.value < 0) this.value = 0;
        });
    }
    
    // Add CSS for animations if not already present
    if (!document.querySelector('style[data-donation-animations]')) {
        const style = document.createElement('style');
        style.setAttribute('data-donation-animations', 'true');
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
            
            .ripple {
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 182, 0, 0.6);
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
            
            .testimonial-card {
                transition: all 0.5s ease;
            }
        `;
        document.head.appendChild(style);
    }
    
    // Donation counter
    const donationCounter = document.querySelector('[data-donation-counter]');
    if (donationCounter) {
        let count = 0;
        const target = parseInt(donationCounter.getAttribute('data-donation-counter'));
        
        const interval = setInterval(() => {
            count += Math.ceil(target / 50);
            if (count >= target) {
                count = target;
                clearInterval(interval);
            }
            donationCounter.textContent = count.toLocaleString() + ' TND';
        }, 50);
    }
});

// Donation tracking
function trackDonation(amount, method) {
    console.log('Donation tracked:', { amount, method, timestamp: new Date() });
    // Send to analytics
}
