@props(['product'])

<div class="card product-card h-100 border-0 shadow-sm hover-lift">
    <div class="product-badges position-absolute top-0 start-0 m-3 z-1">
        @if($product->is_on_sale)
            <span class="badge bg-danger">🔥 {{ $product->discount_percentage ?? '10%' }} OFF</span>
        @endif
        @if($product->is_new)
            <span class="badge bg-gradient-purple">✨ Nuevo</span>
        @endif
        @if($product->is_bestseller)
            <span class="badge bg-warning text-dark">⭐ Top</span>
        @endif
    </div>
    
    <div class="product-image position-relative overflow-hidden">
        @php
            $mainImage = $product->images->first();
            // Fallback safe: si no hay imagen, usar placeholder
            $imagePath = $mainImage ? asset('storage/' . $mainImage->image_path) : 'https://placehold.co/300x300?text=No+Image';
        @endphp
        <img src="{{ $imagePath }}" alt="{{ $product->name }}" class="card-img-top" style="height: 250px; object-fit: cover; transition: transform 0.3s ease;">
        
        <div class="product-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-25 opacity-0 hover-opacity-100 transition-all">
            <a href="{{ route('product.show', $product->slug) }}" class="btn btn-light btn-sm rounded-circle mx-1" title="Ver Detalle">
                <i class="bi bi-eye"></i>
            </a>
            <form action="{{ route('cart.add', $product->slug ?? $product->id) }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn btn-primary btn-sm rounded-circle mx-1" title="Añadir al Carrito">
                    <i class="bi bi-cart-plus"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="card-body d-flex flex-column p-4">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <small class="text-muted text-uppercase" style="font-size: 0.7rem;">{{ $product->category->name ?? 'General' }}</small>
            <div class="rating text-warning" style="font-size: 0.8rem;">
                <i class="bi bi-star-fill"></i>
                <span class="text-muted ms-1">4.5</span>
            </div>
        </div>

        <h6 class="card-title fw-bold mb-2">
            <a href="{{ route('product.show', $product->slug) }}" class="text-decoration-none text-dark stretched-link">
                {{ $product->name }}
            </a>
        </h6>

        <p class="card-text text-muted small mb-3 flex-grow-1" style="line-height: 1.4;">
            {{ Str::limit($product->short_description ?? $product->description, 50) }}
        </p>

        <div class="d-flex justify-content-between align-items-end mt-auto border-top pt-3">
            <div class="price-block">
                @if($product->old_price)
                    <small class="text-decoration-line-through text-muted d-block" style="font-size: 0.8rem;">${{ number_format($product->old_price, 0) }}</small>
                @endif
                <span class="h5 fw-bold text-gradient-purple mb-0">${{ number_format($product->price, 0) }}</span>
            </div>
            <button class="btn btn-sm btn-outline-light text-muted border-0 hover-primary">
                <i class="bi bi-heart"></i>
            </button>
        </div>
    </div>
</div>

<style>
    .hover-lift:hover {
        transform: translateY(-5px);
        transition: transform 0.3s ease;
    }
    .hover-opacity-100:hover {
        opacity: 1 !important;
    }
    .product-image:hover img {
        transform: scale(1.05);
    }
    .hover-primary:hover {
        color: var(--bs-primary) !important;
    }
</style>
