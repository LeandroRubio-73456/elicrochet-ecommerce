@extends('front.account.layout')

@section('title', 'Mis Pedidos | EliCrochet')

@section('account_content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0"><i class="ti ti-shopping-cart me-2 text-primary"></i>Historial de Pedidos</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="users-orders-table">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- jQuery and DataTables are required. Assuming they are not in front-layout by default, we load via CDN or reuse if available --}}
@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#users-orders-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('account.orders') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'created_at', name: 'created_at' },
                { data: 'status_badge', name: 'status', orderable: false, searchable: false },
                { 
                    data: 'total_amount', 
                    name: 'total_amount',
                    render: function(data, type, row) {
                        return '$' + parseFloat(data).toFixed(2);
                    }
                },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
            },
            order: [[ 0, "desc" ]] // Order by ID desc
        });
    });
</script>
@endpush
