<?php

namespace App\Http\Controllers\Front;

use App\Exceptions\BusinessLogicException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Front\StoreCheckoutRequest;
use App\Models\Order;
use App\Models\User;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected CheckoutService $checkoutService,
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();

        // 1. Existing Order Flow (e.g. Custom Order Payment)
        if ($request->has('order')) {
            return $this->handleExistingOrderCheckout($request, $user);
        }

        // 2. Standard Cart Flow
        $cartItems = $this->cartService->getCart();
        $total = $this->cartService->getTotal();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart');
        }

        if ($redirect = $this->removeInvalidCustomOrders($cartItems, 'Se detectó un pedido cancelado en tu carrito y fue eliminado.')) {
            return $redirect;
        }

        return view('front.checkout', compact('cartItems', 'total', 'user'));
    }

    private function handleExistingOrderCheckout(Request $request, User $user)
    {
        Log::info("Checkout payment retry requested. Order ID: {$request->order}, User ID: {$user->id}");

        $order = Order::where('id', $request->order)
            ->where('user_id', $user->id)
            ->firstOrFail();

        Log::info("Order found. Status: '{$order->status}'");

        if ($order->status !== 'pending_payment') {
            Log::warning('Order status mismatch. Redirecting to show.');

            return redirect()->route('account.orders.show', $order->id)
                ->with('error', 'Esta orden no está pendiente de pago.');
        }

        return view('front.checkout_payment', compact('order', 'user'));
    }

    /**
     * Process payment for an EXISTING order (e.g. Custom Orders already in DB)
     */
    public function payExisting(Request $request, Order $order)
    {
        $user = $request->user();

        if ($order->user_id !== $user->id) {
            abort(403);
        }

        if ($order->status !== 'pending_payment') {
            Log::warning("payExisting: Order {$order->id} is not pending payment. Status: {$order->status}");

            return redirect()->route('account.orders.show', $order->id)->with('error', 'Orden no válida para pago.');
        }

        return $this->redirectToPayment($order, backOnError: true);
    }

    public function store(StoreCheckoutRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();
        $cartItems = $this->cartService->getCart();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'El carrito está vacío en la sesión.');
        }

        if ($redirect = $this->removeInvalidCustomOrders($cartItems, 'Un pedido personalizado en tu carrito ya no es válido o fue cancelado. Se ha eliminado automáticamente.')) {
            return $redirect;
        }

        try {
            $order = $this->checkoutService->createOrderFromCart($user, $validated, $cartItems);
        } catch (\Exception $e) {
            Log::error('Checkout Exception: '.$e->getMessage());

            return redirect()->route('checkout')->with('error', 'Error: '.$e->getMessage());
        }

        return $this->redirectToPayment($order);
    }

    /**
     * Pantalla de pago simulada: reemplaza el redirect externo a PayPhone
     * cuando no hay credenciales reales de comercio configuradas.
     */
    public function simulateGateway(Request $request)
    {
        $transactionId = (string) $request->query('transactionId', '');
        $amountCents = (int) $request->query('amount', 0);
        $fakePaymentId = random_int(100000, 999999);

        $orderId = explode('-', $transactionId)[0] ?? null;
        $order = $orderId ? Order::find($orderId) : null;

        return view('front.checkout_simulate', [
            'transactionId' => $transactionId,
            'fakePaymentId' => $fakePaymentId,
            'amount' => $amountCents / 100,
            'order' => $order,
        ]);
    }

    /**
     * Callback de PayPhone: Aquí llega el usuario tras pagar
     */
    public function callback(Request $request)
    {
        $rawOrderId = $request->query('clientTransactionId', '');

        if (empty($rawOrderId)) {
            Log::warning('PayPhone Callback received without clientTransactionId.');

            return redirect()->route('cart')->with('error', 'No se recibió la referencia de la orden.');
        }

        $payphoneId = $request->query('id');
        $orderId = explode('-', $rawOrderId)[0];

        Log::info("PayPhone Callback received. PayPhone ID: {$payphoneId}, Raw Order ID: {$rawOrderId}, Real Order ID: {$orderId}");
        Log::info('Full Callback Request: ', $request->all());

        if (! $payphoneId) {
            return redirect()->route('cart')->with('error', 'No se recibió el ID de pago.');
        }

        try {
            $order = $this->checkoutService->confirmPayment(
                (int) $payphoneId,
                (string) $rawOrderId,
                $request->query('simulated_status'),
                $orderId
            );

            return view('front.checkout-success', compact('order'));
        } catch (BusinessLogicException $e) {
            return redirect()->route('cart')->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Callback Exception: '.$e->getMessage());

            return redirect()->route('cart')->with('error', 'Error al confirmar: '.$e->getMessage());
        }
    }

    /**
     * Prepara el link de pago (real o simulado) y redirige al usuario.
     * $backOnError decide si, ante un fallo, se vuelve a la página anterior
     * (pago de una orden ya existente) o al checkout (flujo de carrito).
     */
    private function redirectToPayment(Order $order, bool $backOnError = false)
    {
        try {
            return redirect()->away($this->checkoutService->initiatePayment($order));
        } catch (\Exception $e) {
            Log::error("PayPhone Error for Order ID {$order->id}: ".$e->getMessage());

            return $backOnError
                ? back()->with('error', 'Error: '.$e->getMessage())
                : redirect()->route('checkout')->with('error', 'Error al generar link de pago: '.$e->getMessage());
        }
    }

    /**
     * Si algún item del carrito referencia un pedido personalizado que ya
     * no existe o fue cancelado, lo quita del carrito y devuelve el
     * redirect correspondiente. Devuelve null si el carrito está bien.
     */
    private function removeInvalidCustomOrders(Collection $cartItems, string $message)
    {
        foreach ($cartItems as $cartItem) {
            if (! $cartItem->custom_order_id) {
                continue;
            }

            $customOrder = Order::find($cartItem->custom_order_id);

            if (! $customOrder || $customOrder->status === 'cancelled') {
                $this->cartService->removeFromCart($cartItem->product_id ?? $cartItem->custom_order_id);

                return redirect()->route('cart')->with('error', $message);
            }
        }

        return null;
    }
}
