<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
    @include('layouts.head-page-meta', ['title' => 'Hola']) @include('layouts.head-css')
</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body @bodySetup>
    @include('layouts.layout-vertical')

    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">
            @include('layouts.breadcrumb', [
                'breadcrumb-item' => 'Dashboard',
                'breadcrumb-item-active' => 'Home',
            ])
            <div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h4 class="m-0">Lista de Productos</h4>
                    <a href="products/create">
                        <button type="button" class="btn btn-primary d-flex align-items-center gap-2">
                            <i class="ti ti-plus"></i>
                            Agregar Producto
                        </button>
                    </a>
                </div>

                {{-- filtro --}}
                <div class="card mb-2">
                    <div class="card-body">
                        <form action="{{ route('back.products.index') }}" method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label fw-bold">Buscar</label>
                                <input type="text" class="form-control" id="search" name="search"
                                    placeholder="Nombre o descripción..." value="{{ request('search') }}">
                            </div>

                            <div class="col-md-3">
                                <label for="status" class="form-label fw-bold">Estado</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Todos los estados</option>
                                    @foreach (['draft', 'active', 'out_of_stock', 'discontinued', 'archived'] as $status)
                                        <option value="{{ $status }}"
                                            {{ request('status') == $status ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="category_id" class="form-label fw-bold">Categoría</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">Todas las categorías</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-filter me-1"></i> Filtrar
                                    </button>
                                    <a href="{{ route('back.products.index') }}" class="btn btn-secondary">
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
                                        <th>#</th>
                                        <th>Nombre</th>
                                        <th>Categoria</th>
                                        <th>Precio</th>
                                        <th>Stock</th>
                                        <th>Estado</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr>
                                            <td>{{ $product->id }}</td>
                                            <td>
                                                <strong>{{ $product->name }}</strong><br>
                                                <small>{{ $product->short_description }}</small>
                                            </td>
                                            <td>
                                                {{ $product->category->name ?? 'Sin categoría' }}
                                            </td>
                                            <td>${{ number_format($product->price, 2) }}</td>
                                            <td>
                                                <span
                                                    class="f-12 badge bg-light-{{ $product->stock > 10 ? 'success' : ($product->stock > 0 ? 'warning' : 'danger') }}">
                                                    {{ $product->stock }} unidades
                                                </span>
                                            </td>
                                            <td>
                                                {!! $product->status_badge !!}
                                                @if ($product->status === 'active' && $product->stock <= 0)
                                                    <span class="badge bg-danger mt-1 f-12">
                                                        <i class="ti ti-exclamation-triangle me-1"></i> Sin Stock
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2 justify-content-center">
                                                    <a href="{{ route('back.products.edit', $product) }}"
                                                        class="btn btn-outline-primary">
                                                        <i class="ti ti-edit"></i>
                                                    </a>
                                                    <a href="{{ route('back.products.show', $product) }}"
                                                        class="btn btn-outline-info">
                                                        <i class="ti ti-eye"></i>
                                                    </a>

                                                    <button type="button"
                                                        class="btn btn-outline-danger delete-product-btn"
                                                        data-product-id="{{ $product->id }}" {{-- CRÍTICO: Usamos el helper route() para generar la URL DELETE CORRECTA --}}
                                                        data-action-url="{{ route('back.products.destroy', $product) }}">
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
            </div>

        </div>
    </div>
    <!-- [ Main Content ] end -->
    @include('layouts.footer-block')

    <!-- [Page Specific JS] start -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/back/plugins/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/js/back/pages/dashboard-default.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.delete-product-btn').forEach(button => {
                button.addEventListener('click', function() {
                    // Leer la URL de la acción directamente desde el atributo data-action-url
                    const actionUrl = this.getAttribute('data-action-url');
                    const row = this.closest('tr');

                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: '¡Eliminarás este producto PERMANENTEMENTE! Esta acción es irreversible.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, ¡Eliminar Producto!',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {

                            // Usamos la URL CORRECTA obtenida del botón
                            fetch(actionUrl, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify({
                                        _method: 'DELETE' // Sobrescribimos a DELETE
                                    })
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        // Si el status code no es 200, lanza un error (esto capturará el 404 si persiste)
                                        throw new Error(
                                            'Error en la respuesta del servidor al eliminar.'
                                        );
                                    }
                                    // Si Laravel no devuelve JSON, esta línea fallará, pero el 404 es el problema principal ahora.
                                    return response.json();
                                })
                                .then(data => {
                                    row.remove();

                                    Swal.fire({
                                        icon: 'success',
                                        title: '¡Producto Eliminado!',
                                        text: data.message ||
                                            'El producto ha sido eliminado del inventario.',
                                        timer: 2000,
                                        showConfirmButton: false,
                                        toast: true,
                                        position: 'top-end'
                                    });
                                })
                                .catch(error => {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'No se pudo eliminar el producto. Verifica la consola para más detalles.'
                                    });
                                    console.error('Error de eliminación:', error);
                                });
                        }
                    });
                });
            });
        });
    </script>
    <!-- [Page Specific JS] end -->

    @include('layouts.footer-js')
</body>
<!-- [Body] end -->

</html>
