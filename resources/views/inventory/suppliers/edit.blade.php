<x-app-layout>
@section('title', 'Editar Proveedor')

<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ri-edit-line me-1"></i>Editar Proveedor</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Proveedores</a></li>
                            <li class="breadcrumb-item active">Editar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Información del Proveedor</h5></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $supplier->name) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">RNC</label>
                                    <input type="text" name="rnc" class="form-control" value="{{ old('rnc', $supplier->rnc) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Contacto</label>
                                    <input type="text" name="contact_name" class="form-control" value="{{ old('contact_name', $supplier->contact_name) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $supplier->email) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $supplier->phone) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Móvil</label>
                                    <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $supplier->mobile) }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Dirección</label>
                                    <textarea name="address" class="form-control" rows="2">{{ old('address', $supplier->address) }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Sitio Web</label>
                                    <input type="url" name="website" class="form-control" value="{{ old('website', $supplier->website) }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Notas</label>
                                    <textarea name="notes" class="form-control" rows="2">{{ old('notes', $supplier->notes) }}</textarea>
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                               id="is_active" {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Activo</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line me-1"></i>Actualizar
                                </button>
                                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
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
</x-app-layout>