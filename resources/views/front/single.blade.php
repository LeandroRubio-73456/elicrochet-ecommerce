{{-- resources/views/front/single.blade.php --}}

@extends('layouts.front-layout')

@section('title', $product->name . ' | EliCrochet')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-6">
            <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                 alt="{{ $product->name }}" class="img-fluid rounded shadow">
            
            </div>
        
        <div class="col-md-6">
            <h1 class="f-w-600">{{ $product->name }}</h1>
            <p class="h3 text-primary mb-3">${{ number_format($product->price, 2) }}</p>

            <p class="text-muted">{{ $product->description }}</p>

            @if($product->isAvailable())
                <span class="badge bg-success mb-4">En Stock: {{ $product->stock }} unidades</span>
                <form action="{{ route('cart.add', $product->slug) }}" method="POST">
                    @csrf
                    <div class="input-group w-50 mb-3">
                        <input type="number" name="quantity" class="form-control" value="1" min="1" max="{{ $product->stock }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="ti ti-shopping-cart me-2"></i> Añadir al Carrito
                        </button>
                    </div>
                </form>
            @else
                <span class="badge bg-danger mb-4">Agotado Temporalmente</span>
            @endif

            <p class="mt-4">
                **Categoría:** <a href="{{ route('category.show', $product->category->slug) }}">{{ $product->category->name }}</a>
            </p>
        </div>
    </div>
    
    @if(count($relatedProducts) > 0)
    <h3 class="mt-5">Productos Relacionados</h3>
    <div class="row">
        {{-- Bucle para mostrar $relatedProducts --}}
    </div>
    @endif
</div>
@endsection