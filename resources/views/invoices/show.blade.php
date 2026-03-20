<x-app-layout>
<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

<style>
/* ══════════════════════════════════════
   PRINT
══════════════════════════════════════ */
@media print {
    .page-content { padding: 0 !important; }
    .no-print, .page-title-box, nav, .sidebar, header, footer { display: none !important; }
    .inv-shell { box-shadow: none !important; border: none !important; }
    .print-page { padding: 0 !important; }
}

/* ══════════════════════════════════════
   ACTION BAR
══════════════════════════════════════ */
.action-bar {
    display: flex; align-items: center; gap: .6rem; flex-wrap: wrap;
    margin-bottom: 1.25rem;
}
.btn-back {
    display: inline-flex; align-items: center; gap: .35rem;
    font-size: .84rem; font-weight: 600; color: #344563;
    background: #fff; border: 1.5px solid #e2e8f0; border-radius: 2rem;
    padding: .42rem .9rem; text-decoration: none; transition: all .18s;
}
.btn-back:hover { border-color: #405189; color: #405189; background: #f8faff; }
.btn-print {
    display: inline-flex; align-items: center; gap: .35rem;
    font-size: .84rem; font-weight: 600; color: #405189;
    background: rgba(64,81,137,.07); border: 1.5px solid rgba(64,81,137,.18);
    border-radius: 2rem; padding: .42rem .9rem; cursor: pointer; transition: all .18s;
}
.btn-print:hover { background: #405189; color: #fff; border-color: #405189; }
.btn-cancel-inv {
    display: inline-flex; align-items: center; gap: .35rem;
    font-size: .84rem; font-weight: 600; color: #e74c3c;
    background: rgba(231,76,60,.07); border: 1.5px solid rgba(231,76,60,.2);
    border-radius: 2rem; padding: .42rem .9rem; cursor: pointer; transition: all .18s;
    margin-left: auto;
}
.btn-cancel-inv:hover { background: #e74c3c; color: #fff; border-color: #e74c3c; }

/* ══════════════════════════════════════
   STATUS BADGE
══════════════════════════════════════ */
.status-badge {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .28rem .8rem; border-radius: 2rem; font-size: .78rem; font-weight: 700;
    letter-spacing: .03em;
}
.status-badge .sdot { width: 6px; height: 6px; border-radius: 50%; }
.badge-pendiente { background: #fff8e6; color: #b45309; border: 1px solid rgba(240,147,43,.25); }
.badge-pendiente .sdot { background: #f0932b; }
.badge-pagada    { background: #d1fae5; color: #065f46; border: 1px solid rgba(10,179,156,.25); }
.badge-pagada    .sdot { background: #0ab39c; }
.badge-cancelada { background: #fee2e2; color: #991b1b; border: 1px solid rgba(231,76,60,.2); }
.badge-cancelada .sdot { background: #e74c3c; }

/* ══════════════════════════════════════
   INVOICE SHELL
══════════════════════════════════════ */
.inv-shell {
    background: #fff;
    border: none; border-radius: 1rem;
    box-shadow: 0 4px 32px rgba(64,81,137,.1);
    overflow: hidden;
}

/* Top gradient banner */
.inv-banner {
    background: linear-gradient(135deg, #405189 0%, #0ab39c 100%);
    padding: 1.6rem 2rem;
    display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 1rem;
}
.inv-banner-company { color: #fff; }
.inv-banner-company h3 { font-size: 1.25rem; font-weight: 800; margin: 0 0 .2rem; }
.inv-banner-company p { font-size: .82rem; opacity: .85; margin: 0; line-height: 1.5; }
.inv-banner-right { text-align: right; color: #fff; }
.inv-banner-right .inv-label { font-size: .7rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; opacity: .75; }
.inv-banner-right .inv-num-big { font-size: 1.35rem; font-weight: 800; font-family: monospace; letter-spacing: .05em; }
.inv-banner-right .inv-date { font-size: .8rem; opacity: .8; margin-top: .15rem; }

/* ══════════════════════════════════════
   BODY SECTIONS
══════════════════════════════════════ */
.inv-body { padding: 2rem; }

/* Info cards row */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.75rem;
}
.info-card {
    background: #f8faff;
    border: 1px solid #edf0f7;
    border-radius: .65rem;
    padding: 1rem 1.1rem;
}
.info-card-label {
    font-size: .67rem; font-weight: 700; letter-spacing: .1em;
    text-transform: uppercase; color: #8098bb; margin-bottom: .55rem;
    display: flex; align-items: center; gap: .35rem;
}
.info-card-label i { font-size: .9rem; }
.info-card-name { font-size: .97rem; font-weight: 700; color: #344563; margin-bottom: .2rem; }
.info-card-line { font-size: .8rem; color: #6b7a99; margin-bottom: .12rem; }
.info-card-line i { font-size: .85rem; opacity: .6; }

/* Divider */
.inv-divider { border: none; border-top: 1.5px solid #f0f2f7; margin: 0 0 1.75rem; }

/* ══════════════════════════════════════
   ITEMS TABLE
══════════════════════════════════════ */
.items-table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; }
.items-table thead tr {
    background: linear-gradient(135deg,rgba(64,81,137,.06),rgba(10,179,156,.06));
}
.items-table th {
    font-size: .68rem; font-weight: 700; letter-spacing: .09em; text-transform: uppercase;
    color: #8098bb; padding: .75rem 1rem; border-bottom: 2px solid #e9ecef; white-space: nowrap;
}
.items-table th:not(:first-child) { text-align: right; }
.items-table th:nth-child(1) { text-align: left; }
.items-table th.th-center { text-align: center; }
.items-table td {
    padding: .85rem 1rem; border-bottom: 1px solid #f3f5f9;
    font-size: .87rem; vertical-align: top;
}
.items-table td:not(:first-child) { text-align: right; }
.items-table tbody tr:last-child td { border-bottom: none; }
.items-table tbody tr:hover { background: #fafbff; }
.item-num {
    width: 26px; height: 26px; border-radius: 50%; background: #f0f2f7;
    color: #8098bb; font-size: .72rem; font-weight: 700;
    display: inline-flex; align-items: center; justify-content: center;
}
.item-name { font-weight: 600; color: #344563; }
.item-desc { font-size: .75rem; color: #94a3b8; margin-top: .15rem; }
.td-center { text-align: center !important; }
.qty-chip {
    display: inline-flex; align-items: center; justify-content: center;
    background: #f0f2f7; color: #405189; font-weight: 700; font-size: .8rem;
    width: 28px; height: 28px; border-radius: .35rem;
}
.ins-cover { color: #0ab39c; font-weight: 600; }
.pat-pays  { color: #344563; font-weight: 700; }

/* ══════════════════════════════════════
   TOTALS BOX
══════════════════════════════════════ */
.totals-wrap {
    display: flex; justify-content: flex-end; margin-bottom: 1.5rem;
}
.totals-box {
    min-width: 280px;
    background: #f8faff; border: 1px solid #edf0f7; border-radius: .75rem;
    overflow: hidden;
}
.totals-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: .65rem 1.1rem; border-bottom: 1px solid #f0f2f7;
    font-size: .88rem;
}
.totals-row:last-child { border-bottom: none; }
.totals-row.total-final {
    background: linear-gradient(135deg,rgba(64,81,137,.06),rgba(10,179,156,.06));
    padding: .85rem 1.1rem;
}
.totals-label { color: #6b7a99; font-weight: 500; }
.totals-value { font-weight: 600; color: #344563; }
.totals-label-final { font-weight: 800; color: #344563; font-size: .95rem; }
.totals-value-final { font-weight: 800; color: #405189; font-size: 1.15rem; }
.discount-val { color: #0ab39c; font-weight: 700; }
.auth-row {
    display: flex; align-items: center; gap: .5rem;
    font-size: .78rem; color: #6b7a99; padding: .5rem 1.1rem;
    background: rgba(64,81,137,.03); border-top: 1px solid #f0f2f7;
}
.auth-chip {
    background: rgba(64,81,137,.1); color: #405189;
    font-family: monospace; font-size: .82rem; font-weight: 700;
    padding: .15rem .5rem; border-radius: .3rem;
}

/* ══════════════════════════════════════
   FOOTER
══════════════════════════════════════ */
.inv-footer {
    text-align: center; padding: 1.1rem 2rem;
    background: #f8faff; border-top: 1px solid #f0f2f7;
    font-size: .78rem; color: #94a3b8;
}

/* ══════════════════════════════════════
   MODAL
══════════════════════════════════════ */
.mh-danger { background: linear-gradient(135deg,#e74c3c,#c0392b); color: #fff; border-radius: .5rem .5rem 0 0; }
.mh-danger .btn-close { filter: invert(1); }

/* ══════════════════════════════════════
   TOAST
══════════════════════════════════════ */
#toast-container {
    position: fixed; top: 1.1rem; right: 1.1rem; z-index: 99999;
    display: flex; flex-direction: column; gap: .4rem;
}
.toast-item {
    min-width: 260px; padding: .78rem 1rem; border-radius: .5rem; color: #fff;
    font-size: .85rem; font-weight: 500; display: flex; align-items: center; gap: .5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,.2); animation: toastIn .25s ease;
}
@keyframes toastIn { from{opacity:0;transform:translateX(36px)} to{opacity:1;transform:translateX(0)} }
.toast-success { background: linear-gradient(135deg,#0ab39c,#3d9f80); }
.toast-error   { background: linear-gradient(135deg,#e74c3c,#c0392b); }

@keyframes fadeInUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
.inv-shell { animation: fadeInUp .35s ease; }
</style>

<div id="toast-container"></div>

{{-- ── Breadcrumb ── --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-0">{{ $invoice->invoice_number }}</h4>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">Facturas</a></li>
                <li class="breadcrumb-item active">{{ $invoice->invoice_number }}</li>
            </ol>
        </div>
    </div>
</div>

{{-- Flash → toast --}}
@if(session('success'))
<script>document.addEventListener('DOMContentLoaded',()=>showToast("{{ addslashes(session('success')) }}",'success'));</script>
@endif
@if(session('error'))
<script>document.addEventListener('DOMContentLoaded',()=>showToast("{{ addslashes(session('error')) }}",'error'));</script>
@endif

{{-- ── ACTION BAR ── --}}
<div class="action-bar no-print">
    <a href="{{ route('invoices.index') }}" class="btn-back">
        <i class="ri-arrow-left-line"></i>Volver
    </a>
    <button type="button" class="btn-print" onclick="window.print()">
        <i class="ri-printer-line"></i>Imprimir
    </button>
    <span class="status-badge badge-{{ $invoice->status }}">
        <span class="sdot"></span>{{ ucfirst($invoice->status) }}
    </span>
    @if($invoice->status === 'pendiente' && auth()->user()->role->name === 'admin')
    <button type="button" class="btn-cancel-inv" onclick="openCancelModal()">
        <i class="ri-close-circle-line"></i>Cancelar Factura
    </button>
    @endif
</div>

{{-- ══ INVOICE SHELL ══ --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="inv-shell">

            {{-- Banner superior --}}
            <div class="inv-banner">
                <div class="inv-banner-company">
                    <h3><i class="ri-heart-pulse-line me-2" style="opacity:.8;"></i>Audiofon</h3>
                    @if($invoice->branch)
                        <p>{{ $invoice->branch->name }}</p>
                        @if($invoice->branch->address)
                            <p><i class="ri-map-pin-line me-1" style="opacity:.7;"></i>{{ $invoice->branch->address }}</p>
                        @endif
                        @if($invoice->branch->phone)
                            <p><i class="ri-phone-line me-1" style="opacity:.7;"></i>{{ $invoice->branch->phone }}</p>
                        @endif
                    @endif
                </div>
                <div class="inv-banner-right">
                    <div class="inv-label">Factura</div>
                    <div class="inv-num-big">{{ $invoice->invoice_number }}</div>
                    <div class="inv-date">
                        <i class="ri-calendar-line me-1" style="opacity:.7;"></i>
                        {{ $invoice->created_at->format('d/m/Y') }} &nbsp;·&nbsp; {{ $invoice->created_at->format('H:i') }}
                    </div>
                   
                </div>
            </div>

            {{-- Body --}}
            <div class="inv-body">

                {{-- Info cards --}}
                <div class="info-grid">

                    {{-- Paciente --}}
                    <div class="info-card">
                        <div class="info-card-label">
                            <i class="ri-user-heart-line text-primary"></i>Paciente
                        </div>
                        <div class="info-card-name">
                            {{ $invoice->patient->first_name }} {{ $invoice->patient->last_name }}
                        </div>
                        <div class="info-card-line">
                            <i class="ri-id-card-line"></i> {{ $invoice->patient->cedula }}
                        </div>
                        @if($invoice->patient->phone)
                        <div class="info-card-line">
                            <i class="ri-phone-line"></i> {{ $invoice->patient->phone }}
                        </div>
                        @endif
                        @if($invoice->patient->email)
                        <div class="info-card-line">
                            <i class="ri-mail-line"></i> {{ $invoice->patient->email }}
                        </div>
                        @endif
                    </div>



                    {{-- Sucursal y emisor --}}
                    <div class="info-card">
                        <div class="info-card-label">
                            <i class="ri-building-2-line text-info"></i>Sucursal / Emitida por
                        </div>
                        <div class="info-card-name">{{ $invoice->branch->name }}</div>
                        @if($invoice->branch->address)
                        <div class="info-card-line">
                            <i class="ri-map-pin-line"></i> {{ $invoice->branch->address }}
                        </div>
                        @endif
                        <div class="info-card-line mt-1" style="font-weight:600;color:#344563;">
                            <i class="ri-user-line"></i> {{ $invoice->user->name }}
                        </div>
                        <div class="info-card-line" style="font-size:.73rem;color:#94a3b8;text-transform:capitalize;">
                            <i class="ri-shield-user-line"></i> {{ $invoice->user->role->name ?? '—' }}
                        </div>
                    </div>

                </div>

                <hr class="inv-divider">

                {{-- Items table --}}
                <div class="table-responsive mb-0">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th style="width:40px;">#</th>
                                <th style="text-align:left;">Servicio</th>
                                <th>Precio unit.</th>
                                <th class="th-center">Cant.</th>
                                <th>Subtotal</th>
                                @if($invoice->insurance)
                                    <th>Cubre seguro</th>
                                    <th>Paga paciente</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $i => $item)
                            <tr>
                                <td><span class="item-num">{{ $i + 1 }}</span></td>
                                <td>
                                    <div class="item-name">{{ $item->service->name }}</div>
                                    @if($item->service->description)
                                    <div class="item-desc">{{ $item->service->description }}</div>
                                    @endif
                                </td>
                                <td>RD$ {{ number_format($item->price, 2) }}</td>
                                <td class="td-center">
                                    <span class="qty-chip">{{ $item->quantity }}</span>
                                </td>
                                <td>RD$ {{ number_format($item->subtotal, 2) }}</td>
                                @if($invoice->insurance)
                                <td>
                                    <span class="ins-cover">
                                        RD$ {{ number_format($item->insurance_amount ?? 0, 2) }}
                                    </span>
                                    @if($item->coverage_percentage)
                                    <div style="font-size:.7rem;color:#0ab39c;opacity:.7;">
                                        {{ number_format($item->coverage_percentage, 0) }}%
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="pat-pays">
                                        RD$ {{ number_format($item->patient_amount ?? $item->subtotal, 2) }}
                                    </span>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Totals --}}
                <div class="totals-wrap">
                    <div class="totals-box">
                        <div class="totals-row">
                            <span class="totals-label">Subtotal</span>
                            <span class="totals-value">RD$ {{ number_format($invoice->subtotal, 2) }}</span>
                        </div>
                        @if($invoice->insurance_discount > 0)
                        <div class="totals-row">
                            <span class="totals-label">
                                Descuento seguro
                            </span>
                            <span class="discount-val">− RD$ {{ number_format($invoice->insurance_discount, 2) }}</span>
                        </div>
                        @endif
                        <div class="totals-row total-final">
                            <span class="totals-label-final">Total a pagar</span>
                            <span class="totals-value-final">RD$ {{ number_format($invoice->total, 2) }}</span>
                        </div>
                        @if($invoice->authorization_number)
                        <div class="auth-row">
                            <i class="ri-file-check-line"></i>
                            N° Autorización: <span class="auth-chip">{{ $invoice->authorization_number }}</span>
                        </div>
                        @endif
                    </div>
                </div>

            </div>{{-- /inv-body --}}

            {{-- Footer --}}
            <div class="inv-footer">
                <i class="ri-heart-pulse-line me-1"></i>
                Audiofon · Gracias por su preferencia · Factura generada el
                {{ $invoice->created_at->translatedFormat('d \d\e F \d\e Y \a \l\a\s H:i') }}
            </div>

        </div>{{-- /inv-shell --}}
    </div>
</div>

</div><!-- container -->
</div><!-- page-content -->

{{-- ══════════════════════════════════
     MODAL: Cancelar
══════════════════════════════════ --}}
@if($invoice->status === 'pendiente' && auth()->user()->role->name === 'admin')
<div class="modal fade" id="cancelModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">
            <div class="modal-header mh-danger py-3">
                <h5 class="modal-title"><i class="ri-error-warning-line me-2"></i>Cancelar Factura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div style="width:54px;height:54px;border-radius:50%;background:linear-gradient(135deg,#e74c3c,#c0392b);display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:#fff;margin:0 auto 1rem;">
                    <i class="ri-close-circle-line"></i>
                </div>
                <p class="mb-1 fw-semibold fs-5">{{ $invoice->invoice_number }}</p>
                <p class="text-muted mb-0" style="font-size:.85rem;">
                    {{ $invoice->patient->first_name }} {{ $invoice->patient->last_name }}
                </p>
                <p class="text-muted mt-2 mb-0" style="font-size:.84rem;">
                    Esta acción es <strong>irreversible</strong>. ¿Confirmas la cancelación?
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                        style="min-width:95px;border-radius:2rem;">Volver</button>
                <form action="{{ route('invoices.cancel', $invoice) }}" method="POST" style="display:inline;">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-danger"
                            style="min-width:130px;border-radius:2rem;">
                        <i class="ri-close-circle-line me-1"></i>Cancelar factura
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
function openCancelModal() {
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
</script>
@endpush

</x-app-layout>