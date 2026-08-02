@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-0">Reporte de Honorarios Médicos</h4>
            <div class="page-title-right">
                <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line"></i> Volver
                </a>
                <a href="{{ route('admin.reports.export', ['type' => 'fees'] + request()->all()) }}" class="btn btn-success">
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
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendientes</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Pagados</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelados</option>
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
                                <i class="ri-money-dollar-circle-line"></i>
                            </span>
                        </div>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Total Honorarios</p>
                        <h4 class="mb-0">RD$ {{ number_format($stats['total_amount'], 2) }}</h4>
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
                                <i class="ri-checkbox-circle-line"></i>
                            </span>
                        </div>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Pagados</p>
                        <h4 class="mb-0">RD$ {{ number_format($stats['paid'], 2) }}</h4>
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
                                <i class="ri-time-line"></i>
                            </span>
                        </div>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Pendientes</p>
                        <h4 class="mb-0">RD$ {{ number_format($stats['pending'], 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-danger-subtle border-0">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-danger text-white rounded-circle fs-18">
                                <i class="ri-close-circle-line"></i>
                            </span>
                        </div>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Cancelados</p>
                        <h4 class="mb-0">RD$ {{ number_format($stats['cancelled'], 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico -->
<div class="row g-3 mb-3">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Top Médicos por Honorarios</h5>
            </div>
            <div class="card-body">
                <div class="chart-h200">
                    <canvas id="topDoctorsFeesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Honorarios -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Médico</th>
                        <th>Paciente</th>
                        <th>Total Factura</th>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <th>Honorario</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fees as $fee)
                    <tr>
                        <td>#{{ $fee->id }}</td>
                        <td>{{ $fee->doctor->name ?? 'N/A' }}</td>
                        <td>{{ $fee->invoice->patient->full_name ?? 'N/A' }}</td>
                        <td>RD$ {{ number_format($fee->invoice_total, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $fee->calculation_type === 'percentage' ? 'info' : 'primary' }}">
                                {{ $fee->calculation_type === 'percentage' ? 'Porcentaje' : 'Monto Fijo' }}
                            </span>
                        </td>
                        <td>
                            {{ $fee->calculation_type === 'percentage' ? $fee->calculation_value . '%' : 'RD$ ' . number_format($fee->calculation_value, 2) }}
                        </td>
                        <td><strong>RD$ {{ number_format($fee->fee_amount, 2) }}</strong></td>
                        <td>
                            <span class="badge bg-{{ $fee->status === 'paid' ? 'success' : ($fee->status === 'pending' ? 'warning' : 'danger') }}">
                                {{ ucfirst($fee->status) }}
                            </span>
                        </td>
                        <td>{{ $fee->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="ri-money-dollar-circle-line d-block mb-2" style="font-size:48px;"></i>
                            No se encontraron honorarios
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $fees->appends(request()->query())->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const topDoctors = @json($topDoctors);
    const ctx = document.getElementById('topDoctorsFeesChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: topDoctors.map(d => d.doctor?.name || 'N/A'),
            datasets: [{
                label: 'Honorarios Pagados (RD$)',
                data: topDoctors.map(d => parseFloat(d.total_fees)),
                backgroundColor: ['#405189', '#0ab39c', '#f7b84b', '#f06548', '#7066e0', '#299cdb', '#34c38f', '#f1615c', '#5b73e8', '#f7b84b'],
                borderColor: '#fff',
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
                            return 'RD$ ' + context.parsed.y.toLocaleString('es-DO', {minimumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false }
                },
                y: {
                    grid: { color: '#e9ecef' },
                    ticks: {
                        callback: function(value) {
                            return 'RD$ ' + value.toLocaleString('es-DO');
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection