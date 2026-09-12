<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomOrderRequest;
use App\Models\Category;
use App\Models\Order;
use App\Services\CartService;
use App\Services\CustomOrderService;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected CustomOrderService $customOrderService,
    ) {}

    /**
     * List user orders.
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->whereNot(function ($query) {
                $query->whereIn('type', [Order::TYPE_STOCK, 'stock'])
                    ->where('status', Order::STATUS_PENDING_PAYMENT);
            })
            ->with(['items.product', 'parentItem.order'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('front.account.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Policy check manual
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Hide checkout draft parent orders from customer views
        if (in_array($order->type, [Order::TYPE_STOCK, 'stock']) && $order->status === Order::STATUS_PENDING_PAYMENT) {
            return redirect()->route('cart')->with('info', 'Tienes un checkout pendiente. Revisa tu carrito para continuar.');
        }

        return view('front.account.orders.show', compact('order'));
    }

    /**
     * Cancel order logic.
     */
    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // If user tries to cancel a checkout draft parent order, just delete it.
        if (in_array($order->type, [Order::TYPE_STOCK, 'stock']) && $order->status === Order::STATUS_PENDING_PAYMENT) {
            $order->delete();

            return redirect()->route('cart')->with('success', 'Checkout cancelado. Puedes volver a intentar cuando quieras.');
        }

        if ($order->canTransitionTo(Order::STATUS_CANCELLED)) {
            // Restore stock if order was PAID or READY TO SHIP or WORKING
            if (in_array($order->status, [Order::STATUS_PAID, Order::STATUS_WORKING, Order::STATUS_READY_TO_SHIP])) {
                foreach ($order->items as $item) {
                    if ($item->product_id && $item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }
            }

            $order->status = Order::STATUS_CANCELLED;
            $order->save();

            return redirect()->back()->with('success', 'Orden cancelada exitosamente.');
        }

        return redirect()->back()->with('error', 'No se puede cancelar la orden en este estado.');
    }

    /**
     * Confirm receipt (shipped -> completed).
     */
    public function confirmReceipt(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // canTransitionTo no cubre el tipo 'catalog' con datos legacy, así
        // que se permite confirmar directamente desde SHIPPED en cualquier caso.
        if ($order->status === Order::STATUS_SHIPPED || $order->canTransitionTo(Order::STATUS_COMPLETED)) {
            $order->status = Order::STATUS_COMPLETED;
            $order->save();

            return redirect()->back()->with('success', 'Recepción confirmada. ¡Gracias por tu compra!');
        }

        return redirect()->back()->with('error', 'No se puede confirmar la orden. Estado actual: '.$order->status);
    }

    // --- Custom Order Logic ---

    public function createCustom()
    {
        $categories = Category::where('status', 'active')->get();

        return view('front.account.orders.custom_create', compact('categories'));
    }

    public function storeCustom(StoreCustomOrderRequest $request)
    {
        if ($this->customOrderService->hasActiveQuotation(Auth::user())) {
            return back()->with('error', 'Ya tienes una solicitud de cotización en proceso. Por favor espera a que sea revisada antes de enviar otra.');
        }

        $validated = $request->validated();
        $category = Category::findOrFail($validated['category_id']);

        $specErrors = $category->validateSpecs($validated['custom_specs'] ?? []);
        if (! empty($specErrors)) {
            return back()->withErrors($specErrors)->withInput();
        }

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $imagePaths[] = $file->store('custom_orders', 'public');
            }
        }

        $this->customOrderService->create(
            Auth::user(),
            $category,
            $validated['description'],
            $validated['custom_specs'] ?? [],
            $imagePaths
        );

        return redirect()->route('account.orders.index')
            ->with('success', 'Solicitud enviada. Te enviaremos una cotización pronto.');
    }

    public function addCustomToCart(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->type !== Order::TYPE_CUSTOM) {
            return redirect()->back()->with('error', 'Solo los pedidos personalizados pueden agregarse al carrito desde aquí.');
        }

        if ($order->status !== Order::STATUS_PENDING_PAYMENT) {
            return redirect()->back()->with('error', 'Este pedido no está pendiente de pago.');
        }

        try {
            $this->cartService->addCustomOrder($order);

            return redirect()->route('cart')->with('success', 'Pedido personalizado agregado al carrito.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
