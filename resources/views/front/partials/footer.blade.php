<footer class="footer-modern">
    <div class="container">
        <!-- Main Footer Content -->
        <div class="row g-4 pb-5">
            <!-- Brand Section -->
            <div class="col-lg-4 col-md-6">
                <div class="footer-brand mb-4">
                    <img src="{{ asset('Logo.png') }}" alt="EliCrochet" height="50" class="mb-3">
                    <p class="footer-description">
                        Tejemos sueños y creamos compañeros inolvidables con la mejor calidad y artesanía.
                    </p>
                </div>
                <div class="social-links">
                    <a href="#" class="social-link" aria-label="Instagram">
                        <i class="ti-instagram"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Facebook">
                        <i class="ti-facebook"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6">
                <h5 class="footer-title">Enlaces</h5>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}">Inicio</a></li>
                    <li><a href="{{ route('shop') }}">Tienda</a></li>
                    <li><a href="{{ route('contact') }}">Contacto</a></li>
                    <li><a href="{{ route('cart') }}">Carrito</a></li>
                </ul>
            </div>

            <!-- Customer Service -->
            <div class="col-lg-3 col-md-6">
                <h5 class="footer-title">Ayuda</h5>
                <ul class="footer-links">
                    <li><a href="#">Seguimiento de pedido</a></li>
                    <li><a href="#">Política de devolución</a></li>
                    <li><a href="#">Preguntas frecuentes</a></li>
                    <li><a href="#">Términos y condiciones</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-3 col-md-6">
                <h5 class="footer-title">Contacto</h5>
                <ul class="footer-contact">
                    <li>
                        <i class="ti-map"></i>
                        <span>Quito, Ecuador</span>
                    </li>
                    <li>
                        <i class="ti-email"></i>
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
                <div class="col-md-6 text-center text-md-end">
                    <div class="payment-methods">
                        <span class="payment-label">Métodos de pago:</span>
                        <svg width="32" height="24" viewBox="0 0 32 24" fill="none" class="payment-icon">
                            <rect width="32" height="24" rx="4" fill="#1434CB"/>
                            <path d="M13.2 17.6h5.6V6.4h-5.6v11.2z" fill="#FF5F00"/>
                            <path d="M13.6 12a7.1 7.1 0 012.4-5.6 7.2 7.2 0 100 11.2A7.1 7.1 0 0113.6 12z" fill="#EB001B"/>
                            <path d="M27.2 12a7.2 7.2 0 01-11.2 6 7.2 7.2 0 000-12 7.2 7.2 0 0111.2 6z" fill="#F79E1B"/>
                        </svg>
                        <svg width="32" height="24" viewBox="0 0 32 24" fill="none" class="payment-icon">
                            <rect width="32" height="24" rx="4" fill="#00579F"/>
                            <path d="M14.4 8.8L12 15.2h-2.4l-1.2-4.7c-.1-.3-.2-.4-.4-.5-.4-.2-1-.4-1.6-.5l.1-.3h2.7c.4 0 .7.2.8.6l.7 3.8 1.8-4.4h2.5zm5.9 4.3c0-1.7-2.4-1.8-2.4-2.5 0-.2.2-.5.7-.5.6-.1 1.1 0 1.5.2l.3-1.3c-.4-.1-.9-.2-1.5-.2-2.3 0-3.9 1.2-3.9 2.9 0 1.3 1.1 2 2 2.4.9.4 1.2.7 1.2 1.1 0 .6-.7.9-1.4.9-.7 0-1.3-.2-1.8-.4l-.3 1.4c.5.2 1.3.4 2.2.4 2.5 0 4.1-1.2 4.1-3.1h.3zm6.3 2.1h2.1l-1.8-6.4h-2c-.3 0-.6.2-.7.5l-2.4 5.9h2.4l.5-1.3h2.9v1.3zm-2.6-3.1l1.2-3.3.7 3.3h-1.9zm-8.8-3.3l-1.9 6.4h-2.3l1.9-6.4h2.3z" fill="white"/>
                        </svg>
                    </div>
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
    .footer-brand img {
        filter: brightness(0) invert(1);
        opacity: 0.9;
    }

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