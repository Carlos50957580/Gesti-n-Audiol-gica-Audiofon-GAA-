<x-app-layout>
@section('title', 'Nuevo Proveedor')

<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ri-add-line me-1"></i>Nuevo Proveedor</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Proveedores</a></li>
                            <li class="breadcrumb-item active">Nuevo</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('suppliers.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Información del Proveedor</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name') }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">RNC</label>
                                    <input type="text" name="rnc" class="form-control @error('rnc') is-invalid @enderror" 
                                           value="{{ old('rnc') }}" placeholder="101-12345-6">
                                    @error('rnc')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Contacto</label>
                                    <input type="text" name="contact_name" class="form-control" 
                                           value="{{ old('contact_name') }}" placeholder="Nombre del contacto">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email') }}" placeholder="contacto@proveedor.com">
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" name="phone" class="form-control" 
                                           value="{{ old('phone') }}" placeholder="(809) 555-0100">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Móvil</label>
                                    <input type="text" name="mobile" class="form-control" 
                                           value="{{ old('mobile') }}" placeholder="(829) 555-0200">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Dirección</label>
                                    <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Sitio Web</label>
                                    <input type="url" name="website" class="form-control @error('website') is-invalid @enderror" 
                                           value="{{ old('website') }}" placeholder="https://proveedor.com">
                                    @error('website')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Notas</label>
                                    <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                               id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
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
                                    <i class="ri-save-line me-1"></i>Guardar Proveedor
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