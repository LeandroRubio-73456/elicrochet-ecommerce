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
                        <h1 class="mb-0 fw-bold pe-2">Finalizar Compra</h1>
                        <span class="badge bg-light-primary text-primary border border-primary px-3 py-2 rounded-pill">
                            <i class="ti ti-shield-check me-1"></i> Checkout Seguro
                        </span>
                    </div>
                </div>
            </div>

            <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
                @csrf
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
                                    <div class="bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                        <span class="fw-bold">1</span>
                                    </div>
                                    <h5 class="mb-0 fw-bold">Información de Envío</h5>
                                </div>
                            </div>
                            <div class="card-body p-4 pt-0">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="customer_name" class="form-label">Nombres <span class="text-danger">*</span></label>
                                        <input type="text" id="customer_name" name="customer_name" class="form-control bg-light border-0"
                                            value="{{ auth()->user()->name ?? '' }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="customer_lastname" class="form-label">Apellidos <span class="text-danger">*</span></label>
                                        <input type="text" id="customer_lastname" name="customer_lastname" class="form-control bg-light border-0"
                                            value="{{ auth()->user()->lastname ?? '' }}" required>
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
                                        <label for="shipping_reference" class="form-label">Referencia</label>
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

                        <!-- Paso 2: Método de Pago -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white p-4 border-bottom-0">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                        <span class="fw-bold">2</span>
                                    </div>
                                    <h5 class="mb-0 fw-bold">Método de Pago</h5>
                                </div>
                            </div>
                            <div class="card-body p-4 pt-0 text-center">
                                <div class="alert alert-success border-0 mb-0">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-cash fs-3 me-3 text-success"></i>
                                        <div class="text-start">
                                            <h6 class="mb-1 fw-bold">Pago del Pedido</h6>
                                            <p class="mb-0 small">Al confirmar, se generará tu pedido para ser procesado.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna Derecha: Resumen Optimizado -->
                    <div class="col-lg-4 wow fadeInRight" data-wow-delay="0.4s">
                        <div class="card border-0 shadow-lg position-sticky" style="top: 100px;">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h4 class="card-title fw-bold mb-0">Tu Pedido</h4>
                                    <a href="{{ route('cart') }}" class="small text-decoration-none text-primary fw-semibold">
                                        <i class="ti ti-pencil me-1"></i>Editar
                                    </a>
                                </div>

                                <!-- Lista de Items Optimizada -->
                                <div class="order-items-container mb-4" style="max-height: 350px; overflow-y: auto;">
                                    @foreach ($cartItems as $item)
                                        @php
                                            $imagePath = 'https://placehold.co/80x80?text=Sin+Imagen';
                                            if ($item->product && $item->product->images->first()) {
                                                $imagePath = asset('storage/' . $item->product->images->first()->image_path);
                                            } elseif (isset($item->attributes['image']) && $item->attributes['image']) {
                                                $imagePath = asset('storage/' . $item->attributes['image']);
                                            }
                                            $name = $item->product ? $item->product->name : ($item->attributes['name'] ?? 'Pedido Personalizado');
                                        @endphp
                                        
                                        <div class="order-item border rounded p-3 mb-3 bg-white position-relative">
                                            <div class="d-flex gap-3">
                                                <!-- Imagen del producto -->
                                                <div class="flex-shrink-0">
                                                    <div class="position-relative">
                                                        <img src="{{ $imagePath }}" 
                                                             class="rounded" 
                                                             width="80" 
                                                             height="80"
                                                             style="object-fit: cover;"
                                                             alt="{{ $name }}">
                                                        <!-- Badge de cantidad -->
                                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                                            {{ $item->quantity }}
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Información del producto -->
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fw-bold text-dark">{{ $name }}</h6>
                                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                                        <small class="text-muted">
                                                            {{ $item->quantity }} × ${{ number_format($item->price, 2) }}
                                                        </small>
                                                        <span class="fw-bold text-primary">
                                                            ${{ number_format($item->price * $item->quantity, 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Resumen de Totales -->
                                <div class="order-summary">
                                    <hr class="my-3">
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Subtotal ({{ count($cartItems) }} {{ count($cartItems) == 1 ? 'artículo' : 'artículos' }})</span>
                                        <span class="fw-semibold">${{ number_format($total, 2) }}</span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="text-muted">Envío</span>
                                        <span class="text-success fw-bold">
                                            <i class="ti ti-truck-delivery me-1"></i>Gratis
                                        </span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top border-2">
                                        <span class="h5 fw-bold mb-0">Total</span>
                                        <span class="h4 fw-bold text-primary mb-0">${{ number_format($total, 2) }}</span>
                                    </div>
                                </div>

                                <!-- Botón de Confirmación -->
                                <button type="submit" 
                                        class="btn btn-primary w-100 py-3 mt-4 shadow-lg pulse-button"
                                        id="submitBtn">
                                    <i class="ti ti-check me-2"></i> Confirmar Pedido
                                </button>

                                <!-- Información de Seguridad -->
                                <div class="d-flex justify-content-center mt-3">
                                    <span class="badge bg-light-primary text-primary border border-primary px-3 py-2 rounded-pill">
                                        <i class="ti ti-lock me-1"></i> Transacción 100% segura
                                    </span>
                                </div>

                                <p class="text-center text-muted small mt-3 mb-0">
                                    Al confirmar, aceptas nuestros <a href="#" class="text-decoration-none text-primary">Términos y Condiciones</a>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('styles')
<style>
    /* Estilos personalizados para el checkout */
    .order-items-container {
        scrollbar-width: thin;
        scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
    }

    .order-items-container::-webkit-scrollbar {
        width: 6px;
    }

    .order-items-container::-webkit-scrollbar-track {
        background: transparent;
    }

    .order-items-container::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
        border-radius: 10px;
    }

    .order-item {
        transition: all 0.3s ease;
    }

    .order-item:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }

    .pulse-button {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(var(--bs-primary-rgb), 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(var(--bs-primary-rgb), 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(var(--bs-primary-rgb), 0);
        }
    }

    .form-control:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.15);
    }

    .is-invalid {
        border: 2px solid #dc3545 !important;
    }
</style>
@endpush

@push('scripts')
<script>
    // Validación mejorada del formulario
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');

        // Deshabilitar el botón para evitar doble envío
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Procesando...';

        // Validación de campos requeridos
        const requiredFields = this.querySelectorAll('[required]');
        let isValid = true;
        let firstInvalidField = null;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('is-invalid');
                
                if (!firstInvalidField) {
                    firstInvalidField = field;
                }
                
                // Agregar evento para remover el error al escribir
                field.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                }, { once: true });
            } else {
                field.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            
            // Scroll al primer campo inválido
            if (firstInvalidField) {
                firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalidField.focus();
            }
            
            // Mostrar mensaje de error
            const alertHtml = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ti ti-alert-circle me-2"></i>
                    Por favor, completa todos los campos obligatorios marcados con (*).
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            const form = document.getElementById('checkoutForm');
            form.insertAdjacentHTML('afterbegin', alertHtml);
            
            // Restaurar el botón
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="ti ti-check me-2"></i> Confirmar Pedido';
            
            // Scroll al mensaje de error
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    // Validación en tiempo real para email
    const emailField = document.getElementById('customer_email');
    if (emailField) {
        emailField.addEventListener('blur', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailRegex.test(this.value)) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    }

    // Validación en tiempo real para teléfono
    const phoneField = document.getElementById('customer_phone');
    if (phoneField) {
        phoneField.addEventListener('input', function() {
            this.value = this.value.replace(/[^\d\s\-\+\(\)]/g, '');
        });
    }
</script>
@endpush
