@extends('layouts.back-layout')

@section('title', 'Gestión de Usuarios')

@section('content')
    @include('layouts.breadcrumb', [
        'item' => 'Dashboard',
        'active' => 'Usuarios',
    ])

    <div>
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="m-0">Lista de Usuarios</h4>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
                <i class="ti ti-plus"></i>
                Agregar Usuario
            </a>
        </div>

        <div class="card tbl-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="users-table" class="table table-hover table-striped w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>Rol</span>
                                        <button class="dropdown border-0 bg-transparent p-0 d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" onclick="event.stopPropagation()">
                                            <i class="ti ti-filter text-muted cursor-pointer"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item filter-option" href="#" data-column="4" data-value="">Todos</a></li>
                                            <li><a class="dropdown-item filter-option" href="#" data-column="4" data-value="admin">Admin</a></li>
                                            <li><a class="dropdown-item filter-option" href="#" data-column="4" data-value="customer">Cliente</a></li>
                                        </ul>
                                    </div>
                                </th>
                                <th>Fecha Registro</th>
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
    <script src="{{ asset('assets/js/libs/sweetalert2.all.min.js') }}"></script>
    <script type="module">
        $(document).ready(function() {
            var table = $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                orderCellsTop: false,
                fixedHeader: true,
                ajax: {
                    url: "{{ route('admin.users.index') }}"
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'role', name: 'role' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: "text-center" }
                ],
                dom: '<"row mb-3"<"col-md-4"l><"col-md-4"f><"col-md-4 text-end"B>>' +
                     '<"row"<"col-sm-12"tr>>' +
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                buttons: [
                    {
                        extend: 'collection',
                        text: '<i class="ti ti-download me-1"></i> Exportar',
                        className: 'btn btn-primary btn-sm',
                        buttons: [
                            {
                                extend: 'excelHtml5',
                                text: '<i class="ti ti-file-spreadsheet me-1"></i> Excel',
                                exportOptions: {
                                    columns: [0, 2, 3, 4, 5]
                                }
                            },
                            {
                                extend: 'pdfHtml5',
                                text: '<i class="ti ti-file-text me-1"></i> PDF',
                                exportOptions: {
                                    columns: [0, 2, 3, 4, 5]
                                }
                            }
                        ]
                    }
                ],
                // initComplete removed
            });

            // Lógica para filtrar al hacer click en el dropdown item
            $('.filter-option').on('click', function(e) {
                e.preventDefault();
                var colIndex = $(this).data('column');
                var val = $(this).data('value');
                table.column(colIndex).search(val).draw();
            });

            // Delete Event
            $('#users-table').on('click', '.delete-user-btn', function() {
                const actionUrl = $(this).data('action-url');
                const userName = $(this).data('user-name');
                const row = $(this).closest('tr');

                Swal.fire({
                    title: '¿Estás seguro?',
                    html: `Eliminarás al usuario <strong>${userName}</strong> permanentemente.`,
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
                            if (!response.ok) return response.json().then(data => { throw new Error(data.message) });
                            return response.json();
                        })
                        .then(data => {
                            table.ajax.reload();
                            Swal.fire('Eliminado', data.message, 'success');
                        })
                        .catch(error => {
                            Swal.fire('Error', error.message || 'Error al eliminar', 'error');
                        });
                    }
                });
            });

            // Filter Role Click removed

            // Show toast for session messages
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: '{{ session('success') }}',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            @endif
        });
    </script>
@endpush
