<x-app-layout>
<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

<style>
/* ── Variables ── */
:root {
    --rp:#405189; --rt:#0ab39c; --ra:#f7b84b; --rr:#f06548; --rv:#7066e0;
    --ink:#1e2535; --muted:#6b7a99; --border:#edf0f7; --surface:#f8faff; --radius:.85rem;
}

/* ── Stat glass cards ── */
.stat-card {
    background:rgba(255,255,255,.6); backdrop-filter:blur(10px);
    border:1px solid rgba(255,255,255,.8); border-radius:var(--radius);
    box-shadow:0 2px 20px rgba(64,81,137,.09); padding:1.2rem 1.3rem;
    border-left:4px solid transparent; position:relative; overflow:hidden;
    transition:transform .18s, box-shadow .18s; margin-bottom:1.25rem;
}
.stat-card:hover { transform:translateY(-3px); box-shadow:0 8px 28px rgba(64,81,137,.14); }
.stat-card.sc-a { border-color:var(--ra); }
.stat-card.sc-p { border-color:var(--rp); }
.stat-card.sc-r { border-color:var(--rr); }
.stat-ico { width:42px; height:42px; border-radius:.55rem; display:flex; align-items:center; justify-content:center; font-size:1.2rem; margin-bottom:.75rem; }
.sc-a .stat-ico { background:rgba(247,184,75,.12); color:var(--ra); }
.sc-p .stat-ico { background:rgba(64,81,137,.1);   color:var(--rp); }
.sc-r .stat-ico { background:rgba(240,101,72,.1);  color:var(--rr); }
.stat-val { font-size:1.55rem; font-weight:800; color:var(--ink); letter-spacing:-.04em; line-height:1; }
.stat-lbl { font-size:.72rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-top:.2rem; }
.stat-ghost { position:absolute; right:-6px; bottom:-6px; font-size:4rem; opacity:.04; line-height:1; }

/* ── Main card ── */
.main-card { background:#fff; border-radius:var(--radius); box-shadow:0 2px 20px rgba(64,81,137,.08); border:none; overflow:hidden; }
.main-card-head { padding:.9rem 1.25rem; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:.65rem; }
.main-card-head h5 { margin:0; font-size:.95rem; font-weight:700; color:var(--ink); flex-grow:1; }

/* ── Search bar ── */
.search-bar {
    display:flex; align-items:center;
    background:var(--surface); border:1.5px solid var(--border);
    border-radius:2rem; padding:.42rem .75rem .42rem 1rem;
    transition:border-color .2s, box-shadow .2s;
}
.search-bar:focus-within { border-color:var(--rp); box-shadow:0 0 0 3px rgba(64,81,137,.1); }
.search-bar input { border:none; background:transparent; outline:none; font-size:.85rem; flex:1; min-width:0; }
.search-bar i { color:var(--muted); font-size:.95rem; margin-right:.4rem; }

/* ── Table ── */
.rec-table { width:100%; border-collapse:collapse; }
.rec-table th {
    font-size:.67rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase;
    color:var(--muted); padding:.75rem 1rem; border-bottom:2px solid var(--border);
    background:var(--surface); white-space:nowrap;
}
.rec-table td { padding:.85rem 1rem; border-bottom:1px solid var(--border); font-size:.85rem; color:var(--ink); vertical-align:middle; }
.rec-table tbody tr:last-child td { border-bottom:none; }
.rec-table tbody tr { transition:background .12s; }
.rec-table tbody tr:hover td { background:var(--surface); }

/* ── Patient avatar ── */
.pat-av {
    width:34px; height:34px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:.72rem; font-weight:800; color:#fff;
    background:linear-gradient(135deg,var(--rp),#6b7fe0);
}

/* ── Invoice badge ── */
.inv-num { font-size:.78rem; font-weight:800; color:var(--rp); font-family:monospace; }
.badge-pend { background:rgba(247,184,75,.15); color:#b8860b; font-size:.7rem; font-weight:700; padding:.22rem .65rem; border-radius:2rem; }

/* ── Amount ── */
.amt-cell { font-size:.92rem; font-weight:800; color:var(--ink); white-space:nowrap; }
.days-old { font-size:.7rem; color:var(--muted); }
.days-old.urgent { color:var(--rr); font-weight:700; }

/* ── Pay button ── */
.btn-pay {
    background:linear-gradient(135deg,var(--rp),var(--rt));
    color:#fff; border:none; border-radius:2rem;
    padding:.38rem .9rem; font-size:.78rem; font-weight:700;
    cursor:pointer; transition:all .18s; display:flex; align-items:center; gap:.35rem;
    white-space:nowrap;
}
.btn-pay:hover { opacity:.88; transform:translateY(-1px); box-shadow:0 4px 14px rgba(64,81,137,.3); }

/* ── Empty state ── */
.empty-state { text-align:center; padding:3.5rem 1rem; color:var(--muted); }
.empty-state i { font-size:3rem; opacity:.2; display:block; margin-bottom:.75rem; }
.empty-state p { font-size:.88rem; margin:0; }

/* ── MODAL ── */
.mh-pay { background:linear-gradient(135deg,var(--rp),var(--rt)); border-radius:.55rem .55rem 0 0; }
.mh-pay .btn-close { filter:invert(1); }

/* Payment method cards */
.method-card {
    border:2px solid var(--border); border-radius:.65rem; padding:.9rem;
    cursor:pointer; transition:all .18s; background:#fff; position:relative;
}
.method-card:hover { border-color:var(--rp); background:rgba(64,81,137,.03); }
.method-card.selected { border-color:var(--rp); background:rgba(64,81,137,.05); }
.method-card .method-ico {
    width:36px; height:36px; border-radius:.45rem; margin-bottom:.5rem;
    display:flex; align-items:center; justify-content:center; font-size:1.1rem;
}
.method-card .method-label { font-size:.8rem; font-weight:700; color:var(--ink); }
.method-card .method-check {
    position:absolute; top:.5rem; right:.5rem; width:18px; height:18px;
    border-radius:50%; background:var(--rp); color:#fff;
    display:none; align-items:center; justify-content:center; font-size:.65rem;
}
.method-card.selected .method-check { display:flex; }

/* Amount input in method card */
.method-amount { margin-top:.65rem; display:none; }
.method-card.selected .method-amount { display:block; }
.method-amount input {
    width:100%; border:1.5px solid var(--border); border-radius:.45rem;
    padding:.42rem .65rem; font-size:.87rem; font-weight:700; outline:none;
    transition:border-color .18s;
}
.method-amount input:focus { border-color:var(--rp); box-shadow:0 0 0 2px rgba(64,81,137,.1); }
.method-ref { margin-top:.4rem; display:none; }
.method-card.selected .method-ref { display:block; }
.method-ref input { width:100%; border:1.5px solid var(--border); border-radius:.45rem; padding:.35rem .6rem; font-size:.78rem; outline:none; }
.method-ref input:focus { border-color:var(--rp); }

/* Invoice summary in modal */
.inv-summary { background:var(--surface); border-radius:.55rem; padding:.9rem 1rem; border:1px solid var(--border); }
.inv-sum-row { display:flex; justify-content:space-between; font-size:.83rem; padding:.22rem 0; }
.inv-sum-row.total-row { font-weight:800; font-size:.95rem; border-top:1px solid var(--border); margin-top:.3rem; padding-top:.55rem; color:var(--rp); }

/* Payment breakdown */
.pay-total-box { background:linear-gradient(135deg,rgba(64,81,137,.06),rgba(10,179,156,.06)); border-radius:.65rem; padding:.9rem 1rem; border:1px solid rgba(64,81,137,.12); margin-top:.85rem; }
.pay-total-row { display:flex; justify-content:space-between; font-size:.82rem; padding:.2rem 0; color:var(--muted); }
.pay-total-row.pay-final { font-size:1rem; font-weight:800; color:var(--ink); border-top:1px solid rgba(64,81,137,.15); margin-top:.35rem; padding-top:.5rem; }
.pay-diff-ok   { color:var(--rt); }
.pay-diff-warn { color:var(--rr); }

/* ── Vuelto box ── */
.vuelto-box {
    display:none;
    margin-top:.75rem;
    background:linear-gradient(135deg,rgba(10,179,156,.08),rgba(10,179,156,.04));
    border:1.5px solid rgba(10,179,156,.3); border-radius:.65rem;
    padding:.85rem 1.1rem;
    animation:fadeVuelto .25s ease;
}
@keyframes fadeVuelto { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }
.vuelto-lbl {
    font-size:.7rem; font-weight:800; text-transform:uppercase;
    letter-spacing:.09em; color:var(--rt); margin-bottom:.25rem;
    display:flex; align-items:center; gap:.4rem;
}
.vuelto-amt {
    font-size:1.55rem; font-weight:900; color:var(--rt);
    letter-spacing:-.04em; line-height:1;
}
.vuelto-sub { font-size:.74rem; color:var(--muted); margin-top:.2rem; }

/* ── Toast ── */
#toast-c { position:fixed; top:1.1rem; right:1.1rem; z-index:99999; display:flex; flex-direction:column; gap:.4rem; }
.t-item { min-width:260px; padding:.75rem 1rem; border-radius:.5rem; color:#fff; font-size:.84rem; font-weight:500; display:flex; align-items:center; gap:.5rem; box-shadow:0 4px 20px rgba(0,0,0,.2); animation:tin .25s ease; }
@keyframes tin { from{opacity:0;transform:translateX(36px)} to{opacity:1;transform:translateX(0)} }
.t-ok  { background:linear-gradient(135deg,#0ab39c,#3d9f80); }
.t-err { background:linear-gradient(135deg,#f06548,#c0392b); }

/* ── Animations ── */
@keyframes fadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
.fa0{animation:fadeUp .38s ease both}
.fa1{animation:fadeUp .38s .06s ease both}
.fa2{animation:fadeUp .38s .12s ease both}
</style>

<div id="toast-c"></div>

{{-- ── Breadcrumb ── --}}
<div class="row mb-3 align-items-center">
    <div class="col">
        <h4 class="mb-0" style="font-weight:800;letter-spacing:-.02em;">Cobros y Recibos</h4>
        <ol class="breadcrumb mb-0 mt-1" style="font-size:.78rem;">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Recibos</li>
        </ol>
    </div>
</div>

{{-- ── Stat cards ── --}}
<div class="row g-4 mb-1">
    <div class="col-xl-4 col-md-6 fa0">
        <div class="stat-card sc-a">
            <div class="stat-ico"><i class="ri-file-list-3-line"></i></div>
            <div class="stat-val">{{ $invoices->total() }}</div>
            <div class="stat-lbl">Facturas pendientes</div>
            <div class="stat-ghost"><i class="ri-file-list-fill"></i></div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 fa1">
        <div class="stat-card sc-r">
            <div class="stat-ico"><i class="ri-coins-line"></i></div>
            <div class="stat-val">RD$ {{ number_format($totalAmount, 0, ',', '.') }}</div>
            <div class="stat-lbl">Total por cobrar</div>
            <div class="stat-ghost"><i class="ri-coins-fill"></i></div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 fa2">
        <div class="stat-card sc-p">
            <div class="stat-ico"><i class="ri-calendar-line"></i></div>
            <div class="stat-val">{{ now()->locale('es')->isoFormat('D MMM') }}</div>
            <div class="stat-lbl">{{ now()->locale('es')->isoFormat('dddd') }}</div>
            <div class="stat-ghost"><i class="ri-calendar-fill"></i></div>
        </div>
    </div>
</div>

{{-- ── Filters + Table ── --}}
<div class="main-card fa1">
    <div class="main-card-head">
        <div style="width:30px;height:30px;border-radius:.4rem;background:rgba(247,184,75,.12);color:var(--ra);display:flex;align-items:center;justify-content:center;">
            <i class="ri-bill-line"></i>
        </div>
        <h5>Facturas pendientes de cobro</h5>
    </div>

    {{-- Filters --}}
    <div class="p-3 border-bottom" style="border-color:var(--border)!important; background:var(--surface);">
        <form method="GET" action="{{ route('receipts.index') }}" id="filter-form">
            <div class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="search-bar">
                        <i class="ri-search-line"></i>
                        <input type="text" name="q" value="{{ request('q') }}"
                               placeholder="Buscar por nombre, cédula o FAC-..."
                               autocomplete="off">
                    </div>
                </div>
                @if($isAdmin)
                <div class="col-md-3">
                    <select name="branch_id" class="form-select form-select-sm"
                            style="border-radius:2rem;border:1.5px solid var(--border);font-size:.83rem;"
                            onchange="document.getElementById('filter-form').submit()">
                        <option value="">Todas las sucursales</option>
                        @foreach($branches as $br)
                            <option value="{{ $br->id }}" {{ request('branch_id') == $br->id ? 'selected' : '' }}>
                                {{ $br->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm" style="border-radius:2rem;font-size:.8rem;">
                        <i class="ri-search-line me-1"></i>Buscar
                    </button>
                    @if(request()->hasAny(['q','branch_id']))
                        <a href="{{ route('receipts.index') }}" class="btn btn-light btn-sm ms-1" style="border-radius:2rem;font-size:.8rem;">
                            <i class="ri-close-line"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="rec-table">
            <thead>
                <tr>
                    <th>Factura</th>
                    <th>Paciente</th>
                    @if($isAdmin)<th>Sucursal</th>@endif
                    <th>Seguro</th>
                    <th class="text-end">Total</th>
                    <th class="text-center">Emitida</th>
                    <th class="text-center">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                    @php
                        $fn    = $inv->patient->first_name ?? '';
                        $ln    = $inv->patient->last_name  ?? '';
                        $ini   = strtoupper(substr($fn,0,1).substr($ln,0,1));
                        $days  = $inv->created_at->diffInDays(now());
                        $urgnt = $days >= 7;
                    @endphp
                    <tr>
                        <td>
                            <div class="inv-num">{{ $inv->invoice_number }}</div>
                            <span class="badge-pend mt-1 d-inline-block">Pendiente</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="pat-av">{{ $ini ?: '?' }}</div>
                                <div>
                                    <div style="font-weight:600;font-size:.85rem;">{{ $fn }} {{ $ln }}</div>
                                    <div style="font-size:.72rem;color:var(--muted);">{{ $inv->patient->cedula ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        @if($isAdmin)
                        <td><span style="font-size:.82rem;">{{ $inv->branch->name ?? '—' }}</span></td>
                        @endif
                        <td>
                            @if($inv->insurance)
                                <span style="background:rgba(10,179,156,.1);color:var(--rt);font-size:.73rem;font-weight:700;padding:.18rem .55rem;border-radius:2rem;">
                                    <i class="ri-shield-check-line me-1"></i>{{ $inv->insurance->name }}
                                </span>
                            @else
                                <span style="color:var(--muted);font-size:.8rem;">—</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="amt-cell">RD$ {{ number_format($inv->total, 2, ',', '.') }}</div>
                            @if($inv->insurance_discount > 0)
                                <div style="font-size:.7rem;color:var(--rt);">Seguro: -RD$ {{ number_format($inv->insurance_discount, 0, ',', '.') }}</div>
                            @endif
                        </td>
                        <td class="text-center">
                            <div style="font-size:.82rem;">{{ $inv->created_at->format('d/m/Y') }}</div>
                            <div class="days-old {{ $urgnt ? 'urgent' : '' }}">
                                hace {{ $days }}d{{ $urgnt ? ' ⚠' : '' }}
                            </div>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn-pay"
                                    onclick="openPayModal({{ $inv->id }})">
                                <i class="ri-cash-line"></i>Cobrar
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isAdmin ? 7 : 6 }}">
                            <div class="empty-state">
                                <i class="ri-check-double-line"></i>
                                <p>
                                    @if(request()->hasAny(['q','branch_id']))
                                        No se encontraron facturas con ese criterio.
                                    @else
                                        ¡Sin facturas pendientes! Todo al día.
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($invoices->hasPages())
    <div class="p-3 border-top d-flex justify-content-center" style="border-color:var(--border)!important;">
        {{ $invoices->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

</div>
</div>

{{-- ════════════════════════════════════════
     MODAL DE PAGO
════════════════════════════════════════ --}}
<div class="modal fade" id="payModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:.75rem;overflow:hidden;">

            <div class="modal-header mh-pay py-3 text-white px-4">
                <div>
                    <h5 class="modal-title mb-0 d-flex align-items-center gap-2">
                        <i class="ri-cash-line fs-18"></i>
                        Registrar Pago
                    </h5>
                    <div style="font-size:.78rem;opacity:.85;margin-top:.1rem;" id="modal-inv-num">—</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <div id="modal-loading" class="text-center py-4">
                    <div class="spinner-border text-primary" style="width:2rem;height:2rem;"></div>
                    <div class="mt-2 text-muted" style="font-size:.84rem;">Cargando datos…</div>
                </div>

                <div id="modal-content" style="display:none;">
                    {{-- ── Resumen de la factura ── --}}
                    <div class="inv-summary mb-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--rp),#6b7fe0);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.78rem;font-weight:800;" id="modal-av">?</div>
                            <div>
                                <div style="font-weight:700;font-size:.9rem;color:var(--ink);" id="modal-patient-name">—</div>
                                <div style="font-size:.74rem;color:var(--muted);" id="modal-patient-cedula">—</div>
                            </div>
                            <div class="ms-auto text-end">
                                <div style="font-size:.72rem;color:var(--muted);" id="modal-branch">—</div>
                                <div style="font-size:.72rem;color:var(--muted);" id="modal-date">—</div>
                            </div>
                        </div>
                        <hr style="margin:.5rem 0;border-color:var(--border);">
                        <div id="modal-items-list"></div>
                        <div class="inv-sum-row" id="row-subtotal" style="display:none;">
                            <span style="color:var(--muted);">Subtotal</span><span id="modal-subtotal">—</span>
                        </div>
                        <div class="inv-sum-row" id="row-insurance" style="display:none;">
                            <span style="color:var(--rt);"><i class="ri-shield-check-line me-1"></i>Descuento seguro</span>
                            <span style="color:var(--rt);" id="modal-insurance">—</span>
                        </div>
                        <div class="inv-sum-row total-row">
                            <span>Total a cobrar</span>
                            <span id="modal-total">—</span>
                        </div>
                    </div>

                    {{-- ── Form de pago ── --}}
                    <form id="pay-form" action="{{ route('receipts.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="invoice_id" id="f-invoice-id">

                        <p style="font-size:.78rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.75rem;">
                            Método de pago
                        </p>

                        <div class="row g-3 mb-3">
                            {{-- Efectivo --}}
                            <div class="col-md-4">
                                <div class="method-card" id="mc-cash" onclick="toggleMethod('cash')">
                                    <div class="method-check"><i class="ri-check-line"></i></div>
                                    <div class="method-ico" style="background:rgba(10,179,156,.1);color:var(--rt);">
                                        <i class="ri-money-dollar-circle-line"></i>
                                    </div>
                                    <div class="method-label">Efectivo</div>
                                    <div class="method-amount">
                                        <input type="number" name="cash_amount" id="i-cash"
                                               min="0" step="0.01" placeholder="RD$ 0.00"
                                               oninput="recalcPay()">
                                    </div>
                                </div>
                            </div>

                            {{-- Tarjeta --}}
                            <div class="col-md-4">
                                <div class="method-card" id="mc-card" onclick="toggleMethod('card')">
                                    <div class="method-check"><i class="ri-check-line"></i></div>
                                    <div class="method-ico" style="background:rgba(64,81,137,.1);color:var(--rp);">
                                        <i class="ri-bank-card-line"></i>
                                    </div>
                                    <div class="method-label">Tarjeta</div>
                                    <div class="method-amount">
                                        <input type="number" name="card_amount" id="i-card"
                                               min="0" step="0.01" placeholder="RD$ 0.00"
                                               oninput="recalcPay()">
                                    </div>
                                    <div class="method-ref">
                                        <input type="text" name="card_reference" id="i-card-ref"
                                               placeholder="Referencia (opcional)">
                                    </div>
                                </div>
                            </div>

                            {{-- Transferencia --}}
                            <div class="col-md-4">
                                <div class="method-card" id="mc-transfer" onclick="toggleMethod('transfer')">
                                    <div class="method-check"><i class="ri-check-line"></i></div>
                                    <div class="method-ico" style="background:rgba(112,102,224,.1);color:var(--rv);">
                                        <i class="ri-exchange-dollar-line"></i>
                                    </div>
                                    <div class="method-label">Transferencia</div>
                                    <div class="method-amount">
                                        <input type="number" name="transfer_amount" id="i-transfer"
                                               min="0" step="0.01" placeholder="RD$ 0.00"
                                               oninput="recalcPay()">
                                    </div>
                                    <div class="method-ref">
                                        <input type="text" name="transfer_reference" id="i-transfer-ref"
                                               placeholder="N° de referencia (opcional)">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Resumen de pago ── --}}
                        <div class="pay-total-box">
                            <div class="pay-total-row" id="pr-cash"    style="display:none;">
                                <span><i class="ri-money-dollar-circle-line me-1"></i>Efectivo</span><span id="pd-cash">—</span>
                            </div>
                            <div class="pay-total-row" id="pr-card"    style="display:none;">
                                <span><i class="ri-bank-card-line me-1"></i>Tarjeta</span><span id="pd-card">—</span>
                            </div>
                            <div class="pay-total-row" id="pr-transfer" style="display:none;">
                                <span><i class="ri-exchange-dollar-line me-1"></i>Transferencia</span><span id="pd-transfer">—</span>
                            </div>
                            <div class="pay-total-row pay-final">
                                <span>Total ingresado</span>
                                <span id="pd-total">RD$ 0.00</span>
                            </div>
                            <div style="font-size:.78rem;margin-top:.35rem;text-align:right;" id="pd-diff"></div>
                        </div>

                        {{-- Vuelto / Cambio --}}
                        <div class="vuelto-box" id="vuelto-box">
                            <div class="vuelto-lbl">
                                <i class="ri-exchange-funds-line"></i>
                                Cambio a devolver al paciente
                            </div>
                            <div class="vuelto-amt" id="vuelto-amt">RD$ 0.00</div>
                            <div class="vuelto-sub" id="vuelto-sub">—</div>
                        </div>

                        {{-- Notas --}}
                        <div class="mt-3">
                            <label style="font-size:.78rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;">
                                Notas (opcional)
                            </label>
                            <textarea name="notes" rows="2"
                                      class="form-control mt-1"
                                      style="border:1.5px solid var(--border);border-radius:.5rem;font-size:.85rem;resize:none;"
                                      placeholder="Observaciones del cobro…"
                                      onclick="event.stopPropagation()"></textarea>
                        </div>

                        {{-- Error server-side --}}
                        @if($errors->has('payment'))
                            <div class="alert alert-danger mt-3 py-2" style="font-size:.83rem;">
                                <i class="ri-error-warning-line me-1"></i>{{ $errors->first('payment') }}
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal"
                        style="border-radius:2rem;font-size:.83rem;">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2"
                        id="btn-confirm-pay"
                        style="border-radius:2rem;padding:.45rem 1.1rem;font-size:.85rem;background:linear-gradient(135deg,var(--rp),var(--rt));border:none;"
                        onclick="submitPay()">
                    <span class="spinner-border spinner-border-sm d-none" id="pay-spin"></span>
                    <i class="ri-check-line" id="pay-icon"></i>
                    <span>Confirmar pago</span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const CSRF          = document.querySelector('meta[name="csrf-token"]').content;
const URL_INV_DATA  = '/api/receipts/invoice-data/';
let   invoiceTotal  = 0;

/* ── Open modal ─────────────────────────────────────────── */
async function openPayModal(invoiceId) {
    resetModal();
    document.getElementById('f-invoice-id').value = invoiceId;
    const modal = new bootstrap.Modal(document.getElementById('payModal'));
    modal.show();

    try {
        const r    = await fetch(URL_INV_DATA + invoiceId, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        const data = await r.json();
        if (!r.ok) throw new Error(data.error || 'Error al cargar');
        fillModal(data);
    } catch (e) {
        showToast(e.message, 'err');
        bootstrap.Modal.getInstance(document.getElementById('payModal')).hide();
    }
}

function resetModal() {
    document.getElementById('modal-loading').style.display  = '';
    document.getElementById('modal-content').style.display  = 'none';
    ['cash','card','transfer'].forEach(m => {
        document.getElementById('mc-'+m).classList.remove('selected');
        const inp = document.getElementById('i-'+m);
        if (inp) inp.value = '';
    });
    ['i-card-ref','i-transfer-ref'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    document.querySelector('[name="notes"]').value = '';
    recalcPay();
}

function fillModal(d) {
    invoiceTotal = d.total;

    document.getElementById('modal-inv-num').textContent     = d.invoice_number;
    document.getElementById('modal-patient-name').textContent = d.patient_name;
    document.getElementById('modal-patient-cedula').textContent = 'Cédula: ' + d.patient_cedula;
    document.getElementById('modal-branch').textContent      = d.branch_name;
    document.getElementById('modal-date').textContent        = 'Emitida: ' + d.created_at;
    document.getElementById('modal-av').textContent          =
        d.patient_name.split(' ').map(w => w[0] || '').slice(0,2).join('').toUpperCase();

    // Items list
    const itemsBox = document.getElementById('modal-items-list');
    itemsBox.innerHTML = d.items.map(it =>
        `<div class="inv-sum-row">
            <span style="color:var(--muted)">${it.name} ×${it.qty}</span>
            <span>RD$ ${fmt(it.subtotal)}</span>
        </div>`
    ).join('');

    // Subtotal / insurance rows
    if (d.insurance_discount > 0) {
        document.getElementById('row-subtotal').style.display  = '';
        document.getElementById('row-insurance').style.display = '';
        document.getElementById('modal-subtotal').textContent  = 'RD$ ' + fmt(d.subtotal);
        document.getElementById('modal-insurance').textContent = '− RD$ ' + fmt(d.insurance_discount);
        if (d.insurance_name) {
            document.getElementById('row-insurance').querySelector('span').innerHTML =
                `<i class="ri-shield-check-line me-1"></i>${d.insurance_name}`;
        }
    }
    document.getElementById('modal-total').textContent = 'RD$ ' + fmt(d.total);

    // Auto-fill cash with full amount as convenience
    document.getElementById('mc-cash').classList.add('selected');
    document.getElementById('i-cash').value = d.total.toFixed(2);

    document.getElementById('modal-loading').style.display = 'none';
    document.getElementById('modal-content').style.display = '';
    recalcPay();
}

/* ── Toggle method card ─────────────────────────────────── */
function toggleMethod(m) {
    const card = document.getElementById('mc-'+m);
    const inp  = document.getElementById('i-'+m);
    const isOn = card.classList.contains('selected');

    if (isOn) {
        card.classList.remove('selected');
        if (inp) inp.value = '';
    } else {
        card.classList.add('selected');
        if (inp) { inp.focus(); }
    }
    recalcPay();
}

/* ── Recalculate totals ─────────────────────────────────── */
function recalcPay() {
    const cash     = parseFloat(document.getElementById('i-cash')?.value)     || 0;
    const card     = parseFloat(document.getElementById('i-card')?.value)     || 0;
    const transfer = parseFloat(document.getElementById('i-transfer')?.value) || 0;
    const total    = cash + card + transfer;

    // Show/hide breakdown rows
    toggleRow('pr-cash',     'pd-cash',     cash);
    toggleRow('pr-card',     'pd-card',     card);
    toggleRow('pr-transfer', 'pd-transfer', transfer);

    document.getElementById('pd-total').textContent = 'RD$ ' + fmt(total);

    const diffEl    = document.getElementById('pd-diff');
    const vueltoBox = document.getElementById('vuelto-box');
    const vueltoAmt = document.getElementById('vuelto-amt');
    const vueltoSub = document.getElementById('vuelto-sub');

    if (invoiceTotal <= 0) { diffEl.textContent = ''; vueltoBox.style.display = 'none'; return; }

    const diff       = total - invoiceTotal;
    // El vuelto en efectivo: lo que sobra del efectivo después de cubrir
    // la parte que tarjeta/transferencia no cubren
    const nonCash    = card + transfer;
    const cashNeeded = Math.max(0, invoiceTotal - nonCash);  // cuánto efectivo se necesita
    const vuelto     = cash - cashNeeded;                    // excedente en efectivo

    // ── Vuelto box ──
    if (vuelto > 0.009) {
        vueltoBox.style.display = '';
        vueltoAmt.textContent   = 'RD$ ' + fmt(vuelto);
        vueltoSub.textContent   = `Efectivo recibido: RD$ ${fmt(cash)} · Efectivo necesario: RD$ ${fmt(cashNeeded)}`;
    } else {
        vueltoBox.style.display = 'none';
    }

    // ── Status line ──
    const cardOrTransferExcess = (card + transfer) - Math.min(invoiceTotal, card + transfer);
    if (Math.abs(diff) < 0.01) {
        diffEl.innerHTML = '<span class="pay-diff-ok"><i class="ri-check-double-line me-1"></i>Monto exacto ✓</span>';
    } else if (vuelto > 0.009 && Math.abs(diff - vuelto) < 0.01) {
        // El excedente es solo de efectivo → válido
        diffEl.innerHTML = '<span class="pay-diff-ok"><i class="ri-check-double-line me-1"></i>Pago con cambio ✓</span>';
    } else if (diff > 0 && vuelto <= 0.009) {
        // Excedente en tarjeta o transferencia → error
        diffEl.innerHTML = `<span class="pay-diff-warn"><i class="ri-error-warning-line me-1"></i>Exceso en tarjeta/transferencia: RD$ ${fmt(diff)}</span>`;
    } else if (diff < -0.009) {
        diffEl.innerHTML = `<span class="pay-diff-warn"><i class="ri-error-warning-line me-1"></i>Faltan RD$ ${fmt(Math.abs(diff))}</span>`;
    }
}

function toggleRow(rowId, valId, amount) {
    const row = document.getElementById(rowId);
    const val = document.getElementById(valId);
    if (!row || !val) return;
    row.style.display = amount > 0 ? '' : 'none';
    val.textContent   = 'RD$ ' + fmt(amount);
}

/* ── Submit ─────────────────────────────────────────────── */
function submitPay() {
    const cash     = parseFloat(document.getElementById('i-cash')?.value)     || 0;
    const card     = parseFloat(document.getElementById('i-card')?.value)     || 0;
    const transfer = parseFloat(document.getElementById('i-transfer')?.value) || 0;
    const total    = cash + card + transfer;

    if (total <= 0) { showToast('Ingresa al menos un monto de pago.', 'err'); return; }

    // Validar: tarjeta + transferencia no pueden exceder el total de la factura
    if ((card + transfer) > invoiceTotal + 0.01) {
        showToast('Tarjeta y/o transferencia no pueden exceder el total de la factura.', 'err'); return;
    }

    // El efectivo puede ser mayor (genera vuelto). Lo demás debe cuadrar.
    const nonCash    = card + transfer;
    const cashNeeded = Math.max(0, invoiceTotal - nonCash);
    if (cash < cashNeeded - 0.01) {
        showToast(`Faltan RD$ ${fmt(cashNeeded - cash)} para completar el pago.`, 'err'); return;
    }

    document.getElementById('btn-confirm-pay').disabled = true;
    document.getElementById('pay-spin').classList.remove('d-none');
    document.getElementById('pay-icon').classList.add('d-none');
    document.getElementById('pay-form').submit();
}

/* ── Toast ──────────────────────────────────────────────── */
function showToast(msg, type) {
    const d = document.createElement('div');
    d.className = 'T-item t-' + (type || 'ok');
    d.innerHTML = `<i class="ri-${type==='err'?'error-warning':'checkbox-circle'}-line"></i>${msg}`;
    document.getElementById('toast-c').appendChild(d);
    setTimeout(() => { d.style.transition='opacity .3s'; d.style.opacity='0'; setTimeout(()=>d.remove(),320); }, 3500);
}

/* ── Format ─────────────────────────────────────────────── */
function fmt(n) {
    return parseFloat(n||0).toLocaleString('es-DO',{minimumFractionDigits:2,maximumFractionDigits:2});
}

/* ── Re-open modal if validation failed ─────────────────── */
@if($errors->has('payment'))
    document.addEventListener('DOMContentLoaded', () => {
        const inv = document.querySelector('[name="invoice_id"]')?.value || '';
        if (inv) openPayModal(inv);
    });
@endif

@if(session('success'))
    document.addEventListener('DOMContentLoaded', () => showToast('{{ session('success') }}', 'ok'));
@endif
</script>
@endpush

</x-app-layout>