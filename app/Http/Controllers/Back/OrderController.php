<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Order::with(['user', 'address']);

            $this->applyFilters($query, $request);

            // 4. Paginación
            $totalRecords = Order::count();
            $filteredRecords = $query->count();
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            $orders = $query->skip($start)->take($length)->get();

            // 5. Transformación
            $data = $orders->map(fn ($order) => $this->transformOrder($order));

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data,
            ]);
        }

        return view('back.orders.index');
    }

    public function show(Order $order)
    {
        $order->load(['user', 'address', 'items.product']);

        return view('back.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|max:20',
        ]);

        // Special validation check: When moving to 'pending_payment', total_amount is required for custom orders (quotations)
        if ($request->status === 'pending_payment' && $order->type === 'custom') {
            $request->validate([
                'total_amount' => 'required|numeric|min:0.01',
            ], [
                'total_amount.required' => 'Debe ingresar el valor total de la cotización antes de solicitar el pago.',
            ]);
        }

        // Logic: Client/Admin strict cancellation
        if ($request->status === 'cancelled' && $order->status === 'processing') {
            return back()->with('error', 'No se puede cancelar una orden que ya está en fabricación (Trabajando).');
        }

        $data = [
            'status' => $request->status,
        ];

        // Update amount if present and we are in quotation phase
        if ($request->has('total_amount') && $order->type === 'custom') {
            $data['total_amount'] = $request->total_amount;
        }

        $order->update($data);

        return back()->with('success', 'Estado de la orden actualizado correctamente.');

    }

    private function applyFilters($query, Request $request)
    {
        // 1. Busqueda Global
        if ($request->has('search') && ! empty($request->input('search.value'))) {
            $searchValue = $request->input('search.value');
            $query->where(function ($q) use ($searchValue) {
                $q->where('customer_name', 'like', "%{$searchValue}%")
                    ->orWhere('customer_email', 'like', "%{$searchValue}%")
                    ->orWhere('id', 'like', "%{$searchValue}%");
            });
        }

        // 2. Filtro por Estado (Columna 3)
        if ($request->has('columns')) {
            $statusSearch = $request->input('columns.3.search.value');
            if (! empty($statusSearch)) {
                $query->where('status', $statusSearch);
            }
        }

        // 3. Ordenamiento
        if ($request->has('order')) {
            $orderColumnIndex = $request->input('order.0.column');
            $orderDirection = $request->input('order.0.dir');
            // Mapeo visual de columnas a base de datos
            $columns = ['id', 'customer_name', 'total_amount', 'status', 'created_at', 'actions'];

            if (isset($columns[$orderColumnIndex]) && $columns[$orderColumnIndex] !== 'actions') {
                $query->orderBy($columns[$orderColumnIndex], $orderDirection);
            }
        } else {
            $query->latest();
        }
    }

    private function transformOrder($order)
    {
        // Cliente
        $customer = '
            <div class="d-flex flex-column">
                <span class="fw-bold">'.$order->customer_name.'</span>
                <small class="text-muted">'.$order->customer_email.'</small>
            </div>';

        // Tipo
        $typeBadge = match ($order->type) {
            'custom' => '<span class="badge bg-light-primary f-12">Personalizado</span>',
            'standard' => '<span class="badge bg-light-secondary f-12">Estándar</span>',
            default => '<span class="badge bg-light-secondary f-12">'.$order->type.'</span>',
        };

        // Estado
        $statusBadge = match ($order->status) {
            'quotation' => '<span class="badge f-12 bg-light-info">Cotización</span>',
            'in_review' => '<span class="badge f-12 bg-light-warning">En revisión</span>',
            'processing' => '<span class="badge f-12 bg-light-info">Trabajando</span>',
            'shipped' => '<span class="badge f-12 bg-light-primary">Enviado</span>',
            'completed' => '<span class="badge f-12 bg-light-success">Completado</span>',
            'cancelled' => '<span class="badge f-12 bg-light-danger">Cancelado</span>',
            'pending_payment' => '<span class="badge f-12 bg-light-secondary">Pendiente de Pago</span>',
            'paid' => '<span class="badge f-12 bg-light-success">Pagado</span>',
            default => '<span class="badge f-12 bg-light-secondary">'.$order->status.'</span>',
        };

        // Acciones
        $viewUrl = route('admin.back.orders.show', $order);
        $actions = '<a href="'.$viewUrl.'" class="btn btn-outline-info"><i class="ti-eye"></i></a>';

        return [
            'id' => '#ORD-'.str_pad($order->id, 5, '0', STR_PAD_LEFT),
            'id' => '#ORD-'.str_pad($order->id, 5, '0', STR_PAD_LEFT),
            'customer_name' => $customer,
            'type' => $typeBadge,
            'total' => '$'.number_format($order->total_amount, 2),
            'status' => $statusBadge,
            'created_at' => $order->created_at->format('d M Y, H:i'),
            'actions' => $actions,
        ];
    }
}
