<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.head-page-meta', ['title' => 'Detalle del Producto: ' . $product->name])
    @include('layouts.head-css')
</head>

<body @bodySetup>
    @include('layouts.layout-vertical')

    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">
            @include('layouts.breadcrumb', [
                'breadcrumb-item' => 'Productos',
                'breadcrumb-item-active' => 'Detalle',
            ])

            <!-- [ sample-page ] start -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h4 lass="m-0">Detalle del Producto</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('back.products.edit', $product) }}" class="btn btn-primary ">
                                <i class="ti ti-edit f-18 me-2"></i> Editar Producto
                            </a>
                            <a href="{{ route('back.products.index') }}" class="btn btn-secondary">
                                <i class="ti ti-arrow-left f-18 me-2"></i> Volver a la Lista
                            </a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div>
                                        @if ($product->images->count() > 0)

                                            {{-- 2. SOLUCIÓN ALTURA FIJA Y SIN SALTO (Aspect Ratio Trick) --}}
                                            <div class="bg-light rounded overflow-hidden"
                                                style="position: relative; padding-top: 75%; height: 0;">

                                                {{-- Contenedor del Carrusel, ahora posicionado de forma absoluta dentro del contenedor de altura fija --}}
                                                <div id="productCarousel" class="carousel slide ecomm-prod-slider"
                                                    data-bs-ride="carousel"
                                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">

                                                    <div class="carousel-inner w-100 h-100">
                                                        @foreach ($product->images as $key => $image)
                                                            <div
                                                                class="carousel-item {{ $key == 0 ? 'active' : '' }} w-100 h-100">
                                                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                                                    class="d-block w-100 h-100"
                                                                    alt="{{ $product->name . ' - Imagen ' . ($key + 1) }}"
                                                                    style="object-fit: contain;">
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    @if ($product->images->count() > 1)
                                                        <button class="carousel-control-prev" type="button"
                                                            data-bs-target="#productCarousel" data-bs-slide="prev">
                                                            <span class="carousel-control-prev-icon"
                                                                aria-hidden="true"></span>
                                                            <span class="visually-hidden">Anterior</span>
                                                        </button>
                                                        <button class="carousel-control-next" type="button"
                                                            data-bs-target="#productCarousel" data-bs-slide="next">
                                                            <span class="carousel-control-next-icon"
                                                                aria-hidden="true"></span>
                                                            <span class="visually-hidden">Siguiente</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div> {{-- Fin del Contenedor de Altura Fija --}}

                                            @if ($product->images->count() > 1)
                                                {{-- Las miniaturas siempre estarán directamente debajo del contenedor principal --}}
                                                <div
                                                    class="d-flex justify-content-center gap-2 mt-3 product-carousel-indicators">
                                                    @foreach ($product->images as $key => $image)
                                                        <button type="button" data-bs-target="#productCarousel"
                                                            data-bs-slide-to="{{ $key }}"
                                                            class="{{ $key == 0 ? 'active' : '' }} border-0 p-0 rounded product-thumbnail"
                                                            aria-label="Diapositiva {{ $key + 1 }}">
                                                            <img src="{{ asset('storage/' . $image->image_path) }}"
                                                                class="d-block rounded" alt="{{ $product->name }}"
                                                                style="width: 70px; height: 70px; object-fit: cover;">
                                                        </button>
                                                    @endforeach
                                                </div>
                                            @endif
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                style="position: relative; padding-top: 75%; height: 0;">
                                                <div class="text-center"
                                                    style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                                    <i class="ti ti-package f-48 text-muted"></i>
                                                    <p class="text-muted mt-2">Sin imágenes cargadas</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Columna derecha: Información del producto -->
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        {!! $product->status_badge !!}
                                        @if ($product->status === 'active' && $product->stock <= 0)
                                            <span class="badge bg-danger mt-1 fs-6">
                                                <i class="ti ti-exclamation-triangle me-1"></i> Sin Stock
                                            </span>
                                        @endif
                                    </div>

                                    <h2 class="mb-3">{{ $product->name }}</h2>

                                    <p class="text-muted mb-4">{{ $product->description }}</p>

                                    <!-- Información básica -->
                                    <div class="table-responsive mb-4">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <td class="text-muted text-sm py-1">Categoría:</td>
                                                    <td class="py-1 fw-bold">
                                                        {{ $product->category->name ?? 'Sin categoría' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted text-sm py-1">Precio:</td>
                                                    <td class="py-1">
                                                        <h4 class="mb-0 text-success">
                                                            ${{ number_format($product->price, 2) }}</h4>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted text-sm py-1">Stock disponible:</td>
                                                    <td class="py-1">
                                                        <span
                                                            class="badge fs-6 bg-light-{{ $product->stock > 10 ? 'success' : ($product->stock > 0 ? 'warning' : 'danger') }}">
                                                            {{ $product->stock }} unidades
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted text-sm py-1">ID del producto:</td>
                                                    <td class="py-1">{{ $product->id }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted text-sm py-1">URL amigable:</td>
                                                    <td class="py-1">{{ $product->slug }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Información de auditoría -->
                                    <div class="card bg-light mt-4">
                                        <div class="card-body">
                                            <h6 class="mb-3">Información de Auditoría</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p class="mb-1 text-muted small">Creado:</p>
                                                    <p class="mb-0 fw-bold">
                                                        {{ $product->created_at->format('d/m/Y H:i') }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="mb-1 text-muted small">Última actualización:</p>
                                                    <p class="mb-0 fw-bold">
                                                        {{ $product->updated_at->format('d/m/Y H:i') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs de información adicional -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header pb-0">
                                    <ul class="nav nav-tabs profile-tabs mb-0" id="productTabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="details-tab" data-bs-toggle="tab"
                                                href="#details-content" role="tab"
                                                aria-controls="details-content" aria-selected="true">Detalles
                                                Adicionales</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="inventory-tab" data-bs-toggle="tab"
                                                href="#inventory-content" role="tab"
                                                aria-controls="inventory-content" aria-selected="false">Inventario</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="metadata-tab" data-bs-toggle="tab"
                                                href="#metadata-content" role="tab"
                                                aria-controls="metadata-content" aria-selected="false">Metadatos</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        <!-- Detalles adicionales -->
                                        <div class="tab-pane show active" id="details-content" role="tabpanel"
                                            aria-labelledby="details-tab">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h5>Información del Producto</h5>
                                                    <hr class="my-3">
                                                    <div class="table-responsive">
                                                        <table class="table table-borderless">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="text-muted text-sm py-1">Estado:</td>
                                                                    <td class="py-1">{!! $product->status_badge !!}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-muted text-sm py-1">Stock crítico:
                                                                    </td>
                                                                    <td class="py-1">
                                                                        {{ $product->stock <= 5 ? 'SÍ' : 'NO' }}</td>
                                                                </tr>
                                                                @if ($product->sku)
                                                                    <tr>
                                                                        <td class="text-muted text-sm py-1">SKU:</td>
                                                                        <td class="py-1">
                                                                            <code>{{ $product->sku }}</code>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h5>Información de Categoría</h5>
                                                    <hr class="my-3">
                                                    <div class="table-responsive">
                                                        <table class="table table-borderless">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="text-muted text-sm py-1">Categoría
                                                                        principal:</td>
                                                                    <td class="py-1">
                                                                        {{ $product->category->name ?? 'N/A' }}</td>
                                                                </tr>
                                                                @if ($product->category)
                                                                    <tr>
                                                                        <td class="text-muted text-sm py-1">Descripción
                                                                            categoría:</td>
                                                                        <td class="py-1">
                                                                            {{ $product->category->description ?? 'N/A' }}
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Inventario -->
                                        <div class="tab-pane" id="inventory-content" role="tabpanel"
                                            aria-labelledby="inventory-tab">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="card bg-light">
                                                        <div class="card-body text-center">
                                                            <h3
                                                                class="text-{{ $product->stock > 10 ? 'success' : ($product->stock > 0 ? 'warning' : 'danger') }}">
                                                                {{ $product->stock }}
                                                            </h3>
                                                            <p class="text-muted mb-0">Unidades en stock</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card bg-light">
                                                        <div class="card-body text-center">
                                                            <h3 class="text-primary">
                                                                ${{ number_format($product->price * $product->stock, 2) }}
                                                            </h3>
                                                            <p class="text-muted mb-0">Valor total en inventario</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Metadatos -->
                                        <div class="tab-pane" id="metadata-content" role="tabpanel"
                                            aria-labelledby="metadata-tab">
                                            <div class="table-responsive">
                                                <table class="table table-borderless">
                                                    <tbody>
                                                        <tr>
                                                            <td class="text-muted text-sm py-1">Creado:</td>
                                                            <td class="py-1">
                                                                {{ $product->created_at->format('d/m/Y H:i:s') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted text-sm py-1">Actualizado:</td>
                                                            <td class="py-1">
                                                                {{ $product->updated_at->format('d/m/Y H:i:s') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted text-sm py-1">Imágenes:</td>
                                                            <td class="py-1">{{ $product->images->count() }}
                                                                archivo(s)</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted text-sm py-1">Visibilidad:</td>
                                                            <td class="py-1">
                                                                {{ $product->status === 'active' ? 'Público' : 'Privado' }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ sample-page ] end -->
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>
    <!-- [ Main Content ] end -->

    @include('layouts.footer-block')

    @include('layouts.footer-js')
</body>

</html>
