@extends('layouts.front-layout')

@section('title', 'Pedido Personalizado')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="text-center mb-4 fw-bold text-primary"><i class="ti ti-wand me-2"></i>Haz realidad tu idea</h2>
                    
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <i class="ti ti-info-circle fs-4 me-2"></i>
                        <div>
                            <strong>Nota Importante:</strong> Esta es una <strong>solicitud de cotización</strong>. 
                            Una vez enviada, revisaremos los detalles y te contactaremos con el precio final y el tiempo estimado de entrega.
                        </div>
                    </div>

                    <form action="{{ route('custom-order.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Datos del Cliente -->
                        <h5 class="mb-3 mt-4 border-bottom pb-2">Datos de Contacto</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre Completo</label>
                                <input type="text" name="customer_name" class="form-control" value="{{ auth()->user()->name ?? old('customer_name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="customer_email" class="form-control" value="{{ auth()->user()->email ?? old('customer_email') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="customer_phone" class="form-control" value="{{ old('customer_phone') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha deseada de entrega (Opcional)</label>
                                <input type="date" name="suggested_date" class="form-control" min="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <!-- Detalles del Pedido -->
                        <h5 class="mb-3 mt-4 border-bottom pb-2">Detalles del Diseño</h5>
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción detallada</label>
                            <textarea name="description" class="form-control" rows="5" placeholder="Describe tu idea: colores, tamaño, tipo de tejido, para quién es, etc..." required>{{ old('description') }}</textarea>
                            <div class="form-text">Sé lo más específico posible para darte una cotización exacta.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Imágenes de referencia (Opcional)</label>
                            <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                            <div class="form-text">Puedes subir fotos, bocetos o ejemplos de internet.</div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fs-5">
                            <i class="ti ti-send me-2"></i> Enviar Solicitud
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
