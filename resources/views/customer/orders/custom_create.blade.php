@extends('customer.layout')

@section('title', 'Pedido Personalizado | EliCrochet')

@section('customer_content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 py-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-light-primary p-3 me-3">
                        <i class="ti ti-wand fs-4 text-primary"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold">Solicitar Diseño Personalizado</h4>
                        <p class="mb-0 text-muted">Cuéntanos tu idea y agregaremos magia. Recibirás una cotización personalizada.</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('customer.custom.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="alert alert-info border-0 bg-light-info text-dark d-flex align-items-center mb-4">
                        <i class="ti ti-info-circle me-3 fs-4"></i>
                        <div>
                            <strong>¿Cómo funciona?</strong>
                            <ol class="mb-0 ps-3 mt-1 small">
                                <li>Elige el tipo de producto (Categoría) y completa los detalles específicos.</li>
                                <li>Describe tu idea con detalle y sube imágenes de referencia.</li>
                                <li>Revisaremos tu solicitud y te enviaremos una <strong>cotización</strong>.</li>
                            </ol>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Tipo de Producto <span class="text-danger">*</span></label>
                            <select name="category_id" id="categorySelect" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="" disabled selected>Selecciona una opción...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Dynamic Specs Container -->
                    <div id="dynamicSpecsContainer" class="mb-4 d-none p-3 bg-light rounded border">
                        <h6 class="fw-bold mb-3 text-primary"><i class="ti ti-ruler me-2"></i>Especificaciones</h6>
                        <div class="row" id="dynamicSpecsRow">
                            <!-- Inputs injected via JS -->
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Describe tu idea</label>
                        <textarea name="description" rows="5" class="form-control" placeholder="Ej: Quiero un diseño especial con detalles dorados..." required>{{ old('description') }}</textarea>
                        <small class="text-muted">Incluye detalles adicionales no cubiertos arriba.</small>
                    </div>

                    @push('scripts')
                    <script>
                        const categories = @json($categories->map(fn($c) => ['id' => $c->id, 'specs' => $c->required_specs]));
                        
                        document.getElementById('categorySelect').addEventListener('change', function() {
                            const catId = parseInt(this.value);
                            const category = categories.find(c => c.id === catId);
                            const container = document.getElementById('dynamicSpecsContainer');
                            const row = document.getElementById('dynamicSpecsRow');
                            
                            row.innerHTML = '';
                            
                            if (category && category.specs && category.specs.length > 0) {
                                container.classList.remove('d-none');
                                category.specs.forEach(spec => {
                                    const col = document.createElement('div');
                                    col.className = 'col-md-6 mb-3';
                                    
                                    let inputHtml = '';
                                    const fieldName = `custom_specs[${spec.name}]`;
                                    const isRequired = spec.required ? 'required' : '';
                                    const label = `<label class="form-label small fw-bold text-uppercase">${spec.name} ${spec.required ? '<span class="text-danger">*</span>' : ''}</label>`;
                                    
                                    if (spec.type === 'select') {
                                        let options = spec.options.map(opt => `<option value="${opt}">${opt}</option>`).join('');
                                        inputHtml = `<select name="${fieldName}" class="form-select" ${isRequired}><option value="">Seleccionar...</option>${options}</select>`;
                                    } else if (spec.type === 'number') {
                                        inputHtml = `<input type="number" name="${fieldName}" class="form-control" ${isRequired}>`;
                                    } else {
                                        inputHtml = `<input type="text" name="${fieldName}" class="form-control" ${isRequired}>`;
                                    }
                                    
                                    col.innerHTML = `
                                        ${label}
                                        ${inputHtml}
                                    `;
                                    row.appendChild(col);
                                });
                            } else {
                                container.classList.add('d-none');
                            }
                        });
                    </script>
                    @endpush

                    <div class="mb-4">
                        <label class="form-label fw-bold">Imágenes de Referencia (Opcional)</label>
                        <div class="input-group">
                            <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                            <label class="input-group-text"><i class="ti ti-upload"></i></label>
                        </div>
                        <small class="text-muted">Puedes subir múltiples imágenes para ayudarnos a entender tu idea.</small>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('customer.dashboard') }}" class="btn btn-light border">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="ti ti-send me-2"></i> Enviar Solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
