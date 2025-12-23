@extends('layouts.back-layout')

@section('title', 'Crear Usuario')

@section('content')
    @include('layouts.breadcrumb', [
        'item' => 'Usuarios',
        'active' => 'Crear Nuevo',
    ])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-4">Añadir Nuevo Usuario</h5>

                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-bold">Nombre Completo <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        placeholder="Ej. Juan Pérez" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label fw-bold">Correo Electrónico <span
                                            class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control"
                                        placeholder="juan@ejemplo.com" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="role" class="form-label fw-bold">Rol de Usuario <span
                                            class="text-danger">*</span></label>
                                    <select name="role" id="role" class="form-select" required>
                                        <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Cliente
                                        </option>
                                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                                            Administrador</option>
                                    </select>
                                    <small class="text-muted">Los administradores tienen acceso completo al
                                        dashboard.</small>
                                    @error('role')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label fw-bold">Contraseña <span
                                            class="text-danger">*</span></label>
                                    <input type="password" name="password" id="password" class="form-control"
                                        placeholder="••••••••" required>
                                    <small class="text-muted">Mínimo 8 caracteres.</small>
                                    @error('password')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label fw-bold">Confirmar Contraseña <span
                                            class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control" placeholder="••••••••" required>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                <i class="ti-x me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti-device-floppy me-2"></i>Guardar Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

