<x-app-layout>
@section('title', 'Proveedores')

<div class="page-content">
    <div class="container-fluid">
        {{-- Header --}}
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ri-truck-line me-1"></i>Proveedores</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Proveedores</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label">Buscar</label>
                        <input type="text" name="search" class="form-control" 
                               value="{{ request('search') }}" placeholder="Nombre, RNC, email o teléfono">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-search-line me-1"></i>Buscar
                        </button>
                        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                            <i class="ri-refresh-line me-1"></i>Limpiar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Lista de Proveedores</h5>
                <a href="{{ route('suppliers.create') }}" class="btn btn-primary btn-sm">
                    <i class="ri-add-line me-1"></i>Nuevo Proveedor
                </a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="ri-check-line me-1"></i>{{ session('success') }}
                        <button class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="ri-error-warning-line me-1"></i>{{ session('error') }}
                        <button class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Proveedor</th>
                                <th>RNC</th>
                                <th>Contacto</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suppliers as $supplier)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-xs">
                                                <span class="avatar-title rounded-circle bg-primary bg-soft text-primary fw-bold">
                                                    {{ strtoupper(substr($supplier->name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $supplier->name }}</div>
                                                @if($supplier->email)
                                                    <small class="text-muted">{{ $supplier->email }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td><code>{{ $supplier->rnc ?? '—' }}</code></td>
                                    <td>{{ $supplier->contact_name ?? '—' }}</td>
                                    <td>
                                        @if($supplier->phone)
                                            <i class="ri-phone-line text-muted me-1"></i>{{ $supplier->phone }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($supplier->is_active)
                                            <span class="badge bg-success-subtle text-success">Activo</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('suppliers.show', $supplier) }}" 
                                               class="btn btn-sm btn-info" title="Ver">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            <a href="{{ route('suppliers.edit', $supplier) }}" 
                                               class="btn btn-sm btn-warning" title="Editar">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            <form action="{{ route('suppliers.destroy', $supplier) }}" 
                                                  method="POST" class="d-inline" 
                                                  onsubmit="return confirm('¿Eliminar proveedor?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-danger" title="Eliminar">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="ri-truck-line fs-1 text-muted opacity-25 d-block mb-2"></i>
                                        <p class="text-muted mb-0">No hay proveedores registrados.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $suppliers->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>