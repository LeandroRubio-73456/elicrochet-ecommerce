<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Order::with('user');

            $this->applyFilters($query, $request);

            $total = Order::count();
            $filtered = $query->count();

            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $orders = $query->skip($start)->take($length)->get();

            $data = $orders->map(fn ($order) => $this->transformOrder($order));

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $total,
                'recordsFiltered' => $filtered,
                'data' => $data,
            ]);
        }

        return view('back.orders.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load(['items.product', 'user', 'address']);

        return view('back.orders.show', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string',
            'total_amount' => 'nullable|numeric|min:0',
        ]);

        $this->updateQuotationAmount($request, $order);
        $this->checkStatusNotifications($request, $order);

        return DB::transaction(function () use ($request, $order) {
            $this->restoreStockIfCancelled($request, $order);

            $order->status = $request->status;
            $order->save();

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Orden actualizada correctamente.');
        });
    }

    private function updateQuotationAmount(Request $request, Order $order)
    {
        if ($order->type !== Order::TYPE_CUSTOM || $order->status !== Order::STATUS_QUOTATION) {
            return;
        }

        if ($request->has('total_amount')) {
            $order->total_amount = $request->total_amount;

            // PROPAGATE PRICE TO ITEM
            $customItem = $order->items()->whereNull('product_id')->first();
            if ($customItem) {
                $customItem->price = $request->total_amount;
                $customItem->save();
            }
        }

        if ($request->status === Order::STATUS_PENDING_PAYMENT && $order->total_amount <= 0) {
            // Since we cannot return a redirect from validation here easily without throwing or refactoring significantly,
            // we will validate this before this method call or throw validation exception.
            // For now, let's just throw validation exception which Laravel handles.
            throw \Illuminate\Validation\ValidationException::withMessages([
                'total_amount' => 'El monto debe ser mayor a 0 para solicitar el pago.',
            ]);
        }
    }

    private function checkStatusNotifications(Request $request, Order $order)
    {
        $email = $order->customer_email ?? $order->user?->email;

        if (! $email) {
            return;
        }

        try {
            // Quotation -> Pending Payment
            if ($request->status === Order::STATUS_PENDING_PAYMENT && $order->status !== Order::STATUS_PENDING_PAYMENT) {
                \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\PriceAssignedNotification($order));
            }

            // -> Shipped
            if ($request->status === Order::STATUS_SHIPPED && $order->status !== Order::STATUS_SHIPPED) {
                \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\OrderShippedNotification($order));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error sending order notification email: '.$e->getMessage());
        }
    }

    private function restoreStockIfCancelled(Request $request, Order $order)
    {
        if ($request->status !== 'cancelled' || $order->status === 'cancelled') {
            return;
        }

        // Only restore if it was previously deducted
        $deductedStatuses = ['paid', 'working', 'ready_to_ship', 'shipped', 'completed'];

        if (in_array($order->status, $deductedStatuses)) {
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    $product = \App\Models\Product::lockForUpdate()->find($item->product_id);
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                }
            }
        }
    }

    private function applyFilters($query, Request $request)
    {
        // 1. Column Search (Status)
        if ($request->has('columns')) {
            $statusSearch = $request->input('columns.4.search.value');
            if (! empty($statusSearch)) {
                $query->where('status', $statusSearch);
            }
        }

        // Also keep request->status for direct links if any
        if ($request->has('status') && $request->status != 'all' && ! empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Global search (ID or Customer Name)
        if ($request->has('search') && ! empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Order (sorting)
        if ($request->has('order')) {
            $colIdx = $request->input('order.0.column');
            $dir = $request->input('order.0.dir');
            // Maps: 0->id, 1->customer, 2->type, 3->amount, 4->status
            $cols = ['id', 'customer_name', 'type', 'total_amount', 'status', 'created_at', 'actions'];
            if (isset($cols[$colIdx]) && $cols[$colIdx] !== 'actions') {
                $query->orderBy($cols[$colIdx], $dir);
            }
        } else {
            // Default sorting: Newest first
            $query->orderBy('id', 'desc');
        }
    }

    private function transformOrder($order)
    {
        // Status Badge (Spanish & Styled)
        $statusHtml = match ($order->status) {
            'quotation' => '<span class="badge bg-light-warning text-dark f-12">En Cotización</span>',
            'pending_payment' => '<span class="badge bg-light-warning text-warning f-12">Pendiente de Pago</span>',
            'paid' => '<span class="badge bg-light-success text-success f-12">Pagado</span>',
            'working' => '<span class="badge bg-light-info text-info f-12">En Fabricación</span>',
            'ready_to_ship' => '<span class="badge bg-light-primary text-primary f-12">Listo para Envío</span>',
            'shipped' => '<span class="badge bg-light-success text-success f-12">Enviado</span>',
            'completed' => '<span class="badge bg-light-success text-success f-12">Completado</span>',
            'cancelled' => '<span class="badge bg-light-danger text-danger f-12">Cancelado</span>',
            default => '<span class="badge bg-light-secondary text-secondary f-12">'.ucfirst($order->status).'</span>'
        };

        // Actions
        $showUrl = route('admin.orders.show', $order);
        $actions = '<a href="'.$showUrl.'" class="btn btn-outline-info"><i class="ti-eye"></i></a>';

        // Type Badge (Spanish)
        $typeBadge = match ($order->type) {
            'custom' => '<span class="badge bg-light-info text-info border border-info f-12">Personalizado</span>',
            'catalog' => '<span class="badge bg-light-dark text-dark border f-12">Catálogo</span>',
            default => '<span class="badge bg-light-primary text-primary border border-primary f-12">Stock</span>'
        };

        return [
            'id' => $order->order_number, // Use display ID
            'customer_name' => $order->customer_name ?? $order->user->name ?? 'Invitado',
            'type' => $typeBadge,
            'total' => '$'.number_format($order->total_amount, 2),
            'status' => $statusHtml,
            'created_at' => $order->created_at->format('d/m/Y H:i'),
            'actions' => $actions,
        ];
    }
}
