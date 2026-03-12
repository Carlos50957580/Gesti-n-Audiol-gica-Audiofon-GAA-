<x-app-layout>
<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

<style>
/* ══════════════════════════════════════
   STAT CARDS
══════════════════════════════════════ */
.inv-stat {
    border: 1px solid rgba(64,81,137,.12); border-radius: .85rem;
    box-shadow: 0 2px 14px rgba(64,81,137,.06);
    overflow: hidden; position: relative;
    transition: transform .2s, box-shadow .2s, border-color .2s;
    background: rgba(255,255,255,.55);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}
.inv-stat:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(64,81,137,.12);
    border-color: rgba(64,81,137,.22);
    background: rgba(255,255,255,.75);
}
.inv-stat-body { padding: 1.25rem 1.4rem; position: relative; z-index: 1; }
.inv-stat-label {
    font-size: .7rem; font-weight: 700; letter-spacing: .1em;
    text-transform: uppercase; color: #8098bb; margin-bottom: .3rem;
}
.inv-stat-value { font-size: 1.65rem; font-weight: 800; color: #344563; line-height: 1.1; }
.inv-stat-sub   { font-size: .78rem; color: #8098bb; margin-top: .25rem; }
.inv-stat-icon  {
    position: absolute; right: 1.25rem; top: 50%; transform: translateY(-50%);
    width: 50px; height: 50px; border-radius: .65rem;
    display: flex; align-items: center; justify-content: center; font-size: 1.6rem;
}
.stat-total    { border-left: 3px solid #405189; }
.stat-pending  { border-left: 3px solid #f0932b; }
.stat-paid     { border-left: 3px solid #0ab39c; }
.stat-cancelled{ border-left: 3px solid #e74c3c; }
.stat-total    .inv-stat-icon { color: #405189; background: rgba(64,81,137,.08); }
.stat-pending  .inv-stat-icon { color: #f0932b; background: rgba(240,147,43,.08); }
.stat-paid     .inv-stat-icon { color: #0ab39c; background: rgba(10,179,156,.08); }
.stat-cancelled .inv-stat-icon{ color: #e74c3c; background: rgba(231,76,60,.08); }

/* ══════════════════════════════════════
   FILTER BAR
══════════════════════════════════════ */
.filter-bar {
    background: #fff; border-radius: .75rem;
    box-shadow: 0 2px 14px rgba(64,81,137,.08);
    padding: 1rem 1.25rem;
    display: flex; align-items: center; gap: .75rem; flex-wrap: wrap;
}
.filter-bar .filter-search {
    flex: 1; min-width: 220px; position: relative;
}
.filter-bar .filter-search .ri-search-line {
    position: absolute; left: .75rem; top: 50%; transform: translateY(-50%);
    color: #8098bb; font-size: 1rem; pointer-events: none;
}
.filter-bar .filter-search input {
    width: 100%; border: 1.5px solid #e2e8f0; border-radius: 2rem;
    padding: .5rem 1rem .5rem 2.2rem; font-size: .87rem; color: #344563;
    outline: none; transition: border-color .2s, box-shadow .2s; background: #f8faff;
}
.filter-bar .filter-search input:focus {
    border-color: #405189; box-shadow: 0 0 0 3px rgba(64,81,137,.1); background: #fff;
}
.filter-bar .filter-select {
    border: 1.5px solid #e2e8f0; border-radius: 2rem; background: #f8faff;
    padding: .48rem 2rem .48rem 1rem; font-size: .85rem; color: #344563;
    outline: none; cursor: pointer; transition: border-color .2s;
    appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24'%3E%3Cpath fill='%238098bb' d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right .7rem center;
    min-width: 145px;
}
.filter-bar .filter-select:focus { border-color: #405189; }
.filter-bar .btn-reset {
    border: 1.5px solid #e2e8f0; border-radius: 2rem; background: #f8faff;
    padding: .48rem .9rem; font-size: .82rem; color: #8098bb; cursor: pointer;
    transition: all .18s; white-space: nowrap;
    display: flex; align-items: center; gap: .3rem;
}
.filter-bar .btn-reset:hover { border-color: #e74c3c; color: #e74c3c; background: #fff5f5; }

/* ══════════════════════════════════════
   TABLE CARD
══════════════════════════════════════ */
.inv-card {
    border: none; border-radius: .85rem;
    box-shadow: 0 2px 18px rgba(64,81,137,.09);
    overflow: hidden; background: #fff;
}
.inv-card-header {
    padding: 1rem 1.4rem;
    display: flex; align-items: center; gap: .75rem; flex-wrap: wrap;
    border-bottom: 1px solid #f0f2f7;
}
.inv-card-header h5 { font-size: .95rem; font-weight: 700; color: #344563; margin: 0; flex-grow: 1; }

.inv-table th {
    font-size: .68rem; font-weight: 700; letter-spacing: .09em; text-transform: uppercase;
    color: #8098bb; border-bottom: 2px solid #e9ecef; padding: .85rem 1rem; white-space: nowrap;
    background: #fafbff;
}
.inv-table td { padding: .85rem 1rem; vertical-align: middle; border-bottom: 1px solid #f3f5f9; }
.inv-table tbody tr:last-child td { border-bottom: none; }
.inv-table tbody tr { transition: background .15s; }
.inv-table tbody tr:hover { background: #f8faff; }

/* Invoice number chip */
.inv-num {
    font-family: monospace; font-size: .82rem; font-weight: 700;
    background: linear-gradient(135deg,rgba(64,81,137,.08),rgba(10,179,156,.08));
    color: #405189; border: 1px solid rgba(64,81,137,.15);
    padding: .22rem .65rem; border-radius: .35rem; white-space: nowrap;
    display: inline-block;
}
.inv-num:hover { text-decoration: none; color: #405189; }

/* Patient avatar */
.pat-av {
    width: 34px; height: 34px; border-radius: 50%; flex-shrink: 0;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .75rem; font-weight: 700; color: #fff;
    background: linear-gradient(135deg,#405189,#0ab39c);
}

/* Status pills */
.s-pill {
    display: inline-flex; align-items: center; gap: .28rem;
    padding: .22rem .65rem; border-radius: 2rem; font-size: .72rem; font-weight: 700;
}
.s-pill .sdot { width: 5px; height: 5px; border-radius: 50%; }
.pill-pendiente  { background: #fff8e6; color: #b45309; }
.pill-pendiente  .sdot { background: #f0932b; }
.pill-pagada     { background: #d1fae5; color: #065f46; }
.pill-pagada     .sdot { background: #0ab39c; }
.pill-cancelada  { background: #fee2e2; color: #991b1b; }
.pill-cancelada  .sdot { background: #e74c3c; }

/* Insurance badge */
.ins-badge {
    font-size: .73rem; font-weight: 600; padding: .18rem .55rem;
    border-radius: 2rem; white-space: nowrap;
}
.ins-badge-yes { background: linear-gradient(135deg,rgba(64,81,137,.1),rgba(10,179,156,.1)); color: #405189; border: 1px solid rgba(64,81,137,.15); }
.ins-badge-no  { background: #f0f2f7; color: #94a3b8; }

/* Amount cells */
.amt { font-size: .88rem; font-weight: 600; color: #344563; }
.amt-discount { font-size: .83rem; font-weight: 600; color: #0ab39c; }
.amt-total { font-size: .92rem; font-weight: 800; color: #344563; }

/* Branch chip */
.branch-chip {
    font-size: .73rem; font-weight: 600; color: #0ab39c;
    background: rgba(10,179,156,.08); border: 1px solid rgba(10,179,156,.2);
    padding: .16rem .5rem; border-radius: 2rem; white-space: nowrap;
}

/* Action buttons */
.btn-action {
    width: 30px; height: 30px; padding: 0; border: none; border-radius: .35rem;
    display: inline-flex; align-items: center; justify-content: center; transition: all .15s;
}
.btn-action:hover { transform: scale(1.12); }

/* Empty state */
.empty-state { padding: 4rem 1rem; text-align: center; }
.empty-state i { font-size: 3.5rem; opacity: .2; color: #8098bb; display: block; margin-bottom: 1rem; }
.empty-state p { color: #8098bb; font-size: .9rem; margin: 0; }

/* ══════════════════════════════════════
   MODAL CANCEL
══════════════════════════════════════ */
.mh-danger { background: linear-gradient(135deg,#e74c3c,#c0392b); color: #fff; border-radius: .5rem .5rem 0 0; }
.mh-danger .btn-close { filter: invert(1); }

/* ══════════════════════════════════════
   ANIMATIONS
══════════════════════════════════════ */
@keyframes fadeInUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
.anim-row { animation: fadeInUp .28s ease both; }
.anim-row:nth-child(1){animation-delay:.03s}.anim-row:nth-child(2){animation-delay:.06s}
.anim-row:nth-child(3){animation-delay:.09s}.anim-row:nth-child(4){animation-delay:.12s}
.anim-row:nth-child(5){animation-delay:.15s}.anim-row:nth-child(6){animation-delay:.18s}
.anim-row:nth-child(7){animation-delay:.21s}.anim-row:nth-child(8){animation-delay:.24s}
.anim-stat { animation: fadeInUp .35s ease both; }
.anim-stat:nth-child(1){animation-delay:.05s}.anim-stat:nth-child(2){animation-delay:.1s}
.anim-stat:nth-child(3){animation-delay:.15s}.anim-stat:nth-child(4){animation-delay:.2s}

/* ══════════════════════════════════════
   TOAST
══════════════════════════════════════ */
#toast-container {
    position: fixed; top: 1.1rem; right: 1.1rem; z-index: 99999;
    display: flex; flex-direction: column; gap: .4rem; pointer-events: none;
}
.toast-item {
    min-width: 260px; padding: .78rem 1rem; border-radius: .5rem; color: #fff;
    font-size: .85rem; font-weight: 500; display: flex; align-items: center; gap: .5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,.2); animation: toastIn .25s ease;
    pointer-events: auto;
}
@keyframes toastIn { from{opacity:0;transform:translateX(36px)} to{opacity:1;transform:translateX(0)} }
.toast-success { background: linear-gradient(135deg,#0ab39c,#3d9f80); }
.toast-error   { background: linear-gradient(135deg,#e74c3c,#c0392b); }
</style>

<div id="toast-container"></div>

{{-- ── Breadcrumb ── --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-0">Facturas</h4>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Facturas</li>
            </ol>
        </div>
    </div>
</div>

{{-- ── Flash sessions → toast ── --}}
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', () => showToast("{{ addslashes(session('success')) }}", 'success'));
</script>
@endif
@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', () => showToast("{{ addslashes(session('error')) }}", 'error'));
</script>
@endif

{{-- ══ STAT CARDS ══ --}}
@php
    $allStatuses = $invoices->getCollection();
    $totalAmt    = $allStatuses->sum('total');
    $cntPending  = $allStatuses->where('status','pendiente')->count();
    $cntPaid     = $allStatuses->where('status','pagada')->count();
    $cntCancelled= $allStatuses->where('status','cancelada')->count();
    $amtPending  = $allStatuses->where('status','pendiente')->sum('total');
    $amtPaid     = $allStatuses->where('status','pagada')->sum('total');
@endphp
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3 anim-stat">
        <div class="inv-stat stat-total">
            <div class="inv-stat-body">
                <div class="inv-stat-label">Total registradas</div>
                <div class="inv-stat-value">{{ $invoices->total() }}</div>
                <div class="inv-stat-sub">RD$ {{ number_format($totalAmt, 2) }} en esta página</div>
            </div>
            <div class="inv-stat-icon"><i class="ri-file-list-3-line"></i></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3 anim-stat">
        <div class="inv-stat stat-pending">
            <div class="inv-stat-body">
                <div class="inv-stat-label">Pendientes</div>
                <div class="inv-stat-value" style="color:#b45309;">{{ $cntPending }}</div>
                <div class="inv-stat-sub">RD$ {{ number_format($amtPending, 2) }}</div>
            </div>
            <div class="inv-stat-icon"><i class="ri-time-line"></i></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3 anim-stat">
        <div class="inv-stat stat-paid">
            <div class="inv-stat-body">
                <div class="inv-stat-label">Pagadas</div>
                <div class="inv-stat-value" style="color:#0ab39c;">{{ $cntPaid }}</div>
                <div class="inv-stat-sub">RD$ {{ number_format($amtPaid, 2) }}</div>
            </div>
            <div class="inv-stat-icon"><i class="ri-checkbox-circle-line"></i></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3 anim-stat">
        <div class="inv-stat stat-cancelled">
            <div class="inv-stat-body">
                <div class="inv-stat-label">Canceladas</div>
                <div class="inv-stat-value" style="color:#e74c3c;">{{ $cntCancelled }}</div>
                <div class="inv-stat-sub">en esta página</div>
            </div>
            <div class="inv-stat-icon"><i class="ri-close-circle-line"></i></div>
        </div>
    </div>
</div>

{{-- ══ FILTER BAR ══ --}}
<form method="GET" action="{{ route('invoices.index') }}" id="filter-form" class="mb-3">
    <div class="filter-bar">

        {{-- Búsqueda --}}
        <div class="filter-search">
            <i class="ri-search-line"></i>
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Buscar por paciente o cédula."
                   id="search-input" autocomplete="off">
        </div>

        {{-- Estado --}}
        <select name="status" class="filter-select" onchange="this.form.submit()">
            <option value="">Todos los estados</option>
            <option value="pendiente"  @selected(request('status')==='pendiente')>Pendiente</option>
            <option value="pagada"     @selected(request('status')==='pagada')>Pagada</option>
            <option value="cancelada"  @selected(request('status')==='cancelada')>Cancelada</option>
        </select>

        {{-- Sucursal (solo admin) --}}
        @if(auth()->user()->role->name === 'admin')
        <select name="branch_id" class="filter-select" onchange="this.form.submit()">
            <option value="">Todas las sucursales</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}" @selected(request('branch_id') == $branch->id)>
                    {{ $branch->name }}
                </option>
            @endforeach
        </select>
        @endif

        {{-- Rango de fechas --}}
        <input type="date" name="date_from" value="{{ request('date_from') }}"
               class="filter-select" style="padding:.48rem .75rem;"
               onchange="this.form.submit()" title="Desde">
        <input type="date" name="date_to" value="{{ request('date_to') }}"
               class="filter-select" style="padding:.48rem .75rem;"
               onchange="this.form.submit()" title="Hasta">

        {{-- Reset --}}
        @if(request()->hasAny(['q','status','branch_id','date_from','date_to']))
        <a href="{{ route('invoices.index') }}" class="btn-reset">
            <i class="ri-close-line"></i>Limpiar
        </a>
        @endif

        {{-- Botón buscar (para el campo de texto) --}}
        <button type="submit" class="btn btn-primary btn-sm"
                style="border-radius:2rem;padding:.45rem .9rem;white-space:nowrap;">
            <i class="ri-search-line me-1"></i>Buscar
        </button>

        <a href="{{ route('invoices.create') }}"
           class="btn btn-success btn-sm ms-auto d-flex align-items-center gap-1"
           style="border-radius:2rem;padding:.45rem .9rem;white-space:nowrap;">
            <i class="ri-add-line"></i>Nueva Factura
        </a>
    </div>
</form>

{{-- ══ TABLE CARD ══ --}}
<div class="inv-card">
    <div class="inv-card-header">
        <h5><i class="ri-file-list-3-line me-2 text-primary"></i>Listado de Facturas</h5>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Indicadores de filtros activos --}}
            @if(request('status'))
                <span class="s-pill pill-{{ request('status') }}">
                    <span class="sdot"></span>{{ ucfirst(request('status')) }}
                </span>
            @endif
            @if(request('q'))
                <span style="font-size:.78rem;color:#8098bb;">
                    <i class="ri-search-line me-1"></i>«{{ request('q') }}»
                </span>
            @endif
            <span style="font-size:.78rem;color:#8098bb;margin-left:.25rem;">
                {{ $invoices->total() }} {{ $invoices->total() === 1 ? 'resultado' : 'resultados' }}
            </span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table inv-table mb-0">
            <thead>
                <tr>
                    <th># Factura</th>
                    <th>Paciente</th>
                    @if(auth()->user()->role->name === 'admin')
                    <th>Sucursal</th>
                    @endif
                    <th>Seguro</th>
                    <th>Subtotal</th>
                    <th>Desc. Seguro</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                <tr class="anim-row">
                    <td>
                        <a href="{{ route('invoices.show', $inv) }}" class="inv-num">
                            {{ $inv->invoice_number }}
                        </a>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="pat-av">{{ strtoupper(substr($inv->patient->first_name,0,1).substr($inv->patient->last_name,0,1)) }}</div>
                            <div>
                                <div class="fw-semibold" style="font-size:.87rem;color:#344563;">
                                    {{ $inv->patient->first_name }} {{ $inv->patient->last_name }}
                                </div>
                                <div style="font-size:.73rem;color:#8098bb;font-family:monospace;">
                                    {{ $inv->patient->cedula }}
                                </div>
                            </div>
                        </div>
                    </td>
                    @if(auth()->user()->role->name === 'admin')
                    <td>
                        <span class="branch-chip">{{ $inv->branch->name }}</span>
                    </td>
                    @endif
                    <td>
                        @if($inv->insurance)
                            <span class="ins-badge ins-badge-yes">
                                <i class="ri-shield-check-line me-1"></i>{{ $inv->insurance->name }}
                            </span>
                        @else
                            <span class="ins-badge ins-badge-no">Privado</span>
                        @endif
                    </td>
                    <td><span class="amt">RD$ {{ number_format($inv->subtotal, 2) }}</span></td>
                    <td>
                        @if($inv->insurance_discount > 0)
                            <span class="amt-discount">
                                <i class="ri-arrow-down-line" style="font-size:.75rem;"></i>
                                RD$ {{ number_format($inv->insurance_discount, 2) }}
                            </span>
                        @else
                            <span style="color:#cbd5e1;font-size:.83rem;">—</span>
                        @endif
                    </td>
                    <td><span class="amt-total">RD$ {{ number_format($inv->total, 2) }}</span></td>
                    <td>
                        <span class="s-pill pill-{{ $inv->status }}">
                            <span class="sdot"></span>{{ ucfirst($inv->status) }}
                        </span>
                    </td>
                    <td>
                        <span style="font-size:.82rem;color:#344563;font-weight:500;">
                            {{ $inv->created_at->format('d/m/Y') }}
                        </span>
                        <div style="font-size:.72rem;color:#8098bb;">{{ $inv->created_at->format('H:i') }}</div>
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('invoices.show', $inv) }}"
                               class="btn btn-action bg-info-subtle text-info" title="Ver detalle">
                                <i class="ri-eye-fill fs-13"></i>
                            </a>
                            @if($inv->status === 'pendiente' && auth()->user()->role->name === 'admin')
                            <button type="button"
                                    class="btn btn-action bg-danger-subtle text-danger"
                                    title="Cancelar"
                                    onclick="openCancelModal({{ $inv->id }}, '{{ $inv->invoice_number }}', '{{ addslashes($inv->patient->first_name.' '.$inv->patient->last_name) }}')">
                                <i class="ri-close-circle-fill fs-13"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ auth()->user()->role->name === 'admin' ? 10 : 9 }}">
                        <div class="empty-state">
                            <i class="ri-file-list-3-line"></i>
                            @if(request()->hasAny(['q','status','branch_id','date_from','date_to']))
                                <p>No se encontraron facturas con los filtros aplicados.</p>
                                <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-light mt-2" style="border-radius:2rem;">
                                    <i class="ri-refresh-line me-1"></i>Limpiar filtros
                                </a>
                            @else
                                <p>No hay facturas registradas aún.</p>
                                <a href="{{ route('invoices.create') }}" class="btn btn-sm btn-primary mt-2" style="border-radius:2rem;">
                                    <i class="ri-add-line me-1"></i>Crear primera factura
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($invoices->hasPages())
    <div class="d-flex align-items-center justify-content-between px-4 py-3"
         style="border-top:1px solid #f0f2f7;">
        <div style="font-size:.8rem;color:#8098bb;">
            Mostrando <strong>{{ $invoices->firstItem() }}–{{ $invoices->lastItem() }}</strong>
            de <strong>{{ $invoices->total() }}</strong> facturas
        </div>
        <div>
            {{ $invoices->appends(request()->query())->links() }}
        </div>
    </div>
    @endif
</div>

</div><!-- container -->
</div><!-- page-content -->

{{-- ══════════════════════════════════════
     MODAL: Confirmar Cancelación
══════════════════════════════════════ --}}
<div class="modal fade" id="cancelModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header mh-danger py-3">
                <h5 class="modal-title"><i class="ri-error-warning-line me-2"></i>Cancelar Factura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="pat-av mx-auto mb-3" id="cancel-av"
                     style="width:54px;height:54px;font-size:1.15rem;background:linear-gradient(135deg,#e74c3c,#c0392b);">?</div>
                <p class="mb-1 fw-semibold fs-5" id="cancel-patient">—</p>
                <p class="mb-0" style="font-size:.85rem;color:#8098bb;">
                    Factura <code id="cancel-num" style="font-size:.88rem;background:#f0f2f7;padding:.1rem .4rem;border-radius:.3rem;color:#405189;"></code>
                </p>
                <p class="text-muted mt-2 mb-0" style="font-size:.84rem;">
                    Esta acción es <strong>irreversible</strong>. ¿Confirmas la cancelación?
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="min-width:95px;border-radius:2rem;">Volver</button>
                <form id="cancel-form" method="POST" style="display:inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-danger" style="min-width:120px;border-radius:2rem;">
                        <i class="ri-close-circle-line me-1"></i>Cancelar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openCancelModal(id, num, patient) {
    const ini = patient.split(' ').map(w => w[0]).slice(0,2).join('').toUpperCase();
    document.getElementById('cancel-av').textContent       = ini;
    document.getElementById('cancel-patient').textContent  = patient;
    document.getElementById('cancel-num').textContent      = num;
    document.getElementById('cancel-form').action          = '/invoices/' + id + '/cancel';
    new bootstrap.Modal(document.getElementById('cancelModal')).show();
}

function showToast(msg, type) {
    const d = document.createElement('div');
    d.className = 'toast-item toast-' + (type || 'success');
    d.innerHTML = '<i class="ri-' + (type === 'error' ? 'error-warning' : 'checkbox-circle') + '-line fs-16"></i>' + msg;
    document.getElementById('toast-container').appendChild(d);
    setTimeout(() => {
        d.style.transition = 'opacity .32s'; d.style.opacity = '0';
        setTimeout(() => d.remove(), 340);
    }, 3500);
}

// Submit on Enter for search input
document.getElementById('search-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); this.form.submit(); }
});
</script>
@endpush

</x-app-layout>