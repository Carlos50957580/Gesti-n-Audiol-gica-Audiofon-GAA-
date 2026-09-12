<x-app-layout>
@section('title', 'Editar Producto')

<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ri-edit-line me-1"></i>Editar Producto</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Productos</a></li>
                            <li class="breadcrumb-item active">Editar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Información Básica</h5></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Categoría</label>
                                    <select name="category_id" class="form-select">
                                        <option value="">Sin categoría</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Código <span class="text-danger">*</span></label>
                                    <input type="text" name="code" class="form-control" value="{{ old('code', $product->code) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Código de Barras</label>
                                    <input type="text" name="barcode" class="form-control" value="{{ old('barcode', $product->barcode) }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Descripción</label>
                                    <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Unidad <span class="text-danger">*</span></label>
                                    <input type="text" name="unit" class="form-control" value="{{ old('unit', $product->unit) }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Precios y Stock</h5></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Precio de Costo</label>
                                    <div class="input-group">
                                        <span class="input-group-text">RD$</span>
                                        <input type="number" step="0.01" name="cost_price" class="form-control" 
                                               value="{{ old('cost_price', $product->cost_price) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Precio de Venta</label>
                                    <div class="input-group">
                                        <span class="input-group-text">RD$</span>
                                        <input type="number" step="0.01" name="sale_price" class="form-control" 
                                               value="{{ old('sale_price', $product->sale_price) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Stock Mínimo</label>
                                    <input type="number" name="min_stock" class="form-control" 
                                           value="{{ old('min_stock', $product->min_stock) }}" min="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Stock Máximo</label>
                                    <input type="number" name="max_stock" class="form-control" 
                                           value="{{ old('max_stock', $product->max_stock) }}" min="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Stock actual por sucursal --}}
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Stock Actual por Sucursal</h5></div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Sucursal</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-center">Reservado</th>
                                            <th class="text-center">Disponible</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($branches as $branch)
                                            @php $stock = $stocks->get($branch->id); @endphp
                                            <tr>
                                                <td>{{ $branch->name }}</td>
                                                <td class="text-center"><strong>{{ $stock->quantity ?? 0 }}</strong></td>
                                                <td class="text-center text-muted">{{ $stock->reserved_quantity ?? 0 }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-info-subtle text-info">
                                                        {{ $stock->available_quantity ?? 0 }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Imagen</h5></div>
                        <div class="card-body text-center">
                            <img id="imagePreview" src="{{ $product->image_url }}" alt="" 
                                 style="max-width:100%; max-height:200px; border-radius:8px; margin-bottom:1rem;">
                            <input type="file" name="image" class="form-control" accept="image/*" id="imageInput">
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Opciones</h5></div>
                        <div class="card-body">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="has_tax" value="1" 
                                       id="has_tax" {{ old('has_tax', $product->has_tax) ? 'checked' : '' }}>
                                <label class="form-check-label" for="has_tax">Aplica ITBIS</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                       id="is_active" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Activo</label>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line me-1"></i>Actualizar
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
            document.getElementById('imagePreview').src = ev.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
</x-app-layout>