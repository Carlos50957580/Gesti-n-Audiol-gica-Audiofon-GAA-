@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">
                <i class="ri-cloud-line me-1"></i> Secuencias e-CF (Facturación Electrónica)
            </h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Secuencias e-CF</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- Estadísticas --}}
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-medium text-muted mb-0">Total Secuencias</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ $stats['total'] }}</h4>
                        <span class="text-muted">Registradas</span>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-primary-subtle rounded fs-3">
                            <i class="ri-list-check text-primary"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-medium text-muted mb-0">Activas</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ $stats['activas'] }}</h4>
                        <span class="text-muted">Disponibles</span>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-success-subtle rounded fs-3">
                            <i class="ri-checkbox-circle-line text-success"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-medium text-muted mb-0">Por Vencer (30 días)</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ $stats['por_vencer'] }}</h4>
                        <span class="text-muted">Próximas a expirar</span>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-warning-subtle rounded fs-3">
                            <i class="ri-alarm-warning-line text-warning"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-medium text-muted mb-0">Agotadas</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ $stats['agotadas'] }}</h4>
                        <span class="text-muted">Sin secuencias</span>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-danger-subtle rounded fs-3">
                            <i class="ri-close-circle-line text-danger"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filtros y listado --}}
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="card-title mb-0">Listado de Secuencias e-CF</h5>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('ecf-sequences.create') }}" class="btn btn-primary">
                            <i class="ri-add-line me-1"></i> Nueva Secuencia
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body border-bottom">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tipo e-CF</label>
                        <select name="tipo_ecf" class="form-select">
                            <option value="">Todos</option>
                            @foreach($tiposEcf as $code => $nombre)
                                <option value="{{ $code }}" {{ request('tipo_ecf') == $code ? 'selected' : '' }}>
                                    {{ $nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="">Todos</option>
                            <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activas</option>
                            <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivas</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Sucursal</label>
                        <select name="branch_id" class="form-select">
                            <option value="">Todas</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="ri-search-line me-1"></i> Filtrar
                        </button>
                        <a href="{{ route('ecf-sequences.index') }}" class="btn btn-light">
                            <i class="ri-refresh-line"></i>
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tipo</th>
                                <th>Prefijo</th>
                                <th>Rango</th>
                                <th>Siguiente e-NCF</th>
                                <th>Progreso</th>
                                <th>Vencimiento</th>
                                <th>Sucursal</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($secuencias as $seq)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary fs-12">
                                            E{{ $seq->tipo_ecf }}
                                        </span>
                                    </td>
                                    <td><code>{{ $seq->prefijo }}</code></td>
                                    <td>
                                        <small class="text-muted">
                                            {{ number_format($seq->desde) }} - {{ number_format($seq->hasta) }}
                                        </small>
                                    </td>
                                    <td>
                                        <code class="text-success">{{ $seq->siguiente_encf }}</code>
                                    </td>
                                    <td style="min-width: 140px;">
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                @php
                                                    $porc = $seq->porcentaje_uso;
                                                    $color = $porc >= 90 ? 'bg-danger' : ($porc >= 70 ? 'bg-warning' : 'bg-success');
                                                @endphp
                                                <div class="progress-bar {{ $color }}" 
                                                     role="progressbar" 
                                                     style="width: {{ $porc }}%"></div>
                                            </div>
                                            <small class="ms-2 text-muted">{{ $porc }}%</small>
                                        </div>
                                        <small class="text-muted">
                                            {{ number_format($seq->secuencias_disponibles) }} disponibles
                                        </small>
                                    </td>
                                    <td>
                                        @if($seq->tipo_ecf === '32')
                                            <span class="text-muted small">No requiere</span>
                                        @else
                                            @php
                                                $vencida = $seq->fecha_vencimiento && $seq->fecha_vencimiento->isPast();
                                                $porVencer = $seq->fecha_vencimiento && !$vencida && $seq->fecha_vencimiento->diffInDays(now()) <= 30;
                                            @endphp
                                            <span class="badge {{ $vencida ? 'bg-danger' : ($porVencer ? 'bg-warning' : 'bg-light text-dark') }}">
                                                {{ $seq->fecha_vencimiento?->format('d/m/Y') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $seq->branch->name ?? 'Global' }}</small>
                                    </td>
                                    <td>
                                        <form action="{{ route('ecf-sequences.toggle', $seq) }}" method="POST" class="d-inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $seq->estado ? 'btn-success' : 'btn-secondary' }}"
                                                    title="{{ $seq->estado ? 'Desactivar' : 'Activar' }}">
                                                <i class="ri-{{ $seq->estado ? 'checkbox-circle' : 'close-circle' }}-line"></i>
                                                {{ $seq->estado ? 'Activa' : 'Inactiva' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                                <i class="ri-more-2-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('ecf-sequences.show', $seq) }}">
                                                        <i class="ri-eye-line me-1"></i> Ver detalle
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('ecf-sequences.edit', $seq) }}">
                                                        <i class="ri-edit-line me-1"></i> Editar
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('ecf-sequences.destroy', $seq) }}" 
                                                          method="POST"
                                                          onsubmit="return confirm('¿Eliminar esta secuencia?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="ri-delete-bin-line me-1"></i> Eliminar
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="ri-inbox-line fs-48 d-block mb-2"></i>
                                            No hay secuencias registradas.
                                            <br>
                                            <a href="{{ route('ecf-sequences.create') }}" class="btn btn-sm btn-primary mt-2">
                                                <i class="ri-add-line me-1"></i> Crear primera secuencia
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $secuencias->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection