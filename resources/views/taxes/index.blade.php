@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Impuestos</h4>
            <div class="page-title-right">
                <a href="{{ route('taxes.create') }}" class="btn btn-primary">
                    <i class="ri-add-line"></i> Nuevo Impuesto
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
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-centered mb-0">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Porcentaje</th>
                                <th>Descripción</th>
                                <th>Por Defecto</th>
                                <th>Estado</th>
                                <th>Servicios Asociados</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($taxes as $tax)
                            <tr>
                                <td><code class="text-primary">{{ $tax->code }}</code></td>
                                <td><strong>{{ $tax->name }}</strong></td>
                                <td><span class="badge bg-info">{{ $tax->rate }}%</span></td>
                                <td>{{ $tax->description ?? 'N/A' }}</td>
                                <td>
                                    @if($tax->is_default)
                                        <span class="badge bg-success"><i class="ri-star-fill me-1"></i> Por Defecto</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $tax->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $tax->is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $tax->services_count ?? $tax->services()->count() }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('taxes.edit', $tax) }}" class="btn btn-soft-primary btn-sm" title="Editar">
                                            <i class="ri-edit-2-line"></i>
                                        </a>
                                        <form action="{{ route('taxes.destroy', $tax) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-soft-danger btn-sm" 
                                                    onclick="return confirm('¿Estás seguro de eliminar este impuesto?')" title="Eliminar">
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
                                    No hay impuestos registrados
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection