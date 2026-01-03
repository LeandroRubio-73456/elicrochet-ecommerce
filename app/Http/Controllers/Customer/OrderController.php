<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * List user orders.
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with('items.product')
            ->orderBy('id', 'desc') // Explicitly enforce ID desc
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Policy check manual
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('customer.orders.show', compact('order'));
    }

    /**
     * Cancel order logic.
     */
    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
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

        // Fix: Explicitly check for STATUS_SHIPPED to allow confirmation even if canTransitionTo fails (e.g. type mismatch)
        if ($order->status === Order::STATUS_SHIPPED || $order->canTransitionTo(Order::STATUS_COMPLETED)) {
            $order->status = Order::STATUS_COMPLETED;
            $order->save();

            return redirect()->back()->with('success', 'Recepción confirmada. ¡Gracias por tu compra!');
        }

        return redirect()->back()->with('error', 'No se puede confirmar la orden. Estado actual: ' . $order->status);
    }

    // --- Custom Order Logic ---

    public function createCustom()
    {
        $categories = \App\Models\Category::where('status', 'active')->get();

        return view('customer.orders.custom_create', compact('categories'));
    }

    public function storeCustom(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|max:1000',
            'images.*' => 'image|max:2048',
            'custom_specs' => 'array', // Validation logic below
        ]);

        $category = \App\Models\Category::findOrFail($request->category_id);

        // 1. Dynamic Validation
        $specErrors = $category->validateSpecs($request->custom_specs ?? []);
        if (! empty($specErrors)) {
            return back()->withErrors($specErrors)->withInput();
        }

        $order = new Order;
        $order->user_id = Auth::id();
        $order->status = Order::STATUS_QUOTATION;
        $order->type = Order::TYPE_CUSTOM;
        $order->customer_name = Auth::user()->name;
        $order->customer_email = Auth::user()->email;

        // Use user's default shipping info
        $order->shipping_address = Auth::user()->shipping_address;
        $order->shipping_city = Auth::user()->shipping_city;
        $order->shipping_zip = Auth::user()->shipping_zip;

        $order->total_amount = 0; // TBD by Admin
        $order->save();

        // Create Item
        $item = new OrderItem;
        $item->order_id = $order->id;
        $item->product_id = null; // Custom
        // Store category info maybe in custom_description or fetch via relation if we had one.
        // Ideally we should link custom order to category, but specs are enough context.
        $item->custom_description = "Categoría: {$category->name}\n\n".$request->description;
        $item->price = 0;
        $item->quantity = 1;
        $item->custom_specs = $request->custom_specs; // Save JSON

        // Handle images
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $imagePaths[] = $file->store('custom_orders', 'public');
            }
        }
        $item->images = $imagePaths;

        $item->images = $imagePaths;

        $item->save();

        // Send Email
        try {
            \Illuminate\Support\Facades\Mail::to($order->customer_email)->send(new \App\Mail\CustomOrderReceived($order));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error sending CustomOrderReceived email: '.$e->getMessage());
        }

        // Notify Admin
        try {
            $admin = \App\Models\User::where('role', 'admin')->first();
            if ($admin) {
                \Illuminate\Support\Facades\Log::info("Found admin for notification: {$admin->email}");
                sleep(11); // Increased to 11s based on user plan
                \Illuminate\Support\Facades\Mail::to($admin->email)->send(new \App\Mail\NewOrderAdminNotification($order));
                \Illuminate\Support\Facades\Log::info("Admin notification sent to {$admin->email}");
            } else {
                \Illuminate\Support\Facades\Log::warning('No admin user found to send notification.');
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error sending NewOrderAdminNotification: '.$e->getMessage());
        }

        return redirect()->route('customer.orders.index')
            ->with('success', 'Solicitud enviada. Te enviaremos una cotización pronto.');
    }

    public function addCustomToCart(Order $order, \App\Providers\CartService $cartService)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== Order::STATUS_PENDING_PAYMENT) {
            return redirect()->back()->with('error', 'Este pedido no está pendiente de pago.');
        }

        try {
            $cartService->addCustomOrder($order);

            return redirect()->route('cart')->with('success', 'Pedido personalizado agregado al carrito.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
