<x-app-layout>
@section('title', 'Reporte de Ventas de Productos')

<div class="page-content">
    <div class="container-fluid">
        {{-- Header --}}
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ri-bar-chart-2-line me-1"></i>Reporte de Ventas de Productos</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Reportes de Productos</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
       <div class="card">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label">Desde</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from', now()->toDateString()) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Hora desde</label>
                <input type="time" name="time_from" class="form-control" value="{{ request('time_from', '00:00') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Hasta</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to', now()->toDateString()) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Hora hasta</label>
                <input type="time" name="time_to" class="form-control" value="{{ request('time_to', '23:59') }}">
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
                <label class="form-label">Usuario</label>
                <select name="user_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ (string) $userId === (string) $u->id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-search-line me-1"></i>Filtrar
                </button>
                <a href="{{ url()->current() }}" class="btn btn-light">
                    <i class="ri-refresh-line me-1"></i>Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

        {{-- KPIs principales --}}
        <div class="row g-3 mb-3">
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title rounded-circle bg-primary-subtle text-primary fs-4">
                                    <i class="ri-file-list-3-line"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-0 small">Total Facturas</p>
                                <h3 class="mb-0">{{ number_format($totalInvoices) }}</h3>
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
                                <span class="avatar-title rounded-circle bg-info-subtle text-info fs-4">
                                    <i class="ri-money-dollar-circle-line"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-0 small">Total Facturado</p>
                                <h3 class="mb-0">RD$ {{ number_format($totalFacturado, 2) }}</h3>
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
                                    <i class="ri-check-double-line"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-0 small">Total Cobrado</p>
                                <h3 class="mb-0 text-success">RD$ {{ number_format($totalCobrado, 2) }}</h3>
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
                                    <i class="ri-time-line"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-0 small">Saldo Pendiente</p>
                                <h3 class="mb-0 text-danger">RD$ {{ number_format($totalPendiente, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Estado de facturas --}}
        <div class="row g-3 mb-3">
            @foreach([
                ['pendiente', 'Pendientes', 'warning', 'ri-time-line'],
                ['pagada_parcial', 'Pagadas Parcial', 'info', 'ri-loader-4-line'],
                ['pagada', 'Pagadas', 'success', 'ri-checkbox-circle-line'],
                ['cancelada', 'Canceladas', 'danger', 'ri-close-circle-line'],
            ] as [$key, $label, $color, $icon])
                @php $row = $byStatus[$key] ?? null; @endphp
                <div class="col-xl-3 col-md-6">
                    <div class="card border-{{ $color }}-subtle">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-0 small">
                                        <i class="{{ $icon }} me-1 text-{{ $color }}"></i>{{ $label }}
                                    </p>
                                    <h4 class="mb-0">{{ $row->count ?? 0 }}</h4>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block">Total</small>
                                    <strong class="text-{{ $color }}">RD$ {{ number_format($row->total ?? 0, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Cobros por método + Gráfico de ventas --}}
        <div class="row g-3 mb-3">
            <div class="col-xl-4">
                <div class="card h-100">
                    <div class="card-header"><h5 class="card-title mb-0">Cobros por Método de Pago</h5></div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3 p-3 rounded" style="background:#e8f8f0;">
                            <div>
                                <i class="ri-money-bill-line fs-4 text-success"></i>
                                <div class="small text-muted mt-1">Efectivo</div>
                                <strong class="fs-5">RD$ {{ number_format($cobros->efectivo, 2) }}</strong>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mb-3 p-3 rounded" style="background:#f0f4ff;">
                            <div>
                                <i class="ri-bank-card-line fs-4 text-primary"></i>
                                <div class="small text-muted mt-1">Tarjeta</div>
                                <strong class="fs-5">RD$ {{ number_format($cobros->tarjeta, 2) }}</strong>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mb-3 p-3 rounded" style="background:#f4f0ff;">
                            <div>
                                <i class="ri-exchange-dollar-line fs-4" style="color:#7c3aed;"></i>
                                <div class="small text-muted mt-1">Transferencia</div>
                                <strong class="fs-5">RD$ {{ number_format($cobros->transferencia, 2) }}</strong>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>TOTAL COBRADO:</strong>
                            <strong class="fs-4 text-success">RD$ {{ number_format($cobros->total, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Ventas por Día</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="salesChart" height="120"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ventas por sucursal + Ventas por categoría --}}
        <div class="row g-3 mb-3">
            <div class="col-xl-6">
                <div class="card h-100">
                    <div class="card-header"><h5 class="card-title mb-0">Ventas por Sucursal</h5></div>
                    <div class="card-body">
                        @forelse($byBranch as $br)
                            @php
                                $pct = $totalFacturado > 0 ? ($br->total / $totalFacturado * 100) : 0;
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-semibold">{{ $br->branch->name ?? 'Sin sucursal' }}</span>
                                    <span>RD$ {{ number_format($br->total, 2) }}</span>
                                </div>
                                <div class="progress" style="height:8px;">
                                    <div class="progress-bar" style="width:{{ $pct }}%;background:linear-gradient(90deg,#405189,#0ab39c);"></div>
                                </div>
                                <small class="text-muted">{{ $br->count }} facturas · {{ number_format($pct, 1) }}%</small>
                            </div>
                        @empty
                            <p class="text-muted text-center py-4 mb-0">Sin datos en el período</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card h-100">
                    <div class="card-header"><h5 class="card-title mb-0">Ventas por Categoría</h5></div>
                    <div class="card-body">
                        <div class="d-flex justify-content-center mb-3">
                            <div style="width:220px;height:220px;">
                                <canvas id="categoryChart"></canvas>
                            </div>
                        </div>
                        @foreach($byCategory as $cat)
                            <div class="d-flex justify-content-between mb-2 small">
                                <span>
                                    <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:{{ $cat->category_color }};margin-right:.4rem;"></span>
                                    {{ $cat->category_name }}
                                </span>
                                <strong>RD$ {{ number_format($cat->revenue, 2) }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Top productos --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ri-trophy-line me-1 text-warning"></i>Top 10 Productos Más Vendidos
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:60px;">#</th>
                                <th>Código</th>
                                <th>Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Ingresos</th>
                                <th>Participación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $i => $prod)
                                @php
                                    $pct = $maxProductQty > 0 ? ($prod->qty / $maxProductQty * 100) : 0;
                                @endphp
                                <tr>
                                    <td>
                                        <span class="badge bg-{{ $i < 3 ? 'warning' : 'secondary' }}-subtle text-{{ $i < 3 ? 'warning' : 'secondary' }}">
                                            #{{ $i + 1 }}
                                        </span>
                                    </td>
                                    <td><code>{{ $prod->code }}</code></td>
                                    <td class="fw-semibold">{{ $prod->name }}</td>
                                    <td class="text-center"><strong>{{ $prod->qty }}</strong></td>
                                    <td class="text-end fw-bold text-success">RD$ {{ number_format($prod->revenue, 2) }}</td>
                                    <td style="min-width:150px;">
                                        <div class="progress" style="height:6px;">
                                            <div class="progress-bar" style="width:{{ $pct }}%;background:linear-gradient(90deg,#405189,#0ab39c);"></div>
                                        </div>
                                        <small class="text-muted">{{ number_format($pct, 1) }}%</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="ri-shopping-bag-line fs-1 opacity-25 d-block mb-2"></i>
                                        No hay productos vendidos en el período
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Listado de ventas (DataTable) --}}
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="ri-file-list-3-line me-1"></i>Listado de Ventas del Período
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="salesTable" class="table table-hover align-middle" style="width:100%;">
                <thead class="table-light">
                    <tr>
                        <th>No. Factura</th>
                        <th>Fecha</th>
                        <th>Paciente</th>
                        <th>Sucursal</th>
                        <th>Vendido por</th>
                        <th>Estado</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Pagado</th>
                        <th class="text-end">Pendiente</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $inv)
                        @php
                            $statusColors = [
                                'pendiente' => 'warning',
                                'pagada_parcial' => 'info',
                                'pagada' => 'success',
                                'cancelada' => 'danger',
                            ];
                            $statusLabels = [
                                'pendiente' => 'Pendiente',
                                'pagada_parcial' => 'Pago Parcial',
                                'pagada' => 'Pagada',
                                'cancelada' => 'Cancelada',
                            ];
                            $color = $statusColors[$inv->status] ?? 'secondary';
                            $label = $statusLabels[$inv->status] ?? $inv->status;
                            $pendiente = max(0, $inv->total - $inv->paid_amount);
                        @endphp
                        <tr>
                            <td><code>{{ $inv->number }}</code></td>
                            <td data-order="{{ $inv->created_at->timestamp }}">
                                {{ $inv->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td>{{ $inv->patient->first_name ?? '' }} {{ $inv->patient->last_name ?? '' }}</td>
                            <td>{{ $inv->branch->name ?? '—' }}</td>
                            <td>{{ $inv->user->name ?? '—' }}</td>
                            <td>
                                <span class="badge bg-{{ $color }}-subtle text-{{ $color }}">{{ $label }}</span>
                            </td>
                            <td class="text-end">RD$ {{ number_format($inv->total, 2) }}</td>
                            <td class="text-end text-success">RD$ {{ number_format($inv->paid_amount, 2) }}</td>
                            <td class="text-end {{ $pendiente > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">
                                RD$ {{ number_format($pendiente, 2) }}
                            </td>
                            <td class="text-center">
                                <a href="{{ url('/product-invoices/'.$inv->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="ri-eye-line"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
    Chart.defaults.font.family = "'Inter', sans-serif";

    // ── Gráfico de ventas por día ──────────────────────
    const salesData = @json($chartDays);
    new Chart(document.getElementById('salesChart'), {
        type: 'bar',
        data: {
            labels: salesData.map(d => d.label),
            datasets: [{
                data: salesData.map(d => d.total),
                backgroundColor: 'rgba(64,81,137,.72)',
                borderColor: '#405189',
                borderWidth: 1.5,
                borderRadius: 5,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: c => 'RD$ ' + c.parsed.y.toLocaleString('es-DO', { minimumFractionDigits: 2 })
                    }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f0f2f7' },
                    ticks: {
                        callback: v => v >= 1000 ? 'RD$' + (v / 1000).toFixed(0) + 'k' : 'RD$' + v
                    }
                }
            }
        }
    });

    // ── Gráfico de ventas por categoría ────────────────
    const catData = @json($byCategory);
    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: catData.map(c => c.category_name),
            datasets: [{
                data: catData.map(c => parseFloat(c.revenue)),
                backgroundColor: catData.map(c => c.category_color || '#405189'),
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: c => c.label + ': RD$ ' + c.parsed.toLocaleString('es-DO', { minimumFractionDigits: 2 })
                    }
                }
            }
        }
    });
</script>
@endpush
</x-app-layout>