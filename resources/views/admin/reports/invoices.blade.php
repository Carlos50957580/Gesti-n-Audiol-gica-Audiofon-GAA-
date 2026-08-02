@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-0">Reporte de Facturación</h4>
            <div class="page-title-right">
                <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line"></i> Volver
                </a>
                <a href="{{ route('admin.reports.export', ['type' => 'invoices'] + request()->all()) }}" class="btn btn-success">
                    <i class="ri-file-excel-line"></i> Exportar
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reports.invoices') }}" class="row g-3">
            <!-- Fila 1 -->
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
                    <option value="pendiente" {{ request('status') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="pagada" {{ request('status') == 'pagada' ? 'selected' : '' }}>Pagada</option>
                    <option value="cancelada" {{ request('status') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
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
                <label class="form-label">Buscar</label>
                <input type="text" name="search" class="form-control" placeholder="#Factura, NCF, paciente..." value="{{ request('search') }}">
            </div>

            <!-- Fila 2 -->
            <div class="col-md-2">
                <label class="form-label">Recepcionista</label>
                <select name="user_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Seguro Médico</label>
                <select name="insurance_id" class="form-select">
                    <option value="">Todos</option>
                    <option value="null" {{ request('insurance_id') == 'null' ? 'selected' : '' }}>Sin Seguro</option>
                    @foreach($insuranceList as $insurance)
                        <option value="{{ $insurance->id }}" {{ request('insurance_id') == $insurance->id ? 'selected' : '' }}>
                            {{ $insurance->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Método de Pago</label>
                <select name="payment_method" class="form-select">
                    <option value="">Todos</option>
                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Efectivo</option>
                    <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Tarjeta</option>
                    <option value="transfer" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>Transferencia</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Con NCF</label>
                <select name="with_ncf" class="form-select">
                    <option value="">Todos</option>
                    <option value="1" {{ request('with_ncf') == '1' ? 'selected' : '' }}>Con NCF</option>
                    <option value="0" {{ request('with_ncf') == '0' ? 'selected' : '' }}>Sin NCF</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tiene Seguro</label>
                <select name="has_insurance" class="form-select">
                    <option value="">Todos</option>
                    <option value="1" {{ request('has_insurance') == '1' ? 'selected' : '' }}>Con Seguro</option>
                    <option value="0" {{ request('has_insurance') == '0' ? 'selected' : '' }}>Sin Seguro</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-1">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="ri-search-line"></i> Filtrar
                </button>
                <a href="{{ route('admin.reports.invoices') }}" class="btn btn-secondary w-100">
                    <i class="ri-refresh-line"></i> Limpiar
                </a>
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
                                <i class="ri-file-text-line"></i>
                            </span>
                        </div>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Total Facturas</p>
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
                                <i class="ri-money-dollar-circle-line"></i>
                            </span>
                        </div>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Total Facturado</p>
                        <h4 class="mb-0">RD$ {{ number_format($stats['total_amount'], 2) }}</h4>
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
                        <p class="text-muted mb-1">Pendiente de Cobro</p>
                        <h4 class="mb-0">RD$ {{ number_format($stats['total_pending'], 2) }}</h4>
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
                                <i class="ri-receipt-line"></i>
                            </span>
                        </div>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Con NCF</p>
                        <h4 class="mb-0">{{ number_format($stats['with_ncf']) }}</h4>
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
                <h5 class="card-title mb-0">Métodos de Pago</h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="chart-h200">
                            <canvas id="paymentMethodChart"></canvas>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="legends">
                            <div class="d-flex align-items-center mb-2">
                                <span class="legend-dot" style="width:12px;height:12px;border-radius:50%;background:#0ab39c;display:inline-block;margin-right:8px;"></span>
                                <span>Efectivo: <strong>RD$ {{ number_format($paymentMethods['cash'], 2) }}</strong></span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <span class="legend-dot" style="width:12px;height:12px;border-radius:50%;background:#405189;display:inline-block;margin-right:8px;"></span>
                                <span>Tarjeta: <strong>RD$ {{ number_format($paymentMethods['card'], 2) }}</strong></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="legend-dot" style="width:12px;height:12px;border-radius:50%;background:#f7b84b;display:inline-block;margin-right:8px;"></span>
                                <span>Transferencia: <strong>RD$ {{ number_format($paymentMethods['transfer'], 2) }}</strong></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Facturación por Recepcionista</h5>
            </div>
            <div class="card-body">
                <div class="chart-h200">
                    <canvas id="recepcionistaChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Seguros -->
<div class="card mb-3">
    <div class="card-header">
        <h5 class="card-title mb-0">Facturación por Seguro Médico</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Seguro</th>
                        <th>Facturas</th>
                        <th>Total Facturado</th>
                        <th>Descuento</th>
                        <th>%</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($insurances as $index => $insurance)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $insurance->name }}</strong></td>
                        <td>{{ number_format($insurance->invoices_count) }}</td>
                        <td>RD$ {{ number_format($insurance->invoices_sum_total ?? 0, 2) }}</td>
                        <td>RD$ {{ number_format($insurance->invoices_sum_insurance_discount ?? 0, 2) }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="progress w-75 me-2" style="height:8px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $stats['total_amount'] > 0 ? (($insurance->invoices_sum_total ?? 0) / $stats['total_amount'] * 100) : 0 }}%"></div>
                                </div>
                                <span>{{ number_format(($insurance->invoices_sum_total ?? 0) / max($stats['total_amount'], 1) * 100, 1) }}%</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No hay datos de seguros
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tabla de Facturas -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Paciente</th>
                        <th>Médico</th>
                        <th>Recepcionista</th>
                        <th>Sucursal</th>
                        <th>Seguro</th>
                        <th>Subtotal</th>
                        <th>Impuestos</th>
                        <th>Descuento</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                    <tr>
                        <td>#{{ $invoice->id }}</td>
                        <td>{{ $invoice->patient->full_name ?? 'N/A' }}</td>
                        <td>{{ $invoice->doctor->name ?? 'N/A' }}</td>
                        <td>{{ $invoice->user->name ?? 'N/A' }}</td>
                        <td>{{ $invoice->branch->name ?? 'N/A' }}</td>
                        <td>
                            @if($invoice->insurance)
                                <span class="badge bg-success">{{ $invoice->insurance->name }}</span>
                            @else
                                <span class="badge bg-secondary">Sin seguro</span>
                            @endif
                        </td>
                        <td>RD$ {{ number_format($invoice->subtotal, 2) }}</td>
                        <td>RD$ {{ number_format($invoice->tax_amount, 2) }}</td>
                        <td>RD$ {{ number_format($invoice->insurance_discount, 2) }}</td>
                        <td><strong>RD$ {{ number_format($invoice->total, 2) }}</strong></td>
                        <td>
                            <span class="badge bg-{{ $invoice->status === 'pagada' ? 'success' : ($invoice->status === 'pendiente' ? 'warning' : 'danger') }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                        <td>{{ $invoice->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center text-muted py-4">
                            <i class="ri-file-list-3-line d-block mb-2" style="font-size:48px;"></i>
                            No se encontraron facturas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $invoices->appends(request()->query())->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Métodos de Pago
    const paymentMethods = @json($paymentMethods);
    const ctx1 = document.getElementById('paymentMethodChart').getContext('2d');
    new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: ['Efectivo', 'Tarjeta', 'Transferencia'],
            datasets: [{
                data: [
                    paymentMethods.cash || 0,
                    paymentMethods.card || 0,
                    paymentMethods.transfer || 0
                ],
                backgroundColor: ['#0ab39c', '#405189', '#f7b84b'],
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

    // Facturación por Recepcionista
    const recepcionistas = @json($recepcionistas);
    const ctx2 = document.getElementById('recepcionistaChart').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: recepcionistas.map(d => d.name),
            datasets: [{
                label: 'Total Facturado (RD$)',
                data: recepcionistas.map(d => parseFloat(d.invoices_sum_total || 0)),
                backgroundColor: ['#405189', '#0ab39c', '#f7b84b', '#f06548', '#7066e0', '#299cdb'],
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
                            return 'RD$ ' + context.parsed.y.toLocaleString('es-DO', {minimumFractionDigits: 2}) +
                                   ' (' + (recepcionistas[context.dataIndex]?.invoices_count || 0) + ' facturas)';
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