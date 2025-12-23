<x-mail::message>
# ¡Buenas noticias {{ $order->customer_name }}!

Hemos cotizado tu pedido personalizado #{{ $order->id }}.

**Total a Pagar:** ${{ number_format($order->total_amount, 2) }}

Ya puedes proceder al pago para comenzar con la fabricación.

<x-mail::button :url="route('customer.orders.show', $order)">
Ver Cotización y Pagar
</x-mail::button>

Si tienes alguna duda, contáctanos.

Gracias,<br>
{{ config('app.name') }}

<br>
<small style="color: #999;">
*Nota: No se aceptan devoluciones en productos personalizados una vez iniciada la fabricación.*
</small>
</x-mail::message>
