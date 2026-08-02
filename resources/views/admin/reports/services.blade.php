@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-0">Reporte de Servicios</h4>
            <div class="page-title-right">
                <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line"></i> Volver
                </a>
                <a href="{{ route('admin.reports.export', ['type' => 'services'] + request()->all()) }}" class="btn btn-success">
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
            <div class="col-md-4">
                <label class="form-label">Categoría</label>
                <select name="category_id" class="form-select">
                    <option value="">Todas</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Estado</label>
                <select name="is_active" class="form-select">
                    <option value="">Todos</option>
                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Activos</option>
                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
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
                                <i class="ri-stethoscope-line"></i>
                            </span>
                        </div>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Total Servicios</p>
                        <h4 class="mb-0">{{ number_format($services->total()) }}</h4>
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
                        <p class="text-muted mb-1">Activos</p>
                        <h4 class="mb-0">{{ number_format($services->where('is_active', 1)->count()) }}</h4>
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
                                <i class="ri-close-circle-line"></i>
                            </span>
                        </div>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Inactivos</p>
                        <h4 class="mb-0">{{ number_format($services->where('is_active', 0)->count()) }}</h4>
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
                                <i class="ri-folder-line"></i>
                            </span>
                        </div>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Categorías</p>
                        <h4 class="mb-0">{{ number_format($categories->count()) }}</h4>
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
                <h5 class="card-title mb-0">Top 10 Servicios Más Facturados</h5>
            </div>
            <div class="card-body">
                <div class="chart-h200">
                    <canvas id="topServicesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Ingresos por Categoría</h5>
            </div>
            <div class="card-body">
                <div class="chart-h200">
                    <canvas id="categoryRevenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Servicios -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Duración</th>
                        <th>Historia Clínica</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                    <tr>
                        <td>#{{ $service->id }}</td>
                        <td><code>{{ $service->code ?? 'N/A' }}</code></td>
                        <td>{{ $service->name }}</td>
                        <td>{{ $service->category->name ?? 'Sin categoría' }}</td>
                        <td>RD$ {{ number_format($service->price, 2) }}</td>
                        <td>{{ $service->duration_minutes ? $service->duration_minutes . ' min' : 'N/A' }}</td>
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
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="ri-stethoscope-line d-block mb-2" style="font-size:48px;"></i>
                            No se encontraron servicios
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

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Top 10 Servicios Más Facturados
    const topServices = @json($topServices);
    const ctx1 = document.getElementById('topServicesChart').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: topServices.map(d => d.name.length > 20 ? d.name.substring(0, 20) + '...' : d.name),
            datasets: [{
                label: 'Ingresos (RD$)',
                data: topServices.map(d => parseFloat(d.total_revenue)),
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
                    grid: { display: false },
                    ticks: {
                        font: { size: 10 }
                    }
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

    // Ingresos por Categoría
    const categoryRevenue = @json($revenueByCategory);
    const colors = ['#405189', '#0ab39c', '#f7b84b', '#f06548', '#7066e0', '#299cdb', '#34c38f', '#f1615c', '#5b73e8', '#f7b84b'];
    const ctx2 = document.getElementById('categoryRevenueChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: categoryRevenue.map(d => d.name),
            datasets: [{
                data: categoryRevenue.map(d => parseFloat(d.total_revenue)),
                backgroundColor: colors.slice(0, categoryRevenue.length),
                borderWidth: 3,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 12,
                        font: { size: 11 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = ((context.parsed / total) * 100).toFixed(1);
                            return 'RD$ ' + context.parsed.toLocaleString('es-DO', {minimumFractionDigits: 2}) + ' (' + percentage + '%)';
                        }
                    }
                }
            },
            cutout: '60%'
        }
    });
});
</script>
@endpush
@endsection