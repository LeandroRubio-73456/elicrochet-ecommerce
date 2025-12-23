<footer class="bg-dark text-white pt-5 pb-3 mt-auto">
    <div class="container">
        <div class="row g-4 justify-content-between">
            <div class="col-lg-4 col-md-6">
                <h3 class="fw-bold text-white mb-3">EliCrochet</h3>
                <p class="text-white-50">Tejemos sueños y creamos compañeros inolvidables con la mejor calidad y artesanía.</p>
                <div class="d-flex gap-3 social-icons mt-4">
                    <a href="#" class="text-white-50 hover-white"><i class="ti ti-brand-instagram fs-4"></i></a>
                    <a href="#" class="text-white-50 hover-white"><i class="ti ti-brand-facebook fs-4"></i></a>
                    <a href="#" class="text-white-50 hover-white"><i class="ti ti-brand-whatsapp fs-4"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <h5 class="fw-bold mb-3 text-white">Enlaces Rápidos</h5>
                <ul class="list-unstyled text-white-50">
                    <li class="mb-2"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none hover-primary">Inicio</a></li>
                    <li class="mb-2"><a href="{{ route('shop') }}" class="text-white-50 text-decoration-none hover-primary">Tienda</a></li>
                    <li class="mb-2"><a href="{{ route('contact') }}" class="text-white-50 text-decoration-none hover-primary">Contacto</a></li>
                    <li class="mb-2"><a href="{{ route('cart') }}" class="text-white-50 text-decoration-none hover-primary">Carrito</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h5 class="fw-bold mb-3 text-white">Contáctanos</h5>
                <ul class="list-unstyled text-white-50">
                    <li class="mb-2"><i class="ti ti-map-pin me-2"></i> Quito, Ecuador</li>
                    <li class="mb-2"><i class="ti ti-mail me-2"></i> contacto@elicrochet.com</li>
                    <li class="mb-2"><i class="ti ti-phone me-2"></i> +593 9 9999 9999</li>
                </ul>
            </div>
        </div>
        <hr class="border-white-10 my-4 opacity-10">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0 text-white-50 small">&copy; {{ date('Y') }} EliCrochet. Todos los derechos reservados.</p>
            </div>
            <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
                <i class="ti ti-credit-card text-white-50 fs-5 me-2"></i>
                <i class="ti ti-brand-visa text-white-50 fs-5 me-2"></i>
                <i class="ti ti-brand-mastercard text-white-50 fs-5"></i>
            </div>
        </div>
    </div>
</footer>
