@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-0">Reporte de Pacientes</h4>
            <div class="page-title-right">
                <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line"></i> Volver
                </a>
                <a href="{{ route('admin.reports.export', ['type' => 'patients'] + request()->all()) }}" class="btn btn-success">
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
            <div class="col-md-2">
                <label class="form-label">Con Seguro</label>
                <select name="has_insurance" class="form-select">
                    <option value="">Todos</option>
                    <option value="1" {{ request('has_insurance') == '1' ? 'selected' : '' }}>Con Seguro</option>
                    <option value="0" {{ request('has_insurance') == '0' ? 'selected' : '' }}>Sin Seguro</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Buscar</label>
                <input type="text" name="search" class="form-control" placeholder="Nombre o cédula..." value="{{ request('search') }}">
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
    <div class="col-xl-3 col-md-6">
        <div class="card bg-primary-subtle border-0">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-primary text-white rounded-circle fs-18">
                                <i class="ri-team-line"></i>
                            </span>
                        </div>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Total Pacientes</p>
                        <h4 class="mb-0">{{ number_format($stats['total']) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-success-subtle border-0">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-success text-white rounded-circle fs-18">
                                <i class="ri-user-add-line"></i>
                            </span>
                        </div>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Nuevos este mes</p>
                        <h4 class="mb-0">{{ number_format($stats['new_this_month']) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-info-subtle border-0">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-info text-white rounded-circle fs-18">
                                <i class="ri-shield-check-line"></i>
                            </span>
                        </div>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Con Seguro</p>
                        <h4 class="mb-0">{{ number_format($stats['with_insurance']) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-warning-subtle border-0">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-warning text-white rounded-circle fs-18">
                                <i class="ri-user-line"></i>
                            </span>
                        </div>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Sin Seguro</p>
                        <h4 class="mb-0">{{ number_format($stats['without_insurance']) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos -->
<div class="row g-3 mb-3">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Pacientes por Mes</h5>
            </div>
            <div class="card-body">
                <div class="chart-h200">
                    <canvas id="patientsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Distribución por Género</h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="chart-h200">
                            <canvas id="genderChart"></canvas>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="legends">
                            <div class="d-flex align-items-center mb-2">
                                <span class="legend-dot" style="width:12px;height:12px;border-radius:50%;background:#405189;display:inline-block;margin-right:8px;"></span>
                                <span>Masculino: <strong>{{ $stats['male'] }}</strong></span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <span class="legend-dot" style="width:12px;height:12px;border-radius:50%;background:#0ab39c;display:inline-block;margin-right:8px;"></span>
                                <span>Femenino: <strong>{{ $stats['female'] }}</strong></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="legend-dot" style="width:12px;height:12px;border-radius:50%;background:#e5e7eb;display:inline-block;margin-right:8px;"></span>
                                <span>No especificado: <strong>{{ $stats['total'] - $stats['male'] - $stats['female'] }}</strong></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Seguros -->
<div class="card mb-3">
    <div class="card-header">
        <h5 class="card-title mb-0">Seguros Más Comunes</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Seguro</th>
                        <th>Pacientes</th>
                        <th>%</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topInsurances as $index => $insurance)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $insurance->name }}</td>
                        <td>{{ number_format($insurance->patients_count) }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="progress w-75 me-2" style="height:8px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $stats['total'] > 0 ? ($insurance->patients_count / $stats['total'] * 100) : 0 }}%"></div>
                                </div>
                                <span>{{ number_format(($insurance->patients_count / max($stats['total'], 1)) * 100, 1) }}%</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            No hay datos de seguros
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tabla de Pacientes -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Cédula</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Género</th>
                        <th>Seguro</th>
                        <th>Sucursal</th>
                        <th>Fecha Registro</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                    <tr>
                        <td>#{{ $patient->id }}</td>
                        <td>{{ $patient->full_name }}</td>
                        <td>{{ $patient->cedula ?? 'N/A' }}</td>
                        <td>{{ $patient->phone ?? 'N/A' }}</td>
                        <td>{{ $patient->email ?? 'N/A' }}</td>
                        <td>{{ $patient->gender ?? 'N/A' }}</td>
                        <td>
                            @if($patient->insurance)
                                <span class="badge bg-success">{{ $patient->insurance->name }}</span>
                            @else
                                <span class="badge bg-secondary">Sin seguro</span>
                            @endif
                        </td>
                        <td>{{ $patient->branch->name ?? 'N/A' }}</td>
                        <td>{{ $patient->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="ri-team-line d-block mb-2" style="font-size:48px;"></i>
                            No se encontraron pacientes
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $patients->appends(request()->query())->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de Pacientes por Mes
    const patientsData = @json($patientsByMonth);
    const ctx1 = document.getElementById('patientsChart').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: patientsData.map(d => d.month),
            datasets: [{
                label: 'Nuevos Pacientes',
                data: patientsData.map(d => d.total),
                backgroundColor: 'rgba(64,81,137,0.7)',
                borderColor: '#405189',
                borderWidth: 2,
                borderRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Pacientes: ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#e9ecef' },
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Gráfico de Distribución por Género
    const ctx2 = document.getElementById('genderChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Masculino', 'Femenino', 'No especificado'],
            datasets: [{
                data: [
                    {{ $stats['male'] }},
                    {{ $stats['female'] }},
                    {{ $stats['total'] - $stats['male'] - $stats['female'] }}
                ],
                backgroundColor: ['#405189', '#0ab39c', '#e5e7eb'],
                borderWidth: 3,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            cutout: '65%'
        }
    });
});
</script>
@endpush
@endsection