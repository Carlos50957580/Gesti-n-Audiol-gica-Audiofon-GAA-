@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Servicios / Estudios</h4>
            <div class="page-title-right">
                <a href="{{ route('services.create') }}" class="btn btn-primary">
                    <i class="ri-add-line"></i> Nuevo Servicio
                </a>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Filtros -->
                <form method="GET" class="row g-3 mb-3" id="filterForm">
                    <div class="col-md-2">
                        <label class="form-label">Categoría</label>
                        <select name="category_id" class="form-select" onchange="this.form.submit()">
                            <option value="">Todas</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Estado</label>
                        <select name="is_active" class="form-select" onchange="this.form.submit()">
                            <option value="">Todos</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Activos</option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Historia Clínica</label>
                        <select name="requires_clinical_record" class="form-select" onchange="this.form.submit()">
                            <option value="">Todos</option>
                            <option value="1" {{ request('requires_clinical_record') == '1' ? 'selected' : '' }}>Requiere HC</option>
                            <option value="0" {{ request('requires_clinical_record') == '0' ? 'selected' : '' }}>No requiere HC</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Buscar</label>
                        <input type="text" name="search" class="form-control" placeholder="Nombre, código..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ri-search-line"></i> Filtrar
                        </button>
                    </div>
                </form>

                <!-- Tabla -->
                <div class="table-responsive">
                    <table class="table table-hover table-centered mb-0">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Categoría</th>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Duración</th>
                                <th>HC</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($services as $service)
                            <tr>
                                <td>
                                    <code class="text-primary">{{ $service->code ?? 'N/A' }}</code>
                                </td>
                                <td>
                                    @if($service->category)
                                    <span class="badge" style="background-color: {{ $service->category->color ?? '#6c757d' }}20; color: {{ $service->category->color ?? '#6c757d' }}">
                                        <i class="{{ $service->category->icon ?? 'ri-folder-line' }} me-1"></i>
                                        {{ $service->category->name }}
                                    </span>
                                    @else
                                    <span class="badge bg-secondary">Sin categoría</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $service->name }}</strong>
                                    @if($service->description)
                                    <br><small class="text-muted">{{ Str::limit($service->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>RD$ {{ number_format($service->price, 2) }}</td>
                                <td>{{ $service->duration_minutes ? $service->duration_minutes.' min' : 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ $service->requiresClinicalRecord() ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $service->requiresClinicalRecord() ? 'Sí' : 'No' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $service->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $service->is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('services.edit', $service) }}" class="btn btn-soft-primary btn-sm" title="Editar">
                                            <i class="ri-edit-2-line"></i>
                                        </a>
                                        <form action="{{ route('services.destroy', $service) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-soft-danger btn-sm" 
                                                    onclick="return confirm('¿Eliminar este servicio?\n\nEsto eliminará también las configuraciones de cobertura.')" title="Eliminar">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="ri-inbox-line" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                                    No hay servicios registrados
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $services->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection