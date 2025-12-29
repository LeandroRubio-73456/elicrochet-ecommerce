document.addEventListener('DOMContentLoaded', function () {
    // Defer Heavy Animations (TBT Fix)
    window.addEventListener('load', () => {
        // Intersection Observer para animaciones
        const observerOptions = {
            threshold: 0.15,
            rootMargin: '0px 0px 0px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target); // Save resources
                }
            });
        }, observerOptions);

        // Observar elementos
        const elements = document.querySelectorAll('.category-card-modern, .product-card-modern, .feature-modern, .testimonial-modern');
        elements.forEach((el, index) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';

            const delay = (index % 4) * 0.1;
            el.style.transition = `opacity 0.6s cubic-bezier(0.215, 0.61, 0.355, 1) ${delay}s, transform 0.6s cubic-bezier(0.215, 0.61, 0.355, 1) ${delay}s`;
            observer.observe(el);
        });
    });

    // Lazy loading de imágenes
    const images = document.querySelectorAll('img[loading="lazy"]');
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src || img.src;
                imageObserver.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));

    // Newsletter form
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            alert('¡Gracias por suscribirte! Te enviaremos novedades a ' + email);
            this.reset();
        });
    }
});
