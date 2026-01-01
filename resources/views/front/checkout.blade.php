@extends('layouts.front-layout')

@section('title', 'Finalizar Compra | EliCrochet')

@section('content')
    <section class="py-5 bg-light">
        <div class="container">
            <!-- Breadcrumb -->
            <div class="row mb-4 wow fadeInUp" data-wow-delay="0.1s">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}"
                                    class="text-decoration-none text-muted">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('cart') }}"
                                    class="text-decoration-none text-muted">Carrito</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Checkout</li>
                        </ol>
                    </nav>
                    <div class="d-flex align-items-center justify-content-between">
                        <h1 class="mb-0 fw-bold">Finalizar Compra</h1>
                        <span class="badge bg-light-primary text-primary border border-primary px-3 py-2 rounded-pill">
                            <i class="ti ti-shield-check me-1"></i> Checkout Seguro
                        </span>
                    </div>
                </div>
            </div>


            <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
                @csrf <!-- Token de seguridad de Laravel -->
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="row g-4">
                    <!-- Columna Izquierda: Formulario -->
                    <div class="col-lg-8 wow fadeInLeft" data-wow-delay="0.2s">
                        <!-- Paso 1: Información de Envío -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white p-4 border-bottom-0">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle me-3 px-2">1</div>
                                    <h5 class="mb-0 fw-bold">Información de Envío</h5>
                                </div>
                            </div>
                            <div class="card-body p-4 pt-0">
                                <div class="row g-3">
                                    @php
                                        $fullName = auth()->user()->name ?? '';
                                        $parts = explode(' ', $fullName, 2);
                                        $firstName = $parts[0] ?? '';
                                        $lastName = $parts[1] ?? '';
                                    @endphp
                                    <div class="col-md-6">
                                        <label for="customer_name" class="form-label">Nombres <span class="text-danger">*</span></label>
                                        <input type="text" id="customer_name" name="customer_name" class="form-control bg-light border-0"
                                            value="{{ $firstName }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="customer_lastname" class="form-label">Apellidos <span class="text-danger">*</span></label>
                                        <input type="text" id="customer_lastname" name="customer_lastname" class="form-control bg-light border-0"
                                            value="{{ $lastName }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="customer_email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" id="customer_email" name="customer_email" class="form-control bg-light border-0"
                                            value="{{ auth()->user()->email ?? '' }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="customer_phone" class="form-label">Teléfono <span class="text-danger">*</span></label>
                                        <input type="tel" id="customer_phone" name="customer_phone" class="form-control bg-light border-0"
                                            value="{{ auth()->user()->addresses->first()->phone ?? auth()->user()->phone ?? '' }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="shipping_address" class="form-label">Dirección <span class="text-danger">*</span></label>
                                        <input type="text" id="shipping_address" name="shipping_address" class="form-control bg-light border-0"
                                            value="{{ auth()->user()->addresses->first()->street ?? '' }}" placeholder="Calle, número, depto..." required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="shipping_reference" class="form-label">Referencia <span class="text-danger">*</span></label>
                                        <input type="text" id="shipping_reference" name="shipping_reference" class="form-control bg-light border-0"
                                            value="{{ auth()->user()->addresses->first()->reference ?? '' }}" placeholder="Ej: Junto a la farmacia azul">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="shipping_city" class="form-label">Ciudad <span class="text-danger">*</span></label>
                                        <input type="text" id="shipping_city" name="shipping_city" class="form-control bg-light border-0"
                                            value="{{ auth()->user()->addresses->first()->city ?? '' }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="shipping_province" class="form-label">Provincia <span class="text-danger">*</span></label>
                                        <input type="text" id="shipping_province" name="shipping_province" class="form-control bg-light border-0"
                                            value="{{ auth()->user()->addresses->first()->province ?? '' }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="shipping_zip" class="form-label">Código Postal <span class="text-danger">*</span></label>
                                        <input type="text" id="shipping_zip" name="shipping_zip" class="form-control bg-light border-0"
                                            value="{{ auth()->user()->addresses->first()->postal_code ?? '' }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Paso 2: Método de Pago (Simplificado) -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white p-4 border-bottom-0">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle me-3 px-2">2</div>
                                    <h5 class="mb-0 fw-bold">Método de Pago</h5>
                                </div>
                            </div>
                            <div class="card-body p-4 pt-0 text-center">
                                <div class="alert alert-success border-0">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-cash fs-3 me-3 text-success"></i>
                                        <div>
                                            <h6 class="mb-1">Pago del Pedido</h6>
                                            <p class="mb-0 small">Al confirmar, se generará tu pedido para ser procesado.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna Derecha: Resumen -->
                    <div class="col-lg-4 wow fadeInRight" data-wow-delay="0.4s">
                        <div class="card border-0 shadow-lg position-sticky" style="top: 100px;">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h4 class="card-title fw-bold mb-0">Tu Pedido</h4>
                                    <a href="{{ route('cart') }}" class="text-decoration-none small text-primary fw-bold">
                                        <i class="ti ti-pencil me-1"></i>Modificar Pedido
                                    </a>
                                </div>

                                <!-- Lista de Items Dinámica -->
                                <div class="mb-4" style="max-height: 300px; overflow-y: auto;">
                                    @foreach ($cartItems as $item)
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="flex-shrink-0">
                                            <div class="flex-shrink-0">
                                                @php
                                                    $imagePath = 'https://placehold.co/100x100?text=Sin+Imagen';
                                                    if ($item->product && $item->product->images->first()) {
                                                        $imagePath = asset('storage/' . $item->product->images->first()->image_path);
                                                    } elseif (isset($item->attributes['image']) && $item->attributes['image']) {
                                                        $imagePath = asset('storage/' . $item->attributes['image']);
                                                    }
                                                    $name = $item->product ? $item->product->name : ($item->attributes['name'] ?? 'Custom Order');
                                                @endphp
                                                <img src="{{ $imagePath }}" class="rounded" width="50" height="50"
                                                    alt="{{ $name }}">
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0 text-dark">{{ $name }}</h6>
                                                <small class="text-muted">{{ $item->quantity }} x
                                                    ${{ number_format($item->price, 2) }}</small>
                                            </div>
                                            <div class="text-end fw-bold">
                                                ${{ number_format($item->price * $item->quantity, 2) }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Subtotal</span>
                                    <span class="fw-bold">${{ number_format($total, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Envío</span>
                                    <span class="text-success fw-bold">Gratis</span>
                                </div>
                                <div class="d-flex justify-content-between mt-3 pt-3 border-top">
                                    <span class="h5 fw-bold mb-0">Total</span>
                                    <span class="h4 fw-bold text-primary mb-0">${{ number_format($total, 2) }}</span>
                                </div>

                                <!-- Botón que envía el formulario -->
                                <button type="submit" class="btn btn-primary w-100 py-3 mt-4 shadow-lg pulse-button"
                                    id="submitBtn">
                                    <i class="ti ti-check me-2"></i> Confirmar Pedido
                                </button>

                                <p class="text-center text-muted small mt-3 mb-0">
                                    Al confirmar, aceptas nuestros <a href="#" class="text-decoration-none">Términos
                                        y Condiciones</a>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Validación básica del formulario antes de enviar
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');

            // Deshabilitar el botón para evitar doble clic
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="ti ti-loader me-2"></i> Procesando...';

            // Validación simple de campos requeridos
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Por favor, completa todos los campos obligatorios (*)');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="ti ti-check me-2"></i> Confirmar Pedido';
            }
        });
    </script>
@endpush
