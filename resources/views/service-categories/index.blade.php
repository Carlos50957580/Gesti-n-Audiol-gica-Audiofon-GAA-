@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Categorías de Servicios</h4>
            <div class="page-title-right">
                <a href="{{ route('service-categories.create') }}" class="btn btn-primary">
                    <i class="ri-add-line"></i> Nueva Categoría
                </a>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="ri-check-line me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="ri-error-warning-line me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    @forelse($categories as $category)
    <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm">
                            <span class="avatar-title rounded-circle fs-16" 
                                  style="background-color: {{ $category->color ?? '#405189' }}20; color: {{ $category->color ?? '#405189' }}">
                                <i class="{{ $category->icon ?? 'ri-folder-line' }}"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <h5 class="text-truncate mb-1">{{ $category->name }}</h5>
                        <p class="text-muted mb-0 text-truncate" title="{{ $category->description }}">
                            {{ $category->description ?? 'Sin descripción' }}
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="dropdown">
                            <button class="btn btn-ghost-primary btn-icon btn-sm" data-bs-toggle="dropdown">
                                <i class="ri-more-fill"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('service-categories.edit', $category) }}" class="dropdown-item">
                                    <i class="ri-edit-2-line me-1"></i> Editar
                                </a>
                                <form action="{{ route('service-categories.destroy', $category) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('¿Estás seguro de eliminar esta categoría?')">
                                        <i class="ri-delete-bin-line me-1"></i> Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3 pt-2 border-top">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Servicios:</span>
                        <span class="badge bg-primary">{{ $category->services_count }}</span>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <span class="text-muted">Historia Clínica:</span>
                        <span class="badge {{ $category->requires_clinical_record ? 'bg-success' : 'bg-secondary' }}">
                            {{ $category->requires_clinical_record ? 'Sí' : 'No' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <span class="text-muted">Estado:</span>
                        <span class="badge {{ $category->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $category->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="ri-folder-line" style="font-size: 64px; color: #ccc;"></i>
                <h5 class="mt-3">No hay categorías registradas</h5>
                <p class="text-muted">Comienza creando tu primera categoría de servicios.</p>
                <a href="{{ route('service-categories.create') }}" class="btn btn-primary">
                    <i class="ri-add-line"></i> Crear Categoría
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>
@endsection