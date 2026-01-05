<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $address = $user->addresses()->first(); // Get primary address

        return view('front.account.index', compact('user', 'address'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.auth()->id(),
            'phone' => 'nullable|string|max:20',
        ]);

        auth()->user()->update($request->only('name', 'email', 'phone'));

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    public function updateAddress(Request $request)
    {
        $request->validate([
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'details' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();

        $addressData = $request->only('street', 'city', 'province', 'postal_code', 'phone', 'details');
        $addressData['address'] = $request->street; // Legacy field

        // Ensure user has an address record
        $address = $user->addresses()->first();

        if ($address) {
            $address->update($addressData);
        } else {
            $user->addresses()->create($addressData);
        }

        return back()->with('success', 'Dirección actualizada correctamente.');
    }

    public function orders(Request $request)
    {
        if ($request->ajax()) {
            $query = Order::where('user_id', auth()->id());

            $total = Order::where('user_id', auth()->id())->count();
            $filtered = $query->count();

            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $orders = $query->skip($start)->take($length)->latest()->get();

            $data = $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'total_amount' => $order->total_amount,
                    'status' => $order->status,
                    'created_at' => $order->created_at->format('d M Y, H:i'),
                    'status_badge' => $this->getStatusBadge($order),
                    'actions' => $this->getActions($order),
                ];
            });

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $total,
                'recordsFiltered' => $filtered,
                'data' => $data,
            ]);
        }

        return view('front.account.orders');
    }

    private function getStatusBadge($order)
    {
        return match ($order->status) {
            'quotation' => '<span class="badge bg-secondary">Cotización</span>',
            'in_review' => '<span class="badge bg-warning">En revisión</span>',
            'pending_payment' => '<span class="badge bg-warning">Pend. Pago</span>',
            'paid' => '<span class="badge bg-success">Pagado</span>',
            'processing' => '<span class="badge bg-primary">Trabajando/Fabricando</span>',
            'ready_to_ship' => '<span class="badge bg-info">Listo para envío</span>',
            'shipped' => '<span class="badge bg-info">Enviado</span>',
            'completed' => '<span class="badge bg-success">Completado</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelado</span>',
            default => '<span class="badge bg-light text-dark">'.$order->status.'</span>',
        };
    }

    private function getActions($order)
    {
        $actions = '';

        if (in_array($order->status, ['pending_payment', 'paid', 'quotation'])) {
            $actions .= '<form action="'.route('account.orders.cancel', $order).'" method="POST" class="d-inline" onsubmit="return confirm(\'¿Estás seguro de cancelar este pedido?\')">
                            '.csrf_field().'
                            <button type="submit" class="btn btn-sm btn-danger f-12" title="Cancelar"><i class="ti ti-x"></i></button>
                         </form>';
        }

        if ($order->status === 'shipped') {
            $actions .= '<form action="'.route('account.orders.confirm', $order).'" method="POST" class="d-inline ms-1" onsubmit="return confirm(\'¿Confirmas que recibiste el pedido?\')">
                            '.csrf_field().'
                            <button type="submit" class="btn btn-sm btn-success f-12" title="Confirmar Recepción"><i class="ti ti-check"></i> Recibido</button>
                         </form>';
        }

        return $actions;
    }

    public function cancelOrder(Order $order)
    {
        // Policy check: Ensure order belongs to user
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if (! in_array($order->status, ['pending_payment', 'paid', 'quotation'])) {
            return back()->with('error', 'No se puede cancelar este pedido porque ya está en proceso.');
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Pedido cancelado correctamente.');
    }

    public function confirmReceipt(Order $order)
    {
        // Policy check
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== 'shipped') {
            return back()->with('error', 'Solo puedes confirmar pedidos que han sido enviados.');
        }

        $order->update(['status' => 'completed']);

        return back()->with('success', '¡Gracias por confirmar! Tu pedido está completado.');
    }
}
