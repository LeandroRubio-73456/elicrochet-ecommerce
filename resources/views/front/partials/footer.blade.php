<footer class="footer-modern">
    <div class="container">
        <!-- Main Footer Content -->
        <div class="row g-4 pb-5">
            <!-- Brand Section -->
            <div class="col-lg-4 col-md-6">
                <div class="footer-brand mb-4">
                    <img src="{{asset('Logo.png')}}" alt="EliCrochet" width="55" height="60" class="mb-3" loading="lazy">
                    <p class="footer-description">
                        Tejemos sueños y creamos compañeros inolvidables con la mejor calidad y artesanía.
                    </p>
                </div>
                <div class="social-links">
                    <a href="#" class="social-link" aria-label="Instagram">
                        <i class="ti ti-brand-instagram"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Facebook">
                        <i class="ti ti-brand-facebook"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6">
                <h4 class="footer-title">Enlaces</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}">Inicio</a></li>
                    <li><a href="{{ route('shop') }}">Tienda</a></li>
                    <li><a href="{{ route('contact') }}">Contacto</a></li>
                    <li><a href="{{ route('cart') }}">Carrito</a></li>
                </ul>
            </div>

            <!-- Customer Service -->
            <div class="col-lg-3 col-md-6">
                <h4 class="footer-title">Ayuda</h4>
                <ul class="footer-links">
                    <li><a href="#">Seguimiento de pedido</a></li>
                    <li><a href="#">Política de devolución</a></li>
                    <li><a href="#">Preguntas frecuentes</a></li>
                    <li><a href="#">Términos y condiciones</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-3 col-md-6">
                <h4 class="footer-title">Contacto</h4>
                <ul class="footer-contact">
                    <li>
                        <i class="ti ti-map-pin"></i>
                        <span>Quito, Ecuador</span>
                    </li>
                    <li>
                        <i class="ti ti-mail"></i>
                        <span>contacto@elicrochet.com</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="footer-copyright">
                        &copy; {{ date('Y') }} EliCrochet. Todos los derechos reservados.
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>


<style>
    /* Footer moderno y minimalista */
    .footer-modern {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        color: #ffffff;
        padding: 4rem 0 2rem;
        margin-top: auto;
    }

    /* Brand Section */
    .footer-description {
        color: rgba(255, 255, 255, 0.6);
        line-height: 1.6;
        font-size: 0.9375rem;
        margin: 0;
    }

    /* Social Links */
    .social-links {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
    }

    .social-link {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255, 255, 255, 0.7);
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .social-link:hover {
        background: var(--color-primary, #C16244);
        color: white;
        transform: translateY(-3px);
    }

    /* Footer Titles */
    .footer-title {
        color: #ffffff;
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        position: relative;
        padding-bottom: 0.75rem;
    }

    .footer-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40px;
        height: 2px;
        background: var(--color-primary, #C16244);
        border-radius: 2px;
    }

    /* Footer Links */
    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li {
        margin-bottom: 0.75rem;
    }

    .footer-links a {
        color: rgba(255, 255, 255, 0.6);
        text-decoration: none;
        font-size: 0.9375rem;
        transition: all 0.3s ease;
        display: inline-block;
    }

    .footer-links a:hover {
        color: var(--color-primary, #C16244);
        transform: translateX(5px);
    }

    /* Footer Contact */
    .footer-contact {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-contact li {
        display: flex;
        align-items: start;
        gap: 0.75rem;
        margin-bottom: 1rem;
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.9375rem;
    }

    .footer-contact svg {
        flex-shrink: 0;
        margin-top: 2px;
        opacity: 0.8;
    }

    /* Footer Bottom */
    .footer-bottom {
        padding-top: 2rem;
        margin-top: 3rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .footer-copyright {
        margin: 0;
        color: rgba(255, 255, 255, 0.5);
        font-size: 0.875rem;
    }

    /* Payment Methods */
    .payment-methods {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .payment-label {
        color: rgba(255, 255, 255, 0.5);
        font-size: 0.875rem;
        margin-right: 0.5rem;
    }

    .payment-icon {
        opacity: 0.8;
        transition: opacity 0.3s ease;
    }

    .payment-icon:hover {
        opacity: 1;
    }

    /* Responsive */
    @media (max-width: 991px) {
        .footer-modern {
            padding: 3rem 0 1.5rem;
        }

        .footer-title::after {
            width: 30px;
        }

        .social-links {
            justify-content: center;
        }

        .footer-brand {
            text-align: center;
        }

        .footer-bottom {
            margin-top: 2rem;
            padding-top: 1.5rem;
        }
    }

    @media (max-width: 767px) {
        .footer-modern {
            padding: 2rem 0 1rem;
        }

        .payment-methods {
            justify-content: center;
        }

        .payment-label {
            width: 100%;
            text-align: center;
            margin-bottom: 0.5rem;
        }
    }
</style>