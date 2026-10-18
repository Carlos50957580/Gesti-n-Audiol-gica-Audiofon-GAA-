@extends('layouts.app')

@section('content')
<style>
    .doc-table-wrap { max-height: 72vh; overflow: auto; }
    .doc-table { font-size: .8rem; }
    .doc-table th {
        position: sticky; top: 0; z-index: 2;
        background: #f3f6f9; white-space: nowrap;
        font-size: .68rem; text-transform: uppercase; letter-spacing: .06em;
        border-bottom: 2px solid #e1e6ee;
    }
    .doc-table td { white-space: nowrap; vertical-align: middle; }
    .doc-table td.wrap { white-space: normal; min-width: 260px; }
    .doc-table tfoot td { position: sticky; bottom: 0; background: #f3f6f9; font-weight: 700; }
    .svc-line { display: block; line-height: 1.35; }
</style>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-0">Facturación por Médico</h4>
            <div class="page-title-right">
                <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-xl-3 col-md-4">
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
            <div class="col-xl-2 col-md-4">
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
            <div class="col-xl-3 col-md-4">
                <label class="form-label">Servicio / Estudio</label>
                <select name="service_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach($servicesList as $service)
                        <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                            {{ $service->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-xl-2 col-md-2">
                <label class="form-label">Desde</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-xl-2 col-md-2">
                <label class="form-label">Hasta</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-xl-2 col-md-4">
                <label class="form-label">Estado</label>
                <select name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="pendiente" {{ request('status') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="pagada" {{ request('status') == 'pagada' ? 'selected' : '' }}>Pagada</option>
                    <option value="cancelada" {{ request('status') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            <div class="col-xl-2 col-md-4">
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
            <div class="col-xl-2 col-md-4">
                <label class="form-label">Seguro</label>
                <select name="insurance_id" class="form-select">
                    <option value="">Todos</option>
                    <option value="null" {{ request('insurance_id') == 'null' ? 'selected' : '' }}>Sin seguro</option>
                    @foreach($insuranceList as $ins)
                        <option value="{{ $ins->id }}" {{ request('insurance_id') == $ins->id ? 'selected' : '' }}>
                            {{ $ins->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-xl-4 col-md-8">
                <label class="form-label">Buscar</label>
                <input type="text" name="search" class="form-control"
                       placeholder="Paciente, cédula, N° factura, NCF..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-xl-2 col-md-4 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="ri-search-line"></i> Filtrar
                </button>
                <a href="{{ route('admin.reports.doctors') }}" class="btn btn-light" title="Limpiar">
                    <i class="ri-refresh-line"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-3">
    <div class="col-xl-3 col-md-6">
        <div class="card bg-primary-subtle border-0 mb-0">
            <div class="card-body">
                <p class="text-muted mb-1">Facturas</p>
                <h4 class="mb-0">{{ number_format($stats['total_invoices']) }}</h4>
                <small class="text-muted">{{ number_format($stats['cancelled']) }} canceladas</small>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-info-subtle border-0 mb-0">
            <div class="card-body">
                <p class="text-muted mb-1">Total facturado</p>
                <h4 class="mb-0">RD$ {{ number_format($stats['total_amount'], 2) }}</h4>
                <small class="text-muted">Sin canceladas</small>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-success-subtle border-0 mb-0">
            <div class="card-body">
                <p class="text-muted mb-1">Cobrado</p>
                <h4 class="mb-0">RD$ {{ number_format($stats['total_paid'], 2) }}</h4>
                <small class="text-muted">Facturas pagadas</small>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-warning-subtle border-0 mb-0">
            <div class="card-body">
                <p class="text-muted mb-1">Por cobrar</p>
                <h4 class="mb-0">RD$ {{ number_format($stats['total_pending'], 2) }}</h4>
                <small class="text-muted">Facturas pendientes</small>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-4">
        <div class="card border-0 shadow-sm mb-0">
            <div class="card-body">
                <p class="text-muted mb-1">Impuestos</p>
                <h5 class="mb-0">RD$ {{ number_format($stats['total_tax'], 2) }}</h5>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-4">
        <div class="card border-0 shadow-sm mb-0">
            <div class="card-body">
                <p class="text-muted mb-1">Descuento por seguro</p>
                <h5 class="mb-0">RD$ {{ number_format($stats['insurance_discount'], 2) }}</h5>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-4">
        <div class="card border-0 shadow-sm mb-0">
            <div class="card-body">
                <p class="text-muted mb-1">Honorarios médicos</p>
                <h5 class="mb-0">RD$ {{ number_format($stats['doctor_fees'], 2) }}</h5>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos -->
<div class="row g-3 mb-3">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Facturado por Médico</h5>
            </div>
            <div class="card-body">
                <div style="height:280px;">
                    <canvas id="doctorChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Cobrado vs Por cobrar</h5>
            </div>
            <div class="card-body">
                <div style="height:280px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla grande -->
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">Detalle de facturas por médico</h5>
        <span class="text-muted" style="font-size:.85rem;">
            {{ number_format($invoices->total()) }} facturas · desliza la tabla para ver todas las columnas
        </span>
    </div>
    <div class="card-body p-0">
        <div class="doc-table-wrap">
            <table class="table table-hover table-bordered mb-0 doc-table">
                <thead>
                    <tr>
                        <th>N° Factura</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Médico</th>
                        <th>Sucursal</th>
                        <th>Paciente</th>
                        <th>Cédula</th>
                        <th>Teléfono</th>
                        <th>Seguro</th>
                        <th>N° Autorización</th>
                        <th>Estudios / Servicios</th>
                        <th class="text-center">Cant.</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">Impuestos</th>
                        <th class="text-end">Total c/ impuesto</th>
                        <th class="text-end">Desc. seguro</th>
                        <th class="text-end">Total</th>
                        <th>Recibo</th>
                        <th>Fecha pago</th>
                        <th>Métodos de pago</th>
                        <th class="text-end">Efectivo</th>
                        <th class="text-end">Tarjeta</th>
                        <th class="text-end">Transferencia</th>
                        <th>Ref. tarjeta</th>
                        <th>Ref. transferencia</th>
                        <th>Cobrado por</th>
                        <th>Notas del cobro</th>
                        <th>Tipo comprobante</th>
                        <th>NCF</th>
                        <th>e-NCF</th>
                        <th>RNC cliente</th>
                        <th>Razón social</th>
                        <th>Estado DGII</th>
                        <th>Track ID</th>
                        <th class="text-end">Honorario médico</th>
                        <th>Estado honorario</th>
                        <th>Facturado por</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $statusColors = ['pendiente' => 'warning', 'pagada' => 'success', 'cancelada' => 'danger'];
                        $ncfLabels = [
                            'consumidor_final' => 'Consumidor final',
                            'credito_fiscal'   => 'Crédito fiscal',
                            'gubernamental'    => 'Gubernamental',
                            'regimen_especial' => 'Régimen especial',
                        ];
                        $feeLabels = ['pending' => 'Pendiente', 'paid' => 'Pagado', 'cancelled' => 'Cancelado'];
                    @endphp

                    @forelse($invoices as $invoice)
                        @php
                            $rec      = $invoice->receipt;
                            $fees     = $invoice->doctorFees;
                            $feeTotal = $fees->where('status', '!=', 'cancelled')->sum('fee_amount');
                            if ($fees->isEmpty()) {
                                $feeStatus = null;
                            } elseif ($fees->where('status', 'pending')->count()) {
                                $feeStatus = 'pending';
                            } elseif ($fees->where('status', 'paid')->count()) {
                                $feeStatus = 'paid';
                            } else {
                                $feeStatus = 'cancelled';
                            }
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('invoices.show', $invoice->id) }}" class="fw-bold" target="_blank">
                                    {{ $invoice->invoice_number }}
                                </a>
                            </td>
                            <td>{{ $invoice->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge bg-{{ $statusColors[$invoice->status] ?? 'secondary' }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                            <td class="fw-semibold">{{ $invoice->doctor->name ?? 'Sin médico' }}</td>
                            <td>{{ $invoice->branch->name ?? '—' }}</td>
                            <td>{{ $invoice->patient->full_name ?? 'N/A' }}</td>
                            <td>{{ $invoice->patient->cedula ?? '—' }}</td>
                            <td>{{ $invoice->patient->phone ?? '—' }}</td>
                            <td>{{ $invoice->insurance->name ?? 'Sin seguro' }}</td>
                            <td>{{ $invoice->authorization_number ?: '—' }}</td>
                            <td class="wrap">
                                @forelse($invoice->items as $item)
                                    <span class="svc-line">
                                        • {{ $item->service->name ?? 'Servicio eliminado' }}
                                        @if($item->service?->category)
                                            <span class="badge bg-light text-muted border">{{ $item->service->category->name }}</span>
                                        @endif
                                        <strong>×{{ $item->quantity }}</strong>
                                        <span class="text-muted">(RD$ {{ number_format($item->subtotal, 2) }})</span>
                                    </span>
                                @empty
                                    —
                                @endforelse
                            </td>
                            <td class="text-center">{{ $invoice->items->sum('quantity') }}</td>
                            <td class="text-end">{{ number_format($invoice->subtotal, 2) }}</td>
                            <td class="text-end">{{ number_format($invoice->tax_amount, 2) }}</td>
                            <td class="text-end">{{ number_format($invoice->total_with_tax, 2) }}</td>
                            <td class="text-end">
                                {{ $invoice->insurance_discount > 0 ? '-' . number_format($invoice->insurance_discount, 2) : '—' }}
                            </td>
                            <td class="text-end fw-bold">{{ number_format($invoice->total, 2) }}</td>

                            {{-- Pago --}}
                            <td>{{ $rec?->receipt_number ?? '—' }}</td>
                            <td>{{ $rec ? $rec->created_at->format('d/m/Y H:i') : '—' }}</td>
                            <td>{{ $rec?->payment_summary ?? '—' }}</td>
                            <td class="text-end">{{ $rec && $rec->cash_amount ? number_format($rec->cash_amount, 2) : '—' }}</td>
                            <td class="text-end">{{ $rec && $rec->card_amount ? number_format($rec->card_amount, 2) : '—' }}</td>
                            <td class="text-end">{{ $rec && $rec->transfer_amount ? number_format($rec->transfer_amount, 2) : '—' }}</td>
                            <td>{{ $rec?->card_reference ?: '—' }}</td>
                            <td>{{ $rec?->transfer_reference ?: '—' }}</td>
                            <td>{{ $rec?->user?->name ?? '—' }}</td>
                            <td>{{ $rec?->notes ?: '—' }}</td>

                            {{-- Fiscal --}}
                            <td>{{ $ncfLabels[$invoice->ncf_type] ?? '—' }}</td>
                            <td>{{ $invoice->ncf ?: '—' }}</td>
                            <td>{{ $invoice->encf ?: '—' }}</td>
                            <td>{{ $invoice->customer_rnc ?: '—' }}</td>
                            <td>{{ $invoice->customer_business_name ?: '—' }}</td>
                            <td>
                                @if($invoice->estado_dgii)
                                    <span class="badge {{ $invoice->estado_dgii === 'aceptado' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $invoice->estado_dgii)) }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $invoice->track_id ?: '—' }}</td>

                            {{-- Honorarios --}}
                            <td class="text-end">{{ $feeStatus ? number_format($feeTotal, 2) : '—' }}</td>
                            <td>
                                @if($feeStatus)
                                    <span class="badge bg-{{ $feeStatus === 'paid' ? 'success' : ($feeStatus === 'pending' ? 'warning' : 'danger') }}">
                                        {{ $feeLabels[$feeStatus] }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $invoice->user->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="36" class="text-center text-muted py-5">
                                <i class="ri-file-list-3-line d-block mb-2" style="font-size:48px;"></i>
                                No se encontraron facturas con esos filtros
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if($invoices->count())
                @php
                    $pageInvoices = $invoices->getCollection();
                    $pageValid    = $pageInvoices->where('status', '!=', 'cancelada');
                @endphp
                <tfoot>
                    <tr>
                        <td colspan="11" class="text-end">Totales de esta página (sin canceladas)</td>
                        <td class="text-center">{{ $pageValid->sum(fn($i) => $i->items->sum('quantity')) }}</td>
                        <td class="text-end">{{ number_format($pageValid->sum('subtotal'), 2) }}</td>
                        <td class="text-end">{{ number_format($pageValid->sum('tax_amount'), 2) }}</td>
                        <td class="text-end">{{ number_format($pageValid->sum('total_with_tax'), 2) }}</td>
                        <td class="text-end">{{ number_format($pageValid->sum('insurance_discount'), 2) }}</td>
                        <td class="text-end">{{ number_format($pageValid->sum('total'), 2) }}</td>
                        <td colspan="17"></td>
                        <td class="text-end">
                            {{ number_format($pageValid->sum(fn($i) => $i->doctorFees->where('status', '!=', 'cancelled')->sum('fee_amount')), 2) }}
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        <div class="p-3">
            {{ $invoices->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const colors = ['#405189', '#0ab39c', '#f7b84b', '#f06548', '#7066e0', '#299cdb', '#34c38f', '#f1615c', '#5b73e8', '#6c757d'];
    const money = v => 'RD$ ' + Number(v).toLocaleString('es-DO', {minimumFractionDigits: 2});

    // Facturado por médico
    const byDoctor = @json($byDoctor);
    new Chart(document.getElementById('doctorChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: byDoctor.map(d => d.name),
            datasets: [{
                label: 'Facturado (RD$)',
                data: byDoctor.map(d => d.total_amount),
                backgroundColor: byDoctor.map((_, i) => colors[i % colors.length]),
                borderRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => money(ctx.parsed.y) + ' · ' + byDoctor[ctx.dataIndex].invoices_count + ' facturas'
                    }
                }
            },
            scales: {
                x: { grid: { display: false } },
                y: {
                    grid: { color: '#e9ecef' },
                    ticks: { callback: v => 'RD$ ' + Number(v).toLocaleString('es-DO') }
                }
            }
        }
    });

    // Cobrado vs por cobrar
    const byStatus = @json($byStatus);
    new Chart(document.getElementById('statusChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Cobrado', 'Por cobrar'],
            datasets: [{
                data: [byStatus.pagada, byStatus.pendiente],
                backgroundColor: ['#0ab39c', '#f7b84b'],
                borderWidth: 3,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: { position: 'bottom' },
                tooltip: { callbacks: { label: ctx => money(ctx.parsed) } }
            }
        }
    });
});
</script>
@endpush
@endsection