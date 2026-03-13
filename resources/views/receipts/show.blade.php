<x-app-layout>
<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

<style>
:root {
    --rp:#405189; --rt:#0ab39c; --ra:#f7b84b; --rr:#f06548; --rv:#7066e0;
    --ink:#1e2535; --muted:#6b7a99; --border:#edf0f7; --surface:#f8faff;
}

/* ── Action bar (hidden on print) ── */
.action-bar {
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom:1.25rem; flex-wrap:wrap; gap:.75rem;
}
.btn-print {
    background:linear-gradient(135deg,var(--rp),var(--rt));
    color:#fff; border:none; border-radius:2rem;
    padding:.5rem 1.2rem; font-size:.85rem; font-weight:700;
    cursor:pointer; transition:opacity .18s; display:flex; align-items:center; gap:.4rem;
}
.btn-print:hover { opacity:.88; }

/* ── Receipt paper ── */
.receipt-paper {
    background:#fff; border-radius:.85rem;
    box-shadow:0 4px 40px rgba(64,81,137,.1);
    overflow:hidden; max-width:780px; margin:0 auto;
}

/* ── Header band ── */
.rec-header {
    background:linear-gradient(135deg,#405189 0%,#2d3e7a 50%,#0ab39c 100%);
    padding:2rem 2.25rem; color:#fff; position:relative; overflow:hidden;
}
.rec-header::before {
    content:''; position:absolute; right:-30px; top:-30px;
    width:160px; height:160px; border-radius:50%; background:rgba(255,255,255,.07);
}
.rec-header::after {
    content:''; position:absolute; right:40px; bottom:-50px;
    width:120px; height:120px; border-radius:50%; background:rgba(255,255,255,.05);
}
.rec-brand { font-size:1.4rem; font-weight:900; letter-spacing:-.03em; position:relative; z-index:1; }
.rec-brand span { opacity:.7; font-weight:400; font-size:.9rem; display:block; margin-top:.1rem; }
.rec-num { font-family:monospace; font-size:1.5rem; font-weight:800; letter-spacing:.05em; position:relative; z-index:1; }
.rec-num small { font-size:.72rem; opacity:.7; display:block; font-family:sans-serif; font-weight:400; letter-spacing:0; }

/* Status pill */
.status-pill {
    display:inline-flex; align-items:center; gap:.4rem;
    border-radius:2rem; padding:.3rem .9rem; font-size:.8rem; font-weight:700;
    background:rgba(10,179,156,.2); color:#7fffea; border:1px solid rgba(255,255,255,.2);
    margin-top:.6rem; position:relative; z-index:1;
}

/* ── Body ── */
.rec-body { padding:1.75rem 2.25rem; }

/* Info grid */
.info-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; margin-bottom:1.5rem; }
@media(max-width:580px) { .info-grid { grid-template-columns:1fr; } }

.info-box { background:var(--surface); border-radius:.6rem; padding:.9rem 1rem; border:1px solid var(--border); }
.info-box-lbl { font-size:.67rem; font-weight:800; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); margin-bottom:.35rem; }
.info-box-val { font-size:.9rem; font-weight:700; color:var(--ink); }
.info-box-sub { font-size:.75rem; color:var(--muted); margin-top:.12rem; }

/* ── Divider ── */
.rec-divider { border:none; border-top:1px dashed var(--border); margin:1.25rem 0; }

/* ── Payment methods ── */
.method-row {
    display:flex; align-items:center; justify-content:space-between;
    padding:.65rem 1rem; border-radius:.5rem; margin-bottom:.5rem;
    border:1px solid var(--border);
}
.method-row:last-child { margin-bottom:0; }
.method-ico-sm {
    width:32px; height:32px; border-radius:.4rem; flex-shrink:0;
    display:flex; align-items:center; justify-content:center; font-size:.95rem;
}
.method-row-name { font-size:.85rem; font-weight:600; color:var(--ink); flex:1; margin-left:.75rem; }
.method-row-ref  { font-size:.73rem; color:var(--muted); }
.method-row-amt  { font-size:.92rem; font-weight:800; color:var(--ink); }

/* ── Totals ── */
.totals-box { background:var(--surface); border-radius:.65rem; overflow:hidden; border:1px solid var(--border); }
.tot-row { display:flex; justify-content:space-between; padding:.6rem 1rem; border-bottom:1px solid var(--border); font-size:.85rem; }
.tot-row:last-child { border-bottom:none; background:linear-gradient(135deg,rgba(64,81,137,.06),rgba(10,179,156,.06)); padding:.75rem 1rem; }
.tot-lbl { color:var(--muted); }
.tot-val { font-weight:600; color:var(--ink); }
.tot-row:last-child .tot-lbl,
.tot-row:last-child .tot-val { font-size:1rem; font-weight:800; color:var(--rp); }

/* ── Invoice items ── */
.items-table { width:100%; border-collapse:collapse; }
.items-table th { font-size:.67rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--muted); padding:.55rem .75rem; border-bottom:2px solid var(--border); }
.items-table td { padding:.6rem .75rem; border-bottom:1px solid var(--border); font-size:.83rem; color:var(--ink); }
.items-table tr:last-child td { border-bottom:none; }

/* ── Footer ── */
.rec-footer {
    background:var(--surface); border-top:1px solid var(--border);
    padding:1rem 2.25rem; text-align:center;
    font-size:.75rem; color:var(--muted);
}

/* ── Mixed badge ── */
.mixed-badge {
    display:inline-flex; align-items:center; gap:.3rem;
    background:rgba(112,102,224,.1); color:var(--rv);
    border-radius:2rem; padding:.18rem .65rem; font-size:.73rem; font-weight:700;
    border:1px solid rgba(112,102,224,.2);
}

/* ── Print styles ── */
@media print {
    .action-bar, .page-title-box, nav, .navbar, .sidebar, .breadcrumb { display:none!important; }
    body, .page-content, .container-fluid { padding:0!important; margin:0!important; background:#fff!important; }
    .receipt-paper { box-shadow:none!important; border-radius:0!important; max-width:100%!important; }
    .rec-header { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    .info-box, .method-row, .totals-box, .items-table th { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
}
</style>

{{-- ── Action bar ── --}}
<div class="action-bar">
    <div>
        <h4 class="mb-0" style="font-weight:800;">Recibo de Pago</h4>
        <ol class="breadcrumb mb-0 mt-1" style="font-size:.78rem;">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('receipts.index') }}">Recibos</a></li>
            <li class="breadcrumb-item active">{{ $receipt->receipt_number }}</li>
        </ol>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('invoices.show', $receipt->invoice) }}"
           class="btn btn-light btn-sm d-flex align-items-center gap-1"
           style="border-radius:2rem;font-size:.82rem;">
            <i class="ri-file-text-line"></i>Ver factura
        </a>
        <a href="{{ route('receipts.index') }}"
           class="btn btn-light btn-sm d-flex align-items-center gap-1"
           style="border-radius:2rem;font-size:.82rem;">
            <i class="ri-arrow-left-line"></i>Volver
        </a>
        <button onclick="window.print()" class="btn-print">
            <i class="ri-printer-line"></i>Imprimir
        </button>
    </div>
</div>

{{-- ══════════════════════
     RECEIPT PAPER
══════════════════════ --}}
<div class="receipt-paper">

    {{-- Header --}}
    <div class="rec-header">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="rec-brand">
                    <i class="ri-heart-pulse-line me-2" style="opacity:.8;"></i>Audiofon
                    <span>Clínica Auditiva</span>
                </div>
                <div class="status-pill mt-2">
                    <i class="ri-checkbox-circle-line"></i> Pago registrado
                </div>
            </div>
            <div class="text-end">
                <div class="rec-num">
                    {{ $receipt->receipt_number }}
                    <small>Recibo de pago</small>
                </div>
                <div style="font-size:.78rem;opacity:.75;margin-top:.3rem;position:relative;z-index:1;">
                    {{ $receipt->created_at->format('d/m/Y — g:i A') }}
                </div>
                @if($receipt->is_mixed)
                    <div class="mt-1" style="position:relative;z-index:1;">
                        <span class="mixed-badge" style="background:rgba(255,255,255,.15);color:#fff;border-color:rgba(255,255,255,.25);">
                            <i class="ri-equalizer-line"></i>Pago mixto
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="rec-body">

        {{-- Info grid --}}
        <div class="info-grid">
            <div class="info-box">
                <div class="info-box-lbl"><i class="ri-user-line me-1"></i>Paciente</div>
                <div class="info-box-val">
                    {{ $receipt->invoice->patient->first_name }}
                    {{ $receipt->invoice->patient->last_name }}
                </div>
                <div class="info-box-sub">
                    Cédula: {{ $receipt->invoice->patient->cedula ?? '—' }}
                    @if($receipt->invoice->patient->phone)
                        &nbsp;·&nbsp; {{ $receipt->invoice->patient->phone }}
                    @endif
                </div>
            </div>

            <div class="info-box">
                <div class="info-box-lbl"><i class="ri-building-2-line me-1"></i>Sucursal / Cobrado por</div>
                <div class="info-box-val">{{ $receipt->branch->name }}</div>
                <div class="info-box-sub" style="font-weight:600;color:#344563;">
                    {{ $receipt->user->name }}
                </div>
                <div class="info-box-sub" style="text-transform:capitalize;">
                    {{ $receipt->user->role->name ?? '—' }}
                </div>
            </div>

            <div class="info-box">
                <div class="info-box-lbl"><i class="ri-file-text-line me-1"></i>Factura asociada</div>
                <div class="info-box-val" style="font-family:monospace;">
                    {{ $receipt->invoice->invoice_number }}
                </div>
                <div class="info-box-sub">Emitida el {{ $receipt->invoice->created_at->format('d/m/Y') }}</div>
            </div>

            @if($receipt->invoice->insurance)
            <div class="info-box">
                <div class="info-box-lbl"><i class="ri-shield-check-line me-1"></i>Seguro médico</div>
                <div class="info-box-val">{{ $receipt->invoice->insurance->name }}</div>
                @if($receipt->invoice->authorization_number)
                    <div class="info-box-sub">Autorización: {{ $receipt->invoice->authorization_number }}</div>
                @endif
                <div class="info-box-sub" style="color:var(--rt);">
                    Descuento: RD$ {{ number_format($receipt->invoice->insurance_discount, 2, ',', '.') }}
                </div>
            </div>
            @endif
        </div>

        <hr class="rec-divider">

        {{-- Items --}}
        <p style="font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);margin-bottom:.65rem;">
            Servicios facturados
        </p>
        <div class="table-responsive mb-0">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="text-align:left;">Servicio</th>
                        <th style="text-align:center;width:60px;">Cant.</th>
                        <th style="text-align:right;width:120px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($receipt->invoice->items as $item)
                    <tr>
                        <td>{{ $item->service->name }}</td>
                        <td style="text-align:center;">{{ $item->quantity }}</td>
                        <td style="text-align:right;font-weight:600;">RD$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <hr class="rec-divider">

        {{-- Totals --}}
        <div class="row g-3">
            <div class="col-md-6">
                {{-- Payment methods --}}
                <p style="font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);margin-bottom:.65rem;">
                    Métodos de pago
                </p>

                @if($receipt->cash_amount > 0)
                @php
                    $nonCash    = ($receipt->card_amount ?? 0) + ($receipt->transfer_amount ?? 0);
                    $cashNeeded = max(0, $receipt->total_paid - $nonCash);
                    $vuelto     = $receipt->cash_amount - $cashNeeded;
                @endphp
                <div class="method-row">
                    <div class="method-ico-sm" style="background:rgba(10,179,156,.1);color:var(--rt);">
                        <i class="ri-money-dollar-circle-line"></i>
                    </div>
                    <div style="flex:1;margin-left:.75rem;">
                        <div class="method-row-name" style="margin-left:0;">Efectivo</div>
                        @if($vuelto > 0.009)
                            <div class="method-row-ref">Recibido: RD$ {{ number_format($receipt->cash_amount, 2, ',', '.') }}</div>
                        @endif
                    </div>
                    <span class="method-row-amt">RD$ {{ number_format($cashNeeded, 2, ',', '.') }}</span>
                </div>
                @if($vuelto > 0.009)
                <div class="method-row" style="background:rgba(10,179,156,.05);border-color:rgba(10,179,156,.25);">
                    <div class="method-ico-sm" style="background:rgba(10,179,156,.15);color:var(--rt);">
                        <i class="ri-arrow-left-right-line"></i>
                    </div>
                    <div style="flex:1;margin-left:.75rem;">
                        <div class="method-row-name" style="margin-left:0;color:var(--rt);">Vuelto entregado</div>
                        <div class="method-row-ref">Cambio al paciente</div>
                    </div>
                    <span class="method-row-amt" style="color:var(--rt);">RD$ {{ number_format($vuelto, 2, ',', '.') }}</span>
                </div>
                @endif
                @endif

                @if($receipt->card_amount > 0)
                <div class="method-row">
                    <div class="method-ico-sm" style="background:rgba(64,81,137,.1);color:var(--rp);">
                        <i class="ri-bank-card-line"></i>
                    </div>
                    <div style="flex:1;margin-left:.75rem;">
                        <div class="method-row-name" style="margin-left:0;">Tarjeta</div>
                        @if($receipt->card_reference)
                            <div class="method-row-ref">Ref: {{ $receipt->card_reference }}</div>
                        @endif
                    </div>
                    <span class="method-row-amt">RD$ {{ number_format($receipt->card_amount, 2, ',', '.') }}</span>
                </div>
                @endif

                @if($receipt->transfer_amount > 0)
                <div class="method-row">
                    <div class="method-ico-sm" style="background:rgba(112,102,224,.1);color:var(--rv);">
                        <i class="ri-exchange-dollar-line"></i>
                    </div>
                    <div style="flex:1;margin-left:.75rem;">
                        <div class="method-row-name" style="margin-left:0;">Transferencia</div>
                        @if($receipt->transfer_reference)
                            <div class="method-row-ref">Ref: {{ $receipt->transfer_reference }}</div>
                        @endif
                    </div>
                    <span class="method-row-amt">RD$ {{ number_format($receipt->transfer_amount, 2, ',', '.') }}</span>
                </div>
                @endif

                @if($receipt->notes)
                <div style="margin-top:.75rem;background:var(--surface);border-radius:.5rem;padding:.65rem .9rem;border:1px solid var(--border);">
                    <div style="font-size:.68rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:.25rem;">
                        <i class="ri-sticky-note-line me-1"></i>Notas
                    </div>
                    <div style="font-size:.82rem;color:var(--ink);">{{ $receipt->notes }}</div>
                </div>
                @endif
            </div>

            <div class="col-md-6">
                {{-- Totals summary --}}
                <p style="font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);margin-bottom:.65rem;">
                    Resumen
                </p>
                <div class="totals-box">
                    <div class="tot-row">
                        <span class="tot-lbl">Subtotal servicios</span>
                        <span class="tot-val">RD$ {{ number_format($receipt->invoice->subtotal, 2, ',', '.') }}</span>
                    </div>
                    @if($receipt->invoice->insurance_discount > 0)
                    <div class="tot-row">
                        <span class="tot-lbl" style="color:var(--rt);">Descuento seguro</span>
                        <span class="tot-val" style="color:var(--rt);">− RD$ {{ number_format($receipt->invoice->insurance_discount, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="tot-row">
                        <span class="tot-lbl">Total factura</span>
                        <span class="tot-val">RD$ {{ number_format($receipt->invoice->total, 2, ',', '.') }}</span>
                    </div>
                    <div class="tot-row">
                        <span class="tot-lbl">Total pagado</span>
                        <span class="tot-val" style="color:var(--rt);">RD$ {{ number_format($receipt->total_paid, 2, ',', '.') }}</span>
                    </div>
                </div>

                <div style="margin-top:.75rem;text-align:center;">
                    <span style="background:rgba(10,179,156,.1);color:var(--rt);border-radius:2rem;padding:.3rem 1rem;font-size:.8rem;font-weight:700;border:1px solid rgba(10,179,156,.2);">
                        <i class="ri-checkbox-circle-line me-1"></i>
                        {{ $receipt->payment_summary }}
                    </span>
                </div>
            </div>
        </div>

    </div>

    {{-- Footer --}}
    <div class="rec-footer">
        <i class="ri-heart-pulse-line me-1"></i>
        Audiofon · Gracias por su preferencia ·
        Recibo generado el {{ $receipt->created_at->format('d/m/Y') }} a las {{ $receipt->created_at->format('g:i A') }}
    </div>
</div>

<div style="height:2rem;"></div>
</div>
</div>
</x-app-layout>