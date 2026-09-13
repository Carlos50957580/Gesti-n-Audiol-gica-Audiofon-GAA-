<x-app-layout>
@section('title', 'Cuadre de Caja')

<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="ri-safe-2-line me-1"></i>Cuadre de Caja - Productos</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Inicio</a></li>
                            <li class="breadcrumb-item active">Cuadre de Caja</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════ FILTROS ═══════════════ --}}
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ url('/product-reports/cashier') }}" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label">Desde</label>
                            <input type="date" name="date_from" class="form-control"
                                   value="{{ request('date_from', now()->toDateString()) }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Hasta</label>
                            <input type="date" name="date_to" class="form-control"
                                   value="{{ request('date_to', now()->toDateString()) }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Hora desde</label>
                            <input type="time" name="time_from" class="form-control"
                                   value="{{ request('time_from', '00:00') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Hora hasta</label>
                            <input type="time" name="time_to" class="form-control"
                                   value="{{ request('time_to', '23:59') }}">
                        </div>

                        @if($isAdmin)
                            <div class="col-md-2">
                                <label class="form-label">Sucursal</label>
                                <select name="branch_id" class="form-select">
                                    <option value="">Todas</option>
                                    @foreach($branches as $br)
                                        <option value="{{ $br->id }}" {{ (string) $branchId === (string) $br->id ? 'selected' : '' }}>
                                            {{ $br->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="col-md-2">
                            <label class="form-label">Método de pago</label>
                            <select name="payment_method" class="form-select">
                                <option value="">Todos</option>
                                <option value="efectivo" {{ $paymentMethod === 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                <option value="tarjeta" {{ $paymentMethod === 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                                <option value="transferencia" {{ $paymentMethod === 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                            </select>
                        </div>
                    </div>

                    @if($isAdmin)
    <div class="col-md-2">
        <label class="form-label">Cobrado por</label>
        <select name="user_id" class="form-select">
            <option value="">Todos</option>
            @foreach($cashierUsers as $cu)
                <option value="{{ $cu->id }}" {{ (string) $filterUserId === (string) $cu->id ? 'selected' : '' }}>
                    {{ $cu->name }}
                </option>
            @endforeach
        </select>
    </div>
@endif

                    <div class="row mt-3">
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-filter-3-line me-1"></i>Filtrar
                            </button>
                            <a href="{{ url('/product-reports/cashier') }}" class="btn btn-light">
                                <i class="ri-refresh-line me-1"></i>Limpiar
                            </a>
                            <button type="button" class="btn btn-success ms-auto" onclick="printWithFilters()">
                                <i class="ri-printer-line me-1"></i>Imprimir con estos filtros
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ═══════════════ KPIs ═══════════════ --}}
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <p class="text-muted mb-1">Efectivo</p>
                        <h3 class="text-success mb-0">RD$ {{ number_format($totals->efectivo, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <p class="text-muted mb-1">Tarjeta</p>
                        <h3 class="text-primary mb-0">RD$ {{ number_format($totals->tarjeta, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <p class="text-muted mb-1">Transferencia</p>
                        <h3 class="text-info mb-0">RD$ {{ number_format($totals->transferencia, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card" style="background:#f0f4ff;">
                    <div class="card-body text-center">
                        <p class="text-muted mb-1">Total Cobrado</p>
                        <h3 class="mb-0" style="color:#405189;">RD$ {{ number_format($totals->total, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        @if($isAdmin && $byUser->count())
        {{-- ═══════════════ RESUMEN POR RECEPCIONISTA ═══════════════ --}}
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Resumen por Recepcionista</h5></div>
            <div class="card-body table-responsive">
                <table class="table table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Usuario</th>
                            <th class="text-center">Recibos</th>
                            <th class="text-end">Efectivo</th>
                            <th class="text-end">Tarjeta</th>
                            <th class="text-end">Transferencia</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byUser as $bu)
                            <tr>
                                <td>{{ $bu->user->name ?? '—' }}</td>
                                <td class="text-center">{{ $bu->count }}</td>
                                <td class="text-end">RD$ {{ number_format($bu->efectivo, 2) }}</td>
                                <td class="text-end">RD$ {{ number_format($bu->tarjeta, 2) }}</td>
                                <td class="text-end">RD$ {{ number_format($bu->transferencia, 2) }}</td>
                                <td class="text-end fw-bold">RD$ {{ number_format($bu->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ═══════════════ DETALLE DE RECIBOS ═══════════════ --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Detalle de Recibos ({{ $receipts->count() }})</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Recibo</th>
                            <th>Fecha/Hora</th>
                            <th>Factura</th>
                            <th>Paciente</th>
                            <th>Cobrado por</th>
                            <th class="text-end">Efectivo</th>
                            <th class="text-end">Tarjeta</th>
                            <th class="text-end">Transfer.</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($receipts as $i => $rec)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td><code>{{ $rec->number }}</code></td>
                                <td>{{ $rec->created_at->format('d/m/Y H:i') }}</td>
                                <td><code>{{ $rec->invoice->number }}</code></td>
                                <td>{{ $rec->invoice->patient->first_name }} {{ $rec->invoice->patient->last_name }}</td>
                                <td>{{ $rec->user->name }}</td>
                                <td class="text-end">{{ $rec->cash_amount ? 'RD$ '.number_format($rec->cash_amount, 2) : '—' }}</td>
                                <td class="text-end">{{ $rec->card_amount ? 'RD$ '.number_format($rec->card_amount, 2) : '—' }}</td>
                                <td class="text-end">{{ $rec->transfer_amount ? 'RD$ '.number_format($rec->transfer_amount, 2) : '—' }}</td>
                                <td class="text-end fw-bold">RD$ {{ number_format($rec->total_paid, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-3">No hay cobros en el período con los filtros aplicados</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($receipts->count() > 0)
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td colspan="6" class="text-end">TOTALES</td>
                                <td class="text-end">RD$ {{ number_format($totals->efectivo, 2) }}</td>
                                <td class="text-end">RD$ {{ number_format($totals->tarjeta, 2) }}</td>
                                <td class="text-end">RD$ {{ number_format($totals->transferencia, 2) }}</td>
                                <td class="text-end">RD$ {{ number_format($totals->total, 2) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function printWithFilters() {
    const form = document.getElementById('filterForm');
    const params = new URLSearchParams(new FormData(form));
    window.open("{{ url('/product-reports/cashier/print') }}?" + params.toString(), '_blank');
}
</script>
@endpush
</x-app-layout>