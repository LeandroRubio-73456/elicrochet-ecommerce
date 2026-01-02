@extends('layouts.front-layout')

@section('title', 'Carrito de Compras | EliCrochet')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Tu Carrito</h1>
        

    @if($cartItems->count() > 0)
    <div class="card">
        <div class="card-body">
            @foreach($cartItems as $item)
                <div class="row mb-3 align-items-center border-bottom pb-3">
                    <div class="col-md-2">
                        @php
                            $imagePath = 'https://placehold.co/100x100?text=Sin+Imagen';
                            if ($item->product && $item->product->images->first()) {
                                $imagePath = asset('storage/' . $item->product->images->first()->image_path);
                            } elseif (isset($item->attributes['image']) && $item->attributes['image']) {
                                $imagePath = asset('storage/' . $item->attributes['image']);
                            }
                            
                            $name = $item->product ? $item->product->name : ($item->attributes['name'] ?? 'Producto Desconocido');
                            $link = $item->product ? route('product.show', $item->product->slug) : '#';
                        @endphp
                        <img src="{{ $imagePath }}" class="img-fluid rounded" alt="{{ $name }}" width="50">
                    </div>
                    <div class="col-md-6">
                        <h5 class="mb-1">
                             <a href="{{ $link }}" class="text-dark text-decoration-none">{{ $name }}</a>
                        </h5>
                        @if(isset($item->attributes['is_custom']))
                            <span class="badge bg-info text-dark mb-2">Pedido Personalizado</span>
                        @endif
                        <p class="text-success fw-bold mb-0" id="item-total-{{ $item->product_id ?? 'custom-' . $item->id }}">
                            ${{ number_format($item->subtotal, 2) }}
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        @if($item->product)
                            <div class="d-inline-flex align-items-center mb-2 mb-md-0">
                                <div class="input-group input-group-sm border rounded" style="width: auto;">
                                    <button type="button" class="btn btn-light btn-sm" onclick="updateQuantity({{ $item->product_id }}, -1)">
                                        <i class="ti ti-minus"></i>
                                    </button>
                                    <input type="text" id="quantity-{{ $item->product_id }}" value="{{ $item->quantity }}" class="form-control text-center border-0" min="1" max="{{ $item->product->stock }}" readonly style="width: 50px;">
                                    <button type="button" class="btn btn-light btn-sm" onclick="updateQuantity({{ $item->product_id }}, 1)">
                                        <i class="ti ti-plus"></i>
                                    </button>
                                </div>
                            </div>
                        @else
                            <span class="text-muted d-block mb-2">1 (Fijo)</span>
                        @endif

                        <form action="{{ route('cart.remove', $item->product_id ?? $item->id) }}" method="POST" class="d-inline ms-3">
                            <!-- Note: ensure Controller handles removing by ID if product_id is null, or we need a different route.
                                 Wait, CartController::remove($productId). If I pass item ID it might fail if it looks for product_id.
                                 Let's check controller logic. CartService::removeFromCart uses product_id.
                                 If Custom Order, product_id is null.
                                 I need to update CartService/Controller to remove by ID or handle Custom Order removal.
                                 Let's assume for this step I fix layout, but I might need to fix removal next.
                             -->
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar producto">
                                <i class="ti ti-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
            <div class="text-end mt-4">
                <h4>Total: <span class="text-success" id="cart-total">${{ number_format($cartTotal, 2) }}</span></h4>
                <div class="mt-3">
                     <a href="{{ route('shop') }}" class="btn btn-outline-primary me-2">Seguir Comprando</a>
                     <a href="{{ route('checkout') }}" class="btn btn-primary pulse-button">Proceder al Pago</a>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    function updateQuantity(productId, change) {
    let input = document.getElementById('quantity-' + productId);
    let maxStock = parseInt(input.getAttribute('max'));
    let newQuantity = parseInt(input.value) + change;
    
    if (newQuantity < 1) return;
    if (newQuantity > maxStock) {
        return;
    }
    input.value = newQuantity;
    
    fetch('{{ route("cart.update") }}', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: newQuantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            document.getElementById('cart-total').innerText = '$' + data.cartTotal;
            document.getElementById('item-total-' + productId).innerText = '$' + data.itemTotal;
            
            // Actualizar badge del carrito
            updateCartBadge(data.cartCount);
        }
    });
}

function updateCartBadge(count) {
    let cartBtn = document.getElementById('navbar-cart-btn');
    if(cartBtn) {
        let badge = cartBtn.querySelector('.badge');
        if (count > 0) {
            if(badge) {
                badge.innerText = count;
            } else {
                cartBtn.innerHTML += `<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">${count}</span>`;
            }
        } else if (badge) {
            badge.remove();
        }
    }
}
</script>
@endpush
    @else
        <div class="text-center py-5">
            <i class="ti ti-shopping-cart text-muted mb-3" style="font-size: 3rem;"></i>
            <h3>Tu carrito está vacío</h3>
            <p class="text-muted">¡Agrega algunos productos hermosos!</p>
            <a href="{{ route('shop') }}" class="btn btn-primary mt-3">Ir a la Tienda</a>
        </div>
    @endif
</div>
@endsection
