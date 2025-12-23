@extends('front.account.layout')

@section('title', 'Mi Perfil | EliCrochet')

@section('account_content')
<div class="row">
    <!-- Profile Card -->
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="card-title mb-0"><i class="ti ti-user-circle me-2 text-primary"></i>Datos Personales</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('account.update-profile') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="Ej: 0991234567">
                        </div>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i> Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Address Card -->
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="card-title mb-0"><i class="ti ti-map-pin me-2 text-primary"></i>Dirección de Envío Predeterminada</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('account.update-address') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Calle Principal y Número</label>
                            <input type="text" name="street" class="form-control" value="{{ old('street', $address->street ?? '') }}" required placeholder="Ej: Av. Amazonas y Shyris">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Detalles / Referencia</label>
                            <input type="text" name="details" class="form-control" value="{{ old('details', $address->details ?? '') }}" placeholder="Ej: Edificio Blanco, Piso 2">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ciudad</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', $address->city ?? '') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Provincia</label>
                            <input type="text" name="province" class="form-control" value="{{ old('province', $address->province ?? '') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Código Postal</label>
                            <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $address->postal_code ?? '') }}" required>
                        </div>
                        <!-- Use user phone as default if address phone is empty -->
                        <div class="col-md-6">
                             <label class="form-label">Teléfono de Contacto para Envío</label>
                             <input type="text" name="phone" class="form-control" value="{{ old('phone', $address->phone ?? ($user->phone ?? '')) }}" required>
                        </div>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary"><i class="ti ti-map-pin me-1"></i> Actualizar Dirección</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
