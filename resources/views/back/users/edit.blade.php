@extends('layouts.back-layout')

@section('title', 'Editar Usuario: ' . $user->name)

@section('content')
    @include('layouts.breadcrumb', [
        'item' => 'Usuarios',
        'active' => 'Editar: ' . $user->name,
        'item_url' => route('admin.users.index'),
        'item_icon' => 'ti ti-users',
        'active_icon' => 'ti ti-user-edit'
    ])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-4">Editar Usuario: {{ $user->name }}</h5>

                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-bold">Nombre <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" name="name" id="name" class="form-control"
                                            placeholder="Ej. Juan" value="{{ old('name', $user->name) }}" required>
                                    </div>
                                    @error('name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="lastname" class="form-label fw-bold">Apellidos <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" name="lastname" id="lastname" class="form-control"
                                            placeholder="Ej. Pérez" value="{{ old('lastname', $user->lastname) }}" required>
                                    </div>
                                    @error('lastname')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label fw-bold">Correo Electrónico <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ti ti-mail"></i></span>
                                        <input type="email" name="email" id="email" class="form-control"
                                            placeholder="juan@ejemplo.com" value="{{ old('email', $user->email) }}" required>
                                    </div>
                                    @error('email')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="role" class="form-label fw-bold">Rol de Usuario <span
                                            class="text-danger">*</span></label>
                                    <select name="role" id="role" class="form-select" required>
                                        <option value="customer"
                                            {{ old('role', $user->role) == 'customer' ? 'selected' : '' }}>Cliente
                                        </option>
                                        <option value="admin"
                                            {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrador
                                        </option>
                                    </select>
                                    <small class="text-muted">Los administradores tienen acceso completo al
                                        dashboard.</small>
                                    @error('role')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="alert alert-light-primary" role="alert">
                                    <i class="ti ti-info-circle me-2"></i> Deja los campos de contraseña vacíos si no deseas
                                    cambiarla.
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label fw-bold">Nueva Contraseña</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ti ti-lock"></i></span>
                                        <input type="password" name="password" id="password" class="form-control"
                                            placeholder="••••••••">
                                    </div>
                                    <small class="text-muted">Mínimo 8 caracteres.</small>
                                    @error('password')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label fw-bold">Confirmar Nueva
                                        Contraseña</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ti ti-lock-check"></i></span>
                                        <input type="password" name="password_confirmation" id="password_confirmation"
                                            class="form-control" placeholder="••••••••">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-x me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-2"></i>Actualizar Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

