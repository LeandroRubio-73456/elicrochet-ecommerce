@extends('layouts.back-layout')

@section('title', 'Añadir Categoría')

@section('content')
    @include('layouts.breadcrumb', [
        'item' => 'Categorías',
        'active' => 'Crear Nueva',
    ])
    <h4 class="m-0 mb-3">Añadir una nueva Categoría</h4>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">

                    <form method="POST" action="{{ route('admin.categories.store') }}" id="categoryForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-bold">Nombre de la Categoría
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        placeholder="Ej. Amigurumis" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="slug" class="form-label fw-bold">URL amigable (slug)</label>
                                    <input type="text" name="slug" id="slug" class="form-control"
                                        placeholder="Ej. amigurumis" value="{{ old('slug') }}">
                                    <small class="text-muted">Si lo dejas vacío, se generará automáticamente</small>
                                    @error('slug')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Estado de la Categoría</label>
                                    <div class="row g-3">
                                        @foreach (['active' => ['Activa', 'Visible en la tienda', 'success', 'ti ti-eye'], 'inactive' => ['Inactiva', 'No visible en la tienda', 'warning', 'ti ti-power'], 'archived' => ['Archivada', 'Oculta del sistema', 'secondary', 'ti ti-archive']] as $value => [$label, $description, $color, $icon])
                                            <div class="col-md-6">
                                                <div class="form-check card-radio">
                                                    <input class="form-check-input" type="radio" name="status"
                                                        id="status_{{ $value }}" value="{{ $value }}"
                                                        {{ old('status', 'active') == $value ? 'checked' : '' }} required>
                                                    <label class="form-check-label" for="status_{{ $value }}">
                                                        <span class="mb-1 d-block fw-bold">
                                                            <i class="{{ $icon }} text-{{ $color }} me-2"></i>
                                                            {{ $label }}
                                                        </span>
                                                        <span
                                                            class="text-muted small d-block">{{ $description }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('status')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Icon Picker Component -->
                                <x-icon-picker />
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Descripción
                                <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" name="description" id="description" rows="4" required
                                placeholder="Describe esta categoría...">{{ old('description') }}</textarea>
                            <small class="text-muted">Describe los productos que pertenecen a esta categoría.</small>
                            @error('description')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Dynamic Specs Builder -->
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-primary"><i class="ti ti-settings me-2"></i>Especificaciones Dinámicas</h5>
                                <button type="button" class="btn btn-sm btn-primary" id="btnAddSpec">
                                    <i class="ti ti-plus me-1"></i> Agregar Campo
                                </button>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small">Define qué datos adicionales se deben pedir al cliente para esta categoría (ej: Talla, Color, Altura).</p>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm bg-white" id="specsTable">
                                        <thead>
                                            <tr>
                                                <th>Nombre del Campo</th>
                                                <th style="width: 150px;">Tipo</th>
                                                <th>Opciones (separadas por coma)</th>
                                                <th style="width: 100px;">Req.</th>
                                                <th style="width: 50px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Dynamic Rows -->
                                        </tbody>
                                    </table>
                                </div>
                                <input type="hidden" name="required_specs" id="requiredSpecsJson">
                            </div>
                        </div>

                        @push('scripts')
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const tableBody = document.querySelector('#specsTable tbody');
                                const btnAdd = document.getElementById('btnAddSpec');
                                const hiddenInput = document.getElementById('requiredSpecsJson');
                                const form = document.getElementById('categoryForm');

                                // Add Row
                                btnAdd.addEventListener('click', function() {
                                    const row = document.createElement('tr');
                                    row.innerHTML = `
                                        <td><input type="text" class="form-control form-control-sm spec-name" placeholder="Ej: Talla"></td>
                                        <td>
                                            <select class="form-select form-select-sm spec-type">
                                                <option value="text">Texto</option>
                                                <option value="number">Número</option>
                                                <option value="select">Selección</option>
                                            </select>
                                        </td>
                                        <td><input type="text" class="form-control form-control-sm spec-options" placeholder="Op1, Op2 (Solo para select)" disabled></td>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input class="form-check-input spec-required" type="checkbox" checked>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm p-1 remove-row"><i class="ti ti-trash"></i></button>
                                        </td>
                                    `;
                                    tableBody.appendChild(row);
                                });

                                // Event Delegation
                                tableBody.addEventListener('click', function(e) {
                                    if(e.target.closest('.remove-row')) {
                                        e.target.closest('tr').remove();
                                    }
                                });

                                tableBody.addEventListener('change', function(e) {
                                    if(e.target.classList.contains('spec-type')) {
                                        const row = e.target.closest('tr');
                                        const optionsInput = row.querySelector('.spec-options');
                                        optionsInput.disabled = e.target.value !== 'select';
                                    }
                                });

                                // On Submit, serialize to JSON
                                form.addEventListener('submit', function() {
                                    const specs = [];
                                    document.querySelectorAll('#specsTable tbody tr').forEach(row => {
                                        const name = row.querySelector('.spec-name').value.trim();
                                        if(!name) return;

                                        specs.push({
                                            name: name,
                                            type: row.querySelector('.spec-type').value,
                                            options: row.querySelector('.spec-options').value.split(',').map(s=>s.trim()).filter(Boolean),
                                            required: row.querySelector('.spec-required').checked
                                        });
                                    });
                                    hiddenInput.value = JSON.stringify(specs);
                                });
                            });
                        </script>
                        @endpush


                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-x me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-2"></i>Guardar Categoría
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
