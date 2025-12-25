<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Order::with('user');

            // 1. Column Search (Status) - DataTables sends this via 'columns' array
            // Index 4 is Status
            if ($request->has('columns')) {
                $statusSearch = $request->input("columns.4.search.value");
                if (!empty($statusSearch)) {
                    $query->where('status', $statusSearch);
                }
            }
            
            // Also keep request->status for direct links if any
            if ($request->has('status') && $request->status != 'all' && !empty($request->status)) {
                 $query->where('status', $request->status);
            }

            // Global search (ID or Customer Name)
            if ($request->has('search') && !empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $query->where(function($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%")
                      ->orWhereHas('user', function($u) use ($search) {
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
                if(isset($cols[$colIdx]) && $cols[$colIdx] !== 'actions') {
                    $query->orderBy($cols[$colIdx], $dir);
                }
            } else {
                // Default sorting: Newest first
                $query->orderBy('id', 'desc');
            }

            $total = Order::count();
            $filtered = $query->count();
            
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $orders = $query->skip($start)->take($length)->get();

            $data = $orders->map(function($order) {
                // Status Badge (Spanish & Styled)
                 $statusHtml = match($order->status) {
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
                
                // Actions - Fixed Icon class to ti-eye (Themify) to match other views
                $showUrl = route('admin.orders.show', $order);
                $actions = '<a href="'.$showUrl.'" class="btn btn-outline-info"><i class="ti-eye"></i></a>';

                // Type Badge (Spanish)
                $typeBadge = match($order->type) {
                    'custom' => '<span class="badge bg-light-info text-info border border-info f-12">Personalizado</span>',
                    'catalog' => '<span class="badge bg-light-dark text-dark border f-12">Catálogo</span>',
                    default => '<span class="badge bg-light-primary text-primary border border-primary f-12">Stock</span>'
                };

                return [
                    'id' => $order->order_number, // Use display ID
                    'customer_name' => $order->customer_name ?? $order->user->name ?? 'Invitado',
                    'type' => $typeBadge,
                    'total' => '$' . number_format($order->total_amount, 2),
                    'status' => $statusHtml,
                    'created_at' => $order->created_at->format('d/m/Y H:i'),
                    'actions' => $actions
                ];
            });

            return response()->json([
                "draw" => intval($request->input('draw')),
                "recordsTotal" => $total,
                "recordsFiltered" => $filtered,
                "data" => $data
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
            'total_amount' => 'nullable|numeric|min:0'
        ]);

        // Logic to update total_amount if we are in quotation (or moving from it)
        if ($order->type === Order::TYPE_CUSTOM && $order->status === Order::STATUS_QUOTATION) {
             // Allow updating total_amount anytime while in quotation
             if ($request->has('total_amount')) {
                 $order->total_amount = $request->total_amount;
             }
             
            // If moving to pending_payment, ensure it's not zero
            if ($request->status === Order::STATUS_PENDING_PAYMENT) {
                if ($order->total_amount <= 0) {
                     return back()->withErrors(['total_amount' => 'El monto debe ser mayor a 0 para solicitar el pago.']);
                }
            }

            // PROPAGATE PRICE TO ITEM (Fix for showing $0.00 in item details)
            // If we are updating the total amount, we should also update the intrinsic "Custom Service" item price.
            if ($request->has('total_amount')) {
                // Find the custom item (product_id is null)
                $customItem = $order->items()->whereNull('product_id')->first();
                if ($customItem) {
                    $customItem->price = $request->total_amount;
                    $customItem->save();
                }
            }
        }

        // Check if status transitioned to pending_payment (Quotation -> Pending Payment)
        // AND total_amount is set. We assume if status changed to pending_payment, the price is final.
        if ($request->status === Order::STATUS_PENDING_PAYMENT && $order->status !== Order::STATUS_PENDING_PAYMENT) {
             // Send Price Assigned Notification
             \Illuminate\Support\Facades\Mail::to($order->customer_email)->send(new \App\Mail\PriceAssignedNotification($order));
        }

        // Check if status transitioned to shipped
        if ($request->status === Order::STATUS_SHIPPED && $order->status !== Order::STATUS_SHIPPED) {
             // Send Shipped Notification
             \Illuminate\Support\Facades\Mail::to($order->customer_email)->send(new \App\Mail\OrderShippedNotification($order));
        }

        return DB::transaction(function () use ($request, $order) {
            
            // Check for cancellation and restore stock
            if ($request->status === 'cancelled' && $order->status !== 'cancelled') {
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

            $order->status = $request->status;
            $order->save();

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Orden actualizada correctamente.');
        });
    }
}
