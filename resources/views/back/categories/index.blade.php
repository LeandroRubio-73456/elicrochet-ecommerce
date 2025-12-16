<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.head-page-meta', ['title' => 'Gestión de Categorías'])
    @include('layouts.head-css')
</head>

<body @bodySetup>
    @include('layouts.layout-vertical')

    <div class="pc-container">
        <div class="pc-content">
            @include('layouts.breadcrumb', [
                'breadcrumb-item' => 'Dashboard',
                'breadcrumb-item-active' => 'Categorías',
            ])
            <div>
                {{-- 1. CABECERA Y BOTÓN DE CREAR (CORREGIDO) --}}
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h4 class="m-0">Lista de Categorías</h4>
                    <a href="{{ route('back.categories.create') }}">
                        <button type="button" class="btn btn-primary d-flex align-items-center gap-2">
                            <i class="ti ti-plus"></i>
                            Agregar Categoría
                        </button>
                    </a>
                </div>

                {{-- 2. BLOQUE DE FILTROS (DISEÑO LIMPIO) --}}
                {{-- Nota: Ajustado para usar lógica de categorías si existiera. --}}
                <div class="card mb-3">
                    <div class="card-body">
                        {{-- Asumo que la ruta es back.categories.index para filtrar --}}
                        <form action="{{ route('back.categories.index') }}" method="GET"
                            class="row g-3 align-items-end">

                            {{-- Columna de Búsqueda --}}
                            <div class="col-md-4 col-sm-6">
                                <label for="search" class="form-label fw-bold">Buscar</label>
                                <input type="text" class="form-control" id="search" name="search"
                                    placeholder="Nombre de categoría..." value="{{ request('search') }}">
                            </div>

                            {{-- Columna de Estado (Asumiendo que tienes un filtro de estado) --}}
                            <div class="col-md-4 col-sm-6">
                                <label for="status" class="form-label fw-bold">Estado</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Todos los estados</option>
                                    @foreach (['active', 'inactive', 'archived'] as $status)
                                        <option value="{{ $status }}"
                                            {{ request('status') == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-filter me-1"></i> Filtrar
                                    </button>
                                    <a href="{{ route('back.categories.index') }}" class="btn btn-secondary">
                                        <i class="ti ti-brush me-1"></i> Limpiar
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card tbl-card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Categoría</th>
                                        <th class="text-center"># Productos</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories as $category)
                                        <tr>
                                            <td class="text-end">{{ $category->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="col-auto pe-0 me-2">
                                                        @if ($category->image_path)
                                                            <img src="{{ asset('storage/' . $category->image_path) }}"
                                                                alt="{{ $category->name }}"
                                                                class="wid-40 rounded-circle">
                                                        @else
                                                            <div
                                                                class="wid-40 rounded-circle bg-light d-flex align-items-center justify-content-center">
                                                                <i class="ti ti-category f-18 text-muted"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $category->name }}</h6>
                                                        @if ($category->parent)
                                                            <small class="text-muted f-12">
                                                                <i class="ti ti-arrow-up-right me-1"></i>
                                                                Subcategoría de: {{ $category->parent->name }}
                                                            </small>
                                                        @endif
                                                        <p class="text-muted f-12 mb-0">
                                                            {{ Str::limit($category->description, 80) }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light-primary f-12">
                                                    {{ $category->products_count ?? 0 }} productos
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                {!! $category->status_badge !!}

                                            </td>

                                            {{-- ACCIONES: Solo Editar y Eliminar (Botones más visuales) --}}
                                            <td class="text-center">
                                                <div class="d-flex gap-2 justify-content-center">
                                                    {{-- Botón de Editar --}}
                                                    <a href="{{ route('back.categories.edit', $category) }}"
                                                        class="btn btn-outline-primary">
                                                        <i class="ti ti-edit"></i>
                                                    </a>

                                                    {{-- Botón de Eliminar (SweetAlert2) --}}
                                                    <button type="button"
                                                        class="btn btn-outline-danger delete-category-btn"
                                                        data-category-id="{{ $category->id }}"
                                                        data-category-name="{{ $category->name }}"
                                                        data-action-url="{{ route('back.categories.destroy', $category) }}">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {{-- ... (Paginación si la tienes, etc.) ... --}}
            </div>
        </div>
    </div>
    @include('layouts.footer-block')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/back/plugins/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/js/back/pages/dashboard-default.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Eliminar categoría con SweetAlert
            document.querySelectorAll('.delete-category-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const actionUrl = this.getAttribute('data-action-url');
                    const categoryName = this.getAttribute('data-category-name');
                    const row = this.closest('tr'); // Obtenemos la fila de la tabla

                    Swal.fire({
                        title: '¿Estás seguro?',
                        html: `¿Eliminar la categoría <strong>"${categoryName}"</strong>?<br><br>
                               <small class="text-danger">
                                   <i class="ti ti-alert-circle me-1"></i>
                                   Esta acción eliminará la categoría PERMANENTEMENTE.
                               </small>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, eliminar categoría',
                        cancelButtonText: 'Cancelar',
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return fetch(actionUrl, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify({
                                        _method: 'DELETE'
                                    })
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        // Si el código de estado es 4xx o 5xx
                                        return response.json().then(data => {
                                            throw new Error(data.message ||
                                                'Error al eliminar la categoría'
                                                );
                                        }).catch(() => {
                                            // Fallback si no devuelve JSON
                                            throw new Error(
                                                'Error de servidor o conexión.'
                                                );
                                        });
                                    }
                                    return response.json();
                                })
                                .catch(error => {
                                    Swal.showValidationMessage(
                                        `Error: ${error.message}`
                                    );
                                    // Detenemos el proceso para que el usuario vea el error
                                    return false;
                                });
                        }
                    }).then((result) => {
                        if (result.isConfirmed && result.value) {
                            // Si la eliminación fue exitosa
                            row.remove();

                            Swal.fire({
                                icon: 'success',
                                title: '¡Categoría Eliminada!',
                                text: result.value.message ||
                                    `La categoría "${categoryName}" ha sido eliminada.`,
                                timer: 2000,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        }
                    });
                });
            });
        });
    </script>
    @include('layouts.footer-js')
</body>

</html>
