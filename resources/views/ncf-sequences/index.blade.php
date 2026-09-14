<x-app-layout>
@section('title', 'Comprobantes NCF')

<div class="page-content">
    <div class="container-fluid">
        {{-- Header --}}
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ri-file-shield-2-line me-1"></i>Comprobantes NCF</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Comprobantes NCF</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPIs --}}
        <div class="row g-3 mb-3">
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title rounded-circle bg-primary-subtle text-primary fs-4">
                                    <i class="ri-list-check-2"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-0 small">Total Secuencias</p>
                                <h3 class="mb-0">{{ $totalSequences }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title rounded-circle bg-success-subtle text-success fs-4">
                                    <i class="ri-checkbox-circle-line"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-0 small">Activas</p>
                                <h3 class="mb-0 text-success">{{ $activeSequences }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title rounded-circle bg-danger-subtle text-danger fs-4">
                                    <i class="ri-close-circle-line"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-0 small">Vencidas</p>
                                <h3 class="mb-0 text-danger">{{ $expiredSequences }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title rounded-circle bg-warning-subtle text-warning fs-4">
                                    <i class="ri-alert-line"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-0 small">Por Agotarse</p>
                                <h3 class="mb-0 text-warning">{{ $lowStockSequences }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Buscar</label>
                        <input type="text" name="search" class="form-control" 
                               value="{{ request('search') }}" placeholder="Nombre de la secuencia">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tipo NCF</label>
                        <select name="type_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" {{ request('type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->code }} - {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sucursal</label>
                        <select name="branch_id" class="form-select">
                            <option value="">Todas</option>
                            @foreach($branches as $br)
                                <option value="{{ $br->id }}" {{ request('branch_id') == $br->id ? 'selected' : '' }}>
                                    {{ $br->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Estado</label>
                        <select name="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activas</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivas</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Vencidas</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-search-line me-1"></i>Filtrar
                        </button>
                        <a href="{{ url('/ncf-sequences') }}" class="btn btn-secondary">
                            <i class="ri-refresh-line"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Secuencias Configuradas</h5>
                <a href="{{ url('/ncf-sequences/create') }}" class="btn btn-primary btn-sm">
                    <i class="ri-add-line me-1"></i>Nueva Secuencia
                </a>
            </div>
            <div class="card-body p-0">
                @if(session('success'))
                    <div class="alert alert-success m-3 mb-0">
                        <i class="ri-check-line me-1"></i>{{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger m-3 mb-0">
                        <i class="ri-error-warning-line me-1"></i>{{ session('error') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre</th>
                                <th>Tipo NCF</th>
                                <th>Sucursal</th>
                                <th>Rango Actual</th>
                                <th class="text-center">Disponibles</th>
                                <th>Vence</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sequences as $seq)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $seq->name }}</div>
                                        <small class="text-muted">Serie: {{ $seq->serie }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $seq->type->code }}</div>
                                        <small class="text-muted">{{ $seq->type->name }}</small>
                                    </td>
                                    <td>
                                        {{ $seq->branch ? $seq->branch->name : 'Todas' }}
                                    </td>
                                    <td>
                                        <code style="font-size:.8rem;">{{ $seq->current_ncf }}</code>
                                        <div class="progress mt-1" style="height:4px;">
                                            <div class="progress-bar bg-{{ $seq->status_color }}" 
                                                 style="width:{{ $seq->used_percent }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $seq->used_percent }}% usado</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $seq->remaining > $seq->alert_threshold ? 'success' : 'warning' }}-subtle text-{{ $seq->remaining > $seq->alert_threshold ? 'success' : 'warning' }}">
                                            {{ number_format($seq->remaining) }}
                                        </span>
                                        <div class="small text-muted">de {{ number_format($seq->total_available) }}</div>
                                    </td>
                                    <td>
                                        <div>{{ $seq->valid_until->format('d/m/Y') }}</div>
                                        @if($seq->days_left !== null)
                                            @if($seq->days_left < 0)
                                                <small class="text-danger">Vencida hace {{ abs($seq->days_left) }} días</small>
                                            @elseif($seq->days_left <= 30)
                                                <small class="text-warning">Vence en {{ $seq->days_left }} días</small>
                                            @else
                                                <small class="text-muted">{{ $seq->days_left }} días restantes</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $seq->status_color }}-subtle text-{{ $seq->status_color }}">
                                            {{ $seq->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ url('/ncf-sequences/'.$seq->id) }}" class="btn btn-sm btn-info" title="Ver">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            <a href="{{ url('/ncf-sequences/'.$seq->id.'/edit') }}" class="btn btn-sm btn-warning" title="Editar">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            <form action="{{ url('/ncf-sequences/'.$seq->id.'/toggle') }}" method="POST" class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm btn-{{ $seq->is_active ? 'secondary' : 'success' }}" 
                                                        title="{{ $seq->is_active ? 'Desactivar' : 'Activar' }}">
                                                    <i class="ri-{{ $seq->is_active ? 'pause' : 'play' }}-line"></i>
                                                </button>
                                            </form>
                                            @if($seq->used == 0)
                                                <form action="{{ url('/ncf-sequences/'.$seq->id) }}" method="POST" class="d-inline"
                                                      onsubmit="return confirm('¿Eliminar esta secuencia?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-danger" title="Eliminar">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="ri-file-shield-2-line fs-1 text-muted opacity-25 d-block mb-2"></i>
                                        <p class="text-muted mb-0">No hay secuencias NCF configuradas.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($sequences->hasPages())
                <div class="card-footer">
                    {{ $sequences->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
</x-app-layout>