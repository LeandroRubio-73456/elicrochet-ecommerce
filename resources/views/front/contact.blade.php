@extends('layouts.front-layout')

@section('title', 'Contáctanos | EliCrochet')

@section('content')
<!-- Header con Gradiente y Mapa -->
<!-- Hero Header Minimalista -->
<div class="shop-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="header-content">
                    <h1 class="header-title">
                        Contáctanos
                    </h1>
                    <p class="header-description">
                        ¿Tienes dudas o ideas? Escríbenos y hagamos algo especial juntos.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <nav aria-label="breadcrumb" class="breadcrumb-modern">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}">
                                <i class="ti ti-home"></i>
                                Inicio
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Contacto</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="contact-form py-5">
    <div class="container">
        <div class="row justify-content-center text-center mb-5 wow fadeInUp" data-wow-delay="0.2s">
            <div class="col-md-10 col-xl-6">
                <h5 class="text-primary mb-0">Hablemos</h5>
                <h2 class="my-3">Envíanos tu mensaje</h2>
                <p class="text-muted">Completa el formulario y nos pondremos en contacto contigo lo antes posible.</p>
            </div>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-xxl-6 col-md-8 col-sm-10">
                <div class="card border-0 shadow-lg wow fadeInUp" data-wow-delay="0.3s">
                    <div class="card-body p-5">
                        <form action="{{ route('contact.store') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Nombres</label>
                                    <input type="text" id="name" name="name" class="form-control" required value="{{ old('name') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="lastname" class="form-label">Apellidos</label>
                                    <input type="text" id="lastname" name="lastname" class="form-control" required value="{{ old('lastname') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Correo Electrónico</label>
                                    <input type="email" id="email" name="email" class="form-control" required value="{{ old('email') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Teléfono</label>
                                    <input type="tel" id="phone" name="phone" class="form-control" value="{{ old('phone') }}">
                                </div>
                                <div class="col-12">
                                    <label for="subject" class="form-label">Tipo de Consulta</label>
                                    <select class="form-select" id="subject" name="subject">
                                        <option value="general" selected>Consulta General</option>
                                        <option value="wholesale">Mayorista / Distribución</option>
                                        <option value="issue">Reportar un problema</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="message" class="form-label">Mensaje</label>
                                    <textarea class="form-control" id="message" name="message" rows="4" required>{{ old('message') }}</textarea>
                                </div>
                            </div>
                            
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" value="1" id="policyCheck" name="policy_check" required>
                                <label class="form-check-label text-muted" for="policyCheck">
                                    Al enviar, aceptas nuestra <a href="{{ route('legal.terms') }}#privacy" target="_blank" class="text-primary text-decoration-none">Política de Privacidad</a>.
                                </label>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary shadow-lg">
                                    <i class="ti ti-send me-2"></i> Enviar Mensaje
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>

<!-- FAQ Section -->
<section class="faq-section py-5 bg-light" id="faq">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-md-8">
                <h5 class="text-primary">Resolver dudas</h5>
                <h2 class="mb-3">Preguntas Frecuentes</h2>
                <p class="text-muted">Aquí encuentras respuestas a las consultas más comunes.</p>
            </div>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion custom-accordion" id="faqAccordion">
                    
                    <div class="accordion-item border-0 mb-3 shadow-sm rounded overflow-hidden">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                ¿Realizan envíos a todo el Ecuador?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                Sí, realizamos envíos a nivel nacional a través de Servientrega. El tiempo estimado de entrega suele ser de 24 horas laborables en ciudades principales. Puedes ver el estado de tu pedido en <a href="{{ route('account.orders.index') }}">Mis Pedidos</a>.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 mb-3 shadow-sm rounded overflow-hidden">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                ¿Cómo funcionan los pedidos personalizados?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                Puedes solicitar un pedido personalizado directamente desde tu panel de cliente. <a href="{{ route('account.custom.create') }}">Haz clic aquí para crear un pedido personalizado</a>. Te enviaremos una cotización y, una vez aprobada, comenzaremos la fabricación.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 mb-3 shadow-sm rounded overflow-hidden">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                ¿Cuáles son los métodos de pago?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                Aceptamos pagos seguros en línea mediante PayPhone (Tarjetas de Crédito/Débito) para tu comodidad y seguridad.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
@endsection
