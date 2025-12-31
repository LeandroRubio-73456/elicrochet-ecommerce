@extends('layouts.back-layout')

@section('title', 'Gestión de Órdenes')

@section('content')
@include('layouts.breadcrumb', ['item' => 'Dashboard', 'active' => 'Lista de Órdenes'])
<div class="row">
    <div class="col-12">
        <h4 class="mb-3">Lista de Órdenes</h4>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="orders-table" class="table table-hover table-striped w-100">
                        <thead>
                            <tr>
                                <th>Orden #</th>
                                <th>Cliente</th>
                                <th>Tipo</th>
                                <th>Total</th>
                                <th>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>Estado</span>
                                        <div class="dropdown" onclick="event.stopPropagation()" role="button" tabindex="0">
                                            <i class="ti-filter text-muted cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;"></i>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item filter-option" href="#" data-column="4" data-value="">Todos</a></li>
                                                <li><a class="dropdown-item filter-option" href="#" data-column="4" data-value="quotation">En Cotización</a></li>
                                                <li><a class="dropdown-item filter-option" href="#" data-column="4" data-value="pending_payment">Pendiente Pago</a></li>
                                                <li><a class="dropdown-item filter-option" href="#" data-column="4" data-value="paid">Pagado</a></li>
                                                <li><a class="dropdown-item filter-option" href="#" data-column="4" data-value="working">En Fabricación</a></li>
                                                <li><a class="dropdown-item filter-option" href="#" data-column="4" data-value="ready_to_ship">Listo para Envio</a></li>
                                                <li><a class="dropdown-item filter-option" href="#" data-column="4" data-value="shipped">Enviado</a></li>
                                                <li><a class="dropdown-item filter-option" href="#" data-column="4" data-value="completed">Completado</a></li>
                                                <li><a class="dropdown-item filter-option" href="#" data-column="4" data-value="cancelled">Cancelado</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </th>
                                <th>Fecha</th>
                                <th>Acciones</th>
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
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="module">
        $(document).ready(function() {
            var table = $('#orders-table').DataTable({
                processing: true,
                serverSide: true,
                orderCellsTop: false,
                fixedHeader: true,
                ajax: {
                    url: "{{ route('admin.orders.index') }}"
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'customer_name', name: 'customer_name' },
                    { data: 'type', name: 'type' },
                    { data: 'total', name: 'total_amount' },
                    { data: 'status', name: 'status' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[4, 'desc']], 
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
                                text: '<i class="ti-file me-1"></i> Excel',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4]
                                }
                            },
                            {
                                extend: 'pdfHtml5',
                                text: '<i class="ti-file me-1"></i> PDF',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4]
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

            // Filtro click en badge removed
        });
    </script>
@endpush
