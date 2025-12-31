@extends('layouts.front-layout')

@section('title', 'Contáctanos | EliCrochet')

@section('content')
<!-- Header con Gradiente y Mapa -->
<header class="contact-hero py-5" style="background: linear-gradient(135deg, #F9F1E9 0%, #F9DEC2 100%);">
    <div class="container">
        <div class="row justify-content-center align-items-center text-center text-md-start">
            <div class="col-md-5 wow fadeInLeft" data-wow-delay="0.2s">
                <h2 class="display-6 fw-bold">Habla con un <span class="text-primary" style="background: linear-gradient(135deg, #C16244 0%, #BAA794 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Experto</span></h2>
                <p class="text-muted mt-3">¿Tienes alguna duda sobre nuestros amigurumis o quieres un pedido personalizado? Estamos aquí para ayudarte a tejer tus sueños.</p>
            </div>
            <div class="col-md-6 mt-4 mt-md-0 wow fadeInRight" data-wow-delay="0.4s">
                <img src="{{ asset('assets/images/ecommerce/hero-crochet.png') }}" alt="Contacto EliCrochet" class="img-fluid rounded shadow-lg"> <!-- Usamos la imagen de hero como placeholder si no hay mapa -->
            </div>
        </div>
    </div>
</header>

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
                        <form>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="contact_name" class="form-label">Nombre</label>
                                    <input type="text" id="contact_name" class="form-control" placeholder="Tu Nombre">
                                </div>
                                <div class="col-md-6">
                                    <label for="contact_phone" class="form-label">Teléfono</label>
                                    <input type="number" id="contact_phone" class="form-control" placeholder="Tu Teléfono">
                                </div>
                                <div class="col-12">
                                    <label for="contact_email" class="form-label">Correo Electrónico</label>
                                    <input type="email" id="contact_email" class="form-control" placeholder="correo@ejemplo.com">
                                </div>
                                <div class="col-12">
                                    <label for="contact_type" class="form-label">Tipo de Consulta</label>
                                    <select class="form-select" id="contact_type">
                                        <option selected>Consulta General</option>
                                        <option value="1">Pedido Personalizado</option>
                                        <option value="2">Estado de mi Pedido</option>
                                        <option value="3">Mayorista / Distribución</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="contact_message" class="form-label">Mensaje</label>
                                    <textarea class="form-control" id="contact_message" rows="4" placeholder="¿Cómo podemos ayudarte?"></textarea>
                                </div>
                            </div>
                            
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" value="" id="policyCheck" checked>
                                <label class="form-check-label text-muted" for="policyCheck">
                                    Al enviar, aceptas nuestra <a href="#" class="text-primary text-decoration-none">Política de Privacidad</a>.
                                </label>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="button" class="btn btn-primary shadow-lg">
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
@endsection
