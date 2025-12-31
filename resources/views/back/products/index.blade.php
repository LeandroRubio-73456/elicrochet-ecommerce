@extends('layouts.back-layout')

@section('title', 'Lista de Productos')

@section('content')
    @include('layouts.breadcrumb', ['item' => 'Dashboard', 'active' => 'Lista de Productos'])

    <div>
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="m-0">Lista de Productos</h4>
            <a href="{{ route('admin.products.create') }}">
                <button type="button" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="ti-plus"></i>
                    Agregar Producto
                </button>
            </a>
        </div>

        <div class="card tbl-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="products-table" class="table table-hover table-striped w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Imagen</th>
                                <th>Nombre</th>
                                <th>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>Categoría</span>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 border-0 bg-transparent" data-bs-toggle="dropdown" aria-expanded="false" onclick="event.stopPropagation()">
                                                <i class="ti-filter text-muted cursor-pointer" style="cursor: pointer;"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item filter-option" href="#" data-column="3" data-value="">Todas</a></li>
                                                @foreach ($categories as $category)
                                                    <li><a class="dropdown-item filter-option" href="#" data-column="3" data-value="{{ $category->name }}">{{ $category->name }}</a></li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>Estado</span>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 border-0 bg-transparent" data-bs-toggle="dropdown" aria-expanded="false" onclick="event.stopPropagation()">
                                                <i class="ti-filter text-muted cursor-pointer" style="cursor: pointer;"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item filter-option" href="#" data-column="6" data-value="">Todos</a></li>
                                                <li><a class="dropdown-item filter-option" href="#" data-column="6" data-value="active">Activo</a></li>
                                                <li><a class="dropdown-item filter-option" href="#" data-column="6" data-value="draft">Borrador</a></li>
                                                <li><a class="dropdown-item filter-option" href="#" data-column="6" data-value="out_of_stock">Sin Stock</a></li>
                                                <li><a class="dropdown-item filter-option" href="#" data-column="6" data-value="discontinued">Descontinuado</a></li>
                                                <li><a class="dropdown-item filter-option" href="#" data-column="6" data-value="archived">Archivado</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- DataTables cargará los datos aquí -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="module">
        $(document).ready(function() {
            var table = $('#products-table').DataTable({
                processing: true,
                serverSide: true,
                orderCellsTop: false,
                fixedHeader: true,
                ajax: {
                    url: "{{ route('admin.products.index') }}"
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'image', name: 'images', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'category', name: 'category_id' },
                    { data: 'price', name: 'price' },
                    { data: 'stock', name: 'stock' },
                    { data: 'status', name: 'status' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: "text-center" }
                ],
                // Buscador (f) a la izquierda, Botones (B) a la derecha. length (l) opcional.
                dom: '<"row mb-3"<"col-md-4"l><"col-md-4"f><"col-md-4 text-end"B>>' +
                     '<"row"<"col-sm-12"tr>>' +
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                buttons: [
                    {
                        extend: 'collection',
                        text: '<i class="ti-download me-1"></i> Exportar',
                        className: 'btn btn-primary btn-sm',
                        buttons: [
                            {
                                extend: 'excelHtml5',
                                text: '<i class="ti-file-spreadsheet me-1"></i> Excel',
                                exportOptions: {
                                    columns: [0, 2, 3, 4, 5, 6]
                                }
                            },
                            {
                                extend: 'pdfHtml5',
                                text: '<i class="ti-file-text me-1"></i> PDF',
                                exportOptions: {
                                    columns: [0, 2, 3, 4, 5, 6]
                                }
                            }
                        ]
                    }
                ]
            });

            // Lógica para filtrar al hacer click en el dropdown item
            $('.filter-option').on('click', function(e) {
                e.preventDefault();
                var colIndex = $(this).data('column');
                var val = $(this).data('value');

                // Filtrar y redibujar
                table.column(colIndex).search(val).draw();
            });

            // Delegación de eventos para el botón de eliminar
            $('#products-table').on('click', '.delete-product-btn', function() {
                const actionUrl = $(this).data('action-url');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: '¡Eliminarás este producto PERMANENTEMENTE!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, Eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(actionUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ _method: 'DELETE' })
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('Error al eliminar');
                            return response.json();
                        })
                        .then(data => {
                            table.ajax.reload(); // Recargar DataTables
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: data.message,
                                toast: true,
                                position: 'top-end',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        })
                        .catch(error => {
                            Swal.fire('Error', 'No se pudo eliminar el producto.', 'error');
                        });
                    }
                });
            });

            // Click evento para filtrar por status (Badge Click)
            // Actualizado para buscar en la columna directamente ya que no hay selects visibles
            $('#products-table').on('click', '.status-filter-click', function() {
                var status = $(this).data('status');
                // Columna 6 es Status
                table.column(6).search(status).draw();
            });
        });
    </script>
@endpush
