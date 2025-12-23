@extends('customer.layout')

@section('title', 'Mi Perfil | EliCrochet')

@section('customer_content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom-0 py-3">
        <h5 class="card-title mb-0"><i class="ti ti-user-edit me-2 text-primary"></i>Datos Personales y Envío</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('customer.profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <h6 class="mb-3 text-muted border-bottom pb-2">Información Personal</h6>
            <div class="row g-3 mb-4">
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

            <h6 class="mb-3 text-muted border-bottom pb-2">Dirección de Envío Predeterminada</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                     <label class="form-label">Provincia / Estado</label>
                    <input type="text" name="shipping_province" class="form-control" value="{{ old('shipping_province', $address->province ?? $user->shipping_state ?? '') }}" placeholder="Ej: Pichincha">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ciudad</label>
                    <input type="text" name="shipping_city" class="form-control" value="{{ old('shipping_city', $address->city ?? $user->shipping_city ?? '') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Código Postal / Zona</label>
                    <input type="text" name="shipping_zip" class="form-control" value="{{ old('shipping_zip', $address->postal_code ?? $user->shipping_zip ?? '') }}" placeholder="Ej: 170504">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Dirección / Calle Principal</label>
                    <input type="text" name="shipping_address" class="form-control" value="{{ old('shipping_address', $address->street ?? $user->shipping_address ?? '') }}" placeholder="Ej: Av. Amazonas N35-12 y Corea" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Referencia (Opcional)</label>
                    <input type="text" name="shipping_reference" class="form-control" value="{{ old('shipping_reference', $address->reference ?? '') }}" placeholder="Ej: Junto a la farmacia azul">
                </div>
            </div>

            <h6 class="mb-3 text-muted border-bottom pb-2">Cambiar Contraseña <small class="text-muted fw-normal f-12">(Opcional)</small></h6>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Nueva Contraseña</label>
                    <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para mantener actual">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>
            
            <div class="text-end">
                <button type="submit" class="btn btn-primary px-4"><i class="ti ti-device-floppy me-1"></i> Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
@endsection
