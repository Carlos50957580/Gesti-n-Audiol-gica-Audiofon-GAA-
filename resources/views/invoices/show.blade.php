<x-app-layout>
<div class="page-content" style="padding-top:0;">
<div class="container-fluid pt-3">

<style>
:root {
    --rp:#405189; --rt:#0ab39c; --ra:#f7b84b; --rr:#f06548; --rv:#7066e0;
    --ink:#1e2535; --muted:#6b7a99; --border:#edf0f7; --surface:#f8faff;
}

.btn-print-thermal {
    background: linear-gradient(135deg, #0ab39c, #089b86);
    color: #fff; border: none; border-radius: 2rem;
    padding: .5rem 1.2rem; font-size: .85rem; font-weight: 700;
    cursor: pointer; transition: opacity .18s;
    display: flex; align-items: center; gap: .4rem;
}
.btn-print-thermal:hover { opacity: .88; }

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

/* ── Invoice paper ── */
.invoice-paper {
    background:#fff; border-radius:.85rem;
    box-shadow:0 4px 40px rgba(64,81,137,.1);
    overflow:hidden; max-width:780px; margin:0 auto;
}

/* ── Header band ── */
.inv-header {
    background:linear-gradient(135deg,#405189 0%,#2d3e7a 50%,#0ab39c 100%);
    padding:2rem 2.25rem; color:#fff; position:relative; overflow:hidden;
}
.inv-header::before {
    content:''; position:absolute; right:-30px; top:-30px;
    width:160px; height:160px; border-radius:50%; background:rgba(255,255,255,.07);
}
.inv-header::after {
    content:''; position:absolute; right:40px; bottom:-50px;
    width:120px; height:120px; border-radius:50%; background:rgba(255,255,255,.05);
}
.inv-brand { 
    font-size:1.4rem; font-weight:900; letter-spacing:-.03em; position:relative; z-index:1; 
}
.inv-brand span { 
    opacity:.7; font-weight:400; font-size:.9rem; display:block; margin-top:.1rem; 
}
.inv-num { font-family:monospace; font-size:1.5rem; font-weight:800; letter-spacing:.05em; position:relative; z-index:1; }
.inv-num small { font-size:.72rem; opacity:.7; display:block; font-family:sans-serif; font-weight:400; letter-spacing:0; }

/* Status pill */
.status-pill {
    display:inline-flex; align-items:center; gap:.4rem;
    border-radius:2rem; padding:.3rem .9rem; font-size:.8rem; font-weight:700;
    position:relative; z-index:1;
}
.status-pill.pendiente { background:rgba(247,184,77,.2); color:#f7b84d; border:1px solid rgba(247,184,77,.3); }
.status-pill.pagada { background:rgba(10,179,156,.2); color:#7fffea; border:1px solid rgba(10,179,156,.3); }
.status-pill.cancelada { background:rgba(240,101,72,.2); color:#ffb3a3; border:1px solid rgba(240,101,72,.3); }

/* DGII pill */
.dgii-pill {
    display:inline-flex; align-items:center; gap:.4rem;
    border-radius:2rem; padding:.3rem .9rem; font-size:.78rem; font-weight:700;
    position:relative; z-index:1; margin-left:.4rem;
}
.dgii-pill.aceptado { background:rgba(10,179,156,.25); color:#7fffea; border:1px solid rgba(10,179,156,.4); }
.dgii-pill.enviado { background:rgba(112,102,224,.25); color:#c9c4ff; border:1px solid rgba(112,102,224,.4); }
.dgii-pill.rechazado, .dgii-pill.error { background:rgba(240,101,72,.25); color:#ffb3a3; border:1px solid rgba(240,101,72,.4); }
.dgii-pill.pendiente { background:rgba(247,184,77,.25); color:#ffd989; border:1px solid rgba(247,184,77,.4); }

/* ── Body ── */
.inv-body { padding:1.75rem 2.25rem; }

/* Info grid */
.info-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; margin-bottom:1.5rem; }
@media(max-width:580px) { .info-grid { grid-template-columns:1fr; } }

.info-box { background:var(--surface); border-radius:.6rem; padding:.9rem 1rem; border:1px solid var(--border); }
.info-box-lbl { font-size:.67rem; font-weight:800; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); margin-bottom:.35rem; }
.info-box-val { font-size:.9rem; font-weight:700; color:var(--ink); }
.info-box-sub { font-size:.75rem; color:var(--muted); margin-top:.12rem; }

/* ── e-CF block ── */
.ecf-block {
    background:linear-gradient(135deg, rgba(64,81,137,.04), rgba(10,179,156,.04));
    border:1px dashed rgba(64,81,137,.25);
    border-radius:.65rem; padding:1rem 1.15rem; margin-bottom:1.5rem;
}
.ecf-block-title {
    font-size:.72rem; font-weight:800; text-transform:uppercase; letter-spacing:.1em;
    color:var(--rp); margin-bottom:.75rem; display:flex; align-items:center; gap:.4rem;
}
.ecf-grid { display:grid; grid-template-columns:1fr 1fr; gap:.85rem; }
@media(max-width:580px) { .ecf-grid { grid-template-columns:1fr; } }
.ecf-item-lbl { font-size:.65rem; font-weight:800; text-transform:uppercase; letter-spacing:.08em; color:var(--muted); margin-bottom:.2rem; }
.ecf-item-val { font-size:.85rem; font-weight:700; color:var(--ink); font-family:monospace; word-break:break-all; }
.ecf-qr {
    display:flex; align-items:center; gap:1rem;
    padding:.85rem 1rem; background:#fff; border-radius:.5rem;
    border:1px solid var(--border); margin-top:.85rem;
}
.ecf-qr-img {
    width:90px; height:90px; border-radius:.4rem; background:#fff;
    border:1px solid var(--border); padding:.25rem; flex-shrink:0;
}
.ecf-qr-info { font-size:.78rem; color:var(--muted); line-height:1.5; }
.ecf-qr-info strong { color:var(--ink); }

/* Estado pill en bloque e-CF */
.ecf-status {
    display:inline-flex; align-items:center; gap:.35rem;
    padding:.22rem .7rem; border-radius:2rem; font-size:.72rem; font-weight:800;
    text-transform:uppercase; letter-spacing:.04em;
}
.ecf-status.aceptado { background:rgba(10,179,156,.15); color:#067a6b; }
.ecf-status.enviado { background:rgba(112,102,224,.15); color:#4f44b8; }
.ecf-status.rechazado, .ecf-status.error { background:rgba(240,101,72,.15); color:#c0392b; }
.ecf-status.pendiente, .ecf-status.procesando { background:rgba(247,184,77,.15); color:#a4730a; }
.ecf-status.sin_enviar { background:#f0f2f7; color:#6b7a99; }

/* ── Divider ── */
.inv-divider { border:none; border-top:1px dashed var(--border); margin:1.25rem 0; }

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
.inv-footer {
    background:var(--surface); border-top:1px solid var(--border);
    padding:1rem 2.25rem; text-align:center;
    font-size:.75rem; color:var(--muted);
}

/* ── Print styles ── */
@media print {
    .action-bar, .page-title-box, nav, .navbar, .sidebar, .breadcrumb,
    .no-print, .ecf-actions { display:none!important; }
    body, .page-content, .container-fluid { padding:0!important; margin:0!important; background:#fff!important; }
    .invoice-paper { box-shadow:none!important; border-radius:0!important; max-width:100%!important; }
    .inv-header { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    .info-box, .totals-box, .items-table th, .ecf-block { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    .collapse:not(.show) { display:block!important; }
}
</style>

{{-- ── Action bar ── --}}
<div class="action-bar">
    <div>
        <h4 class="mb-0" style="font-weight:800;">
            Factura {{ $invoice->invoice_number }}
        </h4>
        <ol class="breadcrumb mb-0 mt-1" style="font-size:.78rem;">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">Facturas</a></li>
            <li class="breadcrumb-item active">{{ $invoice->invoice_number }}</li>
        </ol>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('invoices.index') }}"
           class="btn btn-light btn-sm d-flex align-items-center gap-1"
           style="border-radius:2rem;font-size:.82rem;">
            <i class="ri-arrow-left-line"></i>Volver
        </a>

        <button onclick="window.print()" class="btn-print">
            <i class="ri-printer-line"></i>Imprimir
        </button>
        <button type="button" class="btn-print-thermal" onclick="printThermal()">
            <i class="ri-receipt-line"></i>Ticket POS
        </button>

      

        @if($invoice->status === 'pendiente')
            <a href="{{ route('invoices.cancel', $invoice) }}"
               class="btn btn-danger btn-sm d-flex align-items-center gap-1"
               style="border-radius:2rem;font-size:.82rem;"
               onclick="return confirm('¿Estás seguro de cancelar esta factura?')">
                <i class="ri-close-circle-line"></i>Cancelar
            </a>
        @endif
    </div>
</div>

{{-- Alertas de sesión --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="ri-check-double-line me-1"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="ri-error-warning-line me-1"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="ri-information-line me-1"></i>{{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ══════════════════════
     INVOICE PAPER
══════════════════════ --}}
<div class="invoice-paper">

    {{-- Header con datos de la empresa --}}
    <div class="inv-header">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="inv-brand">
                    @if($company['logo'])
                        <img src="{{ asset('storage/' . $company['logo']) }}"
                             alt="{{ $company['name'] }}" height="40" style="filter:brightness(0) invert(1);margin-right:10px;">
                    @else
                        <i class="ri-heart-pulse-line me-2" style="opacity:.8;"></i>
                    @endif
                    {{ $company['name'] }}
                    <span>{{ $company['slogan'] }}</span>
                </div>
                <div style="font-size:.78rem;opacity:.8;margin-top:.3rem;position:relative;z-index:1;">
                    {{ $company['address'] }}
                </div>
                <div style="font-size:.75rem;opacity:.7;margin-top:.1rem;position:relative;z-index:1;">
                    RNC: {{ $company['rnc'] }} | Tel: {{ $company['phone'] }}
                    @if($company['email'])
                        | {{ $company['email'] }}
                    @endif
                </div>
                <div class="mt-2">
                    <span class="status-pill {{ $invoice->status }}">
                        <i class="ri-checkbox-circle-line"></i>
                        {{ ucfirst($invoice->status) }}
                    </span>

                    {{-- ✅ Estado DGII en el header --}}
                    @if($invoice->ncf_type)
                        @php
                            $dgiiClass = $invoice->enviada_dgii
                                ? ($invoice->estado_dgii ?? 'pendiente')
                                : 'sin_enviar';
                            $dgiiLabel = $invoice->enviada_dgii
                                ? ucfirst(str_replace('_', ' ', $invoice->estado_dgii ?? 'enviado'))
                                : 'Sin enviar a DGII';
                            $dgiiIcon = match($dgiiClass) {
                                'aceptado' => 'ri-shield-check-line',
                                'enviado' => 'ri-send-plane-line',
                                'rechazado', 'error' => 'ri-error-warning-line',
                                default => 'ri-time-line',
                            };
                        @endphp
                        <span class="dgii-pill {{ $dgiiClass }}">
                            <i class="{{ $dgiiIcon }}"></i>
                            {{ $dgiiLabel }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="text-end">
                <div class="inv-num">
                    {{ $invoice->invoice_number }}
                    <small>Factura</small>
                </div>
                <div style="font-size:.78rem;opacity:.75;margin-top:.3rem;position:relative;z-index:1;">
                    {{ $invoice->created_at->format('d/m/Y — g:i A') }}
                </div>
                @if($invoice->encf)
                    <div style="font-size:.8rem;opacity:.9;margin-top:.15rem;position:relative;z-index:1;font-family:monospace;font-weight:700;">
                        e-NCF: {{ $invoice->encf }}
                    </div>
                @elseif($invoice->ncf)
                    <div style="font-size:.75rem;opacity:.8;margin-top:.1rem;position:relative;z-index:1;">
                        NCF: {{ $invoice->ncf }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="inv-body">

        {{-- Info grid --}}
        <div class="info-grid">
            <div class="info-box">
                <div class="info-box-lbl"><i class="ri-user-line me-1"></i>Paciente</div>
                <div class="info-box-val">
                    {{ $invoice->patient->first_name }}
                    {{ $invoice->patient->last_name }}
                </div>
                <div class="info-box-sub">
                    Cédula: {{ $invoice->patient->cedula ?? '—' }}
                    @if($invoice->patient->phone)
                        &nbsp;·&nbsp; {{ $invoice->patient->phone }}
                    @endif
                </div>
            </div>

            <div class="info-box">
                <div class="info-box-lbl"><i class="ri-user-voice-line me-1"></i>Médico</div>
                <div class="info-box-val">{{ $invoice->doctor->name ?? 'N/A' }}</div>
                <div class="info-box-sub">{{ $invoice->doctor->branch->name ?? '' }}</div>
            </div>

            <div class="info-box">
                <div class="info-box-lbl"><i class="ri-building-2-line me-1"></i>Sucursal / Emitido por</div>
                <div class="info-box-val">{{ $invoice->branch->name }}</div>
                <div class="info-box-sub" style="font-weight:600;color:#344563;">
                    {{ $invoice->user->name }}
                </div>
            </div>

            @if($invoice->insurance)
            <div class="info-box">
                <div class="info-box-lbl"><i class="ri-shield-check-line me-1"></i>Seguro médico</div>
                <div class="info-box-val">{{ $invoice->insurance->name }}</div>
                @if($invoice->authorization_number)
                    <div class="info-box-sub">Autorización: {{ $invoice->authorization_number }}</div>
                @endif
                <div class="info-box-sub" style="color:var(--rt);">
                    Descuento: {{ $company['currency'] }} {{ number_format($invoice->insurance_discount, 2, ',', '.') }}
                </div>
            </div>
            @endif
        </div>

        {{-- ══════════════════════════════════════════════ --}}
        {{-- ✅ BLOQUE COMPROBANTE ELECTRÓNICO (e-CF)       --}}
        {{-- ══════════════════════════════════════════════ --}}
        @if($invoice->ncf_type)
        <div class="ecf-block">
            <div class="ecf-block-title">
                <i class="ri-shield-check-line"></i>
                Comprobante Electrónico (e-CF)
                @php
                    $ecfStatusClass = $invoice->enviada_dgii
                        ? ($invoice->estado_dgii ?? 'pendiente')
                        : 'sin_enviar';
                    $ecfStatusLabel = $invoice->enviada_dgii
                        ? ($invoice->estado_dgii ?? 'enviado')
                        : 'sin enviar';
                @endphp
                <span class="ecf-status {{ $ecfStatusClass }} ms-auto">
                    <i class="ri-radio-button-line"></i>{{ $ecfStatusLabel }}
                </span>
            </div>

            <div class="ecf-grid">
                <div>
                    <div class="ecf-item-lbl">Tipo de e-CF</div>
                    <div class="ecf-item-val">
                        @switch($invoice->ncf_type)
                            @case('credito_fiscal') e-CF 31 — Crédito Fiscal @break
                            @case('consumidor_final') e-CF 32 — Consumidor Final @break
                            @case('gubernamental') e-CF 45 — Gubernamental @break
                            @case('regimen_especial') e-CF 44 — Régimen Especial @break
                            @default {{ $invoice->ncf_type }}
                        @endswitch
                    </div>
                </div>

                <div>
                    <div class="ecf-item-lbl">e-NCF</div>
                    <div class="ecf-item-val">{{ $invoice->encf ?? '—' }}</div>
                </div>

                @if($invoice->track_id)
                <div>
                    <div class="ecf-item-lbl">Track ID (DGII)</div>
                    <div class="ecf-item-val" style="font-size:.78rem;">{{ $invoice->track_id }}</div>
                </div>
                @endif

                @if($invoice->enviada_dgii_at)
                <div>
                    <div class="ecf-item-lbl">Enviado a DGII</div>
                    <div class="ecf-item-val" style="font-family:sans-serif;font-size:.82rem;">
                        {{ $invoice->enviada_dgii_at->format('d/m/Y H:i') }}
                    </div>
                </div>
                @endif

                @if($invoice->customer_rnc)
                <div>
                    <div class="ecf-item-lbl">RNC / Cédula del cliente</div>
                    <div class="ecf-item-val">{{ $invoice->customer_rnc }}</div>
                </div>
                @endif

                @if($invoice->customer_business_name)
                <div>
                    <div class="ecf-item-lbl">Razón Social</div>
                    <div class="ecf-item-val" style="font-family:sans-serif;">{{ $invoice->customer_business_name }}</div>
                </div>
                @endif
            </div>

            {{-- QR + Links --}}
            @if($invoice->qr_link || $invoice->pdf_cloud_url)
            <div class="ecf-qr">
                @if($invoice->qr_link)
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($invoice->qr_link) }}"
                         alt="QR DGII" class="ecf-qr-img">
                @endif
                <div class="ecf-qr-info">
                    @if($invoice->qr_link)
                        <div style="margin-bottom:.35rem;">
                            <strong><i class="ri-qr-code-line me-1"></i>Consulta en DGII:</strong>
                        </div>
                        <div style="word-break:break-all;font-size:.72rem;">
                            <a href="{{ $invoice->qr_link }}" target="_blank" style="color:var(--rp);">
                                {{ $invoice->qr_link }}
                            </a>
                        </div>
                    @endif
                    @if($invoice->pdf_cloud_url)
                        <div style="margin-top:.5rem;">
                            <a href="{{ $invoice->pdf_cloud_url }}" target="_blank"
                               class="btn btn-sm btn-outline-primary" style="border-radius:2rem;font-size:.75rem;">
                                <i class="ri-file-pdf-line me-1"></i>Ver PDF en la nube
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Si tiene error, mostrar --}}
            @if($invoice->ecfDocuments->where('estado', 'error')->count() > 0)
                @php $lastError = $invoice->ecfDocuments->where('estado', 'error')->sortByDesc('created_at')->first(); @endphp
                <div class="alert alert-danger mt-3 mb-0" style="font-size:.8rem;border-radius:.5rem;">
                    <strong><i class="ri-error-warning-line me-1"></i>Último error:</strong>
                    {{ $lastError->error_message ?? 'Sin detalles' }}
                    @if($lastError->error_code)
                        <span class="badge bg-danger ms-2">Código: {{ $lastError->error_code }}</span>
                    @endif
                </div>
            @endif
        </div>
        @endif

        <hr class="inv-divider">

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
                        <th style="text-align:right;width:100px;">Precio</th>
                        <th style="text-align:right;width:120px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                    <tr>
                        <td>{{ $item->service->name }}</td>
                        <td style="text-align:center;">{{ $item->quantity }}</td>
                        <td style="text-align:right;">{{ $company['currency'] }} {{ number_format($item->price, 2, ',', '.') }}</td>
                        <td style="text-align:right;font-weight:600;">{{ $company['currency'] }} {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <hr class="inv-divider">

        {{-- Totals --}}
        <div class="row g-3">
            <div class="col-md-6">
                @if($invoice->status === 'pagada' && $invoice->receipt)
                    <div class="info-box" style="background:rgba(10,179,156,.05);border-color:rgba(10,179,156,.2);">
                        <div class="info-box-lbl" style="color:var(--rt);">
                            <i class="ri-check-double-line me-1"></i>Pago registrado
                        </div>
                        <div class="info-box-val" style="font-family:monospace;color:var(--rt);">
                            {{ $invoice->receipt->receipt_number }}
                        </div>
                        <div class="info-box-sub">
                            Pagado el {{ $invoice->receipt->created_at->format('d/m/Y') }}
                            por {{ $invoice->receipt->user->name }}
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-md-6">
                <p style="font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);margin-bottom:.65rem;">
                    Resumen
                </p>
                <div class="totals-box">
                    <div class="tot-row">
                        <span class="tot-lbl">Subtotal servicios</span>
                        <span class="tot-val">{{ $company['currency'] }} {{ number_format($invoice->subtotal, 2, ',', '.') }}</span>
                    </div>
                    @if($invoice->tax_amount > 0)
                    <div class="tot-row">
                        <span class="tot-lbl">Impuestos ({{ $company['tax_rate'] }}%)</span>
                        <span class="tot-val">{{ $company['currency'] }} {{ number_format($invoice->tax_amount, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($invoice->insurance_discount > 0)
                    <div class="tot-row">
                        <span class="tot-lbl" style="color:var(--rt);">Descuento seguro</span>
                        <span class="tot-val" style="color:var(--rt);">− {{ $company['currency'] }} {{ number_format($invoice->insurance_discount, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="tot-row">
                        <span class="tot-lbl">Total factura</span>
                        <span class="tot-val">{{ $company['currency'] }} {{ number_format($invoice->total, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Footer con datos de la empresa --}}
    <div class="inv-footer">
        <i class="ri-heart-pulse-line me-1"></i>
        {{ $company['name'] }} · {{ $company['footer_text'] }} ·
        Factura generada el {{ $invoice->created_at->format('d/m/Y') }} a las {{ $invoice->created_at->format('g:i A') }}
        <br>
        <small>{{ $company['business_name'] }} · RNC: {{ $company['rnc'] }} · {{ $company['address'] }}</small>
        @if($invoice->customer_business_name)
            <br><small>Cliente: {{ $invoice->customer_business_name }} (RNC: {{ $invoice->customer_rnc }})</small>
        @endif
        @if($invoice->encf)
            <br><small><strong>e-NCF:</strong> {{ $invoice->encf }} · <strong>Track ID:</strong> {{ $invoice->track_id }}</small>
        @endif
    </div>
</div>

{{-- ══════════════════════════════════════════════ --}}
{{-- ✅ HISTORIAL DE ENVÍOS A DGII (ecf_documents) --}}
{{-- ══════════════════════════════════════════════ --}}
@if($invoice->ecfDocuments->count() > 0)
<div class="invoice-paper mt-4" style="max-width:780px;">
    <div class="inv-body">
        <p style="font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);margin-bottom:.65rem;">
            <i class="ri-history-line me-1"></i>Historial de envíos a DGII
        </p>
        <div class="table-responsive">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="text-align:left;">Fecha</th>
                        <th style="text-align:left;">Estado</th>
                        <th style="text-align:left;">e-NCF</th>
                        <th style="text-align:left;">Track ID</th>
                        <th style="text-align:left;">Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->ecfDocuments->sortByDesc('created_at') as $doc)
                    <tr>
                        <td style="font-size:.78rem;">{{ $doc->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <span class="ecf-status {{ $doc->estado ?? 'sin_enviar' }}">
                                {{ $doc->estado ?? 'pendiente' }}
                            </span>
                        </td>
                        <td style="font-family:monospace;font-size:.78rem;">{{ $doc->encf ?? '—' }}</td>
                        <td style="font-family:monospace;font-size:.72rem;word-break:break-all;">
                            {{ $doc->track_id ? \Illuminate\Support\Str::limit($doc->track_id, 30) : '—' }}
                        </td>
                        <td style="font-size:.78rem;">{{ $doc->user->name ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif<div style="height:2rem;"></div>
</div>
</div>

<iframe id="thermal-frame" style="position:fixed;top:-9999px;left:-9999px;width:0;height:0;border:none;"></iframe>

@push('scripts')
@php
    // ── Código de seguridad y fecha de firma ─────────────────────────────
    // Se buscan primero en pdf_cloud_url y, si no están ahí, en qr_link.
    $codigoSeguridad = null;
    $fechaFirma      = null;

    foreach ([$invoice->pdf_cloud_url, $invoice->qr_link] as $src) {
        if (!$src) continue;

        $qs = parse_url($src, PHP_URL_QUERY);
        if (!$qs) continue;

        parse_str($qs, $p);
        $p = array_change_key_case($p, CASE_LOWER);

        if (!$codigoSeguridad && !empty($p['codigoseguridad'])) {
            $codigoSeguridad = $p['codigoseguridad'];
        }

        if (!$fechaFirma && !empty($p['fechafirma'])) {
            $raw = urldecode($p['fechafirma']);
            try {
                $fechaFirma = \Carbon\Carbon::parse($raw)->format('d/m/Y H:i:s');
            } catch (\Throwable $e) {
                $fechaFirma = $raw;
            }
        }
    }
@endphp

<script>
function printThermal() {
    const frame = document.getElementById('thermal-frame');
    const doc   = frame.contentDocument || frame.contentWindow.document;

    const qrLink = @json($invoice->qr_link);

    // URL del QR (api.qrserver.com)
    const qrImgUrl = qrLink
        ? 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(qrLink)
        : null;

    doc.open();
    doc.write(`<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Factura {{ $invoice->invoice_number }}</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }

  /* Sin tamaño ni márgenes fijos: el ancho lo define la impresora/papel del driver */
  @page { size: auto; margin: 0; }

  html, body {
    width: 100%;
    height: auto;
    margin: 0;
    padding: 0;
    font-family: 'Courier New', 'Consolas', monospace;
    font-size: 11px;
    color: #000;
    line-height: 1.25;
    background: #fff;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
  body { padding: 2mm 3mm; }

  .text-center { text-align: center; }
  .text-right  { text-align: right; }
  .bold        { font-weight: bold; }

  .header {
    width: 100%;
    text-align: center;
    border-bottom: 1px dashed #000;
    padding-bottom: 3px;
    margin-bottom: 3px;
  }
  .header .logo { max-width: 70%; max-height: 16mm; margin-bottom: 2px; }
  .header h1 { font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 1px; }
  .header .info { font-size: 9px; }
  .header .doc-type { font-size: 10px; font-weight: bold; margin-top: 2px; }

  .separator { width: 100%; border-top: 1px dashed #000; margin: 2px 0; }

  .section-title {
    width: 100%;
    text-align: center;
    font-size: 10px;
    font-weight: bold;
    text-transform: uppercase;
    border-top: 1px solid #000;
    border-bottom: 1px solid #000;
    padding: 2px 0;
    margin: 4px 0 2px 0;
    background: transparent;
    color: #000;
  }

  .client { width: 100%; font-size: 10px; margin-bottom: 3px; }
  .client .row { display: flex; justify-content: space-between; gap: 6px; }
  .client .lbl { font-weight: bold; }

  table.items {
    width: 100%;
    border-collapse: collapse;
    font-size: 10px;
    margin-bottom: 3px;
  }
  table.items th {
    border-bottom: 1px dashed #000;
    padding: 2px 0;
    text-align: left;
    font-size: 9px;
  }
  table.items th.text-right { text-align: right; }
  table.items td { padding: 1px 0; vertical-align: top; }
  table.items .product-name { font-weight: bold; }
  table.items .product-code { font-size: 8px; color: #333; }

  .totals { width: 100%; font-size: 10px; margin-top: 2px; }
  .totals .row { display: flex; justify-content: space-between; padding: 1px 0; }
  .totals .row.grand {
    border-top: 1px solid #000;
    border-bottom: 1px solid #000;
    margin-top: 2px;
    padding: 3px 0;
    font-size: 12px;
    font-weight: bold;
  }

  .qr-section { width: 100%; text-align: center; margin: 4px 0; }
  .qr-section img { width: 110px; max-width: 60%; height: auto; }
  .qr-section .qr-label { font-size: 8px; margin-top: 2px; }
  .qr-section .sec-info { font-size: 8px; margin-top: 1px; word-break: break-all; }

  .footer {
    width: 100%;
    text-align: center;
    font-size: 9px;
    border-top: 1px dashed #000;
    padding-top: 3px;
    margin-top: 4px;
  }
  .footer .dgi-info { font-size: 8px; margin-top: 2px; word-break: break-all; }
  .footer .thanks   { font-weight: bold; margin-bottom: 2px; }
</style>
</head>
<body>

{{-- HEADER --}}
<div class="header">
    @if(!empty($company['logo']))
        <img src="{{ asset('storage/' . $company['logo']) }}" alt="Logo" class="logo">
    @endif
    <h1>{{ $company['name'] ?? 'Mi Empresa' }}</h1>
    @if(!empty($company['slogan']))
        <div class="info">{{ $company['slogan'] }}</div>
    @endif
    @if(!empty($company['rnc']))
        <div class="info">RNC: {{ $company['rnc'] }}</div>
    @endif
    @if(!empty($company['address']))
        <div class="info">{{ $company['address'] }}</div>
    @endif
    @if(!empty($company['phone']))
        <div class="info">Tel: {{ $company['phone'] }}</div>
    @endif

    <div class="doc-type">
        @switch($invoice->ncf_type)
            @case('consumidor_final') FACTURA DE CONSUMO @break
            @case('credito_fiscal')   FACTURA DE CRÉDITO FISCAL @break
            @case('gubernamental')    COMPROBANTE GUBERNAMENTAL @break
            @case('regimen_especial') RÉGIMEN ESPECIAL @break
            @default                  COMPROBANTE FISCAL
        @endswitch
    </div>
    @if($invoice->encf)
        <div class="info bold">{{ $invoice->encf }}</div>
    @elseif($invoice->ncf)
        <div class="info bold">{{ $invoice->ncf }}</div>
    @endif
</div>

{{-- DATOS DE LA FACTURA --}}
<div class="client">
    <div class="row">
        <span class="lbl">Factura N°:</span>
        <span>{{ $invoice->invoice_number }}</span>
    </div>
    <div class="row">
        <span class="lbl">Fecha:</span>
        <span>{{ $invoice->created_at->format('d/m/Y H:i') }}</span>
    </div>
    <div class="row">
        <span class="lbl">Estado:</span>
        <span>{{ strtoupper($invoice->status) }}</span>
    </div>
    @if($invoice->ecfSequence && $invoice->ecfSequence->fecha_vencimiento)
        <div class="row">
            <span class="lbl">Vence:</span>
            <span>{{ $invoice->ecfSequence->fecha_vencimiento->format('d/m/Y') }}</span>
        </div>
    @endif
    <div class="row">
        <span class="lbl">Sucursal:</span>
        <span>{{ $invoice->branch->name ?? '—' }}</span>
    </div>
    <div class="row">
        <span class="lbl">Cajero:</span>
        <span>{{ $invoice->user->name ?? '—' }}</span>
    </div>
</div>

<div class="separator"></div>

{{-- PACIENTE --}}
<div class="client">
    <div class="row">
        <span class="lbl">Paciente:</span>
        <span>{{ $invoice->patient->first_name . ' ' . $invoice->patient->last_name }}</span>
    </div>
    <div class="row">
        <span class="lbl">Cédula:</span>
        <span>{{ $invoice->patient->cedula ?? '—' }}</span>
    </div>
    @if($invoice->patient->phone)
    <div class="row">
        <span class="lbl">Tel:</span>
        <span>{{ $invoice->patient->phone }}</span>
    </div>
    @endif
    <div class="row">
        <span class="lbl">Médico:</span>
        <span>{{ $invoice->doctor->name ?? 'N/A' }}</span>
    </div>
</div>

@if($invoice->insurance)
<div class="separator"></div>
<div class="client">
    <div class="row">
        <span class="lbl">Seguro:</span>
        <span>{{ $invoice->insurance->name }}</span>
    </div>
    <div class="row">
        <span class="lbl">Cobertura:</span>
        <span>{{ $invoice->insurance->coverage_percentage }}%</span>
    </div>
    @if($invoice->authorization_number)
    <div class="row">
        <span class="lbl">Autorización:</span>
        <span>{{ $invoice->authorization_number }}</span>
    </div>
    @endif
</div>
@endif

@if($invoice->customer_business_name)
<div class="separator"></div>
<div class="client">
    <div class="row">
        <span class="lbl">Cliente:</span>
        <span>{{ $invoice->customer_business_name }}</span>
    </div>
    @if($invoice->customer_rnc)
    <div class="row">
        <span class="lbl">RNC:</span>
        <span>{{ $invoice->customer_rnc }}</span>
    </div>
    @endif
</div>
@endif

{{-- ITEMS --}}
<div class="section-title">Servicios</div>
<table class="items">
    <thead>
        <tr>
            <th style="width:10%;">Cant</th>
            <th style="width:55%;">Descripción</th>
            <th class="text-right" style="width:35%;">Importe</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->items as $item)
        <tr>
            <td>{{ $item->quantity }}</td>
            <td>
                <div class="product-name">{{ $item->service->name ?? 'Servicio' }}</div>
                <div class="product-code">
                    {{ $item->quantity }} x RD$ {{ number_format($item->price, 2) }}
                    @if($item->insurance_amount > 0)
                        · Seg: RD$ {{ number_format($item->insurance_amount, 2) }}
                    @endif
                </div>
            </td>
            <td class="text-right">RD$ {{ number_format($item->subtotal, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- TOTALES --}}
<div class="totals">
    <div class="row">
        <span>Subtotal:</span>
        <span>RD$ {{ number_format($invoice->subtotal, 2) }}</span>
    </div>
    @if($invoice->tax_amount > 0)
    <div class="row">
        <span>ITBIS:</span>
        <span>RD$ {{ number_format($invoice->tax_amount, 2) }}</span>
    </div>
    @endif
    @if($invoice->insurance_discount > 0)
    <div class="row">
        <span>Desc. seguro:</span>
        <span>- RD$ {{ number_format($invoice->insurance_discount, 2) }}</span>
    </div>
    @endif
    <div class="row grand">
        <span>TOTAL:</span>
        <span>RD$ {{ number_format($invoice->total, 2) }}</span>
    </div>
</div>

{{-- QR + CÓDIGO DE SEGURIDAD + FECHA DE FIRMA --}}
@if($invoice->qr_link || $codigoSeguridad || $fechaFirma)
<div class="qr-section">
    @if($invoice->qr_link)
        <img src="${qrImgUrl}" alt="QR DGII">
        <div class="qr-label">Verifique en dgii.gov.do</div>
    @endif

    @if($codigoSeguridad)
        <div class="sec-info"><strong>Código de Seguridad:</strong> {{ $codigoSeguridad }}</div>
    @endif

    @if($fechaFirma)
        <div class="sec-info"><strong>Fecha Firma:</strong> {{ $fechaFirma }}</div>
    @endif
</div>
@endif

{{-- FOOTER --}}
<div class="footer">
    <div class="thanks">{{ $company['footer_text'] ?? '¡GRACIAS POR SU PREFERENCIA!' }}</div>
    <div>{{ $company['business_name'] ?? $company['name'] }}</div>

    @if($invoice->encf)
        <div class="dgi-info">
            e-CF: {{ $invoice->encf }}
            @if($invoice->track_id)
                <br>Track ID: {{ \Illuminate\Support\Str::limit($invoice->track_id, 20) }}
            @endif
        </div>
    @endif
</div>

<div style="margin-top:12mm;"></div>

</body>
</html>`);
    doc.close();

    setTimeout(() => {
        frame.contentWindow.focus();
        frame.contentWindow.print();
    }, 300);
}
</script>
@endpush

</x-app-layout>