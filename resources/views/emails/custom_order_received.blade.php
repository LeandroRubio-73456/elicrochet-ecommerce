<x-mail::message>
# ¡Hola {{ $order->customer_name }}!

Hemos recibido tu solicitud de pedido personalizado con éxito.

**Número de Solicitud:** #{{ $order->id }}

Nuestro equipo revisará los detalles y te enviaremos una cotización lo antes posible.

<x-mail::button :url="route('customer.orders.show', $order)">
Ver Solicitud
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}

<br>
<small style="color: #999;">
*Nota: No se aceptan devoluciones en productos personalizados una vez iniciada la fabricación.*
</small>
</x-mail::message>
