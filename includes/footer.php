    <!-- Footer -->
    <footer class="footer-modern">
        <div class="footer-glow"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <div class="footer-brand">
                        <i class="fas fa-hand-holding-heart"></i>
                        <span>AidForPeace</span>
                    </div>
                    <p class="footer-tagline">Making a difference, one donation at a time.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="footer-social">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <hr class="footer-divider">
            <p class="footer-copyright">&copy; 2025 AidForPeace. All rights reserved.</p>
        </div>
    </footer>
    
    <style>
        .footer-modern {
            background: linear-gradient(135deg, #07112b 0%, #0d1f3c 100%);
            color: white;
            padding: 50px 0 30px;
            margin-top: 80px;
            position: relative;
            overflow: hidden;
            border-top: 1px solid rgba(255, 215, 0, 0.2);
        }

        .footer-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #ffd700, #00bcd4, #ffd700);
            background-size: 200% 100%;
            animation: gradientMove 3s ease infinite;
        }

        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .footer-glow {
            position: absolute;
            top: -100px;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 200px;
            background: radial-gradient(ellipse, rgba(255, 215, 0, 0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .footer-brand i {
            color: #ffd700;
            font-size: 1.8rem;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .footer-tagline {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.95rem;
            margin: 0;
        }

        .footer-social {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
        }

        .social-link {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 215, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .social-link:hover {
            background: #ffd700;
            color: #07112b;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(255, 215, 0, 0.3);
        }

        .footer-divider {
            border-color: rgba(255, 215, 0, 0.2);
            margin: 30px 0 20px;
        }

        .footer-copyright {
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.9rem;
            margin: 0;
        }

        @media (max-width: 768px) {
            .footer-social {
                justify-content: center;
            }
            
            .footer-brand {
                justify-content: center;
            }
        }
    </style>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Add animation classes to elements when they come into view
        document.addEventListener('DOMContentLoaded', function() {
            // Animate cards on scroll
            const cards = document.querySelectorAll('.card, .card-lift');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry, index) => {
                    if (entry.isIntersecting) {
                        entry.target.style.animationDelay = `${index * 0.1}s`;
                        entry.target.classList.add('animate-slide-up');
                    }
                });
            }, { threshold: 0.1 });

            cards.forEach(card => observer.observe(card));

            // Add hover sound effect (optional)
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(btn => {
                btn.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px)';
                });
                btn.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>
