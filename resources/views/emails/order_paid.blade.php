<x-mail::message>
# ¡Pago Confirmado!

Hola {{ $order->customer_name }}, hemos recibido tu pago correctamente.

**Pedido #{{ $order->id }}**
**Monto:** ${{ number_format($order->total_amount, 2) }}

**Dirección de Envío Confirmada:**
{{ $order->shipping_address }}
{{ $order->shipping_city }}, {{ $order->shipping_province }}
CP: {{ $order->shipping_zip }}

<x-mail::button :url="route('customer.orders.show', $order)">
Ver Detalles del Pedido
</x-mail::button>

Gracias por tu compra,<br>
{{ config('app.name') }}

<br>
<small style="color: #999;">
*Nota: No se aceptan devoluciones en productos personalizados.*
</small>
</x-mail::message>
