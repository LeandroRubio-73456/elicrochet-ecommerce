<x-mail::message>
# ¡Nuevo Pedido Recibido!

Hola Admin,

Has recibido un nuevo pedido en la plataforma.

**Detalles del Pedido:**
- **ID:** #{{ $order->id }}
- **Cliente:** {{ $order->customer_name }} ({{ $order->customer_email }})
- **Fecha:** {{ $order->created_at->format('d/m/Y H:i') }}
- **Tipo:** {{ $order->type === 'custom' ? 'Personalizado (Cotización)' : 'Stock' }}
- **Estado:** {{ ucfirst($order->status) }}

@if($order->type !== 'custom')
**Total:** ${{ number_format($order->total_amount, 2) }}
@endif

<x-mail::button :url="route('admin.orders.show', $order->id)">
Ver Pedido en Panel
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
