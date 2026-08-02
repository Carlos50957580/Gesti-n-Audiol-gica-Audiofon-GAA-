@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-0">Reporte de Citas</h4>
            <div class="page-title-right">
                <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line"></i> Volver
                </a>
                <a href="{{ route('admin.reports.export', ['type' => 'appointments'] + request()->all()) }}" class="btn btn-success">
                    <i class="ri-file-excel-line"></i> Exportar
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">Desde</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Hasta</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Estado</label>
                <select name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="programada" {{ request('status') == 'programada' ? 'selected' : '' }}>Programada</option>
                    <option value="completada" {{ request('status') == 'completada' ? 'selected' : '' }}>Completada</option>
                    <option value="cancelada" {{ request('status') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Médico</label>
                <select name="doctor_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                            {{ $doctor->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
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
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="ri-search-line"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-3">
    <div class="col-xl-2 col-md-4">
        <div class="card bg-primary-subtle border-0">
            <div class="card-body text-center">
                <p class="text-muted mb-1">Total</p>
                <h3 class="mb-0">{{ number_format($stats['total']) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4">
        <div class="card bg-info-subtle border-0">
            <div class="card-body text-center">
                <p class="text-muted mb-1">Programadas</p>
                <h3 class="mb-0">{{ number_format($stats['programadas']) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4">
        <div class="card bg-success-subtle border-0">
            <div class="card-body text-center">
                <p class="text-muted mb-1">Completadas</p>
                <h3 class="mb-0">{{ number_format($stats['completadas']) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4">
        <div class="card bg-danger-subtle border-0">
            <div class="card-body text-center">
                <p class="text-muted mb-1">Canceladas</p>
                <h3 class="mb-0">{{ number_format($stats['canceladas']) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4">
        <div class="card bg-success-subtle border-0">
            <div class="card-body text-center">
                <p class="text-muted mb-1">Tasa de Completación</p>
                <h3 class="mb-0">{{ $stats['completion_rate'] }}%</h3>
            </div>
        </div>
    </div>
</div>

<!-- Tabla -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Paciente</th>
                        <th>Médico</th>
                        <th>Sucursal</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appointment)
                    <tr>
                        <td>#{{ $appointment->id }}</td>
                        <td>{{ $appointment->patient->full_name ?? 'N/A' }}</td>
                        <td>{{ $appointment->doctor->name ?? 'N/A' }}</td>
                        <td>{{ $appointment->branch->name ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</td>
                        <td>
                            <span class="badge bg-{{ $appointment->status === 'completada' ? 'success' : ($appointment->status === 'programada' ? 'info' : 'danger') }}">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="ri-calendar-line d-block mb-2" style="font-size:48px;"></i>
                            No se encontraron citas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $appointments->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection