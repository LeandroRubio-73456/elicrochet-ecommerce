@extends('layouts.back-layout')

@section('title', 'Gestión de Categorías')

@section('content')
    @include('layouts.breadcrumb', [
        'item' => 'Dashboard',
        'active' => 'Categorías',
    ])

    <div>
        {{-- 1. CABECERA Y BOTÓN DE CREAR --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="m-0">Lista de Categorías</h4>
            <a href="{{ route('admin.categories.create') }}">
                <button type="button" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="ti-plus"></i>
                    Agregar Categoría
                </button>
            </a>
        </div>

        <div class="card tbl-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="categories-table" class="table table-hover table-striped w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Icono</th>
                                <th>Categoría</th>
                                <th>Slug</th>
                                <th class="text-center"># Productos</th>
                                <th class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <span>Estado</span>
                                        <button class="dropdown border-0 bg-transparent p-0 d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti-filter text-muted cursor-pointer"></i>
                                        </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item filter-option" href="#" data-column="5" data-value="">Todos</a></li>
                                                <li><a class="dropdown-item filter-option" href="#" data-column="5" data-value="active">Activo</a></li>
                                                <li><a class="dropdown-item filter-option" href="#" data-column="5" data-value="inactive">Inactivo</a></li>
                                                <li><a class="dropdown-item filter-option" href="#" data-column="5" data-value="archived">Archivado</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                             <!-- DataTables -->
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
            var table = $('#categories-table').DataTable({
                processing: true,
                serverSide: true,
                orderCellsTop: false,
                fixedHeader: true,
                ajax: {
                    url: "{{ route('admin.categories.index') }}"
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'icon', name: 'icon', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'slug', name: 'slug' },
                    { data: 'products_count', name: 'products_count', searchable: false, className: "text-center" },
                    { data: 'status', name: 'status', className: "text-center" },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: "text-center" }
                ],
                // Updated DOM to include 'l' (length) as requested, adapting the Products layout
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
                                    columns: [0, 2, 3, 4, 5]
                                }
                            },
                            {
                                extend: 'pdfHtml5',
                                text: '<i class="ti-file-text me-1"></i> PDF',
                                exportOptions: {
                                    columns: [0, 2, 3, 4, 5]
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
                table.column(colIndex).search(val).draw();
            });

            // Delegación de eventos delete
            $('#categories-table').on('click', '.delete-category-btn', function() {
                const actionUrl = $(this).data('action-url');
                const categoryName = $(this).data('category-name');
                
                Swal.fire({
                    title: '¿Estás seguro?',
                    html: `¿Eliminar la categoría <strong>${categoryName}</strong>?<br><br>
                           <small class="text-danger">
                               <i class="ti-alert-circle me-1"></i>
                               Esta acción eliminará la categoría PERMANENTEMENTE.
                           </small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar categoría',
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
                            if (!response.ok) {
                                return response.json().then(data => { throw new Error(data.message); });
                            }
                            return response.json();
                        })
                        .then(data => {
                            table.ajax.reload();
                            Swal.fire({
                                icon: 'success',
                                title: '¡Categoría Eliminada!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        })
                        .catch(error => {
                            Swal.fire('Error', error.message || 'Error al eliminar.', 'error');
                        });
                    }
                });
            });

            // Filtro click en badge removed
        });
    </script>
@endpush
