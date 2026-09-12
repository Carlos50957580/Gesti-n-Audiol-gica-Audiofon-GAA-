<x-app-layout>
@section('title', 'Editar Categoría')

<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ri-edit-line me-1"></i>Editar Categoría</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('product-categories.index') }}">Categorías</a></li>
                            <li class="breadcrumb-item active">Editar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('product-categories.update', $productCategory) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $productCategory->name) }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Código</label>
                                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                           value="{{ old('code', $productCategory->code) }}" placeholder="MED-001">
                                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Descripción</label>
                                    <textarea name="description" class="form-control" rows="3">{{ old('description', $productCategory->description) }}</textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Color</label>
                                    <input type="color" name="color" class="form-control form-control-color" 
                                           value="{{ old('color', $productCategory->color ?? '#405189') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Icono (Remix Icon)</label>
                                    <input type="text" name="icon" class="form-control" 
                                           value="{{ old('icon', $productCategory->icon ?? 'ri-archive-line') }}" 
                                           placeholder="ri-archive-line">
                                </div>

                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                               id="is_active" {{ old('is_active', $productCategory->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Activa</label>
                                    </div>
                                </div>

                                <div class="col-12 mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-save-line me-1"></i>Actualizar
                                    </button>
                                    <a href="{{ route('product-categories.index') }}" class="btn btn-secondary">
                                        <i class="ri-arrow-left-line me-1"></i>Cancelar
                                    </a>
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