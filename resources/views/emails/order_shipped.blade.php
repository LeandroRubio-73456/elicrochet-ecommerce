<x-mail::message>
# ¡Tu pedido está en camino!

Hola {{ $order->customer_name }}, tu pedido #{{ $order->id }} ha sido enviado.

Pronto recibirás tus productos en la dirección registrada.

<x-mail::button :url="route('customer.orders.show', $order)">
Rastrear Pedido
</x-mail::button>

Gracias por confiar en nosotros,<br>
{{ config('app.name') }}

<br>
<small style="color: #999;">
*Nota: No se aceptan devoluciones en productos personalizados.*
</small>
</x-mail::message>
