@props(['product'])

<div class="product-card-modern h-100">
    <a href="{{ route('product.show', $product->slug) }}" class="product-image-wrapper">
        @if($product->images->first())
            <img src="{{ asset('storage/' . $product->images->first()->path) }}" 
                 alt="{{ $product->name }}"
                 class="product-image">
        @else
            <div class="product-placeholder">
                <svg width="48" height="48" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                    <path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/>
                </svg>
            </div>
        @endif

        <div class="position-absolute top-0 start-0 m-2">
            @if($product->is_on_sale)
                <span class="product-badge bg-danger">OFF</span>
            @elseif($product->is_new)
                <span class="product-badge bg-success">Nuevo</span>
            @endif
        </div>

        @if($product->is_featured && !$product->is_on_sale && !$product->is_new)
            <span class="product-badge">Destacado</span>
        @endif
    </a>
    <div class="product-info">
        <a href="{{ route('product.show', $product->slug) }}" class="product-name">
            {{ $product->name }}
        </a>
        <p class="product-description">{{ Str::limit($product->description, 60) }}</p>
        <div class="product-footer">
            <div class="product-price">${{ number_format($product->price, 0, ',', '.') }}</div>
            
            <form action="{{ route('cart.add', $product->slug ?? $product->id) }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn-icon" title="Añadir al carrito">
                    <i class="ti-shopping-cart"></i>
                </button>
            </form>
        </div>
    </div>
</div>
