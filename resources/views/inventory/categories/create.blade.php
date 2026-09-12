<x-app-layout>
@section('title', 'Nueva Categoría')

<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Nueva Categoría de Producto</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('product-categories.store') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Código</label>
                                    <input type="text" name="code" class="form-control" value="{{ old('code') }}" placeholder="MED-001">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Descripción</label>
                                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Color</label>
                                    <input type="color" name="color" class="form-control form-control-color" value="{{ old('color', '#405189') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Icono (Remix Icon)</label>
                                    <input type="text" name="icon" class="form-control" value="{{ old('icon', 'ri-archive-line') }}" placeholder="ri-archive-line">
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Activa</label>
                                    </div>
                                </div>
                                <div class="col-12 mt-3">
                                    <button type="submit" class="btn btn-primary"><i class="ri-save-line me-1"></i>Guardar</button>
                                    <a href="{{ route('product-categories.index') }}" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>