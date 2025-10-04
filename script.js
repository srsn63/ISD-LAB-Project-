  // Mobile menu toggle
        const menuToggle = document.getElementById('menuToggle');
        const navLinks = document.getElementById('navLinks');

        menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            menuToggle.classList.toggle('active');
        });

        // Navbar scroll effect
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    navLinks.classList.remove('active');
                    menuToggle.classList.remove('active');
                }
            });
        });

        // Action cards click animation
        document.querySelectorAll('.action-card').forEach(card => {
            card.addEventListener('click', function() {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 200);
            });
        });

        // Number counter animation
        const counters = document.querySelectorAll('.stat-number');
        const speed = 200;

        counters.forEach(counter => {
            const updateCount = () => {
                const target = counter.innerText;
                const count = 0;
                const numericTarget = parseInt(target.replace(/\D/g, ''));
                
                if (isNaN(numericTarget)) return;

                const inc = numericTarget / speed;

                const timer = setInterval(() => {
                    const current = parseInt(counter.innerText.replace(/\D/g, '')) || 0;
                    if (current < numericTarget) {
                        const newValue = Math.ceil(current + inc);
                        const suffix = target.includes('K') ? 'K+' : target.includes('/') ? '/7' : '+';
                        counter.innerText = newValue + suffix;
                    } else {
                        counter.innerText = target;
                        clearInterval(timer);
                    }
                }, 1);
            };

            const observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting) {
                    updateCount();
                    observer.disconnect();
                }
            });

            observer.observe(counter);
        });

        // Scroll to top button
        const scrollTopBtn = document.getElementById('scrollTop');
        
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                scrollTopBtn.classList.add('active');
            } else {
                scrollTopBtn.classList.remove('active');
            }
        });

        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Form submission
        document.querySelector('.search-btn').addEventListener('click', function(e) {
            e.preventDefault();
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-search"></i> Search Flights';
                alert('Flight search functionality would be implemented here!');
            }, 1500);
        });

        document.querySelector('.newsletter-form button').addEventListener('click', function(e) {
            e.preventDefault();
            const email = document.querySelector('.newsletter-form input').value;
            if (email) {
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subscribing...';
                setTimeout(() => {
                    this.innerHTML = 'Subscribed!';
                    document.querySelector('.newsletter-form input').value = '';
                    setTimeout(() => {
                        this.innerHTML = 'Subscribe';
                    }, 2000);
                }, 1500);
            }
        });