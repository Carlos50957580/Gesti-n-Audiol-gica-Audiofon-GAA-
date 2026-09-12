<x-app-layout>
@section('title', 'Nuevo Producto')

<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ri-add-line me-1"></i>Nuevo Producto</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Productos</a></li>
                            <li class="breadcrumb-item active">Nuevo</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    {{-- Info básica --}}
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Información Básica</h5></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name') }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Categoría</label>
                                    <select name="category_id" class="form-select">
                                        <option value="">Sin categoría</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Código <span class="text-danger">*</span></label>
                                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                           value="{{ old('code') }}" required>
                                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Código de Barras</label>
                                    <input type="text" name="barcode" class="form-control @error('barcode') is-invalid @enderror" 
                                           value="{{ old('barcode') }}">
                                    @error('barcode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Descripción</label>
                                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Unidad <span class="text-danger">*</span></label>
                                    <input type="text" name="unit" class="form-control" 
                                           value="{{ old('unit', 'unidad') }}" required placeholder="unidad, caja, ml, mg">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Precios --}}
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Precios</h5></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Precio de Costo <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">RD$</span>
                                        <input type="number" step="0.01" name="cost_price" class="form-control" 
                                               value="{{ old('cost_price', 0) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Precio de Venta <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">RD$</span>
                                        <input type="number" step="0.01" name="sale_price" class="form-control" 
                                               value="{{ old('sale_price', 0) }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Stock --}}
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Stock Inicial por Sucursal</h5></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Stock Mínimo</label>
                                    <input type="number" name="min_stock" class="form-control" 
                                           value="{{ old('min_stock', 0) }}" min="0">
                                    <small class="text-muted">Alerta cuando el stock baje de este valor</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Stock Máximo</label>
                                    <input type="number" name="max_stock" class="form-control" 
                                           value="{{ old('max_stock') }}" min="0">
                                </div>
                                <div class="col-12">
                                    <hr>
                                    <h6 class="mb-3">Cantidad inicial por sucursal (opcional)</h6>
                                </div>
                                @foreach($branches as $branch)
                                    <div class="col-md-6">
                                        <label class="form-label">{{ $branch->name }}</label>
                                        <input type="number" name="initial_stocks[{{ $branch->id }}]" 
                                               class="form-control" value="{{ old('initial_stocks.'.$branch->id, 0) }}" 
                                               min="0">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    {{-- Imagen --}}
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Imagen</h5></div>
                        <div class="card-body">
                            <input type="file" name="image" class="form-control" accept="image/*" id="imageInput">
                            <div class="mt-3 text-center">
                                <img id="imagePreview" src="#" alt="" 
                                     style="max-width:100%; max-height:200px; display:none; border-radius:8px;">
                            </div>
                        </div>
                    </div>

                    {{-- Opciones --}}
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Opciones</h5></div>
                        <div class="card-body">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="has_tax" value="1" 
                                       id="has_tax" {{ old('has_tax') ? 'checked' : '' }}>
                                <label class="form-check-label" for="has_tax">Aplica ITBIS</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                       id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Activo</label>
                            </div>
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="card">
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line me-1"></i>Guardar Producto
                                </button>
                                <a href="{{ route('products.index') }}" class="btn btn-secondary">
                                    <i class="ri-arrow-left-line me-1"></i>Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('imageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            const preview = document.getElementById('imagePreview');
            preview.src = ev.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
</x-app-layout>